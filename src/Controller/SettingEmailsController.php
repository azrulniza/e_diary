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
        $this->set('title', __('Setting Emails'));
		$emails = $this->SettingEmails->find()->group('email_type_id');
        $settingEmails = $this->paginate($emails);

        $this->set(compact('settingEmails'));
    }

    /**
     * View method
     *
     * @param string|null $id Setting Email id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($email_type_id = null)
    {
        $this->set('title', __('Setting Emails'));
		$englishiId = $this->SettingEmails->find()->where(['email_type_id'=> $email_type_id,'language_id'=>1])->first()->id;
		$malayId = $this->SettingEmails->find()->where(['email_type_id'=> $email_type_id,'language_id'=>2])->first()->id;
        $settingEmailEnglish = $this->SettingEmails->get($englishiId, [
            'contain' => []
        ]);
        $settingEmailMalay = $this->SettingEmails->get($malayId, [
            'contain' => []
        ]);

        $this->set(compact('settingEmail','settingEmailEnglish','settingEmailMalay'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->set('title', __('Setting Emails'));
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
    public function edit($email_type_id = null,$language_id = null)
    {
        $this->set('title', __('Setting Emails'));
		$id = $this->SettingEmails->find()->where(['email_type_id'=> $email_type_id, 'language_id'=> $language_id])->first()->id;
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
        $this->set(compact('settingEmail','email_type_id','language_id'));
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
        $this->set('title', __('Setting Emails'));
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
