<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Utility\Location;
use Cake\Datasource\ConnectionManager;

/**
 * Dashboard Controller
 *
 * @property \App\Model\Table\ClientsTable $Clients
 */
class DashboardsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Tools.AuthUser');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->loadModel('Users');
        $this->loadModel('UsersRoles');
        $this->loadModel('UserLeaves');
        $this->loadModel('Attendances');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        
        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    


        $departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
        $userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        
        $total_attend_month=0;
        $total_absent_month=0;
        $total_time_off_month=0;
        $user_total_late_month=0;

        if ($userRoles->hasRole(['Master Admin'])) {
            //list all user except master admin
			$users = $this->Users->find('list')->where(['status'=>'1'])->order(['Users.name' => 'ASC'])->innerJoinWith('Roles' , function($q){ return $q->where(['Roles.id !='=>'1']);});

            
            if(!empty($departmentSelected) AND !empty($userSelected)){

                // count user late attendance for current month
                $sql_count_user_late = "SELECT COUNT(*) AS total_late from  attendances WHERE `attendance_code_id`=1 AND MONTH(attendances.`cdate`) = MONTH(CURRENT_DATE()) AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00' AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_late= $conn->execute($sql_count_user_late);
                $count_user_total_late= $stmt_sql_count_user_late->fetch('assoc');
                $user_total_late_month = $count_user_total_late['total_late'];

                // count user time off for current month
                $sql_count_user_timeoff="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (MONTH(ul.date_start) = MONTH(CURRENT_DATE()) OR MONTH(ul.date_end) = MONTH(CURRENT_DATE())) AND ul.user_id=$userSelected";
                $stmt_sql_count_user_timeoff= $conn->execute($sql_count_user_timeoff);
                $count_user_timeoff = $stmt_sql_count_user_timeoff->fetch('assoc');
                $total_time_off_month = $count_user_timeoff['total_time_off'];

                // count user absent in current month
                $sql_count_user_absent="SELECT COUNT(*) AS total_absent from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=2 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_absent= $conn->execute($sql_count_user_absent);
                $count_user_absent = $stmt_sql_count_user_absent->fetch('assoc');
                $total_absent_month = $count_user_absent['total_absent'];

                // count user attendance in current month
                $sql_count_user_attendance="SELECT COUNT(*) AS total_attend from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=1 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_attendance= $conn->execute($sql_count_user_attendance);
                $count_user_attendance = $stmt_sql_count_user_attendance->fetch('assoc');
                $total_attend_month = $count_user_attendance['total_attend'];

                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$departmentSelected";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];

                // count late attendance
                $sql_late = "SELECT COUNT(*) AS total_late from  attendances JOIN users ON users.id=attendances.`user_id`  JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE `attendance_code_id`=1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$departmentSelected AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00'";
                $stmt_count_late= $conn->execute($sql_late);
                $count_total_late= $stmt_count_late->fetch('assoc');
                $total_late = $count_total_late['total_late'];

                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$userSelected";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $sql="SELECT COUNT(*) AS total_staff From Users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users_roles.id != 1 AND users.status=1 AND organizations.id=$departmentSelected";
                $stmt_sql = $conn->execute($sql);
                $stmt_sql_result = $stmt_sql->fetch('assoc');
                $count_all_user=$stmt_sql_result['total_staff'];

                //count attendance
                $sql_count_attendance="SELECT count(users.id) AS total_attend FROM users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` LEFT JOIN attendances ON attendances.`user_id` = users.id WHERE users_roles.id != 1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$departmentSelected AND users.id=$userSelected";
                $stmt = $conn->execute($sql_count_attendance);
                $total_attend = $stmt->fetch('assoc');
                $staff_working = $total_attend['total_attend'];
                $staff_absent=$count_all_user-$staff_working;

            }else if(!empty($departmentSelected) AND empty($userSelected)){

                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$departmentSelected";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];

                // count late attendance
                $sql_late = "SELECT COUNT(*) AS total_late from  attendances JOIN users ON users.id=attendances.`user_id`  JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE `attendance_code_id`=1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$departmentSelected AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00'";
                $stmt_count_late= $conn->execute($sql_late);
                $count_total_late= $stmt_count_late->fetch('assoc');
                $total_late = $count_total_late['total_late'];


                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$departmentSelected";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $sql="SELECT COUNT(*) AS total_staff From Users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users_roles.id != 1 AND users.status=1 AND organizations.id=$departmentSelected";
                $stmt_sql = $conn->execute($sql);
                $stmt_sql_result = $stmt_sql->fetch('assoc');
                $count_all_user=$stmt_sql_result['total_staff'];
                
                //count attendance
                $sql_count_attendance="SELECT count(users.id) AS total_attend FROM users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` LEFT JOIN attendances ON attendances.`user_id` = users.id WHERE users_roles.id != 1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$departmentSelected";
                $stmt = $conn->execute($sql_count_attendance);
                $total_attend = $stmt->fetch('assoc');
                $staff_working = $total_attend['total_attend'];
                $staff_absent=$count_all_user-$staff_working;

            }else if(empty($departmentSelected) AND !empty($userSelected)){
                $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userSelected"])->limit(1)->first();
                $user_organization_id=$usersOrganization->organization_id;

                // count user late attendance for current month
                $sql_count_user_late = "SELECT COUNT(*) AS total_late from  attendances WHERE `attendance_code_id`=1 AND MONTH(attendances.`cdate`) = MONTH(CURRENT_DATE()) AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00' AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_late= $conn->execute($sql_count_user_late);
                $count_user_total_late= $stmt_sql_count_user_late->fetch('assoc');
                $user_total_late_month = $count_user_total_late['total_late'];

                // count user time off for current month
                $sql_count_user_timeoff="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (MONTH(ul.date_start) = MONTH(CURRENT_DATE()) OR MONTH(ul.date_end) = MONTH(CURRENT_DATE())) AND ul.user_id=$userSelected";
                $stmt_sql_count_user_timeoff= $conn->execute($sql_count_user_timeoff);
                $count_user_timeoff = $stmt_sql_count_user_timeoff->fetch('assoc');
                $total_time_off_month = $count_user_timeoff['total_time_off'];

                // count user absent in current month
                $sql_count_user_absent="SELECT COUNT(*) AS total_absent from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=2 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_absent= $conn->execute($sql_count_user_absent);
                $count_user_absent = $stmt_sql_count_user_absent->fetch('assoc');
                $total_absent_month = $count_user_absent['total_absent'];

                // count user attendance in current month
                $sql_count_user_attendance="SELECT COUNT(*) AS total_attend from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=1 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_attendance= $conn->execute($sql_count_user_attendance);
                $count_user_attendance = $stmt_sql_count_user_attendance->fetch('assoc');
                $total_attend_month = $count_user_attendance['total_attend'];

                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$user_organization_id";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];

                // count late attendance
                $sql_late = "SELECT COUNT(*) AS total_late from  attendances JOIN users ON users.id=attendances.`user_id`  JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE `attendance_code_id`=1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$user_organization_id AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00'";
                $stmt_count_late= $conn->execute($sql_late);
                $count_total_late= $stmt_count_late->fetch('assoc');
                $total_late = $count_total_late['total_late'];

                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$user_organization_id";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $sql="SELECT COUNT(*) AS total_staff From Users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users_roles.id != 1 AND users.status=1 AND organizations.id=$user_organization_id";
                $stmt_sql = $conn->execute($sql);
                $stmt_sql_result = $stmt_sql->fetch('assoc');
                $count_all_user=$stmt_sql_result['total_staff'];


                //count attendance
                $sql_count_attendance="SELECT count(users.id) AS total_attend FROM users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` LEFT JOIN attendances ON attendances.`user_id` = users.id WHERE users_roles.id != 1 AND DATE(attendances.cdate)=CURDATE() AND users.id=$userSelected";
                $stmt = $conn->execute($sql_count_attendance);
                $total_attend = $stmt->fetch('assoc');
                $staff_working = $total_attend['total_attend'];
                $staff_absent=$count_all_user-$staff_working;

            }else if(empty($departmentSelected) AND empty($userSelected)){

                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves WHERE `leave_status_id`=1";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];

                // count late attendance
                $sql_late = "SELECT COUNT(*) AS total_late from  attendances WHERE `attendance_code_id`=1 AND DATE(attendances.cdate)=CURDATE() AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00'";
                $stmt_count_late= $conn->execute($sql_late);
                $count_total_late= $stmt_count_late->fetch('assoc');
                $total_late = $count_total_late['total_late'];
                
                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE())";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $count_all_user=$this->Users->find()->where(['status'=>'1'])->innerJoinWith('Roles' , function($q){ return $q->where(['Roles.id !='=>'1']);})->count();

                //count attendance
                $sql_count_attendance="SELECT count(users.id) AS total_attend FROM users JOIN users_roles ON users.`id`=users_roles.`user_id` LEFT JOIN attendances ON attendances.`user_id` = users.id WHERE users_roles.id != 1 AND DATE(attendances.cdate)=CURDATE() AND users.status=1";
                $stmt = $conn->execute($sql_count_attendance);
                $total_attend = $stmt->fetch('assoc');
                $staff_working = $total_attend['total_attend'];
                $staff_absent=$count_all_user-$staff_working;

                $totalStaff = $staff_absent + $staff_timeoff + $staff_working;

            }

        }else  if ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {

            $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userId"])->limit(1)->first();
            $user_organization_id=$usersOrganization->organization_id;

            $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
            return $q->where(['UserOrganizations.organization_id'=>$user_organization_id])->where(['Users.status'=>1]);});

            if(!empty($userSelected)){
                // count user late attendance for current month
                $sql_count_user_late = "SELECT COUNT(*) AS total_late from  attendances WHERE `attendance_code_id`=1 AND MONTH(attendances.`cdate`) = MONTH(CURRENT_DATE()) AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00' AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_late= $conn->execute($sql_count_user_late);
                $count_user_total_late= $stmt_sql_count_user_late->fetch('assoc');
                $user_total_late_month = $count_user_total_late['total_late'];

                // count user time off for current month
                $sql_count_user_timeoff="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (MONTH(ul.date_start) = MONTH(CURRENT_DATE()) OR MONTH(ul.date_end) = MONTH(CURRENT_DATE())) AND ul.user_id=$userSelected";
                $stmt_sql_count_user_timeoff= $conn->execute($sql_count_user_timeoff);
                $count_user_timeoff = $stmt_sql_count_user_timeoff->fetch('assoc');
                $total_time_off_month = $count_user_timeoff['total_time_off'];

                // count user absent in current month
                $sql_count_user_absent="SELECT COUNT(*) AS total_absent from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=2 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_absent= $conn->execute($sql_count_user_absent);
                $count_user_absent = $stmt_sql_count_user_absent->fetch('assoc');
                $total_absent_month = $count_user_absent['total_absent'];

                // count user attendance in current month
                $sql_count_user_attendance="SELECT COUNT(*) AS total_attend from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=1 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_attendance= $conn->execute($sql_count_user_attendance);
                $count_user_attendance = $stmt_sql_count_user_attendance->fetch('assoc');
                $total_attend_month = $count_user_attendance['total_attend'];
            }

            //count pending
            $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$user_organization_id";
            /*if(!empty($userSelected)){
                $sql_pending.=" AND user_leaves.user_id=$userSelected";
            }*/
            $stmt_sql_pending= $conn->execute($sql_pending);
            $count_pending= $stmt_sql_pending->fetch('assoc');
            $total_pending = $count_pending['total_pending'];

            // count late attendance
            $sql_late = "SELECT COUNT(*) AS total_late from  attendances JOIN users ON users.id=attendances.`user_id`  JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE `attendance_code_id`=1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$user_organization_id AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00'";
            /*if(!empty($userSelected)){
                $sql_late.=" AND attendances.user_id=$userSelected";
            }*/
            $stmt_count_late= $conn->execute($sql_late);
            $count_total_late= $stmt_count_late->fetch('assoc');
            $total_late = $count_total_late['total_late'];

            //count time off
            $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$user_organization_id";
            /*if(!empty($userSelected)){
                $sql_count_time_off.=" AND ul.user_id=$userSelected";
            }*/
            $stmt_count_time_off= $conn->execute($sql_count_time_off);
            $total_time_off = $stmt_count_time_off->fetch('assoc');
            $staff_timeoff = $total_time_off['total_time_off'];

            //count all user
            $sql="SELECT COUNT(*) AS total_staff From Users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users_roles.id != 1 AND users.status=1 AND organizations.id=$user_organization_id";
            $stmt_sql = $conn->execute($sql);
            $stmt_sql_result = $stmt_sql->fetch('assoc');
            $count_all_user=$stmt_sql_result['total_staff'];
            
            //count attendance
            $sql_count_attendance="SELECT count(users.id) AS total_attend FROM users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` LEFT JOIN attendances ON attendances.`user_id` = users.id WHERE users_roles.id != 1 AND DATE(attendances.cdate)=CURDATE() AND organizations.id=$user_organization_id";
            $stmt = $conn->execute($sql_count_attendance);
            $total_attend = $stmt->fetch('assoc');
            $staff_working = $total_attend['total_attend'];
            $staff_absent=$count_all_user-$staff_working;

			/*foreach($users as $user){
				$user_ids[] = $user->id;
			}
			$users = $this->Users->find('list')->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userId])->order(['name' => 'ASC']);*/

        }else  if ($userRoles->hasRole(['Staff'])) {

            // count user late attendance for current month
                $sql_count_user_late = "SELECT COUNT(*) AS total_late from  attendances WHERE `attendance_code_id`=1 AND MONTH(attendances.`cdate`) = MONTH(CURRENT_DATE()) AND DATE_FORMAT(attendances.cdate, '%H:%i:%s')>='09:00:00' AND attendances.user_id=$userSelected";
                $stmt_sql_count_user_late= $conn->execute($sql_count_user_late);
                $count_user_total_late= $stmt_sql_count_user_late->fetch('assoc');
                $user_total_late_month = $count_user_total_late['total_late'];

            // count user time off for current month
                $sql_count_user_timeoff="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (MONTH(ul.date_start) = MONTH(CURRENT_DATE()) OR MONTH(ul.date_end) = MONTH(CURRENT_DATE())) AND ul.user_id=$userId";
                $stmt_sql_count_user_timeoff= $conn->execute($sql_count_user_timeoff);
                $count_user_timeoff = $stmt_sql_count_user_timeoff->fetch('assoc');
                $total_time_off_month = $count_user_timeoff['total_time_off'];

            // count user absent in current month
                $sql_count_user_absent="SELECT COUNT(*) AS total_absent from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=2 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userId";
                $stmt_sql_count_user_absent= $conn->execute($sql_count_user_absent);
                $count_user_absent = $stmt_sql_count_user_absent->fetch('assoc');
                $total_absent_month = $count_user_absent['total_absent'];

            // count user attendance in current month
            $sql_count_user_attendance="SELECT COUNT(*) AS total_attend from  attendances JOIN users ON users.id=attendances.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` Where `attendance_code_id`=1 AND MONTH(attendances.cdate) = MONTH(CURRENT_DATE()) AND attendances.user_id=$userId";
            $stmt_sql_count_user_attendance= $conn->execute($sql_count_user_attendance);
            $count_user_attendance = $stmt_sql_count_user_attendance->fetch('assoc');
            $total_attend_month = $count_user_attendance['total_attend'];
        }


        if($userSelected){
            $user = $this->Users->find()->where(['id' => $userSelected])->first();
        }

        $departments = $this->Organizations->find('list');

		//$staff_absent = 2;
		//$staff_working = 10;
		$notifications = 3;
		//$staff_timeoff = 1;


        //for graph late in
        /*$thisYear = date('Y');
        $thisMonth = date('m');
        if ($userRoles->hasRole(['Admin','Supervisor'])){
            $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userId"])->limit(1)->first();
            $user_organization_id=$usersOrganization->organization_id;
            $departmentSelected = $user_organization_id;
        }
        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userId"])->limit(1)->first();
            $user_organization_id=$usersOrganization->organization_id;
        $lateCanvasSql = "SELECT COUNT(attn.id) as totalLate,u.name,MONTH(attn.cdate) as monthcanvas
                    FROM attendances attn
                    LEFT JOIN users u ON u.id=attn.user_id
                    LEFT JOIN user_organizations uo on uo.user_id = u.id
                    WHERE DATE_FORMAT(attn.cdate, '%H:%i:%s')>'09:00:00' AND YEAR(attn.cdate)='".$thisYear."'
                    AND attn.status=1";
        if($departmentSelected){
            $lateCanvasSql .= " AND uo.organization_id='".$departmentSelected."'";
        }
       
        $lateCanvasSql .= " GROUP BY MONTH(attn.cdate)";
        $stmt_sql_lateCanvas= $conn->execute($lateCanvasSql);
        $count_lateCanvas= $stmt_sql_lateCanvas->fetchAll('assoc');
        $this->set('Lateresult',$count_lateCanvas);
        
        //for normal late in
        $normalCanvasSql = "SELECT COUNT(attn.id) as totalLate,u.name,MONTH(attn.cdate) as monthcanvas
                    FROM attendances attn
                    LEFT JOIN users u ON u.id=attn.user_id
                    LEFT JOIN user_organizations uo on uo.user_id = u.id
                    WHERE DATE_FORMAT(attn.cdate, '%H:%i:%s')<='09:00:00' AND YEAR(attn.cdate)='".$thisYear."'
                    AND attn.status=1";
        if($departmentSelected){
            $normalCanvasSql .= " AND uo.organization_id='".$departmentSelected."'";
        }
       
        $normalCanvasSql .= " GROUP BY MONTH(attn.cdate)";
        $stmt_sql_normalCanvas= $conn->execute($normalCanvasSql);
        $count_normalCanvas= $stmt_sql_normalCanvas->fetchAll('assoc');
        $this->set('Normalresult',$count_normalCanvas);
*/
        
        //for time in grapf

        $inTimeCanvasSql = "select attn.* 
                        FROM attendances attn
                        WHERE status =1 AND MONTH(attn.cdate)= '".$thisMonth."'";
        if($userSelected){
            $inTimeCanvasSql .= " AND attn.user_id='".$userSelected."'";
        } else {
            $inTimeCanvasSql .= " AND attn.user_id='".$userId."'";
        }
       
        $stmt_sql_inTimeCanvas= $conn->execute($inTimeCanvasSql);
        $count_inTimeCanvas= $stmt_sql_inTimeCanvas->fetchAll('assoc');
        $this->set('inTimeresult',$count_inTimeCanvas);
       

        $this->set(compact('user', 'Lateresult','Normalresult','inTimeresult','inTimeCanvasSql','userRoles','departments','users','departmentSelected','userSelected','staff_absent','staff_working','notifications','staff_timeoff','total_late','total_pending','total_attend_month','total_absent_month','total_time_off_month','user_total_late_month','totalStaff'));
        $this->set('_serialize', ['dashboard']);
    }
    
    public function getUsers()
    {
        $this->loadModel('Users');
        $department_id = $_GET['id'];
        $userId = $this->AuthUser->id();
		$user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		if ($userRoles->hasRole(['Master Admin'])) {
			$users = $this->Users->find();
        }else  if ($userRoles->hasRole(['Supervisor'])) {
			$users = $this->Users->find()->where(['report_to'=>$userId]);
			foreach($users as $user){
				$user_ids[] = $user->id;
			}
			$users = $this->Users->find()->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userId]);
        }else if($userRoles->hasRole(['Admin'])){
			$users = $this->Users->find()->contain(['Organizations'])->where(['report_to'=> $userId]);
		}
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
        $this->viewBuilder()->layout('ajax');
    }

    public function getDetails()
    {
        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');

        $department_id = $_GET['id'];

        $userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        if ($userRoles->hasRole(['Master Admin'])) {
             $users = $this->Users->find('all')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($department_id){
        return $q->where(['UserOrganizations.organization_id'=>$department_id])->where(['Users.status'=>1]);});
        }
       
        
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
        $this->viewBuilder()->layout('ajax');
    }
}
