<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Organizations Controller
 *
 * @property \App\Model\Table\OrganizationsTable $Organizations
 *
 * @method \App\Model\Entity\Organization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
		$this->set('title', __('Departments'));
        $organizations = $this->paginate($this->Organizations->find()->where(['status'=>1]));

        $this->set(compact('organizations'));
    }

    /**
     * View method
     *
     * @param string|null $id Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->set('title', __('Departments'));

        $organization = $this->Organizations->get($id);
        $this->set('organization', $organization);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Departments'));
        $organization = $this->Organizations->newEntity();
        if ($this->request->is('post')) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
			$now = \Cake\I18n\Time::now();
            $user->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $user->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $organization->create_by = $this->Auth->user()['id'];
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The organization has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The organization could not be saved. Please, try again.'));
        }
		$status = [
            1 => __('Active')
		];
        $this->set(compact('organization','status'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->set('title', __('Departments'));

        $organization = $this->Organizations->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
			$now = \Cake\I18n\Time::now();
            $user->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The organization has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The organization could not be saved. Please, try again.'));
        }
		$status = [
            1 => __('Active'),
            0 => __('Disable')
		];
        $this->set(compact('organization','status'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->set('title', __('Departments'));
        
		$this->loadModel('UserOrganizations');
        $this->request->allowMethod(['post', 'delete']);
        $organization = $this->Organizations->get($id);
		
		if($this->UserOrganizations->find()->where(['organization_id'=>$id])->first() != null){
			$this->Flash->error(__('Organization already in used. The organization could not be deleted.'));
		}else{
			$organization->status = 0;
			if ($this->Organizations->save($organization)) {
				$this->Flash->success(__('The organization has been deleted.'));
			} else {
				$this->Flash->error(__('The organization could not be deleted. Please, try again.'));
			}
		}
        return $this->redirect(['action' => 'index']);
    }
}
