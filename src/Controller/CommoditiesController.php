<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Commodities Controller
 *
 * @property \App\Model\Table\CommoditiesTable $Commodities
 * @method \App\Model\Entity\Commodity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommoditiesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $commodities = $this->paginate($this->Commodities);

        $this->set(compact('commodities'));
    }

    /**
     * View method
     *
     * @param string|null $id Commodity id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $commodity = $this->Commodities->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('commodity'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $commodity = $this->Commodities->newEmptyEntity();
        if ($this->request->is('post')) {
            $commodity = $this->Commodities->patchEntity($commodity, $this->request->getData());
            if ($this->Commodities->save($commodity)) {
                $this->Flash->success(__('The commodity has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The commodity could not be saved. Please, try again.'));
        }
        $this->set(compact('commodity'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Commodity id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $commodity = $this->Commodities->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $commodity = $this->Commodities->patchEntity($commodity, $this->request->getData());
            if ($this->Commodities->save($commodity)) {
                $this->Flash->success(__('The commodity has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The commodity could not be saved. Please, try again.'));
        }
        $this->set(compact('commodity'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Commodity id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $commodity = $this->Commodities->get($id);
        if ($this->Commodities->delete($commodity)) {
            $this->Flash->success(__('The commodity has been deleted.'));
        } else {
            $this->Flash->error(__('The commodity could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
