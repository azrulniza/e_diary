<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * UserRoleLogs Controller
 *
 * @property \App\Model\Table\UserRoleLogsTable $UserRoleLogs
 *
 * @method \App\Model\Entity\UserRoleLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserRoleLogsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Roles']
        ];
        $userRoleLogs = $this->paginate($this->UserRoleLogs);

        $this->set(compact('userRoleLogs'));
    }

    /**
     * View method
     *
     * @param string|null $id User Role Log id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userRoleLog = $this->UserRoleLogs->get($id, [
            'contain' => ['Users', 'Roles']
        ]);

        $this->set('userRoleLog', $userRoleLog);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userRoleLog = $this->UserRoleLogs->newEntity();
        if ($this->request->is('post')) {
            $userRoleLog = $this->UserRoleLogs->patchEntity($userRoleLog, $this->request->getData());
            if ($this->UserRoleLogs->save($userRoleLog)) {
                $this->Flash->success(__('The user role log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user role log could not be saved. Please, try again.'));
        }
        $users = $this->UserRoleLogs->Users->find('list', ['limit' => 200]);
        $roles = $this->UserRoleLogs->Roles->find('list', ['limit' => 200]);
        $this->set(compact('userRoleLog', 'users', 'roles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User Role Log id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userRoleLog = $this->UserRoleLogs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userRoleLog = $this->UserRoleLogs->patchEntity($userRoleLog, $this->request->getData());
            if ($this->UserRoleLogs->save($userRoleLog)) {
                $this->Flash->success(__('The user role log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user role log could not be saved. Please, try again.'));
        }
        $users = $this->UserRoleLogs->Users->find('list', ['limit' => 200]);
        $roles = $this->UserRoleLogs->Roles->find('list', ['limit' => 200]);
        $this->set(compact('userRoleLog', 'users', 'roles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User Role Log id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userRoleLog = $this->UserRoleLogs->get($id);
        if ($this->UserRoleLogs->delete($userRoleLog)) {
            $this->Flash->success(__('The user role log has been deleted.'));
        } else {
            $this->Flash->error(__('The user role log could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
