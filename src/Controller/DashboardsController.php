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

        $userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		
		$departments = $this->Organizations->find('list');
        if ($userRoles->hasRole(['OEM', 'Reseller', 'Master Reseller'])) {
          
        }

        $this->set(compact('user', 'userRoles','departments'));
        $this->set('_serialize', ['dashboard']);
    }
	
	public function getUsersByDepartment()
	{
		$this->loadModel('Users');
        $department_id = $_GET['id'];
		
		$users = $this->Users->find('list')->contain('Organizations');

		$users->matching('UserOrganizations', function ($q){
			return $q->where(['organization_id'=> $department_id]);
		});
		var_dump($users->toArray());die();
	}
}
