<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\I18n\I18n;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    public $paginate = [
        'limit' => 10
    ];
    
    protected $appVars;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize() {
        parent::initialize();
        $this->appVars = new \stdClass();
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'email']
                ]
            ],
            'authorize' => [
                'ModifiedTiny' => [
                    'multiRole' => true,
                    'pivotTable' => 'users_roles'
                ]
            ]
        ]);
        $this->loadComponent('Tools.AuthUser');

        $this->set('form_templates', Configure::read('Templates'));
    }

    public function beforeFilter(Event $event){
        $this->Auth->allow(['autologin','verify_reset','verify','signup', 'reset_password', 'confirm_reset_password', 'verify_content_auto_login', 'switch_to','clear_cache']);
        
        if($this->request->session()->read('Config.language')){
            I18n::locale($this->request->session()->read('Config.language')); 
        }
        
        if($this->request->is('ajax')){
            $this->viewBuilder()->layout(false);
        }
        
    }

    public function beforeRender(Event $event) {
        if ($logged = $this->Auth->user()) {
            $this->set('menus', $this->__getMenus());
        }
        $this->set('logged', $logged);
        
        $this->appVars->basePath = \Cake\Routing\Router::url('/'); 
        $this->set('appVars', $this->appVars);
    }

    private function __getMenus() {
        $indexed = [];

        //$authorizer = $this->Auth->getAuthorize('TinyAuth.Tiny');
        $authorizer = $this->Auth->getAuthorize('ModifiedTiny');
        $roleIds = [];
        foreach($this->Auth->user()['Roles'] as $role){
            $roleIds[] = $role->id;
        }

        //debug();
        foreach (TableRegistry::get('Menus')->find()->order(['ordering' => 'ASC']) as $menu) {

            // $request = new \Cake\Network\Request;
            // $request->
            
            

            if ($authorizer->allowed($roleIds, $menu->controller, $menu->action)) {

                $indexed[$menu->parent_id][] = $menu;
            }
        }
        return $indexed;
    }

    private function ___getMainMenu() {
        $files = scandir('../src/Controller/');
        $ignore = [ '.', '..', 'Component', 'AppController.php', 'PagesController.php'];
        //$order = [ 'users' ];
        $orderlyMainMenu = [];
        $disorganisedMainMenu = [];
        foreach ($files as $file) {
            if (!in_array($file, $ignore)) {
                $controller = explode('Controller.php', $file)[0];
                $_controller = $this->fromCamelCase($controller); //mb_strtolower( $controller );
                $item = [
                    'active' => $controller == $this->request->controller,
                    'controller' => $_controller,
                    'label' => Inflector::humanize($controller),
                    'iconclass' => 'fa-' . Inflector::singularize($_controller),
                ];
                if (!empty($order) && in_array($_controller, $order)) {
                    $orderlyMainMenu[array_search($_controller, $order)] = $item;
                } else {
                    $disorganisedMainMenu[] = $item;
                }
            }
        }
        ksort($orderlyMainMenu);
        return array_merge($orderlyMainMenu, $disorganisedMainMenu);
    }

    function fromCamelCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

}
