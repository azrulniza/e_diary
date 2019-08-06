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
        $this->loadModel('Resellers');
        $this->loadModel('Clients');
        $this->loadModel('DeviceLocations');
        $this->loadModel('Users');
        $this->loadModel('UsersResellers');
        $this->loadModel('ProductKeys');
        $this->loadModel('Transfers');
        $this->loadModel('TransferDetails');

        $userId = $this->AuthUser->id();



        //get user details
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();


        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);


        if ($userRoles->hasRole(['OEM', 'Reseller', 'Master Reseller'])) {
          
        }

        $this->set(compact('user', 'userRoles', 'reseller', 'product_keys_available', 
            'no_downline_reseller', 'reseller_keycode_std', 'reseller_keycode_array', 
            'tranfers_keycode_array', 'grand_total_transfer_keycode', 
            'no_cmp', 'reseller_clients', 'keycodes_by_client', 'oem_keycodes'));
        $this->set('_serialize', ['dashboard']);
    }
}
