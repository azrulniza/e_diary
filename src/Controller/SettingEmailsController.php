<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * SettingEmails Controller
 *
 * @property \App\Model\Table\SettingEmailsTable $SettingEmails
 *
 * @method \App\Model\Entity\SettingEmail[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingEmailsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $settingEmails = $this->paginate($this->SettingEmails);

        $this->set(compact('settingEmails'));
    }

    /**
     * View method
     *
     * @param string|null $id Setting Email id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $settingEmail = $this->SettingEmails->get($id, [
            'contain' => []
        ]);

        $this->set('settingEmail', $settingEmail);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $settingEmail = $this->SettingEmails->newEntity();
        if ($this->request->is('post')) {
            $settingEmail = $this->SettingEmails->patchEntity($settingEmail, $this->request->getData());
            if ($this->SettingEmails->save($settingEmail)) {
                $this->Flash->success(__('The setting email has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting email could not be saved. Please, try again.'));
        }
        $this->set(compact('settingEmail'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting Email id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $settingEmail = $this->SettingEmails->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $settingEmail = $this->SettingEmails->patchEntity($settingEmail, $this->request->getData());
            if ($this->SettingEmails->save($settingEmail)) {
                $this->Flash->success(__('The setting email has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting email could not be saved. Please, try again.'));
        }
        $this->set(compact('settingEmail'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Setting Email id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $settingEmail = $this->SettingEmails->get($id);
        if ($this->SettingEmails->delete($settingEmail)) {
            $this->Flash->success(__('The setting email has been deleted.'));
        } else {
            $this->Flash->error(__('The setting email could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
