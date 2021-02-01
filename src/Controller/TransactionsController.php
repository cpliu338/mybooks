<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\I18n\Date;
use Cake\Core\Configure;
use App\Form\TransactionForm;
use Cake\Datasource\ConnectionManager;

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
            	$this->Session->set('transactionDate', $transaction->date1);
            	$tran_id = $transaction->id;
            	/*
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
            	*/
            	$index = 1;
            	while (array_key_exists("entry${index}_accountid", $data)) {
					$entry = $this->Transactions->Entries->newEmptyEntity();
					$entry->account_id = $data["entry${index}_accountid"];
					$entry->transaction_id = $tran_id;
					$entry->status = 'n';
					$entry->real_amount = $data["entry${index}_realamount"] * $data["entry${index}_dbcr"];
					$entry->home_amount = $data["entry${index}_homeamount"] * $data["entry${index}_dbcr"];
					$this->Transactions->Entries->save($entry);
					$index++;
            	}
            	
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
            $transaction->date1 = $this->Session->get('transactionDate');
			$this->add_edit_get($transaction, $account1->id);
        } 
        $this->set(compact('transaction', 'account1'));
    }
    
    public function earnsInterest() {
		$transaction = $this->Transactions->newEmptyEntity();
		$this->loadModel('Entries');          
		$account1_id = $this->request->getQuery('account_id', '1'); 
		$account1 = $this->Accounts->get(
			$account1_id,
			['contain'=>'Tags']
		);
		$account0 = $this->Accounts->findByCode(substr($account1->code, 0, 4))->first();
/*		$this->Flash->success("code like " . 
		$account0 ? $account0->name : "Not found");
				return $this->redirect(['controller'=>'Accounts',
					'action' => 'view', $account1_id]);*/
        if ($this->request->is('post') && !$this->request->is('ajax')) {
        	$data = $this->request->getData();
        	$connection = ConnectionManager::get('default');
        	$error = null;
			$transaction = $this->Transactions->patchEntity($transaction, $data);
        	$result = $connection->transactional(function() use 
        		($transaction, $data, $account1, $account0, &$error) {
				if ($data['entry1_homeamount'] != $data['entry2_homeamount']
					|| $data['entry1_homeamount']<0.005
					|| $data['entry2_homeamount']<0.005
					/*
					|| !array_key_exists('entry1_homeamount', $data)
					|| !array_key_exists('entry2_homeamount', $data)
					*/
					) {
					//'Transaction not balanced';
					return false;
				}
				if (!$this->Transactions->save($transaction)) {
					$error = 'Error saving transaction';
					return false;
				}
// NEED TO CATER FOR DIFF CURRENCY
				$entry1 = $this->Entries->newEmptyEntity();
				$entry1->home_amount = 0-$data['entry1_homeamount'];
				$entry1->real_amount = 0-$data['entry1_homeamount'];
				$entry1->account_id = $data['entry1_accountid'];
				$entry1->transaction_id = $transaction->id;
				$entry1->status = 'n';
				$entry1->labels = '{}';
				if (!$this->Entries->save($entry1)) {
					$error = 'Error saving entry 1';
					return false;
				}
				$entry2 = $this->Entries->newEmptyEntity();
				$entry2->home_amount = $data['entry2_homeamount'];
				$entry2->real_amount = $data['entry2_homeamount'];
				$entry2->account_id = $data['entry2_accountid'];
				$entry2->transaction_id = $transaction->id;
				$entry2->status = 'n';
				$labels = ['labels'=> [$account0 ? $account0->name : "Not found",
					$account1->name]];
				$entry2->labels = json_encode($labels);
				if (!$this->Entries->save($entry2)) {
					$error = 'Error saving entry 2';
					return false;
				}
				return true;
			});
			if (!$result) {
				$this->Flash->error('Error:' . $error); 
			}
			else {
				$this->Flash->success(__('The transaction has been saved.'));
				return $this->redirect(['controller'=>'Accounts',
					'action' => 'view', $data['entry1_accountid']]);
			}
        }
        $stock = $account1->containTags(['Stock']);
            $transaction->date1 = $this->Session->get('transactionDate');
            $transaction->description = $stock ? 'Dividend paid' : 'Interest earned';
            $this->loadModel('Accounts');
			// entry2 is interest
			$entry2 = $this->Entries->newEmptyEntity();
			if ($stock) {
				$account2_choices = $this->Accounts->find('list')->where([
					'name LIKE' => '%Dividend%'
				]);
				$entry2->account_id = 0; // default to first choice
			}
			else {
				$entry2->account_id = $this->Accounts->findByName($account1->currency . 'BankInterest')->first()->id;
				$account2_choices = $this->Accounts->find('list')->where([
					'currency' => $account1->currency,
					'name LIKE' => '%Interest%'
				]);
			}
			// entry1 is bank
			$entry1 = $this->Entries->newEmptyEntity();
		if ($stock) {
			$query = $this->Accounts->find('list');
			$query->matching('Tags', function ($q) {
				return $q->where(['Tags.name' => 'Bank',
						'Accounts.currency'=>'HKD']);
			});
			//debug($query->toArray());
			$account1_choices = $this->Accounts->find('list')->where([
				'id IN' => array_keys($query->toArray())
			]);
			$entry1->account_id = 0;//$account1->id;
		}
		else {
			$entry1->account_id = $account1->id;
			$account1_choices = $this->Accounts->find('list')->where([
				'currency' => $account1->currency,
				'code LIKE' => substr($account1->code, 0, 4) . '%'
			]);
		}
		$this->set(compact('account1', 'transaction',
			'entry1', 'account1_choices',
			'entry2', 'account2_choices'
		));
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
				'tran_date' => $transaction->date1,
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
