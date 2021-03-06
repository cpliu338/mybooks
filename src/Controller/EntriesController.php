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
        /*
        $q = $this->Entries->find()->where([
        		'id IN'=>$this->Entries->jsonMatch('CitiStepUp')
        		])->toArray();
        		*/
        $q = $this->Entries->allLabels(['account_id'=>$id]);
        $this->set(compact('entry', 'q'));
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
    
    public function setBfDate() {
    	/* POST /users/settings ?bfDate=xxx&_csrfToken=yyy */
    	if ($this->request->is('ajax')) {
			$ar = [];
			$account_id = $this->request->getQuery('account_id');
			$summary = $this->request->getQuery('summary');
			if ($summary) {
				$account = $this->Entries->Accounts->get($account_id);
				$cond = ['Entries.account_id IN'=>$this->Entries->Accounts->find()->where([
					'Accounts.code LIKE'=>$account->code . '%'
				])->extract('id')->toArray()];
			}
			else {
				$cond = ['Entries.account_id'=>$account_id];
			}
			$e = $this->Entries->find()->contain(['Transactions'])->where(
				$cond
			)->order('Transactions.date1')->first();
			if (!empty($e))
				$ar[$e->transaction->date1->i18nFormat('yyyy-MM-dd')] = 'first entry';
			$e = $this->Entries->find()->contain(['Transactions'])->where(
				array_merge($cond,
				['Entries.status'=>'n']
			))->order('Transactions.date1')->first();
			if (!empty($e))
				$ar[$e->transaction->date1->i18nFormat('yyyy-MM-dd')] = 'first non-cleared entry';
			$e = $this->Entries->find()->contain(['Transactions'])->where(
				array_merge($cond,
				['Entries.status'=>'n']
			))->order('Transactions.date1')->last();
			if (!empty($e))
				$ar[$e->transaction->date1->i18nFormat('yyyy-MM-dd')] = 'last non-cleared entry';
			$e = $this->Entries->find()->contain(['Transactions'])->where(
				$cond
			)->order('Transactions.date1 desc')->limit(10)->last();
			if (!empty($e))
				$ar[$e->transaction->date1->i18nFormat('yyyy-MM-dd')] = 'recent entries';
			$options = []; foreach ($ar as $date=>$text) {
				array_push($options, compact('date','text'));
			}
			$this->set(compact('options', 'cond'));
			$this->viewBuilder()->setOption('serialize', ['options', 'cond']);
		}
    }
    
    public function reconcile($id=null) {
    	$entry = $this->Entries->get($this->request->getData('recon_id'),
    		['contain'=>['Transactions']]);
    	$ar = $this->Entries->find()->where([
			'status'=>'n',			
			'account_id'=>$entry->account_id
		])->matching('Transactions', function ($q) use ($entry) {
			return $q->where(['Transactions.date1 <=' => $entry->transaction->date1]);
		})->extract('id')->toArray();
		if (is_array($ar) && !empty($ar)) {
	    	$result = $this->Entries->updateAll(['status'=>'c'],
				['id IN' => $ar]);
			$this->Flash->success(var_export($result, true));
		}
		else {
    		$this->Flash->error(__('The entry has not been reconciled.') 
    			);
    	}
		return $this->redirect(['controller'=>'Accounts', 'action' => 'view', $entry->account_id]);
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
