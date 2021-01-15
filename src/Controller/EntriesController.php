<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Entries Controller
 *
 * @property \App\Model\Table\EntriesTable $Entries
 * @method \App\Model\Entity\Entry[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EntriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Accounts', 'Transactions'],
        ];
        $entries = $this->paginate($this->Entries);

        $this->set(compact('entries'));
    }

    /**
     * View method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $entry = $this->Entries->get($id, [
            'contain' => ['Accounts', 'Transactions'],
        ]);

        $this->set(compact('entry'));
    }

    public function viewPeerEntries($id = null)
    {
        $entry = $this->Entries->get($id, [
            'contain' => ['Accounts', 'Transactions'],
        ]);
        $peers = $this->Entries->find()->contain(['Accounts'])->where([
			'Entries.id !='=> $id, 'transaction_id'=>$entry->transaction_id])->toArray();
        $this->set(compact('entry', 'peers'));
		$this->viewBuilder()->setOption('serialize', ['entry', 'peers']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $entry = $this->Entries->newEmptyEntity();
        if ($this->request->is('post')) {
            $entry = $this->Entries->patchEntity($entry, $this->request->getData());
            if ($this->Entries->save($entry)) {
                $this->Flash->success(__('The entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The entry could not be saved. Please, try again.'));
        }
        $accounts = $this->Entries->Accounts->find('list', ['limit' => 200]);
        $transactions = $this->Entries->Transactions->find('list', ['limit' => 200]);
        $this->set(compact('entry', 'accounts', 'transactions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $entry = $this->Entries->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $entry = $this->Entries->patchEntity($entry, $this->request->getData());
            if ($this->Entries->save($entry)) {
                $this->Flash->success(__('The entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The entry could not be saved. Please, try again.'));
        }
        $accounts = $this->Entries->Accounts->find('list', ['limit' => 200]);
        $transactions = $this->Entries->Transactions->find('list', ['limit' => 200]);
        $this->set(compact('entry', 'accounts', 'transactions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Entry id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entry = $this->Entries->get($id);
        if ($this->Entries->delete($entry)) {
            $this->Flash->success(__('The entry has been deleted.'));
        } else {
            $this->Flash->error(__('The entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function check($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $entry = $this->Entries->get($id);
        $status = 'n';
        switch ($entry->status) {
        case 'n':
        	$status = 'c'; break;
        case 'c':
        	$status = 'y'; break;
        }
        $entry->status = $status;
        $this->Entries->save($entry);
        $this->set(compact('status'));
        $this->viewBuilder()->setOption('serialize', ['status']);
    }
    
    public function updateLabels($id) {
    	$raw = $this->request->getData('labels');
    	$ar = preg_split("/\\s+/", $raw ?? '') ?? [];
        $labels = array_values(
        	array_filter($ar, function($element){
			return preg_match('/^[A-Za-z0-9_]+$/', $element)==1;
        })
        );
        $field = json_encode(['labels'=>$labels]);
        $entry = $this->Entries->get($id);
        $this->Entries->patchEntity($entry, ['labels'=>$field]);
        if ($this->Entries->save($entry)) {
        	$result = 'saved';
        }
        else {
        	$result = 'cannot save';
        }
        $this->set(compact('labels', 'result'));
        $this->viewBuilder()->setOption('serialize', ['labels', 'result']);
    }
    
}
