<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;
use \Datetime;
use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Utility\Hash;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
/**
 * UserLeaves Controller
 *
 * @property \App\Model\Table\UserLeavesTable $UserLeaves
 *
 * @method \App\Model\Entity\UserLeave[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserLeavesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function getLanguageId(){
        $session = $this->request->session()->read('Config.language');
        if(isset($session) AND $session == 'en'){
            $language_id=1;
        }else{
            $language_id=2;
        }
        return $language_id;
    }

    public function index()
    {
        
        $this->set('title', __('Time Off'));
        /*$this->paginate = [
            'contain' => ['Users', 'LeaveStatus', 'LeaveTypes']
        ];*/
        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');


        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        $list_status = $this->LeaveStatus->find('list')->where(["status"=>1]);
        
        $language_id = $this->getLanguageId();

        if($language_id==2){ //Bahasa
            $arrayStatus = array();
            foreach ($list_status as $key => $value) {
                if($key==1){
                    $arrayStatus[$key]="Menunggu Kelulusan";
                }elseif($key==2){
                    $arrayStatus[$key]="Diluluskan";
                }elseif($key==3){
                    $arrayStatus[$key]="Ditolak";
                }elseif($key==4){
                    $arrayStatus[$key]="Dibatalkan";
                }elseif($key==5){
                    $arrayStatus[$key]="Tidak Sah";
                }
                
            }
            $list_status = $arrayStatus;
        }

        if ($userRoles->hasRole(['Ketua Pengarah'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1])->order(['name'=>'ASC']);

            $list_user = $this->Users->find('list')->contain(['UsersRoles.Roles'])->innerJoinWith('UsersRoles.Roles', function($q){ return $q->where(['UsersRoles.role_id '=>2]); })->where(["status"=>1])->order(['Users.name'=>'ASC']);
            
            $organizationSelected = $this->request->query('department');
            $staffSelected = $this->request->query('staff');
            $statusSelected = $this->request->query('status');

            if(!empty($organizationSelected)){
               $list_user = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($organizationSelected){
            return $q->where(['UserOrganizations.organization_id'=>$organizationSelected])->where(['Users.status'=>1])->order(['Users.name'=>'ASC']);}); 
            }

            $sql_leave = "SELECT user_leaves.*, leave_status.`name` AS leave_status_name, leave_types.`name`AS leave_type_name,users.id AS user_id, users.name AS user_name, organizations.id AS organization_id, organizations.`name` AS organizations_name FROM user_leaves 
                JOIN users on users.id=user_leaves.user_id
                JOIN users_roles ON users_roles.user_id=users.id
                JOIN leave_status ON leave_status.`id`=user_leaves.`leave_status_id`
                JOIN leave_types ON leave_types.`id` = user_leaves.`leave_type_id`
                JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` 
                JOIN organizations ON organizations.id = user_organizations.`organization_id`";

            if(!empty($organizationSelected) AND !empty($staffSelected) AND !empty($statusSelected)){ // all selected
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND users.id=$staffSelected AND user_leaves.leave_status_id=$statusSelected";
            
            }else if(!empty($organizationSelected) AND empty($staffSelected) AND empty($statusSelected)){ // organization only
                $sql_leave .= " WHERE organizations.id=$organizationSelected";
                
            }else if(!empty($organizationSelected) AND empty($staffSelected) AND !empty($statusSelected)){ // organization & status
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND user_leaves.leave_status_id=$statusSelected";
                
            }else if(!empty($organizationSelected) AND !empty($staffSelected) AND empty($statusSelected)){ //organization & staff
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND users.id=$staffSelected";
                
            }else if(empty($organizationSelected) AND !empty($staffSelected) AND empty($statusSelected)){ // staff
                $sql_leave .= " WHERE users.id=$staffSelected";
                
            }else if(empty($organizationSelected) AND !empty($staffSelected) AND !empty($statusSelected)){ // staff & status
                $sql_leave .= " WHERE users.id=$staffSelected AND user_leaves.leave_status_id=$statusSelected";
               
            }else if(empty($organizationSelected) AND empty($staffSelected) AND !empty($statusSelected)){ // status
                $sql_leave .= " WHERE leave_status.id=$statusSelected";
                
            }


           
            $sql_leave .=" AND users_roles.role_id=2 ORDER BY user_leaves.cdate desc";
            $stmt_sql_leave = $conn->execute($sql_leave);
            $userLeaves = $stmt_sql_leave->fetchAll('assoc');

        }elseif ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1])->order(['name'=>'ASC']);
            $list_user = $this->Users->find('list')->where(["status"=>1])->order(['Users.name'=>'ASC']);
            
            $organizationSelected = $this->request->query('department');
            $staffSelected = $this->request->query('staff');
            $statusSelected = $this->request->query('status');

            if(!empty($organizationSelected)){
               $list_user = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($organizationSelected){
            return $q->where(['UserOrganizations.organization_id'=>$organizationSelected])->where(['Users.status'=>1])->order(['Users.name'=>'ASC']);}); 
            }

            $sql_leave = "SELECT user_leaves.*, leave_status.`name` AS leave_status_name, leave_types.`name`AS leave_type_name,users.id AS user_id, users.name AS user_name, organizations.id AS organization_id, organizations.`name` AS organizations_name FROM user_leaves 
                JOIN users on users.id=user_leaves.user_id
                JOIN leave_status ON leave_status.`id`=user_leaves.`leave_status_id`
                JOIN leave_types ON leave_types.`id` = user_leaves.`leave_type_id`
                JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` 
                JOIN organizations ON organizations.id = user_organizations.`organization_id`";

            if(!empty($organizationSelected) AND !empty($staffSelected) AND !empty($statusSelected)){ // all selected
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND users.id=$staffSelected AND user_leaves.leave_status_id=$statusSelected";
            
            }else if(!empty($organizationSelected) AND empty($staffSelected) AND empty($statusSelected)){ // organization only
                $sql_leave .= " WHERE organizations.id=$organizationSelected";
                
            }else if(!empty($organizationSelected) AND empty($staffSelected) AND !empty($statusSelected)){ // organization & status
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND user_leaves.leave_status_id=$statusSelected";
                
            }else if(!empty($organizationSelected) AND !empty($staffSelected) AND empty($statusSelected)){ //organization & staff
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND users.id=$staffSelected";
                
            }else if(empty($organizationSelected) AND !empty($staffSelected) AND empty($statusSelected)){ // staff
                $sql_leave .= " WHERE users.id=$staffSelected";
                
            }else if(empty($organizationSelected) AND !empty($staffSelected) AND !empty($statusSelected)){ // staff & status
                $sql_leave .= " WHERE users.id=$staffSelected AND user_leaves.leave_status_id=$statusSelected";
               
            }else if(empty($organizationSelected) AND empty($staffSelected) AND !empty($statusSelected)){ // status
                $sql_leave .= " WHERE leave_status.id=$statusSelected";
                
            }


            $sql_leave .=" ORDER BY user_leaves.cdate desc";
            $stmt_sql_leave = $conn->execute($sql_leave);
            $userLeaves = $stmt_sql_leave->fetchAll('assoc');

        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {

           // $list_user = $this->Users->find('list')->contain(['UserOrganizations'])->where(["Users.status"=>1])->where(['UserOrganizations.organization_id'=>"$user_organization_id"]);
            $list_user = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
            return $q->where(['UserOrganizations.organization_id'=>$user_organization_id])->where(['Users.status'=>1])->where(['Users.report_to'=>$user_id])->order(['Users.name'=>'ASC']);});

            //$list_status = $this->LeaveStatus->find('list')->where(["status"=>1]);

            $staffSelected = $this->request->query('staff');
            $statusSelected = $this->request->query('status');

            $sql_leave = "SELECT user_leaves.*, leave_status.`name` AS leave_status_name, leave_types.`name`AS leave_type_name,users.id AS user_id, users.name AS user_name, organizations.id AS organization_id, organizations.`name` AS organizations_name FROM user_leaves 
                JOIN users on users.id=user_leaves.user_id
                JOIN leave_status ON leave_status.`id`=user_leaves.`leave_status_id`
                JOIN leave_types ON leave_types.`id` = user_leaves.`leave_type_id`
                JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` 
                JOIN organizations ON `organizations`.`id` = `user_organizations`.`organization_id`";

            if(!empty($staffSelected) AND !empty($statusSelected)){
                $sql_leave .= " WHERE organizations.id=$user_organization_id AND users.id=$staffSelected AND leave_status.id=$statusSelected";
            
            }else if(empty($staffSelected) AND !empty($statusSelected)){
                $sql_leave .= " WHERE organizations.id=$user_organization_id AND leave_status.id=$statusSelected";
                
            }else if(!empty($staffSelected) AND empty($statusSelected)){
                $sql_leave .= " WHERE organizations.id=$user_organization_id AND users.id=$staffSelected";
                
            }else if(empty($staffSelected) AND empty($statusSelected)){
                $sql_leave .= " WHERE organizations.id=$user_organization_id";
                
            }
            $sql_leave .=" ORDER BY user_leaves.cdate desc";
            $stmt_sql_leave = $conn->execute($sql_leave);
            $userLeaves = $stmt_sql_leave->fetchAll('assoc');

        }elseif ($userRoles->hasRole(['Staff'])) {

            //$list_status = $this->LeaveStatus->find('list')->where(["status"=>1]);

            $statusSelected = $this->request->query('status');

            $sql_leave = "SELECT user_leaves.*, leave_status.`name` AS leave_status_name, leave_types.`name`AS leave_type_name,users.id AS user_id, users.name AS user_name, organizations.id AS organization_id, organizations.`name` AS organizations_name FROM user_leaves 
                JOIN users on users.id=user_leaves.user_id
                JOIN leave_status ON leave_status.`id`=user_leaves.`leave_status_id`
                JOIN leave_types ON leave_types.`id` = user_leaves.`leave_type_id`
                JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` 
                JOIN organizations ON organizations.id = user_organizations.`organization_id`";

            if(!empty($statusSelected)){
                $sql_leave .= " WHERE users.id=$user_id AND leave_status.id=$statusSelected";
            
            }else{
                $sql_leave .= " WHERE users.id=$user_id";
                
            }
            $sql_leave .=" ORDER BY user_leaves.cdate desc";
            $stmt_sql_leave = $conn->execute($sql_leave);
            $userLeaves = $stmt_sql_leave->fetchAll('assoc');
        }
                
        $this->set(compact('userLeaves', 'list_organization','list_user','list_status','staffSelected','organizationSelected','statusSelected','userRoles'));
    }



    public function my_timeoff()
    {
        
        $this->set('title', __('Time Off'));
        /*$this->paginate = [
            'contain' => ['Users', 'LeaveStatus', 'LeaveTypes']
        ];*/
        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');


        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        $list_status = $this->LeaveStatus->find('list')->where(["status"=>1]);
        $language_id = $this->getLanguageId();

        if($language_id==2){ //Bahasa
            $arrayStatus = array();
            foreach ($list_status as $key => $value) {
                if($key==1){
                    $arrayStatus[$key]="Menunggu Kelulusan";
                }elseif($key==2){
                    $arrayStatus[$key]="Diluluskan";
                }elseif($key==3){
                    $arrayStatus[$key]="Ditolak";
                }elseif($key==4){
                    $arrayStatus[$key]="Dibatalkan";
                }elseif($key==5){
                    $arrayStatus[$key]="Tidak Sah";
                }
                
            }
            $list_status = $arrayStatus;
        }

        $yearselected = $this->request->query('att_year');
        $monthselected = $this->request->query('att_month');
        $statusSelected = $this->request->query('status');
        
        /*if (empty($yearselected) OR $yearselected !=' '){
            echo $yearselected = date('Y');
        }

        if (empty($monthselected) OR $monthselected !=' '){
            echo $monthselected = date('m');
        }*/

        if($yearselected){
            $yearselected = $this->request->query('att_year');          
        }else{
            $yearselected = date('Y');
        }
     
        if($monthselected){
            $monthselected = $this->request->query('att_month');
        }else{
            $monthselected = date('m');
        }
        

        $sql_leave = "SELECT user_leaves.*, leave_status.`name` AS leave_status_name, leave_types.`name`AS leave_type_name,users.id AS user_id, users.name AS user_name, organizations.id AS organization_id, organizations.`name` AS organizations_name FROM user_leaves 
            JOIN users on users.id=user_leaves.user_id
            JOIN leave_status ON leave_status.`id`=user_leaves.`leave_status_id`
            JOIN leave_types ON leave_types.`id` = user_leaves.`leave_type_id`
            JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` 
            JOIN organizations ON organizations.id = user_organizations.`organization_id`";

        if(!empty($statusSelected)){
            $sql_leave .= " WHERE users.id=$user_id AND leave_status.id=$statusSelected";
        
        }else{
            $sql_leave .= " WHERE users.id=$user_id";
            
        }

        $sql_leave .=" AND YEAR(user_leaves.date_start)=$yearselected AND MONTH(user_leaves.date_start)=$monthselected";
        $sql_leave .=" ORDER BY user_leaves.cdate desc";
        $stmt_sql_leave = $conn->execute($sql_leave);
        $userLeaves = $stmt_sql_leave->fetchAll('assoc');
        
                
        $this->set(compact('yearselected','monthselected','userLeaves', 'list_organization','list_user','list_status','staffSelected','organizationSelected','statusSelected','userRoles'));
    }


    public function apply()
    {
        $this->set('title', __('Time Off'));

        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('SettingEmails');
        $this->loadModel('Designations');
        $this->loadModel('UserDesignations');

        
        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1])->order(['name' => 'ASC']);
            $list_user = $this->UserLeaves->Users->find('list')->order(['Users.name' => 'ASC'])->where(["status"=>1]);

            $organizationSelected = $this->request->query('department');
            $staffSelected = $this->request->query('staff');

        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {
            $list_user = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
            return $q->where(['UserOrganizations.organization_id'=>$user_organization_id])->where(['Users.status'=>1]);});

            $staffSelected = $this->request->query('staff');

        }elseif ($userRoles->hasRole(['Staff'])) {

        }

        //$userLeave = $this->UserLeaves->newEntity();
        if ($this->request->is('post')) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");

            $data = $this->request->data;
            $error = false;
            //date apply
            $date= $data['apply_date']['year'].'-'.$data['apply_date']['month'].'-'.$data['apply_date']['day'];

            if($userRoles->hasRole(['Supervisor', 'Staff'])){ //check back dated application
                
                if(strtotime($date) < (time()-(60*60*24)) ){
                    $this->Flash->error(__('Date apply cannot be back dated. Please, try again.'));
                    $error=true;
                }
            }
            
            if($data['leave_type']==1){//personal matters

                //end date, default same day with date start
                $date_end= $date;

                //from_time
                $from_time=$data['from_time']['hour'].':'.$data['from_time']['minute'];
                $from_date_time=$date.' '.$from_time;

                 //to_time
                $to_time=$data['to_time']['hour'].':'.$data['to_time']['minute'];
                $to_date_time=$date.' '.$to_time;
                
                $from_day = date('l',strtotime($from_date_time)); //eg : Monday

                if($from_date_time > $to_date_time){
                    $this->Flash->error(__('Start Time cannot be grather End time'));
                    $error=true;
                }else{

                    if($from_day!="Friday"){ //normal lunch hour : 1pm-2pm

                        if($data['from_time']['hour'] <= 13 AND $data['to_time']['hour'] >= 14 ){
                            $time_from_count1 = date_create($from_date_time);
                            $time_from_count2 = date_create($date.' 13:00');
                            $interval_time_from = date_diff($time_from_count1, $time_from_count2);
                            $time_from_count_period= $interval_time_from->format("%H:%I:%S"); 

                            $time_from_count_arr= explode(':', $time_from_count_period);
                            $time_from_count_in_minute = ($time_from_count_arr[0] * 60.0 + $time_from_count_arr[1] * 1.0);


                            $time_to_count1 = date_create($date.' 14:00');
                            $time_to_count2 = date_create($to_date_time);
                            $interval_time_to = date_diff($time_to_count1, $time_to_count2);
                            $time_to_count_period= $interval_time_to->format("%H:%I:%S"); 

                            $time_to_count_arr= explode(':', $time_to_count_period);
                            $time_to_count_in_minute = ($time_to_count_arr[0] * 60.0 + $time_to_count_arr[1] * 1.0);

                            $total_time_off_hour=$time_from_count_in_minute + $time_to_count_in_minute;
                            if($total_time_off_hour > 240){
                                $this->Flash->error(__('Personal matters time off only 4 hours maximum. Please, try again.'));
                                $error=true;
                            }
                        }else{
                            $datetime1 = date_create($from_date_time);
                            $datetime2 = date_create($to_date_time);
                            $interval = date_diff($datetime1, $datetime2);
                            $time_off_period= $interval->format("%H:%I:%S"); 

                            $time_off_period_arr= explode(':', $time_off_period);
                            $time_off_period_in_minute = ($time_off_period_arr[0] * 60.0 + $time_off_period_arr[1] * 1.0);

                            if($time_off_period_in_minute > 240){
                                $this->Flash->error(__('Personal matters time off only 4 hours maximum. Please, try again.'));
                                $error=true;
                            }
                        }

                        //cheking if already apply 4 hour on same date.
                        $timeSameDate=$this->UserLeaves->find()->where(['date_start'=>$date])->where(['user_id'=>$data['staff']])->where(['leave_status_id IN'=>array(1,2)]);

                        foreach ($timeSameDate as $timeSameDate_data) {
                            $startDate1 = $timeSameDate_data->date_start.' '.$timeSameDate_data->start_time;
                            $endDate1 = $timeSameDate_data->date_start.' '.$timeSameDate_data->end_time;
                            $startDate2 = $timeSameDate_data->date_start.' 13:00';
                            $endDate2 = $timeSameDate_data->date_start.' 14:00';

                            //calculate time overlape in lunch hour
                            $overlapeTime = $this->UserLeaves->overlapInMinutes($startDate1, $endDate1, $startDate2, $endDate2);

                            //calculate total apply time
                            $time_from_count1 = date_create($startDate1);
                            $time_from_count2 = date_create($endDate1);
                            $interval_time_from = date_diff($time_from_count1, $time_from_count2);
                            $time_from_count_period= $interval_time_from->format("%H:%I:%S");
                            $time_from_count_arr= explode(':', $time_from_count_period);
                            $time_from_count_in_minute = ($time_from_count_arr[0] * 60.0 + $time_from_count_arr[1] * 1.0);

                            $total_time_in_minute+=$time_from_count_in_minute - $overlapeTime;
                        
                        }

                        if($total_time_in_minute>240){
                            $minuteToHour=$this->UserLeaves->convertToHoursMins($total_time_in_minute, '%02d'. __(' hours ') . '%02d'. __(' minutes'));
                            $msg=__('Personal matters time off only 4 hours maximum. You already apply ') .$minuteToHour;
                            $msg .=__(' time off on '.$date);
                            $this->Flash->error($msg);
                            $error=true;
                        }
                        
                    }else{// Friday special lunch hour : 12.15pm-2.45pm
                        $startDate1 = $from_date_time;
                        $endDate1 = $to_date_time;
                        $startDate2 = $date.' 12:15';
                        $endDate2 = $date.' 14:45';

                        //calculate time overlape in lunch hour
                        $overlapeTime = $this->UserLeaves->overlapInMinutes($startDate1, $endDate1, $startDate2, $endDate2); 

                        //calculate total apply time
                        $time_from_count1 = date_create($from_date_time);
                        $time_from_count2 = date_create($to_date_time);
                        $interval_time_from = date_diff($time_from_count1, $time_from_count2);
                        $time_from_count_period= $interval_time_from->format("%H:%I:%S");
                        $time_from_count_arr= explode(':', $time_from_count_period);
                        $time_from_count_in_minute = ($time_from_count_arr[0] * 60.0 + $time_from_count_arr[1] * 1.0);

                        $total_time_off_hour=$time_from_count_in_minute - $overlapeTime;
                       
                        if($total_time_off_hour > 240){
                            $this->Flash->error(__('Personal matters time off only 4 hours maximum. Please, try again.'));
                            $error=true;
                        }


                        //cheking if already apply 4 hour on same date.
                        $timeSameDate=$this->UserLeaves->find()->where(['date_start'=>$date])->where(['user_id'=>$data['staff']])->where(['leave_status_id IN'=>array(1,2)]);

                        foreach ($timeSameDate as $timeSameDate_data) {
                            $startDate1 = $timeSameDate_data->date_start.' '.$timeSameDate_data->start_time;
                            $endDate1 = $timeSameDate_data->date_start.' '.$timeSameDate_data->end_time;
                            $startDate2 = $timeSameDate_data->date_start.' 12:15';
                            $endDate2 = $timeSameDate_data->date_start.' 14:45';

                            //calculate time overlape in lunch hour
                            $samedate_overlapeTime = $this->UserLeaves->overlapInMinutes($startDate1, $endDate1, $startDate2, $endDate2);

                            //calculate total apply time
                            $time_from_count1 = date_create($startDate1);
                            $time_from_count2 = date_create($endDate1);
                            $interval_time_from = date_diff($time_from_count1, $time_from_count2);
                            $time_from_count_period= $interval_time_from->format("%H:%I:%S");
                            $time_from_count_arr= explode(':', $time_from_count_period);
                            $time_from_count_in_minute = ($time_from_count_arr[0] * 60.0 + $time_from_count_arr[1] * 1.0);

                            $total_time_in_minute+=$time_from_count_in_minute - $samedate_overlapeTime;
                        
                        }

                        if($total_time_in_minute>240){
                            $minuteToHour=$this->UserLeaves->convertToHoursMins($total_time_in_minute, '%02d'. __(' hours ') . '%02d'. __(' minutes'));
                            $msg=__('Personal matters time off only 4 hours maximum. You already apply ') .$minuteToHour;
                            $msg .=__(' time off on '.$date);
                            $this->Flash->error($msg);
                            $error=true;
                        }                        
                       
                    }
                }
                
                              

            }else{
                
                $date_end= $data['date_end']['year'].'-'.$data['date_end']['month'].'-'.$data['date_end']['day'];
            }

            //check if got attahcement
            if(!empty($this->request->data['attachment']['tmp_name'])){
                
                $fileName = $this->request->data['attachment']['name'];
                $str_date = $now->i18nFormat('yyMMdd');
                $fileName = $str_date.'_'.rand(10000,1000000).'_'.$fileName;
                $uploadPath = '/files/timeoff/'.$str_date.'/';
                $path = WWW_ROOT . $uploadPath;
                if (!file_exists($path)) {
                    $oldMask = umask(0);
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                    umask($oldMask);
                }

                $uploadFile = WWW_ROOT . $uploadPath.$fileName;
                $imageFileType = strtolower(pathinfo($uploadFile,PATHINFO_EXTENSION));
                if($imageFileType=="jpg" OR $imageFileType=="png" OR $imageFileType=="jpeg"){
                    if($this->request->data['attachment']['size'] < 5000000){ //5mb = 5000000 kilobyte
                        if(move_uploaded_file($this->request->data['attachment']['tmp_name'],$uploadFile)){
                        
                        }else{
                            $this->Flash->error(__('Unable to upload file, please try again.'));
                            $error=true;
                        }
                    }else{
                        $this->Flash->error(__('Exceeds file limit.Please upload image less than 5MB'));
                        $error=true;
                    }
                }else{
                    $this->Flash->error(__('Unable to upload file, JPG, JPEG & PNG file only allowed.'));
                    $error=true;
                }
                $filename = $uploadPath.$fileName;
            }

        
            if(!$error){
                $sql="INSERT INTO `user_leaves` (user_id,date_start,date_end,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id) VALUES (".$data['staff'].","."'".$date."'".","."'".$date_end."'".","."'".$from_time."'".","."'".$to_time."'".","."'".$data['remark']."'".","."'".$filename."'".",".$user_id.",1,"."'".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."'".",".$data['leave_type'].")";
            
                $stmt = $conn->execute($sql);
                
                $sql_id="SELECT max(id) as last_id FROM `user_leaves`";
                $stmt = $conn->execute($sql_id);
                $last_id = $stmt->fetch('assoc');
                $last_id=$last_id['last_id'];

                if ($last_id>0) {
                    //get supervisor
                    if($userRoles->hasRole(['Master Admin'])){
                        $organization_id=$data['department'];
                    }else{
                       $organization_id= $user_organization_id;
                    }
                    
                    //get time off apply email template
                    $language_id = $this->getLanguageId();

                    $staff_detail = $this->Users->find('all')->contain(['UserDesignations.Designations','UserOrganizations.Organizations'])->where(['id'=>$data['staff']])->limit(1)->first();
                    
                    $staff_department=$staff_detail->user_organizations[0]->organization->name;
                    $staff_organization_id=$staff_detail->user_organizations[0]->organization_id;
                    $staff_designation=$staff_detail->user_designations[0]->designation->name;
                    $staff_email=$staff_detail->email;
                    $staff_name=$staff_detail->name;

                    //get all master admin
                    $user_master=array();            
                    $user_master= $this->Users->find()->contain(['Roles'])->innerJoinWith('UsersRoles.Roles' , function($q){return $q->where(['UsersRoles.role_id'=>1]);});
                    foreach ($user_master as $user) {
                        $email_master[] = $user['email'];
                    }

                    if($data['leave_type']==1){//personal matters
                        $time_off_type="Personal matters";
                    }elseif ($data['leave_type']==2) {
                        $time_off_type="Work Affairs";
                    }


                    $emailTemplates = $this->SettingEmails->find('all')->where(['email_type_id'=>3,'language_id'=>$language_id])->first();
                    $emailTemp_subject =  $emailTemplates->subject;
                    $emailTemp_body = str_replace(array('[STAFF_NAME]', '[DEPARTMENT]', '[DESIGNATION]', '[TIME_OFF_TYPE]', '[TIME_OFF_REASON]', '[TIME_OFF_DATE_START]', '[TIME_OFF_DATE_END]', '[TIME_OFF_TIME_START]', '[TIME_OFF_TIME_END]'), array('{0}', '{1}', '{2}','{3}', '{4}', '{5}','{6}', '{7}', '{8}'), $emailTemplates->body);
                    $subject = __(nl2br($emailTemp_subject));
                    $body = __(nl2br($emailTemp_body),$staff_name,$staff_department,$staff_designation,$time_off_type, $data['remark'], $date, $date_end, $from_time, $to_time);


                    //get all supervisor
                    $reportTo = $this->Users->find()->contain('Roles')->innerJoinWith('UserOrganizations.Organizations' , function($q) use($staff_organization_id){ return $q->where(['UserOrganizations.organization_id'=>$staff_organization_id]);});
                
                    $reportTo->matching('Roles', function ($q) {
                        return $q->where(['Roles.id IN' => [SUPERVISOR]]);
                    });
                
                    $supervisor_email=array();
                    foreach ($reportTo as $key ) {
                        $supervisor_email[] = $key['email'];
                    }

                    //notify supervisor, email
                    try {
                        $email = new Email();

                        // Use a named transport already configured using Email::configTransport()
                        $email->transport('default');

                        // Use a constructed object.
                        $email 
                            ->emailFormat('html')
                            ->to($supervisor_email)
                            ->cc($email_master)
                            ->subject($subject)
                            ->send($body);
                        

                    }catch(\Exception $e){
                        $this->Flash->error(__('Unable to send email'));
                    
                    }
                    //if email template exist, send notification to supervisor
                    /*$sql_supervisor = "SELECT users.* FROM users JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` JOIN `users_roles` ON `users_roles`.`user_id`=`users`.`id` WHERE `users_roles`.`role_id`=2 AND `user_organizations`.`organization_id`=$organization_id UNION SELECT users.* FROM users JOIN `users_roles` ON `users_roles`.`user_id`=`users`.`id` WHERE `users_roles`.`role_id`=1";
                    $stmt_sql_supervisor = $conn->execute($sql_supervisor);
                    $get_supervisor = $stmt_sql_supervisor->fetchAll('assoc');*/

                    //notify supervisor, email


                    //insert log
                    $sql="INSERT INTO `user_leaves_logs` (user_leave_id,user_id,date_start,date_end,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id) VALUES ($last_id,".$data['staff'].","."'".$date."'".","."'".$date_end."'".","."'".$from_time."'".","."'".$to_time."'".","."'".$data['remark']."'".","."'".$filename."'".",".$user_id.",1,"."'".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."'".",".$data['leave_type'].")";
                
                    $stmt = $conn->execute($sql);
                    $this->Flash->success(__('Successfully apply time off.'));

                    return $this->redirect(['action' => 'index']);
                }
            }
            
            //$this->Flash->error(__('The time off could not be saved. Please, try again.'));
        }
        
        $leaveStatuses = $this->UserLeaves->LeaveStatus->find('list', ['limit' => 200]);
        $leaveTypes = $this->UserLeaves->LeaveTypes->find('list', ['limit' => 200]);

        $language_id = $this->getLanguageId();

        if($language_id==2){ //Bahasa
            $arrayType = array();
            foreach ($leaveTypes as $key => $value) {
                if($key==1){
                    $arrayType[$key]="Urusan Peribadi";
                }elseif($key==2){
                    $arrayType[$key]="Urusan Kerja";
                }
                
            }
            $leaveTypes = $arrayType;
        }
       
        $this->set(compact('from_day','userLeave', 'list_user', 'leaveStatuses', 'leaveTypes','userRoles', 'list_organization','staffSelected', 'last_id','user_organization_id','user_id','total_time_off_hour'));
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
    /**
     * View method
     *
     * @param string|null $id User Leave id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->set('title', __('Time Off'));
        $userLeave = $this->UserLeaves->get($id, [
            'contain' => ['Users', 'LeaveStatus', 'LeaveTypes']
        ]);

        $this->set('userLeave', $userLeave);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->set('title', __('Time Off'));

        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);

            $organizationSelected = $this->request->query('department');

        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {

        }elseif ($userRoles->hasRole(['Staff'])) {

        }

        $userLeave = $this->UserLeaves->newEntity();
        if ($this->request->is('post')) {
            $userLeave = $this->UserLeaves->patchEntity($userLeave, $this->request->getData());
            if ($this->UserLeaves->save($userLeave)) {
                $this->Flash->success(__('Time off has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Time off could not be saved. Please, try again.'));
        }
        $list_user = $this->UserLeaves->Users->find('list');
        $leaveStatuses = $this->UserLeaves->LeaveStatus->find('list', ['limit' => 200]);
        $leaveTypes = $this->UserLeaves->LeaveTypes->find('list', ['limit' => 200]);

        $this->set(compact('userLeave', 'list_user', 'leaveStatuses', 'leaveTypes','userRoles', 'list_organization'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User Leave id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->set('title', __('Time Off'));
        $userLeave = $this->UserLeaves->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userLeave = $this->UserLeaves->patchEntity($userLeave, $this->request->getData());
            if ($this->UserLeaves->save($userLeave)) {
                $this->Flash->success(__('The user leave has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user leave could not be saved. Please, try again.'));
        }
        $users = $this->UserLeaves->Users->find('list', ['limit' => 200]);
        $leaveStatuses = $this->UserLeaves->LeaveStatuses->find('list', ['limit' => 200]);
        $leaveTypes = $this->UserLeaves->LeaveTypes->find('list', ['limit' => 200]);
        $this->set(compact('userLeave', 'users', 'leaveStatuses', 'leaveTypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User Leave id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->set('title', __('Time Off'));
        $this->request->allowMethod(['post', 'delete']);
        $userLeave = $this->UserLeaves->get($id);
        if ($this->UserLeaves->delete($userLeave)) {
            $this->Flash->success(__('The user leave has been deleted.'));
        } else {
            $this->Flash->error(__('The user leave could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function cancel($id = null)
    {
        $this->set('title', __('Time Off'));

        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('SettingEmails');
        $this->loadModel('Designations');
        $this->loadModel('UserDesignations');

        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
            $sql_leave_update ="UPDATE user_leaves SET leave_status_id='4', mdate='".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."', modified_by=$user_id WHERE id=$id";
            
            if ($conn->execute($sql_leave_update)) {
                    //get language
                    $language_id = $this->getLanguageId();

                    //get user details
                    $staff_detail = $this->Users->find('all')->contain(['UserDesignations.Designations','UserOrganizations.Organizations'])->where(['id'=>$user_id])->limit(1)->first();
                    
                    $staff_department=$staff_detail->user_organizations[0]->organization->name;
                    $staff_organization_id=$staff_detail->user_organizations[0]->organization_id;
                    $staff_designation=$staff_detail->user_designations[0]->designation->name;
                    $staff_email=$staff_detail->email;
                    $staff_name=$staff_detail->name;

                    //get all master admin
                    $user_master=array();            
                    $user_master= $this->Users->find()->contain(['Roles'])->innerJoinWith('UsersRoles.Roles' , function($q){return $q->where(['UsersRoles.role_id'=>1]);});
                    foreach ($user_master as $user) {
                        $email_master[] = $user['email'];
                    }

                    //get user_leaves detail
                    $sql_leave="SELECT * FROM user_leaves WHERE id=$id";
                    $stmt_sql_leave=$conn->execute($sql_leave);
                    $get_sql_leave= $stmt_sql_leave->fetch('assoc');

                    //insert into user_leave_log
                    $sql_leave_log="INSERT INTO `user_leaves_logs` (user_leave_id,user_id,date_start,date_end,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id, modified_by) VALUES ($id,".$get_sql_leave['user_id'].","."'".$get_sql_leave['date_start']."'".","."'".$get_sql_leave['date_end']."'".","."'".$get_sql_leave['start_time']."'".","."'".$get_sql_leave['end_time']."'".","."'".$get_sql_leave['reason']."'".","."'".$get_sql_leave['filename']."'".",".$get_sql_leave['pic'].",".$get_sql_leave['leave_status_id'].","."'".$get_sql_leave['cdate']."'".",".$get_sql_leave['leave_type_id'].",$user_id)";
                     $stmt = $conn->execute($sql_leave_log);

                    if($get_sql_leave['leave_type_id']==1){//personal matters
                        $time_off_type="Personal matters";
                    }elseif ($get_sql_leave['leave_type_id']==2) {
                        $time_off_type="Work Affairs";
                    }

                    //get time off cancel email template
                    $emailTemplates = $this->SettingEmails->find('all')->where(['email_type_id'=>7,'language_id'=>$language_id])->first();
                    $emailTemp_subject =  $emailTemplates->subject;
                    $emailTemp_body = str_replace(array('[STAFF_NAME]', '[DEPARTMENT]', '[DESIGNATION]', '[TIME_OFF_TYPE]', '[TIME_OFF_REASON]', '[TIME_OFF_DATE_START]', '[TIME_OFF_DATE_END]', '[TIME_OFF_TIME_START]', '[TIME_OFF_TIME_END]'), array('{0}', '{1}', '{2}','{3}', '{4}', '{5}','{6}', '{7}', '{8}'), $emailTemplates->body);
                    $subject = __(nl2br($emailTemp_subject));
                    $body = __(nl2br($emailTemp_body),$staff_name,$staff_department,$staff_designation,$time_off_type, $time_off_type['reason'], $get_sql_leave['date_start'], $get_sql_leave['date_end'], $get_sql_leave['start_time'], $get_sql_leave['end_time']);


                    //get all supervisor
                    $reportTo = $this->Users->find()->contain('Roles')->innerJoinWith('UserOrganizations.Organizations' , function($q) use($staff_organization_id){ return $q->where(['UserOrganizations.organization_id'=>$staff_organization_id]);});
                
                    $reportTo->matching('Roles', function ($q) {
                        return $q->where(['Roles.id IN' => [SUPERVISOR]]);
                    });
                
                    $supervisor_email=array();
                    foreach ($reportTo as $key ) {
                        $supervisor_email[] = $key['email'];
                    }

                    //notify supervisor, email
                    try {
                        $email = new Email();

                        // Use a named transport already configured using Email::configTransport()
                        $email->transport('default');

                        // Use a constructed object.
                        $email 
                            ->emailFormat('html')
                            ->to($supervisor_email)
                            ->cc($email_master)
                            ->subject($subject)
                            ->send($body);
                        

                    }catch(\Exception $e){
                        $this->Flash->error(__('Unable to send email'));
                    
                    }

                $this->Flash->success(__('Time off has been cancelled.'));

                return $this->redirect(['action' => 'my_timeoff']);
            }
            $this->Flash->error(__('Time off could not be saved. Please, try again.'));
        }
        
        return $this->redirect(['action' => 'index']);
    }


    public function approve($id = null)
    {
        $this->set('title', __('Time Off'));

        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('SettingEmails');
        $this->loadModel('Designations');
        $this->loadModel('UserDesignations');

        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);


        if ($this->request->is(['patch', 'post', 'put'])) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
            $sql_leave_update ="UPDATE user_leaves SET leave_status_id='2', mdate='".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."', modified_by=$user_id WHERE id=$id";
            
            if ($conn->execute($sql_leave_update)) {
                //get user_leaves detail
                $sql_leave="SELECT * FROM user_leaves WHERE id=$id";
                $stmt_sql_leave=$conn->execute($sql_leave);
                $get_sql_leave= $stmt_sql_leave->fetch('assoc');

                //insert into user_leave_log
                $sql_leave_log="INSERT INTO `user_leaves_logs` (user_leave_id,user_id,date_start,date_end,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id,modified_by) VALUES ($id,".$get_sql_leave['user_id'].","."'".$get_sql_leave['date_start']."'".","."'".$get_sql_leave['date_end']."'".","."'".$get_sql_leave['start_time']."'".","."'".$get_sql_leave['end_time']."'".","."'".$get_sql_leave['reason']."'".","."'".$get_sql_leave['filename']."'".",".$get_sql_leave['pic'].",".$get_sql_leave['leave_status_id'].","."'".$get_sql_leave['cdate']."'".",".$get_sql_leave['leave_type_id'].", $user_id)";
                $stmt = $conn->execute($sql_leave_log);

                //get language
                $language_id = $this->getLanguageId();

                //get user details
                $staff_detail = $this->Users->find('all')->contain(['UserDesignations.Designations','UserOrganizations.Organizations'])->where(['id'=>$get_sql_leave['user_id']])->limit(1)->first();
                
                $staff_department=$staff_detail->user_organizations[0]->organization->name;
                $staff_organization_id=$staff_detail->user_organizations[0]->organization_id;
                $staff_designation=$staff_detail->user_designations[0]->designation->name;
                $staff_email=$staff_detail->email;
                $staff_name=$staff_detail->name;

                //get all master admin
                $user_master=array();            
                $user_master= $this->Users->find()->contain(['Roles'])->innerJoinWith('UsersRoles.Roles' , function($q){return $q->where(['UsersRoles.role_id'=>1]);});
                foreach ($user_master as $user) {
                    $email_master[] = $user['email'];
                }


                if($get_sql_leave['leave_type_id']==1){//personal matters
                    $time_off_type="Personal matters";
                }elseif ($get_sql_leave['leave_type_id']==2) {
                    $time_off_type="Work Affairs";
                }

                //get time off approve email template
                $emailTemplates = $this->SettingEmails->find('all')->where(['email_type_id'=>4,'language_id'=>$language_id])->first();
                $emailTemp_subject =  $emailTemplates->subject;
                $emailTemp_body = str_replace(array('[STAFF_NAME]', '[DEPARTMENT]', '[DESIGNATION]', '[TIME_OFF_TYPE]', '[TIME_OFF_REASON]', '[TIME_OFF_DATE_START]', '[TIME_OFF_DATE_END]', '[TIME_OFF_TIME_START]', '[TIME_OFF_TIME_END]'), array('{0}', '{1}', '{2}','{3}', '{4}', '{5}','{6}', '{7}', '{8}'), $emailTemplates->body);
                $subject = __(nl2br($emailTemp_subject));
                $body = __(nl2br($emailTemp_body),$staff_name,$staff_department,$staff_designation,$time_off_type, $time_off_type['reason'], $get_sql_leave['date_start'], $get_sql_leave['date_end'], $get_sql_leave['start_time'], $get_sql_leave['end_time']);


                //get all supervisor
                $reportTo = $this->Users->find()->contain('Roles')->innerJoinWith('UserOrganizations.Organizations' , function($q) use($staff_organization_id){ return $q->where(['UserOrganizations.organization_id'=>$staff_organization_id]);});
            
                $reportTo->matching('Roles', function ($q) {
                    return $q->where(['Roles.id IN' => [SUPERVISOR]]);
                });
            
                $supervisor_email=array();
                foreach ($reportTo as $key ) {
                    $supervisor_email[] = $key['email'];
                }

                //notify supervisor, email
                try {
                    $email = new Email();

                    // Use a named transport already configured using Email::configTransport()
                    $email->transport('default');

                    // Use a constructed object.
                    $email 
                        ->emailFormat('html')
                        ->to($staff_email)
                        ->cc($email_master)
                        ->subject($subject)
                        ->send($body);
                    

                }catch(\Exception $e){
                    $this->Flash->error(__('Unable to send email'));
                
                }


                $this->Flash->success(__('Time off has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Time off could not be saved. Please, try again.'));
        }
        
        return $this->redirect(['action' => 'index']);
    }


    public function void($id = null)
    {
        $this->set('title', __('Time Off'));

        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('SettingEmails');
        $this->loadModel('Designations');
        $this->loadModel('UserDesignations');

        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);


        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
            $sql_leave_update ="UPDATE user_leaves SET leave_status_id='5', mdate='".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."', modified_by=$user_id WHERE id=$id";
            
            if ($conn->execute($sql_leave_update)) {
                
                 //get user_leaves detail
                $sql_leave="SELECT * FROM user_leaves WHERE id=$id";
                $stmt_sql_leave=$conn->execute($sql_leave);
                $get_sql_leave= $stmt_sql_leave->fetch('assoc');

                //insert into user_leave_log
                $sql_leave_log="INSERT INTO `user_leaves_logs` (user_leave_id,user_id,date_start,date_end,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id,modified_by) VALUES ($id,".$get_sql_leave['user_id'].","."'".$get_sql_leave['date_start']."'".","."'".$get_sql_leave['date_end']."'".","."'".$get_sql_leave['start_time']."'".","."'".$get_sql_leave['end_time']."'".","."'".$get_sql_leave['reason']."'".","."'".$get_sql_leave['filename']."'".",".$get_sql_leave['pic'].",".$get_sql_leave['leave_status_id'].","."'".$get_sql_leave['cdate']."'".",".$get_sql_leave['leave_type_id'].", $user_id)";
                $stmt = $conn->execute($sql_leave_log);
               
                //get language
                $language_id = $this->getLanguageId();

                //get user details
                $staff_detail = $this->Users->find('all')->contain(['UserDesignations.Designations','UserOrganizations.Organizations'])->where(['id'=>$get_sql_leave['user_id']])->limit(1)->first();
                
                $staff_department=$staff_detail->user_organizations[0]->organization->name;
                $staff_organization_id=$staff_detail->user_organizations[0]->organization_id;
                $staff_designation=$staff_detail->user_designations[0]->designation->name;
                $staff_email=$staff_detail->email;
                $staff_name=$staff_detail->name;

                //get all master admin
                $user_master=array();            
                $user_master= $this->Users->find()->contain(['Roles'])->innerJoinWith('UsersRoles.Roles' , function($q){return $q->where(['UsersRoles.role_id'=>1]);});
                foreach ($user_master as $user) {
                    $email_master[] = $user['email'];
                }


                if($get_sql_leave['leave_type_id']==1){//personal matters
                    $time_off_type="Personal matters";
                }elseif ($get_sql_leave['leave_type_id']==2) {
                    $time_off_type="Work Affairs";
                }

                //get time off void email template
                $emailTemplates = $this->SettingEmails->find('all')->where(['email_type_id'=>6,'language_id'=>$language_id])->first();
                $emailTemp_subject =  $emailTemplates->subject;
                $emailTemp_body = str_replace(array('[STAFF_NAME]', '[DEPARTMENT]', '[DESIGNATION]', '[TIME_OFF_TYPE]', '[TIME_OFF_REASON]', '[TIME_OFF_DATE_START]', '[TIME_OFF_DATE_END]', '[TIME_OFF_TIME_START]', '[TIME_OFF_TIME_END]','[REMARK]'), array('{0}', '{1}', '{2}','{3}', '{4}', '{5}','{6}', '{7}', '{8}', '{9}'), $emailTemplates->body);
                $subject = __(nl2br($emailTemp_subject));
                $body = __(nl2br($emailTemp_body),$staff_name,$staff_department,$staff_designation,$time_off_type, $time_off_type['reason'], $get_sql_leave['date_start'], $get_sql_leave['date_end'], $get_sql_leave['start_time'], $get_sql_leave['end_time'],$get_sql_leave['remark']);


                //get all supervisor
                $reportTo = $this->Users->find()->contain('Roles')->innerJoinWith('UserOrganizations.Organizations' , function($q) use($staff_organization_id){ return $q->where(['UserOrganizations.organization_id'=>$staff_organization_id]);});
            
                $reportTo->matching('Roles', function ($q) {
                    return $q->where(['Roles.id IN' => [SUPERVISOR]]);
                });
            
                $supervisor_email=array();
                foreach ($reportTo as $key ) {
                    $supervisor_email[] = $key['email'];
                }

                //notify supervisor, email
                try {
                    $email = new Email();

                    // Use a named transport already configured using Email::configTransport()
                    $email->transport('default');

                    // Use a constructed object.
                    $email 
                        ->emailFormat('html')
                        ->to($supervisor_email)
                        ->cc($email_master)
                        ->subject($subject)
                        ->send($body);
                    

                }catch(\Exception $e){
                    $this->Flash->error(__('Unable to send email'));
                
                }


                //sent notification email to staff


                $this->Flash->success(__('Time off has been void.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Time off could not be saved. Please, try again.'));
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function reject($id = null)
    {
        $this->set('title', __('Time Off'));

        $this->loadModel('Users');
        $this->loadModel('UserLeaves');
        $this->loadModel('UserLeavesLogs');
        $this->loadModel('LeaveTypes');
        $this->loadModel('LeaveStatus');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('SettingEmails');
        $this->loadModel('Designations');
        $this->loadModel('UserDesignations');

        $conn = ConnectionManager::get('default');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);


        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->data;
            $error = false;
            //date apply
            $remark= $data['remark'];
            $leave_id= $data['user_leave_id'];
            $user_id= $data['user_id'];

            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
            $sql_leave_update ="UPDATE user_leaves SET leave_status_id='3', mdate='".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."', remark='$remark', modified_by=$user_id WHERE id=$leave_id";
            
            if ($conn->execute($sql_leave_update)) {
                //get user_leaves detail
                $sql_leave="SELECT * FROM user_leaves WHERE id=$leave_id";
                $stmt_sql_leave=$conn->execute($sql_leave);
                $get_sql_leave= $stmt_sql_leave->fetch('assoc');

                //insert into user_leave_log
                $sql_leave_log="INSERT INTO `user_leaves_logs` (user_leave_id,user_id,date_start,date_end,start_time,end_time,reason,filename,pic,leave_status_id,remark,cdate,leave_type_id,modified_by) VALUES ($leave_id,".$get_sql_leave['user_id'].","."'".$get_sql_leave['date_start']."'".","."'".$get_sql_leave['date_end']."'".","."'".$get_sql_leave['start_time']."'".","."'".$get_sql_leave['end_time']."'".","."'".$get_sql_leave['reason']."'".","."'".$get_sql_leave['filename']."'".",".$get_sql_leave['pic'].",".$get_sql_leave['leave_status_id'].","."'".$get_sql_leave['remark']."'".","."'".$get_sql_leave['cdate']."'".",".$get_sql_leave['leave_type_id'].",$user_id)";
                $stmt = $conn->execute($sql_leave_log);

                //get language
                $language_id = $this->getLanguageId();

                //get user details
                $staff_detail = $this->Users->find('all')->contain(['UserDesignations.Designations','UserOrganizations.Organizations'])->where(['id'=>$get_sql_leave['user_id']])->limit(1)->first();
                
                $staff_department=$staff_detail->user_organizations[0]->organization->name;
                $staff_organization_id=$staff_detail->user_organizations[0]->organization_id;
                $staff_designation=$staff_detail->user_designations[0]->designation->name;
                $staff_email=$staff_detail->email;
                $staff_name=$staff_detail->name;

                //get all master admin
                $user_master=array();            
                $user_master= $this->Users->find()->contain(['Roles'])->innerJoinWith('UsersRoles.Roles' , function($q){return $q->where(['UsersRoles.role_id'=>1]);});
                foreach ($user_master as $user) {
                    $email_master[] = $user['email'];
                }


                if($get_sql_leave['leave_type_id']==1){//personal matters
                    $time_off_type="Personal matters";
                }elseif ($get_sql_leave['leave_type_id']==2) {
                    $time_off_type="Work Affairs";
                }

                //get time off rejected email template
                $emailTemplates = $this->SettingEmails->find('all')->where(['email_type_id'=>5,'language_id'=>$language_id])->first();
                $emailTemp_subject =  $emailTemplates->subject;
                $emailTemp_body = str_replace(array('[STAFF_NAME]', '[DEPARTMENT]', '[DESIGNATION]', '[TIME_OFF_TYPE]', '[TIME_OFF_REASON]', '[TIME_OFF_DATE_START]', '[TIME_OFF_DATE_END]', '[TIME_OFF_TIME_START]', '[TIME_OFF_TIME_END]','[REMARK]'), array('{0}', '{1}', '{2}','{3}', '{4}', '{5}','{6}', '{7}', '{8}', '{9}'), $emailTemplates->body);
                $subject = __(nl2br($emailTemp_subject));
                $body = __(nl2br($emailTemp_body),$staff_name,$staff_department,$staff_designation,$time_off_type, $time_off_type['reason'], $get_sql_leave['date_start'], $get_sql_leave['date_end'], $get_sql_leave['start_time'], $get_sql_leave['end_time'],$get_sql_leave['remark']);


                //get all supervisor
                $reportTo = $this->Users->find()->contain('Roles')->innerJoinWith('UserOrganizations.Organizations' , function($q) use($staff_organization_id){ return $q->where(['UserOrganizations.organization_id'=>$staff_organization_id]);});
            
                $reportTo->matching('Roles', function ($q) {
                    return $q->where(['Roles.id IN' => [SUPERVISOR]]);
                });
            
                $supervisor_email=array();
                foreach ($reportTo as $key ) {
                    $supervisor_email[] = $key['email'];
                }

                //notify supervisor, email
                try {
                    $email = new Email();

                    // Use a named transport already configured using Email::configTransport()
                    $email->transport('default');

                    // Use a constructed object.
                    $email 
                        ->emailFormat('html')
                        ->to($staff_email)
                        ->cc($email_master)
                        ->subject($subject)
                        ->send($body);
                    

                }catch(\Exception $e){
                    $this->Flash->error(__('Unable to send email'));
                
                }

                $this->Flash->error(__('Time off has been rejected.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Time off could not be saved. Please, try again.'));
        }
        
        return $this->redirect(['action' => 'index']);
    }
}
