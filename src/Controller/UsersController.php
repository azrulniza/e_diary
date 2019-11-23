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
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }
	private $userStatus;

    public function __construct(\Cake\Network\Request $request = null, \Cake\Network\Response $response = null, $name = null, $eventManager = null, $components = null)
    {
        parent::__construct($request, $response, $name, $eventManager, $components);

        $this->userStatus = [
            1 => __('Active'),
            0 => __('Disabled')];
    }
	
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
		$this->loadModel('Organizations');
		$this->loadModel('UserOrganizations');
		
		$search_name = $this->request->query('search');
		if($search_name){
			$search_name = trim($search_name," ");
		}
		$userId = $this->Auth->user()['id'];
		$organizationSelected = $this->request->query('organization');
		$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.id' => $userId])->limit(1)->first();
        
		//get roles
		$user = $this->Users->find()->contain(['Roles'])->Where(['id' => $userId])->limit(1)->first();
		$userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		if ($this->AuthUser->hasRole(MASTER_ADMIN)) {
			if($organizationSelected != null){
				if($search_name != null){
					$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($organizationSelected){
								return $q->where(['UserOrganizations.organization_id'=>$organizationSelected]);
						})
					->autoFields(true)
					->where(['Users.name LIKE '=>"%$search_name%"])
					->where(['Users.status'=>1]);
				}else{
					$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($organizationSelected){
								return $q->where(['UserOrganizations.organization_id'=>$organizationSelected]);
						})
					->autoFields(true)->where(['Users.status'=>1]);
				}
			}else if($search_name != null){
				$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->where(['Users.name LIKE'=>"%$search_name%"])->where(['Users.status'=>1]);
			}else{
				$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->where(['Users.status'=>1]);
			}
        }else if ($this->AuthUser->hasRole(SUPERVISOR) OR $this->AuthUser->hasRole(ADMIN)) {
			$usersOrganization = $this->UserOrganizations->find()->Where(['UserOrganizations.user_id' => $userId])->limit(1)->first();
			$user_organization_id=$usersOrganization->organization_id;
			
			if($search_name != null){
				$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
					return $q->where(['UserOrganizations.organization_id'=>$user_organization_id]);})
				->where(['Users.name LIKE '=>"%$search_name%"])->where(['Users.status'=>1]);
			}else{
				$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($user_organization_id){
					return $q->where(['UserOrganizations.organization_id'=>$user_organization_id]);})
				->where(['Users.status'=>1]);
			}
			
        }else if ($this->AuthUser->hasRole(STAFF)) {
			$query = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->where(['Users.id'=>$currentUser->id,'Users.status'=>1]);
		}

		foreach($query as $user){
			$heads = $this->Users->find()->where(['id'=> $user->report_to]);
			foreach($heads as $head){
				$reportTo[$head->id] = $head->name;
			}
		}
		
		$organizations = $this->Organizations->find('list', ['limit' => 200]);
        $users = $this->paginate($query);
        $this->set(compact('users','reportTo','organizations','organizationSelected','userRoles','search_name','currentUser'));
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
            'contain' => ['Grades','Roles']
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
            1 => __('Active')];
		$this->loadModel('UserOrganizations');
		$this->loadModel('UserDesignations');
		$this->loadModel('UsersRoles');
		$this->loadModel('UsersLogs');
		$this->loadModel('Organizations');
		$this->loadModel('Designations');
		$this->loadModel('SettingEmails');
		$userId = $this->AuthUser->id();
        $user = $this->Users->newEntity();
		$user_role = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
		$userRoles = $this->Users->Roles->initRolesChecker($user_role->roles);
		
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
			$now = \Cake\I18n\Time::now();
			$user->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
			$user->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
			
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
				
				$user_logs = $this->UsersLogs->find()->where(['ic_number'=>$user->ic_number])->first();
				if($user_logs){
					$user_logs->status = 0;
					$this->UsersLogs->save($user_logs);
				}
				$user_id = $this->Users->save($user)->id;
				$userDept = $this->UserOrganizations->newEntity();
				$userDept->user_id = $user_id;
				$userDept->organization_id = $this->request->data['organization'];
				$userDept->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$userDept->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$this->UserOrganizations->save($userDept);
				
				$userDesg = $this->UserDesignations->newEntity();
				$userDesg->user_id = $user_id;
				$userDesg->designation_id = $this->request->data['designation'];
				$userDesg->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$userDesg->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$this->UserDesignations->save($userDesg);
				
				$userRole = $this->UsersRoles->newEntity();
				$userRole->user_id = $user_id;
				$userRole->role_id = $this->request->data['role'];
				$userRole->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$userRole->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
				$this->UsersRoles->save($userRole);
				
				$language_id = $this->getLanguageId();
				$emailTemplates = $this->SettingEmails->find()->where(['email_type_id'=>1,'language_id'=>$language_id])->first();
				$emailTemp_subject = str_replace(array('[USER_NAME]', '[PASSWORD]', '[IC_NUMBER]'), array('{0}', '{1}', '{2}'), $emailTemplates->subject);
				$emailTemp_body = str_replace(array('[USER_NAME]', '[PASSWORD]', '[IC_NUMBER]'), array('{0}', '{1}', '{2}'), $emailTemplates->body);
				$subject = __(nl2br($emailTemp_subject),$user->name,$this->request->data['password'],$user->ic_number);
				$body = __(nl2br($emailTemp_body),$user->name,$this->request->data['password'],$user->ic_number);
				
				//get all supervisor
				$staff_organization_id = $this->request->data['organization'];
				$reportTo = $this->Users->find()->contain('Roles')->innerJoinWith('UserOrganizations.Organizations' , function($q) use($staff_organization_id){ return $q->where(['UserOrganizations.organization_id'=>$staff_organization_id]);})->where(['Users.status'=>1]);
			
				$reportTo->matching('Roles', function ($q) {
					return $q->where(['Roles.id IN' => [SUPERVISOR]]);
				});
			
				$supervisor_email=array();
				foreach ($reportTo as $key ) {
					$supervisor_email[] = $key['email'];
				}
				try {
					$email = new Email();

					// Use a named transport already configured using Email::configTransport()
					$email->transport('default');

					// Use a constructed object.
					$email 
						->emailFormat('html')
						->to($user->email)
						//->cc($supervisor_email) : no need to cc supervisor email when add accout
						->subject($subject)
						->send($body);
				}catch(\Exception $e){
					$this->Flash->error(__('Email could not send. Please, try again.'));
				}

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
			$designations = $this->Designations->find('list', ['limit' => 200])->where(['status'=>1]);
			$organizations = $this->Organizations->find('list', ['limit' => 200])->where(['status'=>1]);
		}else if($this->AuthUser->hasRole($this->AuthUser->hasRole(SUPERVISOR))){
			//roles
			$role=array(1,2);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			$organization = $this->UserOrganizations->find()->where(['user_id'=>$userId])->first()->organization_id;
	        
			$designations = $this->Designations->find('list', ['limit' => 200])->where(['status'=>1]);
			$organizations = $this->Organizations->find('list', ['limit' => 200])->where(['id'=>$organization])->where(['status'=>1]);
		}else if($this->AuthUser->hasRole(ADMIN)){
			//roles
			$role=array(1,2,3);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
		}
		$grades = $this->Users->Grades->find('list', ['limit' => 200])->where(['status'=>1]);
		$userStatus = $this->userStatus;
        $this->set(compact('user', 'organizations','designations', 'userStatus', 'reportTo', 'roles','userRoles','grades'));
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
		$this->loadModel('UsersRoles');
		$this->loadModel('UserRoleLogs');
		$this->loadModel('Organizations');
		$this->loadModel('Designations');
		$this->loadModel('UserDesignations');
        $user = $this->Users->get($id, [
            'contain' => ['Grades','UserDesignations', 'UserOrganizations', 'Roles']
        ]);
		$userId = $this->AuthUser->id();
		$user_role = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
		$userRoles = $this->Users->Roles->initRolesChecker($user_role->roles);
		$user_dept = $this->UserOrganizations->find()->where(['user_id'=> $userId])->first()->organization_id;
        if ($this->request->is(['patch', 'post', 'put'])) {
			 if($this->request->data['new_password']){
                $this->request->data['password']  = $this->request->data['new_password'];
            }
            $user = $this->Users->patchEntity($user, $this->request->getData());
			$now = \Cake\I18n\Time::now();
			$user->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
			
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
				if($_POST['organization']){
					if($chk_org){
					$query = $this->UserOrganizations->query();
					$query->update()
						->set(['organization_id' => $_POST['organization']])
						->where(['user_id' => $id])
						->execute();
				}else{
					$userDept = $this->UserOrganizations->newEntity();
					$userDept->user_id = $id;
					$userDept->organization_id = $_POST['organization'];
					$userDept->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
					$userDept->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
					$this->UserOrganizations->save($userDept);
				}
				}
				$chk_desg = $this->UserDesignations->find()->where(['user_id'=>$id])->first()->user_id;
				if($_POST['designation']){
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
				}
				$chk_role = $this->UsersRoles->find()->where(['user_id'=> $id])->first();
				if($_POST['role']){
					if($chk_role){
						if($chk_role->role_id != $_POST['role']){
							$userRoleLogs = $this->UserRoleLogs->newEntity();
							$userRoleLogs->user_id = $id;
							$userRoleLogs->role_id = $chk_role->role_id;
							$userRoleLogs->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
							$userRoleLogs->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
							$this->UserRoleLogs->save($userRoleLogs);
							
							$query = $this->UsersRoles->query();
							$query->update()
								->set(['role_id' => $_POST['role']])
								->where(['user_id' => $id])
								->execute();
						}
					}else{
						$userRole = $this->UsersRoles->newEntity();
						$userRole->user_id = $id;
						$userRole->role_id = $_POST['role'];
						$userRole->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
						$userRole->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
						$this->UsersRoles->save($userRole);
					}
				}
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
		$selected_role = $this->UsersRoles->find()->where(['user_id'=> $id])->first()->role_id;
		$selected_dept = $this->UserOrganizations->find()->where(['user_id'=> $id])->first()->organization_id;
		$selected_designation = $this->UserDesignations->find()->where(['user_id'=> $id])->first()->designation_id;
		$selected_reportTo = $this->Users->find()->where(['id'=> $id])->first()->report_to;
		if ($this->AuthUser->hasRole(MASTER_ADMIN) ) {
			$roles = $this->Users->Roles->find('list', ['limit' => 200]);
			
			$organizations = $this->Organizations->find('list', ['limit' => 200])->where(['status'=>1]);
			$designations = $this->Designations->find('list', ['limit' => 200])->where(['organization_id'=>$selected_dept])->where(['status'=>1]);
			$reportTo = $this->Users->find('list')
						->order(['Users.name' => 'ASC'])
						->contain(['Roles','Grades','UserDesignations.Designations','UserOrganizations.Organizations'])
						->innerJoinWith('UserOrganizations.Organizations' , function($q) use($selected_dept){
							return $q->where(['UserOrganizations.organization_id'=>$selected_dept]);})
						->where(['Users.status'=>1,'Users.id !='=>$id]);
			$reportTo->matching('Roles', function ($q) {
					return $q->where(['Roles.id IN' => [MASTER_ADMIN,SUPERVISOR]]);
				})->toArray();
			if(empty($reportTo->first()) AND ($selected_role == 1 OR $selected_role == 2)){
				$reportTo = $this->Users->find('list')->order(['Users.name' => 'ASC'])->contain(['Roles','Grades','UserDesignations.Designations','UserOrganizations.Organizations'])->where(['Users.id'=>$selected_reportTo]);
			}
		}else if($this->AuthUser->hasRole($this->AuthUser->hasRole(SUPERVISOR))){
			//roles
			$role=array(1);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			
			$designations = $this->Designations->find('list', ['limit' => 200])->where(['organization_id'=> $user_dept])->orWhere(['status'=>1]);
			$organizations = $this->Organizations->find('list', ['limit' => 200])->where(['status'=>1]);
		}else if($this->AuthUser->hasRole(ADMIN)){
			//roles
			$role=array(1,2,3);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			
			$designations = $this->Designations->find('list', ['limit' => 200])->where(['status'=>1]);
			$organizations = $this->Organizations->find('list', ['limit' => 200])->where(['status'=>1]);
		}
		$userStatus = $this->userStatus;
		$grades = $this->Users->Grades->find('list', ['limit' => 200])->where(['status'=>1]);
        $this->set(compact('user', 'organizations','designations', 'roles', 'reportTo','userStatus','selected_dept','selected_designation','userRoles','selected_reportTo','selected_role','grades'));
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
		$this->loadModel('UsersLogs');
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
		$now = \Cake\I18n\Time::now();

		$user_logs = $this->UsersLogs->newEntity();
		$user_logs->user_id = $id;
		$user_logs->email = $user->email;
		$user_logs->password = $user->password;
		$user_logs->name = $user->name;
		$user_logs->ic_number = $user->ic_number;
		$user_logs->phone = $user->phone;
		$user_logs->report_to = $user->report_to;
		$user_logs->reset_password_key = $user->reset_password_key;
		$user_logs->card_no = $user->card_no;
		$user_logs->grade_id = $user->grade_id;
		$user_logs->skim = $user->skim;
		$user_logs->status = $user->status;
		$user_logs->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
		$user_logs->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
		$user_logs->image = $user->image;
		
		if ($this->UsersLogs->save($user_logs)) {
			$this->Users->delete($user);
			$this->Flash->success(__('The user has been delete.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
        return $this->redirect(['action' => 'index']);
    }
	
	 public function login()
    {
		$this->loadComponent('Captcha.Captcha');
		$this->loadModel('UserLoginLogs');
		$this->loadModel('UsersLogs');
		
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
					//var_dump($user);die();
					$this->Auth->setUser($user);
					
					$userLogins = $this->UserLoginLogs->newEntity();
					$userLogins->user_id = $user['id'];
					$userLogins->ip_address = $this->Users->get_client_ip();
					$this->UserLoginLogs->save($userLogins);

					return $this->redirect($this->Auth->redirectUrl());
				} else {
					if($this->UsersLogs->find()->where(['ic_number'=>$this->request->data['ic_number'],'status'=>1])->first() != null){
						$this->Flash->error(
							__('Your account has been disabled.'), 'default', [], 'auth'
						);
					}else if($this->Users->find()->where(['ic_number'=>$this->request->data['ic_number']])->first() != null){
						$this->Flash->error(
						__('Email or password is incorrect'), 'default', [], 'auth'
						);
					}else{
						$this->Flash->error(
						__('Invalid user'), 'default', [], 'auth'
						);
					}
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
	
	public function reset_password($lang = null)
    {
        $success = false;
		$this->loadModel('SettingEmails');
        $user = $this->Users->newEntity();
		
		$langs = \Cake\Core\Configure::read('Languages');
		$session = $this->request->session();

		if(isset($langs[$lang])){
            $session->write('Config.language', $lang);
            I18n::locale($lang);
			//$this->redirect('/users/verify_reset?switch_to='.$lang.'&user_id='.$user_id);
        }
        if($lang == 'ms_MY'){
			$language_id=2;
		}else{
			$language_id=1;
		}
		
        if ($this->request->is('post')) {
            $user = $this->Users->find()->where(['email' => $this->request->data['email']])->first();
            if ($user != null) {
				$arr_verify = array( 'user_id' => $user->id, 'language_id' => $language_id);
				$data = json_encode($arr_verify);
				$verify_url = Router::url('/', true).'users/verify_reset?param='.base64_encode($data);
				
				$emailTemplates = $this->SettingEmails->find()->where(['email_type_id'=>2,'language_id'=>$language_id])->first();
				$emailTemp_subject = str_replace(array('[USER_NAME]', '[URL_LINK]', '[IC_NUMBER]'), array('{0}', '{1}', '{2}'), $emailTemplates->subject);
				$emailTemp_body = str_replace(array('[USER_NAME]', '[URL_LINK]', '[IC_NUMBER]'), array('{0}', '{1}', '{2}'), $emailTemplates->body);
				$subject = __(nl2br($emailTemp_subject),$user->name,$verify_url,$user->ic_number);
				$body = __(nl2br($emailTemp_body),$user->name,$verify_url,$user->ic_number);
				
				try {
					$email = new Email();

					// Use a named transport already configured using Email::configTransport()
					$email->transport('default');

					// Use a constructed object.
					$email 
						->emailFormat('html')
						->to($user->email)
						->subject($subject)
						->send($body);
				}catch(\Exception $e){
					$this->Flash->error(__('Email could not send. Please, try again.'));
				}
				
				$this->Flash->success(__('Password reset instruction will be sent to e-mail address {0}.', [$user->email]));
            }else{
				$this->Flash->error(__('Email cannot be found. Please, try again.'));
			}
        }
        $this->set(compact('user', 'success'));
        $this->set('_serialize', ['user']);
        $this->viewBuilder()->layout('public_reset');
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
			$lang = 'ms_MY';
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
				$this->Flash->error(__('Invalid user'));
			}
		}
		$this->viewBuilder()->layout('verify_reset');
		$this->set(compact('language_id'));
    }
	public function getDetails()
	{
		$this->loadModel('Designations');
		$department_id = $_GET['id'];
		$role_id = $_GET['role_id'];
        $userId = $this->AuthUser->id();
		$user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
		$userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		
		$users = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Roles'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($department_id){
		return $q->where(['UserOrganizations.organization_id'=>$department_id]);})->where(['Users.status'=>1])->group(['Users.id']);
		
		if($role_id == 3 OR $role_id == 4){
			$users->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [SUPERVISOR]]);
            });
		}else{
			$users->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [MASTER_ADMIN,SUPERVISOR]]);
            });
		}
		
		if ($userRoles->hasRole(['Master Admin'])) {
			$designations = $this->Designations->find('all')->where(['organization_id'=>$department_id])->where(['status'=>1]);
			
			if(empty($users->first()) AND ($role_id == 1 OR $role_id == 2)){
				$users = $this->Users->find('all')->order(['Users.name' => 'ASC'])->contain(['Roles'])->where(['Users.status'=>1])->group(['Users.id']);
			
				$users->matching('Roles', function ($q) {
						return $q->where(['Roles.id IN' => [MASTER_ADMIN]]);
					});
					
			}
        }
		$this->set(compact('designations','users'));
        $this->set('_serialize', ['designations','users']);
        $this->viewBuilder()->layout('ajax');
	}
	
	public function getLanguageId(){
		$session = $this->request->session()->read('Config.language');
		if(isset($session) AND $session == 'en'){
			$language_id=1;
		}else{
			$language_id=2;
		}
		return $language_id;
	}

	public function download() { 
	       $file_path = 'files'.DS.'eDiary_User_Manual_v1.0.pdf';
		    $this->response->file($file_path, array(
		        'download' => true,
		        'name' => 'eDiary_User_Manual.pdf',
		    ));
		    return $this->response;
	}
}
