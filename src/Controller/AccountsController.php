<?php
declare(strict_types=1);

namespace App\Controller;

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
        $accounts = $this->paginate($this->Accounts);

        $this->set(compact('accounts'));
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
            'contain' => ($this->request->is('ajax') ? [] : ['Tags', 'Entries']),
        ]);
        $this->set(compact('account'));
		$this->viewBuilder()->setOption('serialize', ['account']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $account = $this->Accounts->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $account = $this->Accounts->patchEntity($account, $data);
            $parent = $this->Accounts->get($data['parent_id']);
            $account->code = $this->Accounts->findNextChildCode($parent->code);
            //$this->log($parent_code, 'info');
            //debug($account);
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
    	foreach ($this->Accounts->find('underCode', ['code'=>$c])->order('code') as $acc) {
    		$result[] = ['id'=> $acc->id,
    			'value'=> $acc->code . ' : ' . $acc->name];
    	}
    	$this->set(compact('result'));
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