<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

/**
 * Tests Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class TestsController extends AppController {

    public $helpers = ['Tools.AuthUser'];

    function index() {
        
    }

    function auth() {
        //$this->loadHelper('Tools.AuthUser');
        // Read the id of the logged in user as shortcut method (Auth.User.id)
        $uid = $this->AuthUser->id();



// Get the username (Auth.User.username)
        $username = $this->AuthUser->user('email');

// Check for a specific role
        $roles = $this->AuthUser->roles();

// Check for a specific role
//$hasRole = $this->AuthUser->hasRole('admin');  
        $hasRole = $this->AuthUser->hasRole('Super Admin');

        $this->set('data', $hasRole);
        //$this->set('data', $this->AuthUser);
    }

    function q1() {

        $em = TableRegistry::get('Subscriptions');

// Start a new query.
        $subscriptions = $em->find()
                ->where(['start >' => '2015',
                //'end <=' => '2016'
        ]);


        $this->set('subscriptions', $subscriptions);
        // $this->set('_serialize', ['subscriptions']);
    }

    function q2() {

        // using raw query
        $connection = ConnectionManager::get('default');
        $results = $connection->execute('SELECT * FROM users')->fetchAll('obj'); // obj or assoc

        $this->set('results', $results);
    }

    function q3() {
        $this->Devices = TableRegistry::get('Devices');

        $results = $this->Devices->find()->asArray();
        $this->set('results', $results);
    }
    
    function get_connection(){
        $this->Devices = TableRegistry::get('Devices');
        
        $connection = $this->Devices->connection();
        
        $connection->query('CREATE DATABASE i3d_v3_12345');
        
        $this->set('results', $connection);
        
        $this->render("q3");
    }
	
	    function get_master_total_reseller(){
		
			$this->loadModel('Clients');
			
			$total = $this->Clients->find()->contain(['ClientSubscriptions'=>function ($q) {
    return $q
        ->order(['end'=>'DESC'])->limit(1);
},'ClientSubscriptions.Packages'])->where(['Clients.code'=>'0179'])->first();
		
     debug($total);
        
        $this->set('results', $connection);
        
        $this->render("q3");
    }

}
