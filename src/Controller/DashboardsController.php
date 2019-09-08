<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Utility\Location;

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
        $this->loadModel('Organizations');
        
        $departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
        $userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
        
        if ($userRoles->hasRole(['Master Admin'])) {
			$users = $this->Users->find('list');
        }else  if ($userRoles->hasRole(['Supervisor'])) {
			$users = $this->Users->find()->where(['report_to'=>$userId]);
			foreach($users as $user){
				$user_ids[] = $user->id;
			}
			$users = $this->Users->find('list')->where(['report_to IN'=> $user_ids])->orWhere(['report_to'=>$userId]);
        }else  if ($userRoles->hasRole(['Admin'])) {
            $users = $this->Users->find('list')->where(['report_to'=>$userId]);
        }
        if($userSelected){
            $user = $this->Users->find()->where(['id' => $userSelected])->first();
        }
        $departments = $this->Organizations->find('list');
		$staff_absent = 2;
		$staff_working = 10;
		$notifications = 3;
		$staff_timeoff = 1;
        $this->set(compact('user', 'userRoles','departments','users','departmentSelected','userSelected','staff_absent','staff_working','notifications','staff_timeoff'));
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
}
