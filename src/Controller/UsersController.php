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
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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
            4 => __('Password reset requested')];
    }
	
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
		$this->loadModel('Organizations');
		
		$search_name = $this->request->query('search');
		if($search_name){
			$search_name = trim($search_name," ");
		}
		$organizationSelected = $this->request->query('organization');
		$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.id' => $this->Auth->user()['id']])->limit(1)->first();
        
		//get roles
		$user = $this->Users->find()->contain(['Roles'])->Where(['id' => $this->Auth->user()['id']])->limit(1)->first();
		$userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		if ($this->AuthUser->hasRole(MASTER_ADMIN)) {
			if($organizationSelected != null){
				$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($organizationSelected){
								return $q->where(['UserOrganizations.organization_id'=>$organizationSelected]);
						})
				->autoFields(true);
			}else{
				$query = $this->Users->find()->order(['Users.name' => 'ASC'])->contain('Roles')->where(['Users.status'=>1]);
			}
        }else if ($this->AuthUser->hasRole(SUPERVISOR)) {
			$query = $this->Users->find()->order(['Users.id' => 'DESC'])->contain('Roles');
            $query->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [STAFF,ADMIN], 'Users.id !=' => 1]);
            });

        }else if ($this->AuthUser->hasRole(ADMIN)) {
			$query = $this->Users->find()->order(['Users.id' => 'DESC'])->contain('Roles');
			$query->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [STAFF], 'Users.id !=' => 1]);
            });
        }else if ($this->AuthUser->hasRole(STAFF)) {
			$query = $this->Users->find()->order(['Users.id' => 'DESC'])->contain('Roles')->where(['id'=>$currentUser->id,'Users.status'=>1]);
		}
		foreach($query as $user){
			$heads = $this->Users->find()->where(['id'=> $user->report_to]);
			foreach($heads as $head){
				$reportTo[$head->id] = $head->name;
			}
		}
		$organizations = $this->Organizations->find('list', ['limit' => 200]);
        $users = $this->paginate($query);
        $this->set(compact('users','reportTo','organizations','organizationSelected','userRoles'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->userStatus = [
            1 => __('Active'),
            0 => __('Disabled')];
		$this->loadModel('UserOrganizations');
		$this->loadModel('UserDesignations');
		$this->loadModel('Organizations');
		$this->loadModel('Designations');
		$userId = $this->AuthUser->id();
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
			$now = \Cake\I18n\Time::now();
			if(!empty($this->request->data['image']['tmp_name'])){
				$fileName = $this->request->data['image']['name'];
				$str_date = $now->i18nFormat('yyMMdd');
				$fileName = $str_date.'_'.rand(10000,1000000).'_'.$fileName;
				$uploadPath = '/files/staffs/';
				$path = WWW_ROOT . $uploadPath;
				if (!file_exists($path)) {
					$oldMask = umask(0);
					mkdir($path, 0755, true);
					chmod($path, 0755);
					umask($oldMask);
				}
				$uploadFile = WWW_ROOT . $uploadPath.$fileName;
				$imageFileType = strtolower(pathinfo($uploadFile,PATHINFO_EXTENSION));
				if($imageFileType=="jpg" OR $imageFileType=="png" OR $imageFileType=="jpeg"){
					if($this->request->data['image']['size'] < 1048576){
						if(move_uploaded_file($this->request->data['image']['tmp_name'],$uploadFile)){
						
						}else{
							$this->Flash->error(__('Unable to upload file, please try again.'));
						}
					}else{
						$this->Flash->error(__('Exceeds file limit.Please upload image less than 1MB'));
					}
				}else{
					$this->Flash->error(__('Unable to upload file, JPG, JPEG & PNG file only allowed.'));
				}
				$user->image = $uploadPath.$fileName;
			}/* else{
				$this->Flash->error(__('Image is required. Please, try again.'));
			} */
				
            if ($this->Users->save($user)) {
				$user_id = $this->Users->save($user)->id;
				$userDept = $this->UserOrganizations->newEntity();
				$userDept->user_id = $user_id;
				$userDept->organization_id = $_POST['department'];
				$userDept->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$userDept->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$this->UserOrganizations->save($userDept);
				
				$userDesg = $this->UserDesignations->newEntity();
				$userDesg->user_id = $user_id;
				$userDesg->designation_id = $_POST['designation'];
				$userDesg->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$userDesg->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$this->UserDesignations->save($userDesg);
				
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
		if ($this->AuthUser->hasRole(MASTER_ADMIN) ) {
			$roles = $this->Users->Roles->find('list', ['limit' => 200]);
			$reportTo = $this->Users->find('list');/* ->contain('Roles');
			$reportTo->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [SUPERVISOR]]);
            }) */
			$designations = $this->Designations->find('list', ['limit' => 200]);
			$organizations = $this->Organizations->find('list', ['limit' => 200]);
		}else if($this->AuthUser->hasRole($this->AuthUser->hasRole(SUPERVISOR))){
			//roles
			$role=array(1);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			$organization = $this->UserOrganizations->find()->where(['user_id'=>$userId])->first()->organization_id;
	        
			$designations = $this->Designations->find('list', ['limit' => 200]);
			$organizations = $this->Organizations->find('list', ['limit' => 200])->where(['id'=>$organization]);
		}else if($this->AuthUser->hasRole(ADMIN)){
			//roles
			$role=array(1,2,3);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			$reportTo = $this->Users->find('list')->contain('Roles');
			$reportTo->matching('Roles', function ($q) {
					return $q->where(['Roles.id IN' => [STAFF]]);
				});

		}
		$userStatus = $this->userStatus;
        $this->set(compact('user', 'organizations','designations', 'userStatus', 'reportTo', 'roles'));
		$this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->loadModel('UserOrganizations');
		$this->loadModel('Organizations');
		$this->loadModel('Designations');
		$this->loadModel('UserDesignations');
        $user = $this->Users->get($id, [
            'contain' => ['UserDesignations', 'UserOrganizations', 'Roles']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
			 if($this->request->data['new_password']){
                $this->request->data['password']  = $this->request->data['new_password'];
            }
            $user = $this->Users->patchEntity($user, $this->request->getData());
			$now = \Cake\I18n\Time::now();
			if(!empty($this->request->data['image']['tmp_name'])){
				$fileName = $this->request->data['image']['name'];
				$str_date = $now->i18nFormat('yyMMdd');
				$fileName = $str_date.'_'.rand(10000,1000000).'_'.$fileName;
				$uploadPath = '/files/staffs/';
				$path = WWW_ROOT . $uploadPath;
				if (!file_exists($path)) {
					$oldMask = umask(0);
					mkdir($path, 0755, true);
					chmod($path, 0755);
					umask($oldMask);
				}
				$uploadFile = WWW_ROOT . $uploadPath.$fileName;
				$imageFileType = strtolower(pathinfo($uploadFile,PATHINFO_EXTENSION));
				if($imageFileType=="jpg" OR $imageFileType=="png" OR $imageFileType=="jpeg"){
					if($this->request->data['image']['size'] < 1048576){
						if(move_uploaded_file($this->request->data['image']['tmp_name'],$uploadFile)){
							$user->image = $uploadPath.$fileName;
						}else{
							$this->Flash->error(__('Unable to upload file, please try again.'));
						}
					}else{
						$this->Flash->error(__('Exceeds file limit.Please upload image less than 1MB'));
					}
				}else{
					$this->Flash->error(__('Unable to upload file, JPG, JPEG & PNG file only allowed.'));
				}
			}else{
				$previous_image = $this->request->data['previous_image'];
				$user->image = $previous_image;			
			}
            if ($this->Users->save($user)) {
				$now = \Cake\I18n\Time::now();
				$chk_org = $this->UserOrganizations->find()->where(['user_id'=>$id])->first()->user_id;
				if($chk_org){
					$query = $this->UserOrganizations->query();
					$query->update()
						->set(['organization_id' => $_POST['organization']])
						->where(['user_id' => $id])
						->execute();
				}else{
					$userDept = $this->UserOrganizations->newEntity();
					$userDept->user_id = $id;
					$userDept->organization_id = $_POST['department'];
					$userDept->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
					$userDept->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
					$this->UserOrganizations->save($userDept);
				}
				$chk_desg = $this->UserDesignations->find()->where(['user_id'=>$id])->first()->user_id;
				if($chk_desg){
					$query = $this->UserDesignations->query();
					$query->update()
						->set(['designation_id' => $_POST['designation']])
						->where(['user_id' => $id])
						->execute();
				}else{
					$userDesg = $this->UserDesignations->newEntity();
					$userDesg->user_id = $id;
					$userDesg->designation_id = $_POST['designation'];
					$userDesg->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
					$userDesg->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
					$this->UserDesignations->save($userDesg);
				}
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
		if ($this->AuthUser->hasRole(MASTER_ADMIN) ) {
			$roles = $this->Users->Roles->find('list', ['limit' => 200]);

		}else if($this->AuthUser->hasRole($this->AuthUser->hasRole(SUPERVISOR))){
			//roles
			$role=array(1);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);

		}else if($this->AuthUser->hasRole(ADMIN)){
			//roles
			$role=array(1,2,3);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);


		}
        $designations = $this->Designations->find('list', ['limit' => 200]);
        $organizations = $this->Organizations->find('list', ['limit' => 200]);
		$reportTo = $this->Users->find('list')->contain('Roles');
		$reportTo->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [SUPERVISOR]]);
            });
		$userStatus = $this->userStatus;
		$selected_dept = $this->UserOrganizations->find()->where(['user_id'=> $id])->first()->organization_id;
		$selected_designation = $this->UserDesignations->find()->where(['user_id'=> $id])->first()->designation_id;
        $this->set(compact('user', 'organizations','designations', 'roles', 'reportTo','userStatus','selected_dept','selected_designation'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        $user->status = 0;
		if ($this->Users->save($user)) {
			$this->Flash->success(__('The user has been delete.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
        return $this->redirect(['action' => 'index']);
    }
	
	 public function login()
    {
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
				
						$user_id=$currentUser->id;
						$user_email=$currentUser->email;
						$user_name=$currentUser->name;
						
						$users = $this->Users->find()->where(['id' => $user_id])->first();
						
						$this->Users->validator()->remove('roles');
						$this->Users->patchEntity($users, $this->request->data);


						if ($this->Users->save($users)) {
							
							$success = true;
							
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
	public function logout()
    {
        return $this->redirect($this->Auth->logout());
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
            $user = $this->Users->find()->where(['email' => $this->request->data['email']])->first();
            if ($user != null) {
				$this->Flash->success(__('Password reset instruction will be sent to e-mail address {0}.', [$user->email]));
            }else{
				$this->Flash->error(__('Email cannot be found. Please, try again.'));
			}
        }
        $this->set(compact('user', 'success', 'oem'));
        $this->set('_serialize', ['user']);
        $this->viewBuilder()->layout('public_reset');
    }
}
