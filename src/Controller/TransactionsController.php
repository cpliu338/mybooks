<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\I18n\Date;
use Cake\Core\Configure;
use App\Form\TransactionForm;

/**
 * Transactions Controller
 *
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TransactionsController extends AppController
{
	
	public function beforeFilter($event) {
		$this->loadModel('Accounts');
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $transactions = $this->paginate($this->Transactions);

        $this->set(compact('transactions'));
    }

    /**
     * View method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transaction = $this->Transactions->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('transaction'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
    	$this->setupTagFilter();
    	//$tagfilter = $this->viewBuilder()->getVar('tagfilter');
        $transaction = $this->Transactions->newEmptyEntity();
        if ($this->request->is('post') && !$this->request->is('ajax')) {
        	$data = $this->request->getData();
            $transaction->date1 = $data['tran_date'];
            $transaction->description = $data['tran_desc'];
            if ($this->Transactions->save($transaction)) {
            	$tran_id = $transaction->id;
            	$entry1 = $this->Transactions->Entries->newEmptyEntity();
            	$entry1->account_id = $data['entry1_accountid'];
            	$entry1->transaction_id = $transaction->id;
            	$entry1->status = 'n';
            	$entry1->real_amount = $data['entry1_realamount'] * $data['entry1_dbcr'];
            	$entry1->home_amount = $data['entry1_homeamount'] * $data['entry1_dbcr'];
            	$this->Transactions->Entries->save($entry1);
            	// Iterate for entry 2, 3, ...
            	$entry2 = $this->Transactions->Entries->newEmptyEntity();
            	$entry2->account_id = $data['entry2_accountid'];
            	$entry2->transaction_id = $transaction->id;
            	$entry2->status = 'n';
            	$entry2->real_amount = $data['entry2_realamount'] * $data['entry2_dbcr'];
            	$entry2->home_amount = $data['entry2_homeamount'] * $data['entry2_dbcr'];
            	$this->Transactions->Entries->save($entry2);
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller'=>'Accounts',
                		'action' => 'view', $data['entry1_accountid']]);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            return $this->redirect(['action' => 'index']);
        }
        else if ($this->request->is('get')) {
        	$account1 = $this->Accounts->get(
        		$this->request->getQuery('account_id', '1')
			);
			$this->add_edit_get($transaction, $account1->id);
        } 
        $this->set(compact('transaction', 'account1'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|;void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
    	$this->setupTagFilter();
    	//$tagfilter = $this->viewBuilder()->getVar('tagfilter');
        $transaction = $this->Transactions->get($id, [
            'contain' => ['Entries', 'Entries.Accounts'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->newEmptyEntity();
            $data = $this->request->getData();
            $transaction->id = $id;
            $transaction->date1 = $data['tran_date'];
            $transaction->description = $data['tran_desc'];
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                $index = 1;
                $message = 'Entry id ';
                while (array_key_exists("entry${index}_id", $data)) {
                	if ($data["entry${index}_id"]) {
                		$entry = $this->Transactions->Entries->get($data["entry${index}_id"]);
                	}
                	else {
                		$entry = $this->Transactions->Entries->newEmptyEntity();
                		$entry->transaction_id = $id;
                	}
                	$entry->real_amount = $data["entry${index}_realamount"] * $data["entry${index}_dbcr"];
                	$entry->home_amount = $data["entry${index}_homeamount"] * $data["entry${index}_dbcr"];
                	$entry->account_id = $data["entry${index}_accountid"];
                	//$this->log($data["entry${index}_accountid"], 'info');
                	if ($this->Transactions->Entries->save($entry)) {
                		$en_id = $entry->id;
                		$message = $message . ' ' .  $en_id;
                	}
                	else {
                		$index = -1;
                		break;
                	}
					$index++;
                }
                $this->Flash->success($message . ' updated');
                while ($index>0 && array_key_exists("entry${index}_dbcr", $data)) {
					$this->Flash->success('dbcr' . $index);
					$index++;
                }
                return $this->redirect(['controller'=>'Accounts',
                		'action' => 'view', $data['entry1_accountid']]);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $this->set(compact('transaction'));
        $this->add_edit_get($transaction, 0 /*
        	$this->Accounts->get(
        		$this->request->getQuery('account_id', '1')
			)*/
		);
    }
    
    private function add_edit_get($transaction, $account1_id) {
		$form = new TransactionForm();
    	if ($account1_id) { // add with account1 id stated
    		$account1 = $this->Accounts->get($account1_id);
        	$form->set([
				'tran_date' => new Date(),
				'entry1_dbcr' => 1,
				'entry1_accountid' => $account1_id,
			]);
    	}
    	else { //edit
    		$entry = $transaction->entries[0];
    		$account1 = $entry->account;
        	$form->set([
				'tran_date' => $transaction->date1,
				'tran_desc' => $transaction->description,
				'entry1_accountid' => $account1->id,
			]);
		}
		$form->set([
			'entry1_account' => $account1->code . ':' . $account1->name,
		]);
			$entry1_options = [
				'-1'=>$account1->db_label,
				'1'=>$account1->cr_label
				];
		$homeCurrency = Configure::read('Currency');
		$this->set(compact('form', 'entry1_options', 'homeCurrency'));
		$this->loadModel('Commodities');
		$commodity = $this->Commodities->find()->first();
		$commodities = $this->Commodities->find('list');
        $this->set(compact('commodity', 'commodities'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            $this->Flash->success(__('The transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
