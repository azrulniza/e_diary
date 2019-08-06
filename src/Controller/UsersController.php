<?php

namespace App\Controller;

use Cake\Mailer\Email;
use Cake\Utility\Hash;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\I18n\I18n;
use Cake\Routing\Router;
use Cake\Core\Configure;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    private $userStatus;

    public function __construct(\Cake\Network\Request $request = null, \Cake\Network\Response $response = null, $name = null, $eventManager = null, $components = null)
    {
        parent::__construct($request, $response, $name, $eventManager, $components);

        $this->userStatus = [
            1 => __('Active'),
            0 => __('Disabled'),
            2 => __('Pending activation'),
            3 => __('Pending update profile'),
            4 => __('Password reset requested')];
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {

        $currentUser = $this->Users->find()->where(['Users.id' => $this->Auth->user()['id']])->first();

        $query = $this->Users->find()->order(['Users.id' => 'DESC']);

        /**
         * Super admin can see all
         */
        if ($this->AuthUser->hasRole(SUPER_ADMIN)) {

            // no filter required
        } else if ($this->AuthUser->hasRole(SYSTEM_ADMIN)) {


            //  debug("is sys");


            $query->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [SYSTEM_ADMIN, RESELLER, CLIENT], 'Users.id !=' => 1]);
            });

        }
        /**
         * Reseller should see his reseller members and it's CLIENTS
         * note that reseller->clients is different than user->clients
         */ else if ($this->AuthUser->hasRole(RESELLER)) {


            // debug("is reseller");
			debug($this->Auth->user());
            $currentUser = $this->Users->find()
                ->where(['Users.id' => $this->Auth->user()['id']])
                ->first();


            $this->set('_serialize', ['users']);
            $this->render('list_by_reseller_client');
        } else if ($this->AuthUser->hasRole(MASTER_RESELLER)) {

            $currentUser = $this->Users->find()
                ->where(['Users.id' => $this->Auth->user()['id']])
                ->first();

            $this->set('_serialize', ['users']);
            $this->render('list_by_reseller_client');
        }
		/**
         * Role CLIENT will see members of same client only.
         */ else if ($this->AuthUser->hasRole(CLIENT)) {

            $currentUser = $this->Users->find()
                ->where(['Users.id' => $this->Auth->user()['id']])
                ->first();

            $query->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [CLIENT]]);
            });
        }

        $users = $this->paginate($query);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * Signup method
     *
     * @return void Redirects on successful signup, renders view otherwise.
     */
    public function signup()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The signup has been processed.'));
                $this->Auth->setUser($user->toArray());
                return $this->redirect('/');
            } else {
                $this->Flash->error(__('The signup could not be processed. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
        $this->viewBuilder()->layout('public');
    }

    /**
     * Login method
     *
     * @return void Redirects on successful login, renders view otherwise.
     */
    public function login()
    {
		
		/* $this->loadModel('MasterResellerSubscriptions');
		$this->loadModel('ResellerSubscriptions');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                // load roles 
				
               $currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.id' => $user['id']])->first();
			  
               if($currentUser['roles'][0]['id'] != 1 AND $currentUser['roles'][0]['id'] != 2)
			   {
					$userResellers = $this->Users->find()->contain(['Resellers'])->where(['Users.id' => $user['id']]);
				
					foreach($userResellers as $key => $reseller)
					{	
						if ($reseller['resellers'][0]['parent_id'] == 0 )
						{
							$masterResellerSubscriptions = $this->MasterResellerSubscriptions->find()->where(['reseller_id' => $reseller['resellers'][0]['id']]);

							foreach($masterResellerSubscriptions as $masterreseller)
							{
								$currDateTime = time("Y-m-d");
								$date = strtotime($masterreseller->end);
								$datediff = $currDateTime - $date;
								$datediff = floor($datediff/(60*60*24));
							}
						}else
						{
							$resellerSubscriptions = $this->ResellerSubscriptions->find()->where(['reseller_id' => $reseller['resellers'][0]['id']]);

							foreach($resellerSubscriptions as $reseller)
							{
								$currDateTime = time("Y-m-d");
								$date = strtotime($reseller->end);
								$datediff = $currDateTime - $date;
								$datediff = floor($datediff/(60*60*24));
							}
						}
						
					}if(isset($datediff)){
						if($datediff > -8 && $datediff <= 0){
							 $this->Flash->error(__('Pending renew (7 days)'));
						}
						else if($datediff >-31 && $datediff <= 0){
							 $this->Flash->error(__('Pending renew (30 days)'));
						}
						else if($datediff < -30){
							 $this->Flash->error(__('Active'));
						}
						else
						{
							 $this->redirect($this->here);
							 $this->Flash->error(__('Subscription has ended'));
							 
						}
					}
			   }
                $user['Roles'] = $currentUser->roles;
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(
                    __('Email or password is incorrect'), 'default', [], 'auth'
                );
            }
        }
        $this->viewBuilder()->layout('public'); */
		$this->loadComponent('Captcha.Captcha');
		
		if ($this->request->is('post')) {
			$this->Users->setCaptcha('<captcha>', $this->Captcha->getCode('<captcha>'));
			if($this->request->data['<captcha>'] == $this->Captcha->getCode('<captcha>'))
			{
				$user = $this->Auth->identify();
				if ($user) {
					// load roles
					$this->loadModel('Users');
					$this->loadModel('Roles');
					$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.id' => $user['id']])->first();

					$user['Roles'] = $currentUser->roles;
					//debug($user);die();
					$this->Auth->setUser($user);
					
					
					$email = $this->request->query('email');
					$key = $this->request->query('key');
					$code = $this->request->query('code');
					
					if($email!=null && $code!=null){
						$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.email' => $email])->first();
				
						$key_generate=$this->Users->generateRandomString();
						$user_id=$currentUser->id;
						$user_email=$currentUser->email;
						$user_name=$currentUser->name;
						
						$users = $this->Users->find()->where(['id' => $user_id])->first();
						
						$this->Users->validator()->remove('roles');
						$this->Users->patchEntity($users, $this->request->data);

						$users->kms_key = $key_generate;

						if ($this->Users->save($users)) {
							
							$success = true;
							
							\Cake\Core\Configure::load('clientcms', 'default');
							$client_path = Configure::read('cms.cms_url').$code;
							//$client_path = 'http://fmt.i3display.com/staging/i3D_CMS_v12';
							
							$url = $client_path. '/api/kmskey.php?key=' . $key_generate."&email=".$user_email;
							$result = json_decode(file_get_contents($url));
							//$this->Flash->success(__('New password accepted. Please sign in using new password.'));
							
							$users = $this->Auth->identify();
							$users['Roles'] = $currentUser->roles;
							$this->Auth->setUser($users);
							return $this->redirect($this->Auth->redirectUrl());
						} else {
							$this->redirect('/users/login?');
						}
										
					}
		
					return $this->redirect($this->Auth->redirectUrl());
				} else {
					$this->Flash->error(
						__('Email or password is incorrect'), 'default', [], 'auth'
					);
				}
			}else{
				$this->Flash->error(
						__('Captcha Validation False')
					);
			}
            
        }
        $this->viewBuilder()->layout('public');
    }

	public function autologin()
    {
		$email = $this->request->query('email');
		$key = $this->request->query('key');
		$code = $this->request->query('code');
		if ($code!=null && $email!=null && $key!=null) {
			//$this->loadModel('Users');
			//$this->loadModel('Roles');
			$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.kms_key' => $key])->where(['Users.email' => $email])->first();
			$user_id=$currentUser->id;
			$user_email=$currentUser->email;
			$user_name=$currentUser->name;
			
			
			if($currentUser){
				$user = $this->Auth->identify();
				
				$user['Roles'] = $currentUser->roles;
				
				$user['id'] = $currentUser->id;
				$user['email'] = $currentUser->email;
				$user['name'] = $currentUser->name;
				$user['status'] = $currentUser->status;
				
				$this->Auth->setUser($user);
				
				return $this->redirect($this->Auth->redirectUrl());
			}else{
				$this->redirect('/users/login');
			}
			
			
        }else if($code!=null && $email!=null && $key==null){ //first time auto login from CMP
			
			$this->redirect('/users/login?email='.$email.'&code='.$code);
		
		}else{
			$this->Flash->error(
					__('Failed to auto login'), 'default', [], 'auth'
				);
		}
		
        $this->viewBuilder()->layout('public');
    }
    /**
     * Logout method
     *
     * @return void Redirects
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
	
    {
		$this->userStatus = [
            1 => __('Active'),
            0 => __('Disabled'),
            2 => __('Pending activation'),
            3 => __('Pending update profile'),
            4 => __('Password reset requested')];
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            $now = \Cake\I18n\Time::now();
            $user->date_created = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $user->date_modified = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
       if ($this->AuthUser->hasRole(SUPER_ADMIN) OR $this->AuthUser->hasRole(SYSTEM_ADMIN)) {
			$roles = $this->Users->Roles->find('list', ['limit' => 200]);

		}else if($this->AuthUser->hasRole($this->AuthUser->hasRole(MASTER_RESELLER))){
			//roles
			$role=array(1,2,3,8);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);

		}else if($this->AuthUser->hasRole(RESELLER)){
			//roles
			$role=array(1,2,3,4);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);


		}else if ($this->AuthUser->hasRole(CLIENT)) {

            // only client
            $roles = $this->Users->Roles->find('list')->where(['id IN' => [CLIENT]]);

        }
		$departments = $this->Users->Departments->find('list', ['limit' => 200]);
        $userStatus = $this->userStatus;
        $this->set(compact('user', 'roles', 'userStatus','departments'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {

		$this->loadModel('Clients');
        try {
            $user = $this->Users->get($id, [
                'contain' => ['Roles', 'Resellers', 'Clients']
            ]);
        } catch (Cake\Datasource\Exception\RecordNotFoundException $exception) {
            $message = __('User not found');
            $this->set(compact('message'));
            $this->render('/Element/Flash/error');
        }

        // only allow if current user have access to edit entity
        /* if (!$this->AuthUser->hasRole(SUPER_ADMIN) OR ! $this->AuthUser->hasRole(SYSTEM_ADMIN)) {
          $currentUser = $this->Users->find()
          ->contain('Roles')
          ->where(['Users.id' => $this->Auth->user()['id']])->first();


          $roles = Hash::extract($currentUser->roles, '{n}.id');

          //debug($roles);
          $pass = false;
          foreach ($user->roles as $role) {
          if (in_array($role->id, $roles)) {
          // ok: pass
          $pass = true;
          break;
          }
          }

          if (!$pass) {
          $message = __('Your login account did not have privilege to edit this user account.');
          $this->set(compact('message'));
          $this->render('/Element/Flash/error');
          }
          } */

        if ($this->request->is(['patch', 'post', 'put'])) {


            if($this->request->data['new_password']){
                $this->request->data['password']  = $this->request->data['new_password'];
            }
            $user = $this->Users->patchEntity($user, $this->request->data);
			$now = \Cake\I18n\Time::now();
            $user->date_modified = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
				if($this->AuthUser->hasRole(SYSTEM_ADMIN) OR $this->AuthUser->hasRole(SUPER_ADMIN)){
					return $this->redirect(['action' => 'index']);
				}else{
					return $this->redirect(['controller' => 'Dashboards']);
				}
				
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
		$roles = $this->Users->Roles->find('list');
		$resellers = $this->Users->Resellers->find('list');
		//$clients = $this->Users->Clients->find('list');
        /* $currentUserId=  6;
          $clients->matching('Users', function ($q) use ($currentUserId){
          return $q->where(['Users.id' => $currentUserId]);
          });
         */

               if ($this->AuthUser->hasRole(SUPER_ADMIN)) {
            // no filter required
			$clients = $this->Users->Clients->find('list');

        } else if ($this->AuthUser->hasRole(SYSTEM_ADMIN)) {
            // except super admin
           // $roles = $this->Users->Roles->find('list')->where(['id >' => SUPER_ADMIN]);
		    // except super admin, master reseller
			$role=array(1,2,3);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			$resellers = $this->Users->Resellers->find('list');
			$clients = $this->Users->Clients->find('list');
        } else if ($this->AuthUser->hasRole(MASTER_RESELLER)) {

            // except super admin, master reseller
			$role=array(1,2);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);

            // only to what this accout has access to
            $currentUserId = $this->Auth->user()['id'];

            // first step, list reseller belong to users
            $resellers->matching('Users', function ($q) use ($currentUserId) {
                return $q->where(['Users.id' => $currentUserId]);
            });

            // second step, union with reseller belong to above
            $resellers->union($this->Users->Resellers->find('list')
                    ->where(['Resellers.parent_id IN' => function() use ($resellers) {
                            return array_keys($resellers->toArray());
                        }]));
			$clints = $this->Users->Clients->find()->contain(['Users']);

            $clints->matching('Users', function ($q) use ($id) {
                return $q->where(['Users.id' => $id]);
            });

        } else if ($this->AuthUser->hasRole(RESELLER)) {

            // except super admin
            //$roles = $this->Users->Roles->find('list')->where(['id IN' => [CLIENT]]);
			$role=array(1,2,3);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
            // only to what this accout has access to

            $currentUserId = $this->Auth->user()['id'];

            // first step, list reseller belong to users
            $resellers->matching('Users', function ($q) use ($currentUserId) {
                return $q->where(['Users.id' => $currentUserId]);
            });

            // second step, union with reseller belong to above
            $resellers->union($this->Users->Resellers->find('list')
                    ->where(['Resellers.parent_id IN' => function() use ($resellers) {
                            return array_keys($resellers->toArray());
                        }]));
			$clints = $this->Clients->find()->contain(['Users']);
            $clints->matching('Users', function ($q) use ($id) {
                return $q->where(['Users.id' => $id]);
            });
        }else if ($this->AuthUser->hasRole(CLIENT)) {

            // only client
            $roles = $this->Users->Roles->find('list')->where(['id IN' => [CLIENT]]);

            // only to what this accout has access to
            $resellers->matching('Users', function ($q) use ($currentUserId) {
                return $q->where(['Users.id' => $currentUserId]);
            });

            $clints->matching('Users', function ($q) use ($currentUserId) {
                return $q->where(['Users.id' => $currentUserId]);
            });
        }
		$userClient = TableRegistry::get('users_clients');
		$userClient = $userClient->find()->where(['user_id'=>$id])->toArray();
		
		$selected_user = [];
		foreach($userClient as $userselected ){
			$selected_user[$userselected->client_id] = true;
		}
		foreach($clints as $client)
		{
			$clients[$client->id] = $client->name;
		}
		$admin_user_level = $this->Users->UsersRoles->find()->select(['role_id'])->where(['user_id' => $this->AuthUser->id()])->first();
		$user_level = $admin_user_level->role_id;
        $userStatus = $this->userStatus;
        $this->set(compact('clints','selected_user','user', 'roles', 'resellers', 'clients', 'userStatus', 'user_level'));
        $this->set('_serialize', ['dashboard']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function master_resellers()
    {
        if ($this->AuthUser->hasRole(SUPER_ADMIN) OR $this->AuthUser->hasRole(SYSTEM_ADMIN)) {
            $query = \Cake\ORM\TableRegistry::get('Resellers')->find()
                ->contain(['Users', 'DownlineResellers.Users', 'DownlineResellers.Clients.Users'])
                //  ->contain(['Users'])
                ->where(['Resellers.parent_id' => 0]);

            $this->set('resellers', $query);
        } else {
            $this->Flash->error(__('Your login account did not have the access to this module.'));
        }
    }

    public function resellers()
    {
        /**
         * Admin can see all reseller that not master reseller
         */
        if ($this->AuthUser->hasRole(SUPER_ADMIN) OR $this->AuthUser->hasRole(SYSTEM_ADMIN)) {
            $resellers = \Cake\ORM\TableRegistry::get('Resellers')->find()
                ->contain(['Users', 'DownlineResellers.Users'])
                ->where(['Resellers.parent_id >' => 0]);
        }
        /**
         * Reseller can see
         */ else if ($this->AuthUser->hasRole(RESELLER)) {
            $currentUser = $this->Users->find()
                ->contain(['Resellers.Users', 'Resellers.DownlineResellers.Users'])
                ->where(['Users.id' => $this->Auth->user()['id']])
                //->where(['Users.id'=>2])
                ->first();
            $resellers = $currentUser->resellers;
        }
		/**
         * Master Reseller can see
         */ else if ($this->AuthUser->hasRole(MASTER_RESELLER)) {
            $currentUser = $this->Users->find()
                ->contain(['Resellers.Users', 'Resellers.DownlineResellers.Users'])
                ->where(['Users.id' => $this->Auth->user()['id']])
                //->where(['Users.id'=>2])
                ->first();

            $resellers = $currentUser->resellers;
        }

        //$users = $this->paginate($query);

        $this->set(compact('resellers'));
        $this->set('_serialize', ['users']);
        $this->render('resellers');
    }

    public function reset_password($lang)
    {
		
        $success = false;
        $user = $this->Users->newEntity();
		
		$langs = \Cake\Core\Configure::read('Languages');
		$session = $this->request->session();
		if(isset($langs[$lang])){
            $session->write('Config.language', $lang);
            I18n::locale($lang);
			//$this->redirect('/users/verify_reset?switch_to='.$lang.'&user_id='.$user_id);
        }
        if($lang == 'en'){
			$language_id=1;
		}else if($lang == 'cn_CN'){
			$language_id=2;
		}else if($lang == 'cn_ZH'){
			$language_id=3;
		}else{
			$language_id=1;
		}
		
        if ($this->request->is('post')) {
            $user = $this->Users->find()->contain(['Resellers.UplineResellers.Users'])->where(['email' => $this->request->data['email']])->first();
            if ($user != null) {	
				$company_name = $user->resellers[0]->upline_reseller->company_name;
				$user_name = $user->resellers[0]->upline_reseller->users[0]->name;
				$success = true;
				$arr_verify = array( 'user_id' => $user->id, 'language_id' => $language_id);
				$data = json_encode($arr_verify);
				$verify_url = Router::url('/', true).'users/verify_reset?param='.base64_encode($data);
				if($language_id == 1){
					$subject = __('Password Recreation');
					if($oem->m3tech_status==1){//m3tech
						$message = __('<p>Dear {0},</p>'
						. '<p>We have sent you this email in response to your request to reset your password associated to this email address at '.$oem->name .' Keycode Management System.</p>'
						. '<p>To reset your password for '.$oem->name .' Keycode Management System, please click below URL:<br><a href="{1}">{1}</a></p>'
						. '<p>We recommend that you keep your password secure and not share it with anyone. If you feel your password has been compromised, you can change it at your account page and click for "Change Password".</p>'
						. '<p>If it is not you who has requested, please report to info@i3display.com.</p>'
						. '<p>Help is a click away, you can access the knowledge based here:<br><a href="https://support.i3display.com/ ">https://support.i3display.com/ </a></p>'
						. '<p>Or Contact your supplier stated below:</p>'
						. '<p>Mr/Ms {2}<br>{3}</p>'
						. '<p>Thank you.</p><p>Best Regards,<br>'.$oem->name .' Keycode Management.</p>', $user->name, $verify_url, $user_name, $company_name);
					}else{//oem
						$message = __('<p>Dear {0},</p>'
						. '<p>We have sent you this email in response to your request to reset your password associated to this email address at '.$oem->name .' Keycode Management System.</p>'
						. '<p>To reset your password for '.$oem->name .' Keycode Management System, please click below URL:<br><a href="{1}">{1}</a></p>'
						. '<p>We recommend that you keep your password secure and not share it with anyone. If you feel your password has been compromised, you can change it at your account page and click for "Change Password".</p>'
						. '<p>Contact your supplier stated below:</p>'
						. '<p>Mr/Ms {2}<br>{3}</p>'
						. '<p>Thank you.</p><p>Best Regards,<br>'.$oem->name .' Keycode Management.</p>', $user->name, $verify_url, $user_name, $company_name);
					}
					
				}else if($language_id == 2){
					$subject = __('密码重置');
					if($oem->m3tech_status==1){//m3tech
						$message = __('<p>亲爱的 {0},</p>'
						. '<p>在'.$oem->name .' Keycode管理系统中，我们将此邮件发送给您，以确认您要求重置与此邮件地址相关的密码的请求。</p>'
						. '<p>重置你的密码'.$oem->name .'键码管理系统,请点击下面的URL:<br><a href="{1}">{1}</a></p>'
						. '<p>我们建议您保持密码安全，不要与任何人共享。如果你觉得你的密码被泄露了，你可以在你的账户页面上修改密码，点击“修改密码”。</p>'
						. '<p>如果不是您请求的，请向info@i3display.com报告。</p>'
						. '<p>一个点击可获得帮助,您可以访问这里的知识库:<br><a href="https://support.i3display.com/">https://support.i3display.com/ </a></p>'
						. '<p>或者联系你的供应商声明如下:</p>'
						. '<p>女士/先生 {2}<br>{3}</p>'
						. '<p>谢谢。</p><p>致以最亲切的问候,<br>'.$oem->name .'序列号管理。</p>', $user->name, $verify_url, $user_name, $company_name);
					}else{//oem
						$message = __('<p>亲爱的 {0},</p>'
						. '<p>在'.$oem->name .' Keycode管理系统中，我们将此邮件发送给您，以确认您要求重置与此邮件地址相关的密码的请求。</p>'
						. '<p>重置你的密码'.$oem->name .'键码管理系统,请点击下面的URL:<br><a href="{1}">{1}</a></p>'
						. '<p>我们建议您保持密码安全，不要与任何人共享。如果你觉得你的密码被泄露了，你可以在你的账户页面上修改密码，点击“修改密码”。</p>'
						. '<p>或者联系你的供应商声明如下:</p>'
						. '<p>女士/先生 {2}<br>{3}</p>'
						. '<p>谢谢。</p><p>致以最亲切的问候,<br>'.$oem->name .'序列号管理。</p>', $user->name, $verify_url, $user_name, $company_name);
					}
					
				}else if($language_id == 3){
					$subject = __('密碼重置');
					if($oem->m3tech_status==1){//m3tech
						$message = __('<p>親愛的 {0},</p>'
						. '<p>在'.$oem->name .' Keycode管理系統中，我們將此郵件發送給您，以確認您要求重置與此郵件地址相關的密碼的請求。</p>'
						. '<p>重置妳的密碼'.$oem->name .'鍵碼管理系統,請點擊下面的URL:<br><a href="{1}">{1}</a></p>'
						. '<p>我們建議您保持密碼安全，不要與任何人共享。如果妳覺得妳的密碼被泄露了，妳可以在妳的賬戶頁面上修改密碼，點擊“修改密碼”。</p>'
						. '<p>如果不是您請求的，請向info@i3display.com報告。</p>'
						. '<p>壹個點擊可獲得幫助,您可以訪問這裏的知識庫:<br><a href="https://support.i3display.com/ ">https://support.i3display.com/ </a></p>'
						. '<p>或者聯系妳的供應商聲明如下:</p>'
						. '<p>女士/先生 {2}<br>{3}</p>'
						. '<p>謝謝。</p><p>致以最親切的問候,<br>'.$oem->name .'序列號管理。</p>', $user->name, $verify_url, $user_name, $company_name);
					}else{//oem
						$message = __('<p>親愛的 {0},</p>'
						. '<p>在'.$oem->name .' Keycode管理系統中，我們將此郵件發送給您，以確認您要求重置與此郵件地址相關的密碼的請求。</p>'
						. '<p>重置妳的密碼'.$oem->name .'鍵碼管理系統,請點擊下面的URL:<br><a href="{1}">{1}</a></p>'
						. '<p>我們建議您保持密碼安全，不要與任何人共享。如果妳覺得妳的密碼被泄露了，妳可以在妳的賬戶頁面上修改密碼，點擊“修改密碼”。</p>'
						. '<p>或者聯系妳的供應商聲明如下:</p>'
						. '<p>女士/先生 {2}<br>{3}</p>'
						. '<p>謝謝。</p><p>致以最親切的問候,<br>'.$oem->name .'序列號管理。</p>', $user->name, $verify_url, $user_name, $company_name);
					}
					
				}
				try {
					if($oem->m3tech_status==1){//m3tech
						$email = new Email();

						$email->transport('default');
						$email
							->emailFormat('html')
							->to($user->email, $user->name)
							->bcc(array('i3dsupport@i3display.com'))
							->subject($subject)
							->send($message);
						$this->Flash->success(__('Password reset instruction will be sent to e-mail address {0}.', [$user->email]));
					}else{//oem
						$email = new Email();

						$email->transport('default');
						$email
							->emailFormat('html')
							->to($user->email, $user->name)
							->bcc(array('i3dsupport@i3display.com', $oem->oem_email))
							->subject($subject)
							->send($message);
						$this->Flash->success(__('Password reset instruction will be sent to e-mail address {0}.', [$user->email]));
					}
					
				}catch(\Exception $e){
					$this->Flash->error(__('Email could not send. Please, try again.'));
					return $this->redirect(['action' => 'reset_password']);
				}
				
            }
        }
        $this->set(compact('user', 'success', 'oem'));
        $this->set('_serialize', ['user']);
        $this->viewBuilder()->layout('public_reset');
    }

    public function confirm_reset_password($email = '', $code = '')
    {
        $keyError = false;
        $success = false;
        $user = $this->Users->find()->where(['email' => $email])->first();
        if ($user != null AND $user->reset_password_key == $code) {

            if ($user != null) {

                if ($this->request->is(['post','put'])) {

                    $this->Users->validator()->remove('roles');
                    $this->Users->patchEntity($user, $this->request->data);

                    $user->reset_password_key = '';

                    if ($this->Users->save($user)) {
                        $success = true;
                        $this->Flash->success(__('New password accepted. Please sign in using new password.'));

                    } else {
                        debug($user->errors());
                    }
                }
            } else {
                $this->Flash->error(__('Reset password cannot be processed. Please, try again later.'));
            }
        } else {
            $keyError = true;
            $this->Flash->error(__('Invalid reset password token received. Please request new reset password.'));
        }
        $this->set(compact('user', 'success', 'keyError'));
        $this->set('_serialize', ['user']);
        $this->viewBuilder()->layout('public');
    }
	
	public function verify()
    {
		$this->loadModel('Resellers');
		
		$data = $this->request->query('param');
		$data = base64_decode($data);	
		$data = json_decode($data);
		$language_id = $data->language_id;
		if($language_id == 1){
			$lang = 'en';
		}else if($language_id == 2){
			$lang = 'cn_CN';
		}else if($language_id == 3){
			$lang = 'cn_ZH';
		}
		$session = $this->request->session();
        
        $langs = \Cake\Core\Configure::read('Languages');
		
        if(isset($langs[$lang])){
            $session->write('Config.language', $lang);
            I18n::locale($lang);
			$this->redirect('/users/verify?switch_to='.$lang);
        }
        else{
            //@TODO correctly display error
            $this->viewBuilder()->layout('Element/Flash/error');
        }
		if ($this->request->is('post')) {
			$verify_code = $this->request->data['verify_code'];
			$lang =  $this->request->query('switch_to');
			if($this->Resellers->exists(['verification_code' => $verify_code])){
				$reseller = $this->Resellers->find()->contain(['Users'])->where(['verification_code'=>$verify_code])->first();
				if($reseller != null AND $reseller->verification_code == $verify_code AND $reseller->reseller_status_id == 2 AND is_numeric($verify_code))
				{ 
					if($this->request->data['password'] === $this->request->data['confirm_password'])
					{
						//update new password 
						$user=$reseller['users'][0];
						$user=$this->Users->get($user->id);
						$user->password = $this->request->data['password'];
						if ($this->Users->save($user)) {
							
								//update reseller status to 1
								$resellers = $this->Resellers->get($reseller->id); 
								$resellers->reseller_status_id = 1;
								$this->Resellers->save($resellers);
								
								//auto login
								$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.id' => $user->id])->first();
								$user = $user->toArray();
								$user['Roles'] = $currentUser->roles;
								$this->Auth->setUser($user);
								return $this->redirect($this->Auth->redirectUrl().'lang/switch_to/'.$lang);
							
							
						}else{
							$this->Flash->error(__('The code cannot be verified. Please, try again.'));
						}
					}else{
						$this->Flash->error(__('Password not match. Please, try again.'));
					}
				}else if ($reseller->reseller_status_id != 2){
					$this->Flash->error(__('This reseller has been verified.'));
				}
			}else{
				$this->Flash->error(__('Invalid code.'));
			}
			
		}
		$this->viewBuilder()->layout('verify');
    }
	
	public function verify_reset()
    {
		
		$data = $this->request->query('param');
		$data = base64_decode($data);	
		$data = json_decode($data);
		
		$language_id = $data->language_id;
		$user_id = $data->user_id;
		if($language_id == 1){
			$lang = 'en';
		}else if($language_id == 2){
			$lang = 'cn_CN';
		}else if($language_id == 3){
			$lang = 'cn_ZH';
		}
		$session = $this->request->session();
        
        $langs = \Cake\Core\Configure::read('Languages');
        
        if(isset($langs[$lang])){
            $session->write('Config.language', $lang);
            I18n::locale($lang);
			$this->redirect('/users/verify_reset?switch_to='.$lang.'&param1='.$this->request->query('param'));
        }
        else{
            //@TODO correctly display error
            $this->viewBuilder()->layout('Element/Flash/error');
        }
		if ($this->request->is('post')) {
			
			$data = $this->request->data['param1'];
			$data = base64_decode($data);	
			$data = json_decode($data);

			$user_id = $data->user_id;
			$lang =  $this->request->query('switch_to');
			if($this->Users->exists(['id' => $user_id])){
				if($this->request->data['password'] === $this->request->data['confirm_password'])
				{
					$user=$this->Users->get($user_id);
					$user->password = $this->request->data['password'];
					if ($this->Users->save($user)) {
						
						$session->write('Config.language', $lang);
						$this->Flash->success(__('Successfully reset password.'));
						return $this->redirect('/users/login');
						
					}else{
						$this->Flash->error(__('The user cannot be save. Please, try again.'));
					}
					
				}else{
					$this->Flash->error(__('Password not match. Please, try again.'));
				}
			}else{
				$this->Flash->error(__('Invalid user.'));
			}
		}
		$this->viewBuilder()->layout('verify_reset');
    }
}
