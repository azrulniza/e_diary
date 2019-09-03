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
          
        }else  if ($userRoles->hasRole(['Supervisor'])) {
          
        }else  if ($userRoles->hasRole(['Admin'])) {
            
        }else{
            
        }
        if($userSelected){
            $user = $this->Users->find()->where(['id' => $userSelected])->first();
        }
        $users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
        $this->set(compact('user', 'userRoles','departments','users','departmentSelected','userSelected'));
        $this->set('_serialize', ['dashboard']);
    }
    
    public function getUsers()
    {
        $this->loadModel('Users');
        $department_id = $_GET['id'];
        
        $users = $this->Users->find()->contain(['Organizations']);
        $users->matching('Organizations', function ($q) use ($department_id){
                                            return $q->where(['Organizations.id ' => $department_id]);
                                    });
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
        $this->viewBuilder()->layout('ajax');
    }
}
