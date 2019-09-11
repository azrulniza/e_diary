<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
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
        $this->loadModel('Users');
        $this->loadModel('Attendances');
        $this->loadModel('AttendanceLogs');
        $this->loadModel('Organizations');
        $this->loadModel('Designations');

        $this->paginate = [
            'contain' => ['Users', 'AttendanceCodes']
        ];

    $query = $this->Attendances->find()
            ->join([
                'u' => [
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'u.id = attendances.user_id',
                ],
                'uo' => [
                    'table' => 'users_organization',
                    'type' => 'LEFT',
                    'conditions' => 'uo.user_id = u.id',
                ],
                'o' => [
                    'table' => 'Organizations',
                    'type' => 'LEFT',
                    'conditions' => 'o.id = uo.organization_id',
                ]
            ]);

        //$attendances = $this->paginate($this->Attendances);
            $attendances = $this->paginate($query);

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

        $this->set(compact('attendances'));
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
            $attendance->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $attendance->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
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
                if($action=='in'){
                    $attendanceLogTable = TableRegistry::get('AttendanceLogs');
                    $attendance_log = $attendanceLogTable->newEntity();
                    $attendance_log->id=$id;
                    $attendance_log->user_id=$user_id;
                    $attendance_log->attendance_code_id=1;
                    $attendance_log->pic=$userPIC;
                    $attendance_log->cdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $attendance_log->mdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');

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
                    $attendance_log->cdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $attendance_log->mdate=$now->i18nFormat('yyyy-MM-dd HH:mm:ss');

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
