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
        $transaction = $this->Transactions->newEmptyEntity();
        if ($this->request->is('post') && !$this->request->is('ajax')) {
        	//debug(
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
            	$entry1->home_amount = $data['entry1_realamount'] * $data['entry1_dbcr'];
            	$this->Transactions->Entries->save($entry1);
            	$entry2 = $this->Transactions->Entries->newEmptyEntity();
            	$entry2->account_id = $data['entry2_accountid'];
            	$entry2->transaction_id = $transaction->id;
            	$entry2->status = 'n';
            	$entry2->real_amount = $data['entry2_realamount'] * $data['entry2_dbcr'];
            	$entry2->home_amount = $data['entry2_realamount'] * $data['entry2_dbcr'];
            	$this->Transactions->Entries->save($entry2);
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['controller'=>'Accounts',
                		'action' => 'view', $data['entry1_accountid']]);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
            return $this->redirect(['action' => 'index']);
        }
        else if ($this->request->is('ajax') && $this->request->is('get')) {
        	$form = new TransactionForm();
        	$this->loadModel('Accounts');
        	$account1 = $this->Accounts->get(
        		$this->request->getQuery('account_id', '1')
			);
        	$form->set([
				'tran_date' => new Date(),
				'entry1_dbcr' => $this->request->getQuery('db') ? -1 : 1,
				'entry1_accountid' => $account1->id,
				'entry1_accountid' => $account1->id,
				'entry1_account' => $account1->code . ':' . $account1->name,
				//'account_options' => ['-1'=>$account1->db_label, '1'=>$account1->cr_label],
			]);
			$entry1_options = [
				'-1'=>$account1->db_label,
				'1'=>$account1->cr_label
				];
			$homeCurrency = Configure::read('HomeCurrency');
        	$this->set(compact('form', 'entry1_options', 'account1', 'homeCurrency'));
        }
        else { 
        	$this->loadModel('Accounts');
        	$accounts = $this->Accounts->find('list', ['limit' => 200]);
        	$entry1 = $this->Transactions->Entries->newEntity(['account_id'=>1]);
        	$entry2 = $this->Transactions->Entries->newEmptyEntity();
        }
        $this->set(compact('transaction'));
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
        $transaction = $this->Transactions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $this->set(compact('transaction'));
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
