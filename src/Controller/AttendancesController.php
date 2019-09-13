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
        // my_connection is defined in your database config
        $conn = ConnectionManager::get('default');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('UserOrganizations');
        $this->loadModel('Designations');

    
        $userPIC = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userPIC"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        $usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => "$userPIC"])->limit(1)->first();
        $user_organization_id=$usersOrganization->organization_id;

        if ($userRoles->hasRole(['Master Admin'])) {
            $users = $this->Users->find('list');
        }else  if ($userRoles->hasRole(['Supervisor'])) {
            $users = $this->Users->find()->where(['report_to'=>$userPIC]);
            foreach($users as $user){
                $user_ids[] = $user->id;
            }
            $users = $this->Users->find('list')->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userPIC]);
        }else  if ($userRoles->hasRole(['Admin'])) {
            $users = $this->Users->find('list')->where(['report_to'=>$userPIC]);
        }elseif ($userRoles->hasRole(['Staff'])) {
            /*$sql="SELECT Attendances.*, attendance_codes.`name` AS attendance_codes_name, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id
            FROM Attendances
            JOIN attendance_codes ON attendance_codes.`id`=attendances.`attendance_code_id`
            JOIN Users ON Attendances.`user_id`= Users.id
            JOIN User_designations ON user_designations.`user_id`= Users.id
            JOIN designations ON designations.id = user_designations.`designation_id`
            JOIN organizations ON organizations.id = designations.`organization_id`
            WHERE attendances.`user_id`=$userPIC AND DATE(attendances.cdate)=CURDATE()";*/
            $sql="SELECT Attendances.*, attendance_codes.`name` AS attendance_codes_name, Users.name AS username, Users.`report_to`, designations.`id` AS desgination_id, designations.`name` AS designation_name, designations.`gred` AS designation_gred, organizations.`name` AS organization_name, organizations.`id` AS organization_id FROM
                Users 
                JOIN user_organizations ON user_organizations.`user_id`=users.id
                JOIN `user_designations`ON `user_designations`.`user_id`=users.id
                JOIN `designations` ON `designations`.id=`user_designations`.designation_id
                JOIN organizations ON organizations.id = designations.`organization_id`
                RIGHT JOIN `attendances` ON attendances.`user_id`= users.id
                JOIN attendance_codes ON attendance_codes.`id`=attendances.`attendance_code_id`
                WHERE DATE(attendances.cdate)=CURDATE()";
            $stmt = $conn->execute($sql);
            $attendances_in = $stmt->fetchAll('assoc');

            $attendances=array();
            
            foreach($attendances_in as $attendance_in){
                $attendances_std=new \stdClass();

                $has_in = $this->Attendances->find('all')->Where(['user_id'=>$attendance_in['user_id']])->where(['status'=>1])->where(["DATE(cdate)=CURDATE()"])->limit(1)->first();
                $has_out = $this->Attendances->find('all')->Where(['user_id'=>$attendance_in['user_id']])->where(['status'=>2])->where(["DATE(cdate)=CURDATE()"])->limit(1)->first();

                $attendance_in['in']=$has_in->cdate;
                $attendance_in['out']=$has_out->cdate;

                $attendances[$attendance_in['user_id']]=$attendance_in;
            }
        }
        
        $this->set(compact('attendances','userRoles','attendances_in'));
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
        // Create from a string datetime.
        $today_date = date('d-m-Y');

        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        
        $departmentSelected = $this->request->query('department');
        $staffSelected = $this->request->query('staff');
        $userPIC = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userPIC"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        
        if ($userRoles->hasRole(['Master Admin'])) {
            $users = $this->Users->find('list');
        }else  if ($userRoles->hasRole(['Supervisor'])) {
            $users = $this->Users->find()->where(['report_to'=>$userPIC]);
            foreach($users as $user){
                $user_ids[] = $user->id;
            }
            $users = $this->Users->find('list')->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userPIC]);
        }else  if ($userRoles->hasRole(['Admin'])) {
            $users = $this->Users->find('list')->where(['report_to'=>$userPIC]);
        }elseif ($userRoles->hasRole(['Staff'])) {
            $has_attend = $this->Attendances->find('all',array('conditions'=>array('Attendances.user_id'=>"$userPIC", "DATE(Attendances.cdate)=CURDATE()")))->order(['Attendances.cdate'=>'DESC'])->contain(['Users'])->limit(1)->first();

            $user = $this->Users->find('all')->Where(['id'=>"$userPIC"])->limit(1)->first();
        }

        $attendance = $this->Attendances->newEntity();
        if ($this->request->is('post')) {
            $now = \Cake\I18n\Time::now();

            $data = $this->request->data;

            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            //$attendance->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            //$attendance->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $attendance->pic = $userPIC;
            $action=$data['action'];
            $user_id=$data['user_id'];

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
                    $attendance_log->cdate=$cdate;
                    $attendance_log->mdate=$cdate;

                    $this->AttendanceLogs->save($attendance_log);

                    $this->Flash->success(__('Successfully clockin.'));
                }else if($action=='out'){

                    $attendanceLogTable = TableRegistry::get('AttendanceLogs');
                    $attendance_log = $attendanceLogTable->newEntity();
                    $attendance_log->id=$id;
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

       
        

        $this->set(compact('attendance', 'users', 'attendanceCodes','today_date','has_attend','user'));
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
