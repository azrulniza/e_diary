<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;
use \Datetime;
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
        if ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);
            $list_user = $this->Users->find('list')->where(["status"=>1]);
            
            $organizationSelected = $this->request->query('department');
            $staffSelected = $this->request->query('staff');
            $statusSelected = $this->request->query('status');

            $sql_leave = "SELECT user_leaves.*, leave_status.`name` AS leave_status_name, leave_types.`name`AS leave_type_name,users.id AS user_id, users.name AS user_name, organizations.id AS organization_id, organizations.`name` AS organizations_name FROM user_leaves 
                JOIN users on users.id=user_leaves.user_id
                JOIN leave_status ON leave_status.`id`=user_leaves.`leave_status_id`
                JOIN leave_types ON leave_types.`id` = user_leaves.`leave_type_id`
                JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` 
                JOIN organizations ON organizations.id = user_organizations.`organization_id`";

            if(!empty($organizationSelected) AND !empty($staffSelected) AND !empty($statusSelected)){
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND users.id=$staffSelected AND leave_status.id=$statusSelected";
            
            }else if(empty($organizationSelected) AND !empty($staffSelected) AND !empty($statusSelected)){
                $sql_leave .= " WHERE users.id=$staffSelected AND leave_status.id=$statusSelected";
               
            }else if(!empty($organizationSelected) AND empty($staffSelected) AND !empty($statusSelected)){
                $sql_leave .= " WHERE organizations.id=$organizationSelected AND leave_status.id=$statusSelected";
                
            }else if(empty($organizationSelected) AND empty($staffSelected) AND !empty($statusSelected)){
                $sql_leave .= " WHERE leave_status.id=$statusSelected";
                
            }
            $sql_leave .=" ORDER BY user_leaves.cdate desc";
            $stmt_sql_leave = $conn->execute($sql_leave);
            $userLeaves = $stmt_sql_leave->fetchAll('assoc');

        }elseif ($userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) {

           // $list_user = $this->Users->find('list')->contain(['UserOrganizations'])->where(["Users.status"=>1])->where(['UserOrganizations.organization_id'=>"$user_organization_id"]);
            $list_user = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
            return $q->where(['UserOrganizations.organization_id'=>$user_organization_id])->where(['Users.status'=>1]);});

            $list_status = $this->LeaveStatus->find('list')->where(["status"=>1]);

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
                
            }
            $sql_leave .=" ORDER BY user_leaves.cdate desc";
            $stmt_sql_leave = $conn->execute($sql_leave);
            $userLeaves = $stmt_sql_leave->fetchAll('assoc');

        }elseif ($userRoles->hasRole(['Staff'])) {

            $list_status = $this->LeaveStatus->find('list')->where(["status"=>1]);

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
        

        $today_date = date('d-m-Y');    
        $user_id = $this->AuthUser->id();

        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$user_id"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);

        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$user_id"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($userRoles->hasRole(['Master Admin'])) {
            $list_organization = $this->Organizations->find('list')->where(["status"=>1]);
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

            if($userRoles->hasRole(['Admin','Supervisor', 'Staff'])){ //check back dated application
                
                if(strtotime($date) < (time()-(60*60*24)) ){
                    $this->Flash->error(__('Date apply cannot be back dated. Please, try again.'));
                    $error=true;
                }
            }
            
            if($data['leave_type']==1){//personal matters
                
                //from_time
                $from_time=$data['from_time']['hour'].':'.$data['from_time']['minute'];
                $from_date_time=$date.' '.$from_time;
                
                //to_time
                $to_time=$data['to_time']['hour'].':'.$data['to_time']['minute'];
                $to_date_time=$date.' '.$to_time;

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

            /*$userLeave->user_id = $data['staff'];
            $userLeave->date_apply = $date;
            $userLeave->start_time = $from_time;
            $userLeave->end_time = $to_time;
            $userLeave->reason = $data['remark'];
            $userLeave->pic = $user_id;
            $userLeave->leave_status_id = 1;
            $userLeave->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $userLeave->leave_type_id = $data['leave_type'];*/
            if(!$error){
                $sql="INSERT INTO `user_leaves` (user_id,date_apply,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id) VALUES (".$data['staff'].","."'".$date."'".","."'".$from_time."'".","."'".$to_time."'".","."'".$data['remark']."'".","."'".$filename."'".",".$user_id.",1,"."'".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."'".",".$data['leave_type'].")";
            
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
                    
                    $sql_supervisor = "SELECT users.* FROM users JOIN `user_organizations` ON `user_organizations`.`user_id`= `users`.`id` JOIN `users_roles` ON `users_roles`.`user_id`=`users`.`id` WHERE `users_roles`.`role_id`=2 AND `user_organizations`.`organization_id`=$organization_id UNION SELECT users.* FROM users JOIN `users_roles` ON `users_roles`.`user_id`=`users`.`id` WHERE `users_roles`.`role_id`=1";
                    $stmt_sql_supervisor = $conn->execute($sql_supervisor);
                    $get_supervisor = $stmt_sql_supervisor->fetchAll('assoc');

                    //notify supervisor, email


                    //insert log
                    $sql="INSERT INTO `user_leaves_logs` (user_id,date_apply,start_time,end_time,reason,filename,pic,leave_status_id,cdate,leave_type_id) VALUES (".$data['staff'].","."'".$date."'".","."'".$from_time."'".","."'".$to_time."'".","."'".$data['remark']."'".","."'".$filename."'".",".$user_id.",1,"."'".$now->i18nFormat('yyyy-MM-dd HH:mm:ss')."'".",".$data['leave_type'].")";
                
                    $stmt = $conn->execute($sql);
                    $this->Flash->success(__('Successfully apply time off.'));

                    return $this->redirect(['action' => 'index']);
                }
            }
            
            //$this->Flash->error(__('The time off could not be saved. Please, try again.'));
        }
        
        $leaveStatuses = $this->UserLeaves->LeaveStatus->find('list', ['limit' => 200]);
        $leaveTypes = $this->UserLeaves->LeaveTypes->find('list', ['limit' => 200]);

        $this->set(compact('userLeave', 'list_user', 'leaveStatuses', 'leaveTypes','userRoles', 'list_organization','staffSelected', 'last_id','user_organization_id','user_id'));
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
                $this->Flash->success(__('The user leave has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user leave could not be saved. Please, try again.'));
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
}
