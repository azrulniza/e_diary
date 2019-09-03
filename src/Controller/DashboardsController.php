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
        

        $userId = $this->AuthUser->id();



        //get user details
        //$user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();


        //$userRoles = $this->Users->Roles->initRolesChecker($user->roles);


        $this->set(compact('user', 'userRoles'));
        $this->set('_serialize', ['dashboard']);
    }
}
