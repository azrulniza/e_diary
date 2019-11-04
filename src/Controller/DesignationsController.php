<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Designations Controller
 *
 * @property \App\Model\Table\DesignationsTable $Designations
 *
 * @method \App\Model\Entity\Designation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DesignationsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->set('title', __('Designations'));

		$this->loadModel('Users');
		$this->loadModel('Organizations');
		$userId = $this->Auth->user()['id'];
		$organizationSelected = $this->request->query('organization');
		$currentUser = $this->Users->find()->contain(['Roles'])->where(['Users.id' => $userId])->limit(1)->first();
        
		//get roles
		$user = $this->Users->find()->contain(['Roles'])->Where(['id' => $userId])->limit(1)->first();
		$userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		
        $this->paginate = [
            'contain' => ['Organizations']
        ];
		$organizations = $this->Organizations->find('list', ['limit' => 200]);
		if($organizationSelected != null){
			$designations = $this->paginate($this->Designations->find()->where(['organization_id'=>$organizationSelected,'Designations.status'=>1]));
		}else{
			$designations = $this->paginate($this->Designations->find()->where(['Designations.status'=>1]));
		}

        $this->set(compact('designations','organizationSelected','userRoles','organizations'));
    }

    /**
     * View method
     *
     * @param string|null $id Designation id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->set('title', __('Designations'));

        $designation = $this->Designations->get($id, [
            'contain' => ['Organizations', 'UserDesignations']
        ]);

        $this->set('designation', $designation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->set('title', __('Designations'));

        $designation = $this->Designations->newEntity();
        if ($this->request->is('post')) {
            $designation = $this->Designations->patchEntity($designation, $this->request->getData());
            if ($this->Designations->save($designation)) {
                $this->Flash->success(__('The designation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The designation could not be saved. Please, try again.'));
        }
        $organizations = $this->Designations->Organizations->find('list', ['limit' => 200])->where(['status'=>1]);
        $this->set(compact('designation', 'organizations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Designation id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->set('title', __('Designations'));

        $designation = $this->Designations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $designation = $this->Designations->patchEntity($designation, $this->request->getData());
            if ($this->Designations->save($designation)) {
                $this->Flash->success(__('The designation has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The designation could not be updated. Please, try again.'));
        }
        $organizations = $this->Designations->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('designation', 'organizations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Designation id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->set('title', __('Designations'));
        
		$this->loadModel('UserDesignations');
        $this->request->allowMethod(['post', 'delete']);
        $designation = $this->Designations->get($id);
		
		if($this->UserDesignations->find()->where(['designation_id'=>$id])->first() != null){
			$this->Flash->error(__('Designation already in used. The designation could not be deleted.'));
		}else{
			$designation->status = 0;
			if ($this->Designations->save($designation)) {
				$this->Flash->success(__('The designation has been deleted.'));
			} else {
				$this->Flash->error(__('The designation could not be deleted. Please, try again.'));
			}
		}

        return $this->redirect(['action' => 'index']);
    }
}
