<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Core\Configure;
/**
 * Accounts Controller
 *
 * @property \App\Model\Table\AccountsTable $Accounts
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccountsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
    	$this->setupTagFilter();//debug($this);
    	$tagfilter = $this->viewBuilder()->getVar('tagfilter');      
    	$nameFilter=$this->request->getQuery('nameFilter');
    	if (count($tagfilter) == 0) $tagfilter = [0];
    	$query = $this->Accounts->find()
    		->select(['Accounts.id', 'Accounts.currency', 'Accounts.code',
    			'Accounts.name'	])
    		->where(['Accounts.name LIKE' =>'%' . $nameFilter . '%'])
    		->distinct('Accounts.id');
        $accounts = (in_array(0, $tagfilter)) ?
        	$this->paginate($query) :
        	$this->paginate($query->matching('Tags', function ($q) use ($tagfilter) {
        		return $q->where([
					'Tags.id IN' => $tagfilter
					]);
			})
		);
        $this->set(compact('accounts','nameFilter'));
    }

    public function setFilter() {
    	$tagFilter = $this->request->input('json_decode');
    	if (in_array(0, $tagFilter) || empty($tagFilter))
    		$tagFilter = [0];
    	$this->Session->set('tagFilter', implode(",", $tagFilter));
    	$this->set(compact('tagFilter'));
		$this->viewBuilder()->setOption('serialize', ['tagFilter']);
    }

    /**
     * View method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $account = $this->Accounts->get($id, [
            'contain' => ($this->request->is('ajax') ? [] : ['Tags']),
        ]);//debug($account->get('currency'));
        $bfDate = $this->Session->get('bfDate');
        $condition = ['Entries.account_id'=>$account->id];
        $account->entries = $this->entriesInPeriod(array_merge($condition, ['Transactions.date1 >=' => $bfDate]),
        	$bfDate);
		$bf = $this->aggregateBefore(array_merge($condition, ['Transactions.date1 <' => $bfDate]), 
			$bfDate, 'home_amount');
		$this->loadModel('Commodities');
		$commodity = $this->Commodities->find()->first();
		$commodities = $this->Commodities->find('list');
		$currency = Configure::read('Currency');
        $this->set(compact('account', 'bf', 'bfDate', 'commodity', 'commodities', 'currency'));
		$this->viewBuilder()->setOption('serialize', ['account']);
    }
    /**
     * Sumary View method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function summaryView($id = null)
    {
        $account = $this->Accounts->get($id, [
            'contain' => ($this->request->is('ajax') ? [] : ['Tags']),
        ]);
        $bfDate = $this->Session->get('bfDate');
        $condition = ['Accounts.code LIKE'=>$account->code . '%'];
        $account->entries = $this->entriesInPeriod(array_merge($condition, ['Transactions.date1 >=' => $bfDate]),
        	$bfDate);
		$bf = $this->aggregateBefore(array_merge($condition, ['Transactions.date1 <' => $bfDate]), 
			$bfDate, 'real_amount');
        $this->set(compact('account', 'bf', 'bfDate'));
    }

	public function findLabels(string $id) {
		$summary = $this->request->getQuery('summary');
		if ($summary) {
			$account = $this->Accounts->get($id);
			$condition = ['Accounts.code LIKE'=>$account->code . '%'];
		}
		else {
			$condition = ['account_id'=>$id];
		}
        $labels = $this->Accounts->Entries->allLabels($condition);
        $this->set(compact('summary', 'labels'));
		$this->viewBuilder()->setOption('serialize', ['summary', 'labels']);     
	}

    public function checkBalance($account_id) {
    	$acc = $this->Accounts->get($account_id);
    	$first_code = ($acc->code)[0];
    	$balance = $this->aggregateBefore(['Accounts.id'=>$account_id,
    			'Transactions.date1 >=' => '2020-08-01'], '', 'home_amount');
    	if (strpos('15', $first_code) === false) {;
    		// inc = positive
    		$balance = $balance < -0.0001 ?
    			sprintf('%.2f DB', 0-$balance) :
    			sprintf('%.2f &nbsp;&nbsp;', $balance) ;
    	}
    	else {
    		// inc = negative
    		$balance = $balance > 0.0001 ?
    			sprintf('%.2f CR', $balance) :
    			sprintf('%.2f &nbsp;&nbsp;', 0-$balance) ;
    	}
        $this->set(compact('balance', 'account_id'));
		$this->viewBuilder()->setOption('serialize', ['balance', 'account_id']);
    }
    
    private function entriesInPeriod($condition, $bfDate) {
    	return  $this->Accounts->Entries->find()->contain(['Accounts', 'Transactions'])->
        	where($condition)->order('Transactions.date1')->order('Transactions.date1');
    }

    private function aggregateBefore($condition, $bfDate, $amount) {
        $query = $this->Accounts->Entries->find()->contain(['Accounts', 'Transactions']);
        $query->where($condition);
        $aggregate = 0;
        foreach ($query->select([
        		'Entries.account_id', 
        		'total'=> $query->func()->sum("Entries.$amount")])->
        		group('Entries.account_id') as $row) {
        	$aggregate += $row->total;
		}
		return $aggregate;
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
        $account = $this->Accounts->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $account = $this->Accounts->patchEntity($account, $data);
            $parent = $this->Accounts->get($data['parent_id']);
            $account->code = $this->Accounts->findNextChildCode($parent->code);
            if ($this->request->is('ajax')) {
            	$this->set(compact('account'));
            	$this->viewBuilder()->setOption('serialize', ['account']);
            	return;
            }
            if ($this->Accounts->save($account)) {
//            if (empty($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $tags = $this->Accounts->Tags->find('list', ['limit' => 200]);
        $account->parentCode = '';
        $this->set(compact('account', 'tags'));
    }
    
    public function suggest() {
    	$c=$this->request->getQuery('term');
    	$result = [];
    	$tagfilter = explode(',', $this->Session->get('tagFilter'));
    	$query = $this->Accounts->find()->where( ['code LIKE'=>"$c%"]);
    	if (count($tagfilter) == 0) $tagfilter = [0];
    	if (!in_array(0, $tagfilter))
    	$query =
    	$query->matching('Tags', function ($q) use ($tagfilter) {
			return $q->where(['Tags.id IN' => $tagfilter]);
    	});
    	/**/
    	foreach ($query->order('code') as $acc) {
    		$result[] = ['id'=> $acc->id,
    			'value'=> $acc->code . ' : ' . $acc->name];
    	}
    	//$this->set(compact('result'));
        //$this->viewBuilder()->setOption('serialize', ['result']);
        $response = $this->response->withType('application/json')
        	->withStringBody(json_encode($result));
        return $response;
    }

    /**
     * Edit method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $account = $this->Accounts->get($id, [
            'contain' => ['Tags'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData());
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $tags = $this->Accounts->Tags->find('list', ['limit' => 200]);
        $this->set(compact('account', 'tags'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $account = $this->Accounts->get($id);
        if ($this->Accounts->delete($account)) {
            $this->Flash->success(__('The account has been deleted.'));
        } else {
            $this->Flash->error(__('The account could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
