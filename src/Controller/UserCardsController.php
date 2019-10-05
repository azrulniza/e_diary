<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * UserCards Controller
 *
 * @property \App\Model\Table\UserCardsTable $UserCards
 *
 * @method \App\Model\Entity\UserCard[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserCardsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Cards']
        ];
        $userCards = $this->paginate($this->UserCards);

        $this->set(compact('userCards'));
    }

    /**
     * View method
     *
     * @param string|null $id User Card id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userCard = $this->UserCards->get($id, [
            'contain' => ['Users', 'Cards']
        ]);

        $this->set('userCard', $userCard);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userCard = $this->UserCards->newEntity();
        if ($this->request->is('post')) {
            $userCard = $this->UserCards->patchEntity($userCard, $this->request->getData());
            if ($this->UserCards->save($userCard)) {
                $this->Flash->success(__('The user card has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user card could not be saved. Please, try again.'));
        }
        $users = $this->UserCards->Users->find('list', ['limit' => 200]);
        $cards = $this->UserCards->Cards->find('list', ['limit' => 200]);
        $this->set(compact('userCard', 'users', 'cards'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User Card id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userCard = $this->UserCards->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userCard = $this->UserCards->patchEntity($userCard, $this->request->getData());
            if ($this->UserCards->save($userCard)) {
                $this->Flash->success(__('The user card has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user card could not be saved. Please, try again.'));
        }
        $users = $this->UserCards->Users->find('list', ['limit' => 200]);
        $cards = $this->UserCards->Cards->find('list', ['limit' => 200]);
        $this->set(compact('userCard', 'users', 'cards'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User Card id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userCard = $this->UserCards->get($id);
        if ($this->UserCards->delete($userCard)) {
            $this->Flash->success(__('The user card has been deleted.'));
        } else {
            $this->Flash->error(__('The user card could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
