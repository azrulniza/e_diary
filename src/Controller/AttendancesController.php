<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;

/**
 * Attendances Controller
 *
 * @property \App\Model\Table\AttendancesTable $Attendances
 *
 * @method \App\Model\Entity\Attendance[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttendancesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->set('title', __('Attendances'));

        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('Designations');
        $this->loadModel('UserCards');
        $this->loadModel('Cards');

        $today_date = date('d-m-Y');    
        $userPIC = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userPIC"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userPIC"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);

            $organizationSelected = $this->request->query('department');
           
            if(!empty($organizationSelected)){
                $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=Users.`id`
                JOIN `user_designations`ON `user_designations`.`user_id`=Users.`id`
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE `organizations`.id=$organizationSelected AND Users.`status`=1 ORDER BY Users.`name` ASC"; 

            }else{
                $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=Users.`id`
                JOIN `user_designations`ON `user_designations`.`user_id`=Users.`id`
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE Users.`status`=1 ORDER BY Users.`name` ASC"; 
            }
            $stmt = $conn->execute($sql);
            $attendances_in = $stmt->fetchAll('assoc');

            $attendances=array();
            
            foreach($attendances_in as $attendance_in){
                $attendances_std=new \stdClass();

                $has_in = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>1])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
                $has_out = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>2])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();

                $attendance_in['attendance_id']=$has_in->id;

                $attendance_in['in']=$has_in->cdate;
				
				//cater for 2kali clock in
				$attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
				
				if($attendance->status == 1){
					$attendance_in['in']=$attendance->cdate;
					$attendance_in['status']=$attendance->status;
					$attendance_in['attendance_code_name']=$attendance->attendance_code->name;
				}else{
					if($has_in->status == 1 AND $has_out->status != 2){
						$attendance_in['status']=$has_in->status;
						$attendance_in['attendance_code_name']=$has_in->attendance_code->name;

					}else if($has_out->status == 2){
						$attendance_in['status']=$has_out->status;
						$attendance_in['attendance_code_name']=$has_out->attendance_code->name;
					}else{
						$attendance_in['status']=2;
						$attendance_in['attendance_code_name']="Absent";
					}
					$attendance_in['out']=$has_out->cdate;
				}

                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {
            /*$sql="SELECT Attendances.*, attendance_codes.`name` AS attendance_codes_name, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                Users 
                JOIN user_organizations ON user_organizations.`user_id`=`users`.id
                JOIN `user_designations`ON `user_designations`.`user_id`=`users`.id
                JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                RIGHT JOIN `attendances` ON attendances.`user_id`= `users`.id
                JOIN attendance_codes ON attendance_codes.`id`=attendances.`attendance_code_id`
                WHERE organizations.`id`=$user_organization_id AND DATE(`attendances`.cdate)=CURDATE()";*/
              $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=`Users`.id
                JOIN `user_designations`ON `user_designations`.`user_id`=`Users`.id
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE organizations.`id`=$user_organization_id AND Users.`status`=1";  
            $stmt = $conn->execute($sql);
            $attendances_in = $stmt->fetchAll('assoc');

            $attendances=array();
            
            foreach($attendances_in as $attendance_in){
                $attendances_std=new \stdClass();

                $has_in = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>1])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
                $has_out = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>2])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
				
				//cater for 2kali clock in
				$attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
				
				if($attendance->status == 1){
					$attendance_in['in']=$attendance->cdate;
					$attendance_in['status']=$attendance->status;
					$attendance_in['attendance_code_name']=$attendance->attendance_code->name;
				}else{
					$attendance_in['in']=$has_in->cdate;
					if($has_in->status == 1 AND $has_out->status != 2){
						$attendance_in['status']=$has_in->status;
						$attendance_in['attendance_code_name']=$has_in->attendance_code->name;

					}else if($has_out->status == 2){
						$attendance_in['status']=$has_out->status;
						$attendance_in['attendance_code_name']=$has_out->attendance_code->name;
					}else{
						$attendance_in['status']=2;
						$attendance_in['attendance_code_name']="Absent";
					}
					$attendance_in['out']=$has_out->cdate;
				}
                //User Card
                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }elseif ($userRoles->hasRole(['Staff'])) {
           $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=Users.`id`
                JOIN `user_designations`ON `user_designations`.`user_id`=Users.`id`
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE organizations.`id`=$user_organization_id AND Users.`status`=1";  
            $stmt = $conn->execute($sql);
            $attendances_in = $stmt->fetchAll('assoc');

            $attendances=array();
            
            foreach($attendances_in as $attendance_in){
                $attendances_std=new \stdClass();

                $has_in = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>1])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
                $has_out = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>2])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();

                $attendance_in['in']=$has_in->cdate;
				
				//cater for 2kali clock in
				$attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
				
				if($attendance->status == 1){
					$attendance_in['in']=$attendance->cdate;
					$attendance_in['status']=$attendance->status;
					$attendance_in['attendance_code_name']=$attendance->attendance_code->name;
				}else{
					if($has_in->status == 1 AND $has_out->status != 2){
						$attendance_in['status']=$has_in->status;
						$attendance_in['attendance_code_name']=$has_in->attendance_code->name;

					}else if($has_out->status == 2){
						$attendance_in['status']=$has_out->status;
						$attendance_in['attendance_code_name']=$has_out->attendance_code->name;
					}else{
						$attendance_in['status']=2;
						$attendance_in['attendance_code_name']="Absent";
					}
					$attendance_in['out']=$has_out->cdate;
				}
                //User Card
                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }
        
        $this->set(compact('attendances','userRoles','attendance_in','today_date','list_organization','list_user','organizationSelected','user_card'));
    }


    public function card()
    {
        $this->set('title', __('Attendances'));

        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('Designations');
        $this->loadModel('UserCards');
        $this->loadModel('Cards');

        $today_date = date('d-m-Y');    
        $userPIC = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userPIC"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userPIC"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);

            if ($this->request->is('post')) {
                $data = $this->request->data;
                $dateSelected = $data['dateChoose'];
                $organizationSelected = $data['department'];
            }
   
            if (empty($dateSelected)){
                $dateSelected = $this->request->query('dateChoose');
                if (empty($dateSelected)){
                    $dateSelected = date('Y-m-d');
                }
                
            }

            if(empty($organizationSelected)){
                $organizationSelected = $this->request->query('department');
            }
            
           
            if(!empty($organizationSelected)){
                $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=Users.`id`
                JOIN `user_designations`ON `user_designations`.`user_id`=Users.`id`
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE `organizations`.id=$organizationSelected AND Users.`status`=1 ORDER BY Users.`name` ASC"; 

            }else{
                $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=Users.`id`
                JOIN `user_designations`ON `user_designations`.`user_id`=Users.`id`
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE Users.`status`=1 ORDER BY Users.`name` ASC"; 
            }
            $stmt = $conn->execute($sql);
            $attendances_in = $stmt->fetchAll('assoc');

            $attendances=array();
            
            foreach($attendances_in as $attendance_in){
                $attendances_std=new \stdClass();

                $has_in = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>1])->where(['DATE(Attendances.cdate)'=>$dateSelected])->order('Attendances.cdate DESC')->limit(1)->first();
                
                $has_out = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>2])->where(['DATE(Attendances.cdate)'=>$dateSelected])->order('Attendances.cdate DESC')->limit(1)->first();

                $attendance_in['attendance_id']=$has_in->id;

                $attendance_in['in']=$has_in->cdate;
                
                //cater for 2kali clock in
                $attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['DATE(Attendances.cdate)'=>$dateSelected])->order('Attendances.cdate DESC')->limit(1)->first();
                
                if($attendance->status == 1){
                    $attendance_in['in']=$attendance->cdate;
                    $attendance_in['status']=$attendance->status;
                    $attendance_in['attendance_code_name']=$attendance->attendance_code->name;
                }else{
                    if($has_in->status == 1 AND $has_out->status != 2){
                        $attendance_in['status']=$has_in->status;
                        $attendance_in['attendance_code_name']=$has_in->attendance_code->name;

                    }else if($has_out->status == 2){
                        $attendance_in['status']=$has_out->status;
                        $attendance_in['attendance_code_name']=$has_out->attendance_code->name;
                    }else{
                        $attendance_in['status']=2;
                        $attendance_in['attendance_code_name']="Absent";
                    }
                    $attendance_in['out']=$has_out->cdate;
                }

                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)"=>$dateSelected])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {
            /*$sql="SELECT Attendances.*, attendance_codes.`name` AS attendance_codes_name, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                Users 
                JOIN user_organizations ON user_organizations.`user_id`=`users`.id
                JOIN `user_designations`ON `user_designations`.`user_id`=`users`.id
                JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                RIGHT JOIN `attendances` ON attendances.`user_id`= `users`.id
                JOIN attendance_codes ON attendance_codes.`id`=attendances.`attendance_code_id`
                WHERE organizations.`id`=$user_organization_id AND DATE(`attendances`.cdate)=CURDATE()";*/
              $sql="SELECT  Users.id AS user_id, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                users Users 
                JOIN user_organizations ON user_organizations.`user_id`=`Users`.id
                JOIN `user_designations`ON `user_designations`.`user_id`=`Users`.id
                RIGHT JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON `organizations`.id = designations.`organization_id`
                WHERE organizations.`id`=$user_organization_id AND Users.`status`=1";  
            $stmt = $conn->execute($sql);
            $attendances_in = $stmt->fetchAll('assoc');

            $attendances=array();
            
            foreach($attendances_in as $attendance_in){
                $attendances_std=new \stdClass();

                $has_in = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>1])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
                $has_out = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(['Attendances.status'=>2])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
                
                //cater for 2kali clock in
                $attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
                
                if($attendance->status == 1){
                    $attendance_in['in']=$attendance->cdate;
                    $attendance_in['status']=$attendance->status;
                    $attendance_in['attendance_code_name']=$attendance->attendance_code->name;
                }else{
                    $attendance_in['in']=$has_in->cdate;
                    if($has_in->status == 1 AND $has_out->status != 2){
                        $attendance_in['status']=$has_in->status;
                        $attendance_in['attendance_code_name']=$has_in->attendance_code->name;

                    }else if($has_out->status == 2){
                        $attendance_in['status']=$has_out->status;
                        $attendance_in['attendance_code_name']=$has_out->attendance_code->name;
                    }else{
                        $attendance_in['status']=2;
                        $attendance_in['attendance_code_name']="Absent";
                    }
                    $attendance_in['out']=$has_out->cdate;
                }
                //User Card
                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }
        
        $this->set(compact('dateSelected','attendances','userRoles','attendance_in','today_date','list_organization','list_user','organizationSelected','user_card'));
    }


    /**
     * View method
     *
     * @param string|null $id Attendance id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $attendance = $this->Attendances->get($id, [
            'contain' => ['Users', 'AttendanceCodes']
        ]);

        $this->set('attendance', $attendance);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */

    public function clock_in()
    {
        //manual db connection
        $conn = ConnectionManager::get('default');

        // Create from a string datetime.
        $today_date = date('d-m-Y');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('SettingAttendancesReasons');
        
        $departmentSelected = $this->request->query('department');
        $staffSelected = $this->request->query('staff');
        $userPIC = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userPIC"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        
        /*if ($userRoles->hasRole(['Master Admin'])) {
            $users = $this->Users->find('list');
        }else  if ($userRoles->hasRole(['Supervisor'])) {
            $users = $this->Users->find()->where(['report_to'=>$userPIC]);
            foreach($users as $user){
                $user_ids[] = $user->id;
            }
            $users = $this->Users->find('list')->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userPIC]);
        }else  if ($userRoles->hasRole(['Admin'])) {
            $has_attend = $this->Attendances->find('all',array('conditions'=>array('Attendances.user_id'=>"$userPIC", "DATE(Attendances.cdate)=CURDATE()")))->order(['Attendances.cdate'=>'DESC'])->contain(['Users'])->limit(1)->first();
        }elseif ($userRoles->hasRole(['Staff'])) {
            $has_attend = $this->Attendances->find('all',array('conditions'=>array('Attendances.user_id'=>"$userPIC", "DATE(Attendances.cdate)=CURDATE()")))->order(['Attendances.cdate'=>'DESC'])->contain(['Users'])->limit(1)->first();

            $user = $this->Users->find('all')->Where(['id'=>"$userPIC"])->limit(1)->first();
        }*/

        $has_attend = $this->Attendances->find('all',array('conditions'=>array('Attendances.user_id'=>"$userPIC", "DATE(Attendances.cdate)=CURDATE()")))->order(['Attendances.cdate'=>'DESC'])->contain(['Users'])->limit(1)->first();

        $user = $this->Users->find('all')->Where(['id'=>"$userPIC"])->limit(1)->first();

        $attendance = $this->Attendances->newEntity();
        if ($this->request->is('post')) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");

            $data = $this->request->data;

            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            $attendance->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            //$attendance->mdate = date('Y-m-d h:m:s');
            $attendance->pic = $userPIC;
            $action=$data['action'];
            $user_id=$data['user_id'];
            $reason=$data['reason'];
            $remark=$data['remark'];

            $attendance->setting_attendances_reason_id=$reason;
            $attendance->remarks=$remark;
            $attendance->attendance_type_id=2;

            if($action=='in'){
                $attendance->status=1;
                $attendance->attendance_code_id=1;
            }else if($action=='out'){
                $attendance->status=2;
                $attendance->attendance_code_id=2;
            }

            if ($this->Attendances->save($attendance)) {
                $id=$this->Attendances->save($attendance)->id;
                $cdate=$this->Attendances->save($attendance)->cdate;
                if($action=='in'){
                    $attendanceLogTable = TableRegistry::get('AttendanceLogs');
                    $attendance_log = $attendanceLogTable->newEntity();
                    $attendance_log->id=$id;
                    $attendance_log->user_id=$user_id;
                    $attendance_log->attendance_code_id=1;
                    $attendance_log->pic=$userPIC;
                    $attendance_log->setting_attendances_reason_id=$reason;
                    $attendance_log->remarks=$remark;
                    $attendance_log->attendance_type_id=2;
                    $attendance_log->cdate=$cdate;
                    $attendance_log->mdate=$cdate;

                    $this->AttendanceLogs->save($attendance_log);


                    //insert into user_card
                    $start_clockin_time = date("H:i", strtotime("07:30"));
                    $end_clockin_time = date("H:i", strtotime("09:00"));

                    $start_clockout_time = date("H:i", strtotime("16:30"));
                    $end_clockout_time = date("H:i", strtotime("18:00"));

                    if (date('H:i', strtotime($cdate)) >= $start_clockin_time AND date('H:i', strtotime($cdate)) <= $end_clockin_time) {
                        // card yellow (normal clock in)
                        $card="2";
                    }else{
                        // card red (late clock in)
                        $card="3";

                    }
                    $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $sql="INSERT INTO `user_cards` (user_id,card_id,pic,status,cdate,mdate) VALUES (".$user_id.","."'".$card."'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
            
                    $stmt = $conn->execute($sql);


                    //insert into user_card_log
                    $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $sql_log="INSERT INTO `user_cards_logs` (user_id,card_id,pic,status,cdate,mdate) VALUES (".$user_id.","."'".$card."'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
            
                    $stmt_log = $conn->execute($sql_log);

                    $this->Flash->success(__('Successfully clockin.'));


                }else if($action=='out'){

                    $attendanceLogTable = TableRegistry::get('AttendanceLogs');
                    $attendance_log = $attendanceLogTable->newEntity();
                    $attendance_log->id=$id;
                    $attendance_log->user_id=$user_id;
                    $attendance_log->attendance_code_id=2;
                    $attendance_log->pic=$userPIC;
                    $attendance_log->status=2;
                    $attendance_log->setting_attendances_reason_id=$reason;
                    $attendance_log->remarks=$remark;
                    $attendance_log->attendance_type_id=2;
                    $attendance_log->cdate=$cdate;
                    $attendance_log->mdate=$cdate;

                    $this->AttendanceLogs->save($attendance_log);


                    $this->Flash->success(__('Successfully clockout.'));
                }
                

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance could not be saved. Please, try again.'));
        }
        $users = $this->Attendances->Users->find('list', ['limit' => 200]);
        $attendanceCodes = $this->Attendances->AttendanceCodes->find('list', ['limit' => 200]);

        $SettingAttendancesReasons = $this->SettingAttendancesReasons->find('list')->Where(['status'=>1]);
        

        $this->set(compact('attendance', 'users', 'attendanceCodes','today_date','has_attend','user','SettingAttendancesReasons'));
    }

    public function update($id = null)
    {
        //manual db connection
        $conn = ConnectionManager::get('default');

        // Create from a string datetime.
        $today_date = date('d-m-Y');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('SettingAttendancesReasons');
        
        
        $userPIC = $this->AuthUser->id();
        $user_pic = $this->Users->find('all')->Where(['id'=>"$userPIC"])->limit(1)->first();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        
       

        $has_attend = $this->Attendances->find('all',array('conditions'=>array('Attendances.user_id'=>"$id", "DATE(Attendances.cdate)=CURDATE()")))->order(['Attendances.cdate'=>'DESC'])->contain(['Users'])->limit(1)->first();

        $user = $this->Users->find('all')->Where(['id'=>"$id"])->limit(1)->first();

        $attendance = $this->Attendances->newEntity();
        if ($this->request->is('post')) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");

            $data = $this->request->data;

            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            $attendance->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            //$attendance->mdate = date('Y-m-d h:m:s');
            $attendance->pic = $userPIC;
            $action=$data['action'];
            $user_id=$data['user_id'];
            $reason=$data['reason'];
            $remark=$data['remark'];

            $attendance->setting_attendances_reason_id=$reason;
            $attendance->remarks=$remark;
            $attendance->attendance_type_id=2;

            if($action=='in'){
                $attendance->status=1;
                $attendance->attendance_code_id=1;
            }else if($action=='out'){
                $attendance->status=2;
                $attendance->attendance_code_id=2;
            }

            if ($this->Attendances->save($attendance)) {
                $attendance_id=$this->Attendances->save($attendance)->id;
                $cdate=$this->Attendances->save($attendance)->cdate;
                if($action=='in'){
                    $attendanceLogTable = TableRegistry::get('AttendanceLogs');
                    $attendance_log = $attendanceLogTable->newEntity();
                    $attendance_log->id=$attendance_id;
                    $attendance_log->user_id=$user_id;
                    $attendance_log->attendance_code_id=1;
                    $attendance_log->setting_attendances_reason_id=$reason;
                    $attendance_log->remarks=$remark;
                    $attendance_log->attendance_type_id=2;
                    $attendance_log->pic=$userPIC;
                    $attendance_log->cdate=$cdate;
                    $attendance_log->mdate=$cdate;

                    $this->AttendanceLogs->save($attendance_log);

                    //insert into user_card
                    $start_clockin_time = date("H:i", strtotime("07:30"));
                    $end_clockin_time = date("H:i", strtotime("09:00"));

                    $start_clockout_time = date("H:i", strtotime("16:30"));
                    $end_clockout_time = date("H:i", strtotime("18:00"));

                    if (date('H:i', strtotime($cdate)) >= $start_clockin_time AND date('H:i', strtotime($cdate)) <= $end_clockin_time) {
                        // card yellow (normal clock in)
                        $card="2";
                    }else{
                        // card red (late clock in)
                        $card="3";

                    }

                    $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $sql="INSERT INTO `user_cards` (user_id,card_id,pic,status,cdate,mdate) VALUES (".$user_id.","."'".$card."'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
            
                    $stmt = $conn->execute($sql);


                    //insert into user_card_log
                    $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $sql_log="INSERT INTO `user_cards_logs` (user_id,card_id,pic,status,cdate,mdate) VALUES (".$user_id.","."'".$card."'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
            
                    $stmt_log = $conn->execute($sql_log);
                    $this->Flash->success(__('Successfully clockin.'));
                }else if($action=='out'){

                    $attendanceLogTable = TableRegistry::get('AttendanceLogs');
                    $attendance_log = $attendanceLogTable->newEntity();
                    $attendance_log->id=$attendance_id;
                    $attendance_log->user_id=$user_id;
                    $attendance_log->attendance_code_id=2;
                    $attendance_log->pic=$userPIC;
                    $attendance_log->status=2;
                    $attendance_log->cdate=$cdate;
                    $attendance_log->mdate=$cdate;

                    $this->AttendanceLogs->save($attendance_log);
                    
                    $this->Flash->success(__('Successfully clockout.'));

                }
                

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance could not be saved. Please, try again.'));
        }
        $users = $this->Attendances->Users->find('list', ['limit' => 200]);
        $attendanceCodes = $this->Attendances->AttendanceCodes->find('list', ['limit' => 200]);
        $SettingAttendancesReasons = $this->SettingAttendancesReasons->find('list')->Where(['status'=>1]);
    
        $this->set(compact('attendance', 'users', 'attendanceCodes','today_date','has_attend','user','user_pic','SettingAttendancesReasons'));
    }


    public function change_card($id = null)
    { //update card color

        //manual db connection
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('Designations');
        $this->loadModel('UserCards');
        $this->loadModel('Cards');

        $today_date = date('d-m-Y');    
        $userPIC = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userPIC"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userPIC"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;
        
        //User Card
        $user_card = $this->UserCards->find('all')->contain(['Cards','Users'])->where(['UserCards.id'=>$id])->limit(1)->first();
        
        // Card
        $cards = $this->Cards->find('list');


        if ($this->request->is('post')) {
            $data = $this->request->data;

            $user_id = $data['user'];
            $card_id = $data['card'];
            $card_status_id = $data['card_color'];
            $remark = $data['remark'];
            //$attendance_id = $data['attendance'];

            if($user_id!=0 AND $card_id!=""){
                $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
                $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');

                //User Card
                //$card_log = $this->UserCards->find('all')->where(['id'=>$card_id])->limit(1)->first();
                $sql_card="SELECT * FROM user_cards WHERE id=$card_id"; 
                $stmt = $conn->execute($sql_card);
                $card_log = $stmt->fetch('assoc');

                $date=$card_log['cdate'];

                //insert into user_card_log
                $sql_log="INSERT INTO `user_cards_logs` (user_id,card_id,pic,status,cdate,mdate) VALUES (".$card_log['user_id'].","."'".$card_log['card_id']."'".","."'".$card_log['pic']."'".","."'".$card_log['status']."'".","."'".$date."'".","."'".$date."')";
            
                $stmt_log = $conn->execute($sql_log);

                $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=$card_status_id, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user_id AND id=$card_id"; 
                $stmt = $conn->execute($sql_update);


               

                $this->Flash->success(__('Successfully update card status.'));

                return $this->redirect(['action' => 'card']);
            }else{
                $this->Flash->error(__('The card status could not be saved. Please, try again.'));
            }
            
        }
        
        $this->set(compact('user_card','cards'));
    }


    public function add()
    {
        $attendance = $this->Attendances->newEntity();
        if ($this->request->is('post')) {
            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            if ($this->Attendances->save($attendance)) {
                $this->Flash->success(__('The attendance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance could not be saved. Please, try again.'));
        }
        $users = $this->Attendances->Users->find('list', ['limit' => 200]);
        $attendanceCodes = $this->Attendances->AttendanceCodes->find('list', ['limit' => 200]);
        $this->set(compact('attendance', 'users', 'attendanceCodes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Attendance id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $attendance = $this->Attendances->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            if ($this->Attendances->save($attendance)) {
                $this->Flash->success(__('The attendance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance could not be saved. Please, try again.'));
        }
        $users = $this->Attendances->Users->find('list', ['limit' => 200]);
        $attendanceCodes = $this->Attendances->AttendanceCodes->find('list', ['limit' => 200]);
        $this->set(compact('attendance', 'users', 'attendanceCodes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Attendance id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attendance = $this->Attendances->get($id);
        if ($this->Attendances->delete($attendance)) {
            $this->Flash->success(__('The attendance has been deleted.'));
        } else {
            $this->Flash->error(__('The attendance could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
