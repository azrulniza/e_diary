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
        $this->set('title', __('Attendances'));

        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLates');
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

                    $attendance_in['attendance_code_name']= __($attendance->attendance_code->name);
                    
					
				}else{
					if($has_in->status == 1 AND $has_out->status != 2){
						$attendance_in['status']=$has_in->status;
						$attendance_in['attendance_code_name']=__($has_in->attendance_code->name);

					}else if($has_out->status == 2){
						$attendance_in['status']=$has_out->status;
						$attendance_in['attendance_code_name']=__($has_out->attendance_code->name);
					}else{
						$attendance_in['status']=2;
						$attendance_in['attendance_code_name']= __("Absent");
					}
					$attendance_in['out']=$has_out->cdate;
				}

                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $user_cards = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["MONTH(UserCards.cdate)=MONTH(CURDATE())"])->where(["YEAR(UserCards.cdate)=YEAR(CURDATE())"]);
                $attendance_in['user_cards']=$user_cards;

                $attendance_late_remark = $this->AttendanceLates->find('all')->where(["attendance_id"=>$has_in->id])->limit(1)->first();
                $attendance_in['late_remark_id']=$attendance_late_remark->id;
                $attendance_in['late_remark']=$attendance_late_remark->late_remark;
                $attendance_in['late_remark_status']=$attendance_late_remark->status;


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
				
                $attendance_in['attendance_id']=$has_in->id;

                $attendance_in['in']=$has_in->cdate;

				//cater for 2kali clock in
				$attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
				
				if($attendance->status == 1){
					$attendance_in['in']=$attendance->cdate;
					$attendance_in['status']=$attendance->status;
					$attendance_in['attendance_code_name']=__($attendance->attendance_code->name);
				}else{
					$attendance_in['in']=$has_in->cdate;
					if($has_in->status == 1 AND $has_out->status != 2){
						$attendance_in['status']=$has_in->status;
						$attendance_in['attendance_code_name']=__($has_in->attendance_code->name);

					}else if($has_out->status == 2){
						$attendance_in['status']=$has_out->status;
						$attendance_in['attendance_code_name']=__($has_out->attendance_code->name);
					}else{
						$attendance_in['status']=2;
						$attendance_in['attendance_code_name']=__("Absent");
					}
					$attendance_in['out']=$has_out->cdate;
				}
                //User Card
                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $user_cards = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["MONTH(UserCards.cdate)=MONTH(CURDATE())"])->where(["YEAR(UserCards.cdate)=YEAR(CURDATE())"]);
                $attendance_in['user_cards']=$user_cards;

                $attendance_late_remark = $this->AttendanceLates->find('all')->where(["attendance_id"=>$has_in->id])->limit(1)->first();
                $attendance_in['late_remark_id']=$attendance_late_remark->id;
                $attendance_in['late_remark']=$attendance_late_remark->late_remark;
                $attendance_in['late_remark_status']=$attendance_late_remark->status;

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

                $attendance_in['attendance_id']=$has_in->id;

                $attendance_in['in']=$has_in->cdate;
				
				//cater for 2kali clock in
				$attendance = $this->Attendances->find('all')->contain(['AttendanceCodes'])->Where(['user_id'=>$attendance_in['user_id']])->where(["DATE(Attendances.cdate)=CURDATE()"])->order('Attendances.cdate DESC')->limit(1)->first();
				
				if($attendance->status == 1){
					$attendance_in['in']=$attendance->cdate;
					$attendance_in['status']=$attendance->status;
					$attendance_in['attendance_code_name']=__($attendance->attendance_code->name);
				}else{
					if($has_in->status == 1 AND $has_out->status != 2){
						$attendance_in['status']=$has_in->status;
						$attendance_in['attendance_code_name']=__($has_in->attendance_code->name);

					}else if($has_out->status == 2){
						$attendance_in['status']=$has_out->status;
						$attendance_in['attendance_code_name']=__($has_out->attendance_code->name);
					}else{
						$attendance_in['status']=2;
						$attendance_in['attendance_code_name']=__("Absent");
					}
					$attendance_in['out']=$has_out->cdate;
				}
                //User Card
                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)=CURDATE()"])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $user_cards = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["MONTH(UserCards.cdate)=MONTH(CURDATE())"])->where(["YEAR(UserCards.cdate)=YEAR(CURDATE())"]);
                $attendance_in['user_cards']=$user_cards;

                $attendance_late_remark = $this->AttendanceLates->find('all')->where(["attendance_id"=>$has_in->id])->limit(1)->first();
                $attendance_in['late_remark_id']=$attendance_late_remark->id;
                $attendance_in['late_remark']=$attendance_late_remark->late_remark;
                $attendance_in['late_remark_status']=$attendance_late_remark->status;


                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }
        
        // save late attendance remark
        if ($this->request->is('post')) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");

            $data = $this->request->data;

            $attendanceLatesTable = TableRegistry::get('AttendanceLates');

            $attendance_late_exist = $this->AttendanceLates->find()->where(['attendance_id'=>$data['attendance_id']])->limit(1)->first();

            if($attendance_late_exist->id > 0){ //update
                
                
                $attendanceLates = $this->AttendanceLates->patchEntity($attendance_late_exist, $this->request->getData());
                $attendanceLates->attendance_id=$data['attendance_id'];
                $attendanceLates->late_remark=$data['remark'];
                $attendanceLates->status=0;
                $attendanceLates->created_by=$userPIC;
                $attendanceLates->mdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                if ($this->AttendanceLates->save($attendanceLates)) {
                    $this->Flash->success(__('Successfully saved remark'));
                    return $this->redirect(['action' => 'index']);
                }
                
                
            }else{ //add new
                $attendanceLates = $attendanceLatesTable->newEntity();

                $attendanceLates->attendance_id=$data['attendance_id'];
                $attendanceLates->late_remark=$data['remark'];
                $attendanceLates->status=0;
                $attendanceLates->created_by=$userPIC;
                $attendanceLates->cdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $attendanceLates->mdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                
                try{
                    if($this->AttendanceLates->save($attendanceLates)){                   
                        $this->Flash->success(__('Successfully saved remark'));
                        return $this->redirect(['action' => 'index']);
                    }
                    
                }catch(\Exception $e){               
                    $this->Flash->error(__('Unsuccessfully save remark'));               
                }
            }

            

            
        }
        $this->set(compact('attendances','userRoles','attendance_in','today_date','list_organization','list_user','organizationSelected','user_card'));
    }

    public function approve($id=null){
        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLates');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('Designations');
        $this->loadModel('UserCards');
        $this->loadModel('Cards');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
            $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $data = $this->request->data;

            $attendanceLatesTable = TableRegistry::get('AttendanceLates');

            $attendance_late_exist = $this->AttendanceLates->find()->where(['id'=>$id])->limit(1)->first();

            if($attendance_late_exist->id > 0){ //update
                
                
                $attendanceLates = $this->AttendanceLates->patchEntity($attendance_late_exist, $this->request->getData());
                $attendanceLates->pic=$user_id;
                $attendanceLates->status=1;
                $attendanceLates->mdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                if ($this->AttendanceLates->save($attendanceLates)) {

                    $attendance = $this->Attendances->find()->where(['id'=>$attendance_late_exist->attendance_id])->limit(1)->first();
                    $sql_card="SELECT * FROM user_cards WHERE user_id=".$attendance->user_id." AND cdate="."'".date_format($attendance->cdate,"Y-m-d H:i:s")."'"; 
                    $stmt = $conn->execute($sql_card);
                    $card_log = $stmt->fetch('assoc');
            

                    $date=$card_log['cdate'];
                    $mdate=$card_log['mdate'];

                    //insert into user_card_log
                    $sql_log="INSERT INTO `user_cards_logs` (user_card_id,user_id,card_id,pic,status,cdate,mdate,remarks) VALUES (".$card_log['id'].",".$card_log['user_id'].","."'".$card_log['card_id']."'".","."'".$card_log['pic']."'".","."'".$card_log['status']."'".","."'".$date."'".","."'".$cur_date."'".","."'".$card_log['remarks']."'".")";
                
                    $stmt_log = $conn->execute($sql_log);

                    $sql_update="UPDATE `user_cards` SET remarks='Mendapat Kelulusan', card_id=2, pic=$user_id, mdate='$cur_date' WHERE user_id=".$attendance->user_id." AND id=".$card_log['id']; 
                    $stmt = $conn->execute($sql_update);



                    $this->Flash->success(__('Successfully saved'));
                    return $this->redirect(['action' => 'late_approval']);
                }
                
            }else{
                $this->Flash->error(__('Approval could not be saved. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'late_approval']);
    }


    public function reject($id=null){
        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLates');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('Designations');
        $this->loadModel('UserCards');
        $this->loadModel('Cards');

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");

            $data = $this->request->data;

            $attendanceLatesTable = TableRegistry::get('AttendanceLates');

            $attendance_late_exist = $this->AttendanceLates->find()->where(['id'=>$id])->limit(1)->first();

            if($attendance_late_exist->id > 0){ //update
                
                
                $attendanceLates = $this->AttendanceLates->patchEntity($attendance_late_exist, $this->request->getData());
                $attendanceLates->pic=$user_id;
                $attendanceLates->status=2;
                $attendanceLates->mdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                if ($this->AttendanceLates->save($attendanceLates)) {
                    $this->Flash->success(__('Successfully saved'));
                    return $this->redirect(['action' => 'late_approval']);
                }
                
            }else{
                $this->Flash->error(__('Approval could not be saved. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'late_approval']);
    }


    public function late_approval(){
        $this->set('title', __('Attendances'));

        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLates');
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

        if ($userRoles->hasRole(['Ketua Pengarah'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);

            if ($this->request->is('post')) {
                $data = $this->request->data;
                $dateSelected = $data['dateChoose'];
                $organizationSelected = $data['department'];
                $statusSelected = $data['status'];
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

            if(empty($statusSelected)){
                $statusSelected = $this->request->query('status');
            }
            
            $sql="SELECT `attendance_lates`.*, attendances.cdate AS clock_in,users.id AS user_id, users.name AS username, users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM `attendance_lates` 
                    JOIN `attendances` ON attendances.`id`=attendance_lates.`attendance_id`
                    JOIN users ON users.id=attendances.`user_id`
                    JOIN users_roles ON users.id=users_roles.`user_id`
                    JOIN `user_organizations` ON `user_organizations`.`user_id`=users.`id`
                    JOIN organizations ON organizations.`id`=`user_organizations`.`organization_id`
                    JOIN `user_designations`ON `user_designations`.`user_id`=users.`id`
                    JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                    WHERE `users_roles`.role_id=2 AND DATE(attendances.cdate)='$dateSelected'";

            if(!empty($organizationSelected) AND !empty($statusSelected)){

                $sql.=" AND organizations.id=$organizationSelected AND attendance_lates.status=$statusSelected"; 

            }else if(empty($organizationSelected) AND !empty($statusSelected)){

                $sql.=" AND attendance_lates.status=$statusSelected"; 

            }else if(!empty($organizationSelected) AND empty($statusSelected)){

                $sql.=" AND organizations.id=$organizationSelected"; 

            }

            $sql.=" ORDER BY users.`name` ASC";

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
                    $attendance_in['attendance_code_name']=__($attendance->attendance_code->name);
                }else{
                    if($has_in->status == 1 AND $has_out->status != 2){
                        $attendance_in['attendance_code_name']=__($has_in->attendance_code->name);

                    }else if($has_out->status == 2){
                        $attendance_in['attendance_code_name']=__($has_out->attendance_code->name);
                    }else{
                        $attendance_in['attendance_code_name']= __("Absent");
                    }
                    $attendance_in['out']=$has_out->cdate;
                }

                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)"=>$dateSelected])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }elseif ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);

            if ($this->request->is('post')) {
                $data = $this->request->data;
                $dateSelected = $data['dateChoose'];
                $organizationSelected = $data['department'];
                $statusSelected = $data['status'];
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

            if(empty($statusSelected)){
                $statusSelected = $this->request->query('status');
            }
            
            $sql="SELECT `attendance_lates`.*, attendances.cdate AS clock_in,users.id AS user_id, users.name AS username, users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM `attendance_lates` 
                    JOIN `attendances` ON attendances.`id`=attendance_lates.`attendance_id`
                    JOIN users ON users.id=attendances.`user_id`
                    JOIN `user_organizations` ON `user_organizations`.`user_id`=users.`id`
                    JOIN organizations ON organizations.`id`=`user_organizations`.`organization_id`
                    JOIN `user_designations`ON `user_designations`.`user_id`=users.`id`
                    JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                    WHERE DATE(attendances.cdate)='$dateSelected'";

            if(!empty($organizationSelected) AND !empty($statusSelected)){

                $sql.=" AND organizations.id=$organizationSelected AND attendance_lates.status=$statusSelected"; 

            }else if(empty($organizationSelected) AND !empty($statusSelected)){

                $sql.=" AND attendance_lates.status=$statusSelected"; 

            }else if(!empty($organizationSelected) AND empty($statusSelected)){

                $sql.=" AND organizations.id=$organizationSelected"; 

            }

            $sql.=" ORDER BY users.`name` ASC";

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
                    $attendance_in['attendance_code_name']=__($attendance->attendance_code->name);
                }else{
                    if($has_in->status == 1 AND $has_out->status != 2){
                        $attendance_in['attendance_code_name']=__($has_in->attendance_code->name);

                    }else if($has_out->status == 2){
                        $attendance_in['attendance_code_name']=__($has_out->attendance_code->name);
                    }else{
                        $attendance_in['attendance_code_name']= __("Absent");
                    }
                    $attendance_in['out']=$has_out->cdate;
                }

                $user_card = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["DATE(UserCards.cdate)"=>$dateSelected])->limit(1)->first();
                $attendance_in['card']=$user_card->card->name;
                $attendance_in['card_id']=$user_card->id;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {
           $list_organization = $this->Organizations->find('list')->where(["status"=>1])->where(["id"=>$user_organization_id]);

           if ($this->request->is('post')) {
                $data = $this->request->data;
                $dateSelected = $data['dateChoose'];
                $organizationSelected = $data['department'];
                $statusSelected = $data['status'];
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

            if(empty($statusSelected)){
                $statusSelected = $this->request->query('status');
            }

            $sql="SELECT `attendance_lates`.*, attendances.cdate AS clock_in,users.id AS user_id, users.name AS username, users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM `attendance_lates` 
                    JOIN `attendances` ON attendances.`id`=attendance_lates.`attendance_id`
                    JOIN users ON users.id=attendances.`user_id`
                    JOIN `user_organizations` ON `user_organizations`.`user_id`=users.`id`
                    JOIN organizations ON organizations.`id`=`user_organizations`.`organization_id`
                    JOIN `user_designations`ON `user_designations`.`user_id`=users.`id`
                    JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                    WHERE DATE(attendances.cdate)='$dateSelected' AND organizations.id=$user_organization_id"; 

            if(!empty($statusSelected)){

                $sql.=" AND attendance_lates.status=$statusSelected"; 

            }

            $sql.=" ORDER BY users.`name` ASC";

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
                    $attendance_in['attendance_code_name']=__($attendance->attendance_code->name);
                }else{
                    $attendance_in['in']=$has_in->cdate;
                    if($has_in->status == 1 AND $has_out->status != 2){
                        $attendance_in['attendance_code_name']= __($has_in->attendance_code->name);

                    }else if($has_out->status == 2){
                        $attendance_in['attendance_code_name']= __($has_out->attendance_code->name);
                    }else{
                        $attendance_in['attendance_code_name']= __("Absent");
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
        
        $language_id = $this->getLanguageId();
        $statusLate = array(0=>"New Application",1=>"Approved",2=>"Rejected");
        if($language_id==2){ //Bahasa
            $statusLate = array(0=>"Permohonan Baru",1=>"Lulus",2=>"Tidak diterima");
        }
        $attendanceLateStatus = $statusLate;

        $this->set(compact('statusSelected','attendanceLateStatus','dateSelected','attendances','userRoles','attendance_in','today_date','list_organization','list_user','organizationSelected','user_card'));
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
            
           
            $language_id = $this->getLanguageId();
            if($language_id==2){ //Bahasa
                $list_card = $this->Cards->find('list')->where(["status"=>1]);

                $arrayColor = array();
                foreach ($list_card as $key => $value) {
                    if($key==1){
                        $arrayColor[$key]="Hijau";
                    }elseif($key==2){
                        $arrayColor[$key]="Kuning";
                    }elseif($key==3){
                        $arrayColor[$key]="Merah";
                    }
                    
                }
                $list_card  = $arrayColor;
            }else{
                $list_card = $this->Cards->find('list')->where(["status"=>1]);
            }

            $cardselected = $this->request->query('card');
            $organizationSelected = $this->request->query('department');
            $yearselected = $this->request->query('att_year');
            $monthselected = $this->request->query('att_month');
   
            
            if (empty($yearselected)){
                $yearselected = date('Y');
            }

            if (empty($monthselected)){
                $monthselected = date('m');
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

                
                $user_cards = $this->UserCards->find('all')->contain(['Cards'])->where(['UserCards.user_id'=>$attendance_in['user_id']])->where(["MONTH(UserCards.cdate)"=>"$monthselected"])->where(["YEAR(UserCards.cdate)"=>"$yearselected"]);
                
                $attendance_in['user_cards']=$user_cards;

                $attendances[$attendance_in['user_id']]=$attendance_in;

                if(!empty($cardselected)){
                    $count_red=0;

                    foreach($user_cards as $data){
                       if($data->card_id==3){
                            $count_red++;
                       }
                    }

                    if($cardselected==1){//hijau
                        if($count_red <= 3){
                            unset($attendances[$attendance_in['user_id']]); //remove array user with no green card
                        }
                    }else if($cardselected==3){//red
                        if($count_red != 3){
                            unset($attendances[$attendance_in['user_id']]); //remove array user with no red card
                        }
                    }else if($cardselected==2){//yellow
                        if($count_red >= 3){
                            unset($attendances[$attendance_in['user_id']]); //remove array user with no yellow card
                        }
                    }
                    
                }
                
            }
        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {
            
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
        
        $this->set(compact('list_card','cardselected','yearselected','monthselected','attendances','userRoles','attendance_in','today_date','list_organization','list_user','organizationSelected','user_card'));
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
                    $last_id = $stmt->lastInsertId();

                    //insert into user_card_log
                    $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $sql_log="INSERT INTO `user_cards_logs` (user_card_id,user_id,card_id,pic,status,cdate,mdate) VALUES (".$last_id.",".$user_id.","."'".$card."'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
            
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
        
        $language_id = $this->getLanguageId();

        if($language_id==2){ //Bahasa
            $arrayType = array();
            foreach ($SettingAttendancesReasons as $key => $value) {
                if($key==1){
                    $arrayType[$key]="Bekerja luar kawasan";
                }elseif($key==2){
                    $arrayType[$key]="Peranti Biometrik tidak berfungsi";
                }
                
            }
            $SettingAttendancesReasons = $arrayType;
        }
    
        //added by intan

       $forRealtime = "SELECT * FROM attendances WHERE user_id ='".$user['id']."' ORDER BY cdate DESC LIMIT 1";
       $realResult = $conn->execute($forRealtime); 
       $realData = $realResult->fetchAll('assoc');

       foreach ($realData as $timerData){
            $intime = $timerData['cdate'];
       }
       

        $this->set(compact('attendance', 'users', 'attendanceCodes','today_date','has_attend','user','intime','user_pic','SettingAttendancesReasons'));
    }


    public function change_card_month(){
        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLates');
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

        if ($this->request->is('post')) {
            $now = \Cake\I18n\Time::now("Asia/Kuala_Lumpur");
            $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $data = $this->request->data;

            //[card_color] => 3 [remark] => catatan [user_id] => 4 [card_ids] => 8 9 10 11 12 13 [month] => 11 [year] => 2019
            $card_color = $data['card_color'];
            $cur_color = $data['cur_color'];
            $remark = $data['remark'];
            $user = $data['user_id'];

            //User Card
            $user_card = $this->UserCards->find('all')->contain(['Cards','Users'])->where(['UserCards.id IN'=>explode(" ",$data['card_ids'])]);
            
            if(!empty($data['card_ids'])){

                $count=0;
                foreach ($user_card as $key) {
                
                    //$card = $this->UserCards->find()->where(['id'=>$key->id]);
                    $card_id=$key->id;

                    $sql_card="SELECT * FROM user_cards WHERE id=$card_id"; 
                    $stmt = $conn->execute($sql_card);
                    $card_log = $stmt->fetch('assoc');

                    $date=$card_log['cdate'];
                    $mdate=$card_log['mdate'];

                    //insert into user_card_log
                    $sql_log="INSERT INTO `user_cards_logs` (user_card_id,user_id,card_id,pic,status,cdate,mdate,remarks) VALUES (".$card_id.",".$card_log['user_id'].","."'".$card_log['card_id']."'".","."'".$card_log['pic']."'".","."'".$card_log['status']."'".","."'".$date."'".","."'".$mdate."'".","."'".$card_log['remarks']."'".")";
                
                    $stmt_log = $conn->execute($sql_log);

                    if($card_color!=2){//yellow
                        if($cur_color==1 && $card_color==3){ // if change green to red
                            if($count<3){
                                $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=3, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                                $stmt = $conn->execute($sql_update);
                            }elseif($count>=3){
                                $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=2, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                                $stmt = $conn->execute($sql_update);
                            }
                        }else if($cur_color==2 && $card_color==3){ // if change yellow to red
                            if($count<3){
                                $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=3, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                                $stmt = $conn->execute($sql_update);
                            }elseif($count >=3){
                                $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=2, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                                $stmt = $conn->execute($sql_update);
                            }
                        }else if($cur_color==3 && $card_color==1){ // if change red to green
                            
                            $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=3, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                            $stmt = $conn->execute($sql_update);
                            
                        }else if($cur_color==2 && $card_color==1){ // if change yellow to green
                            
                            $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=3, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                            $stmt = $conn->execute($sql_update);
                            
                        }
                        
                        
                    }else{
                        $sql_update="UPDATE `user_cards` SET remarks='$remark', card_id=$card_color, pic=$userPIC, mdate='$cur_date' WHERE user_id=$user AND id=$card_id"; 
                        $stmt = $conn->execute($sql_update);
                    }
                    $count++;
                }

                if($card_color==1){ //if change to green
                    if($count < 4){
                        $loop_size=4-$count;
                        for($i=0; $i<$loop_size; $i++){
                            $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                            $card=3;
                            $sql="INSERT INTO `user_cards` (user_id,card_id,change_card_status,pic,status,cdate,mdate) VALUES (".$user.","."'".$card."'".","."'1'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
                    
                            $stmt = $conn->execute($sql);
                            $last_id = $stmt->lastInsertId();

                            //insert into user_card_log
                            $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                            $sql_log="INSERT INTO `user_cards_logs` (user_card_id,user_id,card_id,change_card_status,pic,status,cdate,mdate) VALUES (".$last_id.",".$user.","."'".$card."'".","."'1'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
                    
                            $stmt_log = $conn->execute($sql_log);
                        }
                    }
                }else if($card_color==3){ //if change to red
                    if($count < 3){
                        $loop_size=3-$count;
                        for($j=0; $j<$loop_size; $j++){
                            $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                            $card=3;
                            $sql="INSERT INTO `user_cards` (user_id,card_id,change_card_status,pic,status,cdate,mdate) VALUES (".$user.","."'".$card."'".","."'1'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
                    
                            $stmt = $conn->execute($sql);
                            $last_id = $stmt->lastInsertId();

                            //insert into user_card_log
                            $cur_date=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                            $sql_log="INSERT INTO `user_cards_logs` (user_card_id,user_id,card_id,change_card_status,pic,status,cdate,mdate) VALUES (".$last_id.",".$user.","."'".$card."'".","."'1'".","."'".$userPIC."'".","."'1'".","."'".$cur_date."'".","."'".$cur_date."')";
                    
                            $stmt_log = $conn->execute($sql_log);
                        }
                    }
                }
                $this->Flash->success(__('Successfully update card status.'));

                return $this->redirect(['action' => 'card']);
            }else{

                $this->Flash->error(__('The card status could not be saved. Please, try again.'));
            }
            
        }
        return $this->redirect(['action' => 'card']);
        //return $this->redirect(['action' => 'late_approval']);
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
