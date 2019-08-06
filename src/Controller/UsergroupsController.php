<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Usergroups Controller
 *
 * @property \App\Model\Table\UsergroupsTable $Usergroups
 */
class UsergroupsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $usergroups = $this->paginate($this->Usergroups);

        $this->set(compact('usergroups'));
        $this->set('_serialize', ['usergroups']);
    }

    /**
     * View method
     *
     * @param string|null $id Usergroup id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usergroup = $this->Usergroups->get($id, [
            'contain' => ['CmsmenusPermission']
        ]);

        $this->set('usergroup', $usergroup);
        $this->set('_serialize', ['usergroup']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usergroup = $this->Usergroups->newEntity();
        if ($this->request->is('post')) {
            $usergroup = $this->Usergroups->patchEntity($usergroup, $this->request->getData());
            if ($this->Usergroups->save($usergroup)) {
                $this->Flash->success(__('The usergroup has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usergroup could not be saved. Please, try again.'));
        }
        $this->set(compact('usergroup'));
        $this->set('_serialize', ['usergroup']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Usergroup id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $usergroup = $this->Usergroups->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $usergroup = $this->Usergroups->patchEntity($usergroup, $this->request->getData());
            if ($this->Usergroups->save($usergroup)) {
                $this->Flash->success(__('The usergroup has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usergroup could not be saved. Please, try again.'));
        }
        $this->set(compact('usergroup'));
        $this->set('_serialize', ['usergroup']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Usergroup id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $usergroup = $this->Usergroups->get($id);
        if ($this->Usergroups->delete($usergroup)) {
            $this->Flash->success(__('The usergroup has been deleted.'));
        } else {
            $this->Flash->error(__('The usergroup could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
