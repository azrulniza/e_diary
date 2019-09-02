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
		$currentUser = $this->Users->find()->where(['Users.id' => $this->Auth->user()['id']])->first();
		$query = $this->Users->find()->order(['Users.id' => 'DESC'])->contain('Roles');
		
		if ($this->AuthUser->hasRole(MASTER_ADMIN)) {

        }else if ($this->AuthUser->hasRole(SUPERVISOR)) {

            $query->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [STAFF], 'Users.id !=' => 1]);
            });

        }else if ($this->AuthUser->hasRole(ADMIN)) {


        }
		foreach($query as $user){
			$heads = $this->Users->find()->where(['id'=> $user->report_to]);
			foreach($heads as $head){
				$reportTo[$head->id] = $head->name;
			}
		}
        $users = $this->paginate($query);
        $this->set(compact('users','reportTo'));
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
            0 => __('Disabled'),
            2 => __('Pending activation'),
            3 => __('Pending update profile'),
            4 => __('Password reset requested')];
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
			//var_dump($user);die();
            if ($this->Users->save($user)) {
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
		}else if($this->AuthUser->hasRole($this->AuthUser->hasRole(SUPERVISOR))){
			//roles
			$role=array(1);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);

		}else if($this->AuthUser->hasRole(ADMIN)){
			//roles
			$role=array(1,2);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);
			$reportTo = $this->Users->find('list')->contain('Roles');
			$reportTo->matching('Roles', function ($q) {
					return $q->where(['Roles.id IN' => [SUPERVISOR]]);
				});

		}
        $organizations = $this->Users->Organizations->find('list', ['limit' => 200]);
		$userStatus = $this->userStatus;
        $this->set(compact('user', 'organizations', 'userStatus', 'reportTo', 'roles'));
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
        $user = $this->Users->get($id, [
            'contain' => ['Organizations', 'Roles']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
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
			$role=array(1,2);
			$roles = $this->Users->Roles->find('list')->where(['Roles.id NOT IN'=>$role]);


		}
        $organizations = $this->Users->Organizations->find('list', ['limit' => 200]);
		$reportTo = $this->Users->find('list')->contain('Roles');
		$reportTo->matching('Roles', function ($q) {
                return $q->where(['Roles.id IN' => [SUPERVISOR]]);
            });
		$userStatus = $this->userStatus;
        $this->set(compact('user', 'organizations', 'roles', 'reportTo','userStatus'));
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
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
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
}
