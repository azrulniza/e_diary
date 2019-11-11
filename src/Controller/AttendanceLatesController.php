<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AttendanceLates Controller
 *
 * @property \App\Model\Table\AttendanceLatesTable $AttendanceLates
 *
 * @method \App\Model\Entity\AttendanceLate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttendanceLatesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Attendances']
        ];
        $attendanceLates = $this->paginate($this->AttendanceLates);

        $this->set(compact('attendanceLates'));
    }

    /**
     * View method
     *
     * @param string|null $id Attendance Late id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $attendanceLate = $this->AttendanceLates->get($id, [
            'contain' => ['Attendances']
        ]);

        $this->set('attendanceLate', $attendanceLate);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $attendanceLate = $this->AttendanceLates->newEntity();
        if ($this->request->is('post')) {
            $attendanceLate = $this->AttendanceLates->patchEntity($attendanceLate, $this->request->getData());
            if ($this->AttendanceLates->save($attendanceLate)) {
                $this->Flash->success(__('The attendance late has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance late could not be saved. Please, try again.'));
        }
        $attendances = $this->AttendanceLates->Attendances->find('list', ['limit' => 200]);
        $this->set(compact('attendanceLate', 'attendances'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Attendance Late id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $attendanceLate = $this->AttendanceLates->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $attendanceLate = $this->AttendanceLates->patchEntity($attendanceLate, $this->request->getData());
            if ($this->AttendanceLates->save($attendanceLate)) {
                $this->Flash->success(__('The attendance late has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attendance late could not be saved. Please, try again.'));
        }
        $attendances = $this->AttendanceLates->Attendances->find('list', ['limit' => 200]);
        $this->set(compact('attendanceLate', 'attendances'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Attendance Late id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attendanceLate = $this->AttendanceLates->get($id);
        if ($this->AttendanceLates->delete($attendanceLate)) {
            $this->Flash->success(__('The attendance late has been deleted.'));
        } else {
            $this->Flash->error(__('The attendance late could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
