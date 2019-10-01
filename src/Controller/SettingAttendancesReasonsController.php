<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * SettingAttendancesReasons Controller
 *
 * @property \App\Model\Table\SettingAttendancesReasonsTable $SettingAttendancesReasons
 *
 * @method \App\Model\Entity\SettingAttendancesReason[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingAttendancesReasonsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $settingAttendancesReasons = $this->paginate($this->SettingAttendancesReasons);

        $this->set(compact('settingAttendancesReasons'));
    }

    /**
     * View method
     *
     * @param string|null $id Setting Attendances Reason id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $settingAttendancesReason = $this->SettingAttendancesReasons->get($id, [
            'contain' => []
        ]);

        $this->set('settingAttendancesReason', $settingAttendancesReason);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $settingAttendancesReason = $this->SettingAttendancesReasons->newEntity();
        if ($this->request->is('post')) {
            $settingAttendancesReason = $this->SettingAttendancesReasons->patchEntity($settingAttendancesReason, $this->request->getData());
			$now = \Cake\I18n\Time::now();
            $settingAttendancesReason->cdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $settingAttendancesReason->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            if ($this->SettingAttendancesReasons->save($settingAttendancesReason)) {
                $this->Flash->success(__('The setting attendances reason has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting attendances reason could not be saved. Please, try again.'));
        }
		$status = [
            1 => __('Active'),
            0 => __('Disable')
		];
        $this->set(compact('settingAttendancesReason','status'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting Attendances Reason id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $settingAttendancesReason = $this->SettingAttendancesReasons->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $settingAttendancesReason = $this->SettingAttendancesReasons->patchEntity($settingAttendancesReason, $this->request->getData());
			$now = \Cake\I18n\Time::now();
            $settingAttendancesReason->mdate = $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            if ($this->SettingAttendancesReasons->save($settingAttendancesReason)) {
                $this->Flash->success(__('The setting attendances reason has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting attendances reason could not be saved. Please, try again.'));
        }
		$status = [
            1 => __('Active'),
            0 => __('Disable')
		];
        $this->set(compact('settingAttendancesReason','status'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Setting Attendances Reason id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $settingAttendancesReason = $this->SettingAttendancesReasons->get($id);
        if ($this->SettingAttendancesReasons->delete($settingAttendancesReason)) {
            $this->Flash->success(__('The setting attendances reason has been deleted.'));
        } else {
            $this->Flash->error(__('The setting attendances reason could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
