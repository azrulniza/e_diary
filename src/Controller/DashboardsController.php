<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Utility\Location;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;


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
        $user_total_normal_month=0;

        if ($userRoles->hasRole(['Master Admin'])) {
            //list all user except master admin
			$users = $this->Users->find('list')->where(['status'=>'1'])->order(['Users.name' => 'ASC']);

            
            if(!empty($departmentSelected) AND !empty($userSelected)){
				$curr_users = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])
					->autoFields(true)->where(['Users.status'=>1,'Users.id'=>$userSelected]);
                // count user late attendance for current month
				$total_late=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') > '09:00:00'){
								$total_late++;
							}
						}
					}
				}
                $user_total_late_month = $total_late;

				// count user normal attendance for current month
				$total_normal=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') < '09:00:00'){
								$total_normal++;
							}
						}
					}
				}
                $user_total_normal_month = $total_normal;
				
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
				$total_attend=0;
				$past_date = '';
				foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())']);	
					foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->attendance_code_id == 1){
								$total_attend++;
							}
						}
					}
					
				}
                $total_attend_month = $total_attend;

                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$departmentSelected";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];
				
				$all_user = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
								return $q->where(['UserOrganizations.organization_id'=>$departmentSelected]);
						})
						->autoFields(true)->where(['Users.status'=>1]);
						
                // count late attendance
                $total_late=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances != null ){
						if($attendances->cdate->format('H:i:s') > '09:00:00'){
							$total_late++;
						}
					}
				}

                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$userSelected";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $sql="SELECT COUNT(*) AS total_staff From users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users.status=1 AND organizations.id=$departmentSelected";
                $stmt_sql = $conn->execute($sql);
                $stmt_sql_result = $stmt_sql->fetch('assoc');
                $count_all_user=$stmt_sql_result['total_staff'];

                //count attendance
				$total_attend=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances->status == 1){
						$total_attend++;
					}
				}
                $staff_working = $total_attend;
                $staff_absent=$count_all_user-$staff_working;
				
            }else if(!empty($departmentSelected) AND empty($userSelected)){
				$all_user = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
								return $q->where(['UserOrganizations.organization_id'=>$departmentSelected]);
						})
						->autoFields(true)->where(['Users.status'=>1]);
						
                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$departmentSelected";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];

                // count late attendance
                $total_late=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances != null ){
						if($attendances->cdate->format('H:i:s') > '09:00:00'){
							$total_late++;
						}
					}
				}


                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$departmentSelected";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $sql="SELECT COUNT(*) AS total_staff From users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users.status=1 AND organizations.id=$departmentSelected";
                $stmt_sql = $conn->execute($sql);
                $stmt_sql_result = $stmt_sql->fetch('assoc');
                $count_all_user=$stmt_sql_result['total_staff'];
                
                //count attendance
				$total_attend=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances->status == 1){
						$total_attend++;
					}
				}
                $staff_working = $total_attend;
                $staff_absent=$count_all_user-$staff_working;

            }else if(empty($departmentSelected) AND !empty($userSelected)){
                $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userSelected"])->limit(1)->first();
                $user_organization_id=$usersOrganization->organization_id;

				$curr_users = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])
					->autoFields(true)->where(['Users.status'=>1,'Users.id'=>$userSelected]);
                // count user late attendance for current month
				$total_late=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') >'09:00:00'){
								$total_late++;
							}
						}
					}
				}
                $user_total_late_month = $total_late;

				// count user normal attendance for current month
				$total_normal=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') < '09:00:00'){
								$total_normal++;
							}
						}
					}
				}
                $user_total_normal_month = $total_normal;

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
				$total_attend=0;
				$past_date = '';
				foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())']);	
					foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->attendance_code_id == 1){
								$total_attend++;
							}
						}
					}
					
				}
                $total_attend_month = $total_attend;

                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$user_organization_id";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];
				
				$all_user = $this->Users->find()->contain(['UserDesignations.Designations','UsersRoles.Roles'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
								return $q->where(['UserOrganizations.organization_id'=>$user_organization_id]);
						})
						->autoFields(true)->where(['Users.status'=>1]);
						
                // count late attendance
                $total_late=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances != null ){
						if($attendances->cdate->format('H:i:s') > '09:00:00'){
							$total_late++;
						}
					}
				}

                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$user_organization_id";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $sql="SELECT COUNT(*) AS total_staff From users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users.status=1 AND organizations.id=$user_organization_id";
                $stmt_sql = $conn->execute($sql);
                $stmt_sql_result = $stmt_sql->fetch('assoc');
                $count_all_user=$stmt_sql_result['total_staff'];


                //count attendance
				$total_attend=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances->status == 1){
						$total_attend++;
					}
				}
                $staff_working = $total_attend;
                $staff_absent=$count_all_user-$staff_working;

            }else if(empty($departmentSelected) AND empty($userSelected)){
				$all_user = $this->Users->find()
					->where(['Users.status'=>1]);
						
                //count pending
                $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves WHERE `leave_status_id`=1";
                $stmt_sql_pending= $conn->execute($sql_pending);
                $count_pending= $stmt_sql_pending->fetch('assoc');
                $total_pending = $count_pending['total_pending'];
				
                // count late attendance
                $total_late=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances != null ){
						if($attendances->cdate->format('H:i:s') > '09:00:00'){
							$total_late++;
						}
					}
				}
                
                //count time off
                $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE())";
                $stmt_count_time_off= $conn->execute($sql_count_time_off);
                $total_time_off = $stmt_count_time_off->fetch('assoc');
                $staff_timeoff = $total_time_off['total_time_off'];

                //count all user
                $count_all_user=$this->Users->find()->where(['status'=>'1'])->count();

                //count attendance
				$total_attend=0;
				foreach($all_user as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
					if($attendances->status == 1){
						$total_attend++;
					}
				}
                $staff_working = $total_attend;
                $staff_absent=$count_all_user-$staff_working;

                $totalStaff = $staff_absent + $staff_timeoff + $staff_working;

            }

        }else  if ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {

            $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userId"])->limit(1)->first();
            $user_organization_id=$usersOrganization->organization_id;

            $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
            return $q->where(['UserOrganizations.organization_id'=>$user_organization_id])->where(['Users.status'=>1]);})->group(['Users.id']);

            if(!empty($userSelected)){
				$curr_users = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])
					->autoFields(true)->where(['Users.status'=>1,'Users.id'=>$userSelected]);
					
                // count user late attendance for current month
				$total_late=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') > '09:00:00'){
								$total_late++;
							}
						}
					}
				}
                $user_total_late_month = $total_late;

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
				$total_attend=0;
				$past_date = '';
				foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())']);	
					foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->attendance_code_id == 1){
								$total_attend++;
							}
						}
					}
					
				}
                $total_attend_month = $total_attend;
				
				// count user normal attendance for current month
				$total_normal=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') < '09:00:00'){
								$total_normal++;
							}
						}
					}
				}
                $user_total_normal_month = $total_normal;
            }
			$all_user = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
								return $q->where(['UserOrganizations.organization_id'=>$user_organization_id]);
						})
					->autoFields(true)->where(['Users.status'=>1]);
            //count pending
            $sql_pending = "SELECT COUNT(*) AS total_pending from  user_leaves JOIN users ON users.id=user_leaves.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE user_leaves.leave_status_id=1 AND organizations.id=$user_organization_id";
            /*if(!empty($userSelected)){
                $sql_pending.=" AND user_leaves.user_id=$userSelected";
            }*/
            $stmt_sql_pending= $conn->execute($sql_pending);
            $count_pending= $stmt_sql_pending->fetch('assoc');
            $total_pending = $count_pending['total_pending'];

            // count late attendance
			$total_late=0;
			$past_date = '';
			foreach($all_user as $user){
				$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
				if($attendances != null ){
					if($attendances->cdate->format('H:i:s') > '09:00:00'){
						$total_late++;
					}
				}
			}

            //count time off
            $sql_count_time_off="SELECT COUNT(*) AS total_time_off FROM `user_leaves` ul JOIN users ON users.id=ul.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id  JOIN organizations ON `organizations`.`id`= user_organizations.`organization_id` WHERE (DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= CURDATE() AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= CURDATE()) AND organizations.id=$user_organization_id";
            /*if(!empty($userSelected)){
                $sql_count_time_off.=" AND ul.user_id=$userSelected";
            }*/
            $stmt_count_time_off= $conn->execute($sql_count_time_off);
            $total_time_off = $stmt_count_time_off->fetch('assoc');
            $staff_timeoff = $total_time_off['total_time_off'];

            //count all user
            $sql="SELECT COUNT(*) AS total_staff From users JOIN users_roles ON users.`id`=users_roles.`user_id` JOIN user_organizations ON `user_organizations`.`user_id`=users.id JOIN organizations ON organizations.id = user_organizations.`organization_id` WHERE users.status=1 AND organizations.id=$user_organization_id";
            $stmt_sql = $conn->execute($sql);
            $stmt_sql_result = $stmt_sql->fetch('assoc');
            $count_all_user=$stmt_sql_result['total_staff'];
            
            //count attendance
			$total_attend=0;
			foreach($all_user as $user){
				$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'DATE(cdate) = CURDATE()'])->order('cdate DESC')->first();
				if($attendances->status == 1){
					$total_attend++;
				}
			}
            $staff_working = $total_attend;
            $staff_absent=$count_all_user-$staff_working;

			/*foreach($users as $user){
				$user_ids[] = $user->id;
			}
			$users = $this->Users->find('list')->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userId])->order(['name' => 'ASC']);*/

        }else  if ($userRoles->hasRole(['Staff'])) {
            	$curr_users = $this->Users->find()->contain(['UserDesignations.Designations','UserOrganizations.Organizations','UsersRoles.Roles'])
					->autoFields(true)->where(['Users.status'=>1,'Users.id'=>$userId]);
            // count user late attendance for current month
				$total_late=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') > '09:00:00'){
								$total_late++;
							}
						}
					}
				}
                $user_total_late_month = $total_late;

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
				$total_attend=0;
				$past_date = '';
				foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())']);	
					foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->attendance_code_id == 1){
								$total_attend++;
							}
						}
					}
					
				}
				$total_attend_month = $total_attend;
				
			// count user normal attendance for current month
				$total_normal=0;
				$past_date = '';
                foreach($curr_users as $user){
					$attendances = $this->Attendances->find()->where(['attendance_code_id'=>1,'user_id'=>$user->id,'MONTH(cdate) = MONTH(CURRENT_DATE())'])->order(['cdate DESC']);
						foreach($attendances as $attendance){
						if(!in_array($attendance->cdate->format('Y-m-d'),$past_date)){
							$past_date = explode(',',$attendance->cdate->format('Y-m-d'));
							if($attendance->cdate->format('H:i:s') < '09:00:00'){
								$total_normal++;
							}
						}
					}
				}
                $user_total_normal_month = $total_normal;

        }


        if($userSelected){
            $user = $this->Users->find()->where(['id' => $userSelected])->first();
        }

        $departments = $this->Organizations->find('list')->where(['status'=>1]);

		//$staff_absent = 2;
		//$staff_working = 10;
		$notifications = 3;
		//$staff_timeoff = 1;


        //for graph late in
        $thisYear = date('Y');
        $thisMonth = date('m');
        if ($userRoles->hasRole(['Admin','Supervisor'])){
            $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userId"])->limit(1)->first();
            $user_organization_id=$usersOrganization->organization_id;
            $departmentSelected = $user_organization_id;
        }
        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userId"])->limit(1)->first();
            $user_organization_id=$usersOrganization->organization_id;
        $lateCanvasSql = "SELECT COUNT(DISTINCT(DAY(attn.cdate))) as totalLate,u.name,MONTH(attn.cdate) as monthcanvas
                    FROM attendances attn
                    LEFT JOIN users u ON u.id=attn.user_id
                    LEFT JOIN user_organizations uo on uo.user_id = u.id
                    WHERE DATE_FORMAT(attn.cdate, '%H:%i:%s')>'09:00:00' AND YEAR(attn.cdate)='".$thisYear."'
                    AND attn.status=1";
        if($userSelected){
            $lateCanvasSql .= " AND attn.user_id='".$userSelected."'";
        } else {
            $lateCanvasSql .= " AND attn.user_id='".$userId."'";
		}
       
        $lateCanvasSql .= " GROUP BY MONTH(attn.cdate)";
        $stmt_sql_lateCanvas= $conn->execute($lateCanvasSql);
        $count_lateCanvas= $stmt_sql_lateCanvas->fetchAll('assoc');
        $this->set('Lateresult',$count_lateCanvas);
        
        //for normal late in
        $normalCanvasSql = "SELECT COUNT(DISTINCT(DAY(attn.cdate))) as totalLate,u.name,MONTH(attn.cdate) as monthcanvas
                    FROM attendances attn
                    LEFT JOIN users u ON u.id=attn.user_id
                    LEFT JOIN user_organizations uo on uo.user_id = u.id
                    WHERE DATE_FORMAT(attn.cdate, '%H:%i:%s')<='09:00:00' AND YEAR(attn.cdate)='".$thisYear."'
                    AND attn.status=1";
        if($userSelected){
            $normalCanvasSql .= " AND attn.user_id='".$userSelected."'";
        } else {
            $normalCanvasSql .= " AND attn.user_id='".$userId."'";
		}
       
        $normalCanvasSql .= " GROUP BY MONTH(attn.cdate)";
        $stmt_sql_normalCanvas= $conn->execute($normalCanvasSql);
        $count_normalCanvas= $stmt_sql_normalCanvas->fetchAll('assoc');
        $this->set('Normalresult',$count_normalCanvas);

		//for all staff time in
		$inTimeDeptCanvasSql = "SELECT attn.* 
                        FROM attendances attn
						LEFT JOIN user_organizations uo ON attn.user_id = uo.user_id
                        WHERE status =1 AND DATE_FORMAT(attn.cdate, '%Y-%m-%d')=CURDATE()";
        if($departmentSelected){
            $inTimeDeptCanvasSql .= " AND uo.organization_id='".$departmentSelected."'";
        }
		$inTimeDeptCanvasSql .= " ORDER BY attn.cdate";
        $stmt_sql_inTimeDeptCanvas= $conn->execute($inTimeDeptCanvasSql);
        $count_inTimeDeptCanvas= $stmt_sql_inTimeDeptCanvas->fetchAll('assoc');
        $this->set('inTimeDeptresult',$count_inTimeDeptCanvas);
		
        $this->set(compact('user', 'Lateresult','Normalresult','inTimeDeptresult','userRoles','departments','users','departmentSelected','userSelected','staff_absent','staff_working','notifications','staff_timeoff','total_late','total_pending','total_attend_month','total_absent_month','total_time_off_month','user_total_late_month','totalStaff','user_total_normal_month'));
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
        return $q->where(['UserOrganizations.organization_id'=>$department_id])->where(['Users.status'=>1]);})->group(['Users.id']);
        }
       
        
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
        $this->viewBuilder()->layout('ajax');
    }
}
