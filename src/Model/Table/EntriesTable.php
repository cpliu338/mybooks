<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;
/**
 * Entries Model
 *
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\BelongsTo $Transactions
 *
 * @method \App\Model\Entity\Entry newEmptyEntity()
 * @method \App\Model\Entity\Entry newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Entry[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Entry get($primaryKey, $options = [])
 * @method \App\Model\Entity\Entry findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Entry patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Entry[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Entry|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Entry saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Entry[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Entry[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Entry[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Entry[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class EntriesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('entries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Transactions', [
            'foreignKey' => 'transaction_id',
            'joinType' => 'INNER',
        ]);
    }
    
    /**
    Find entries with a certain label
    @param string $label the label
    @return the list of id, [0] if none found
    */
    public function jsonMatch(string $label) {
    	$sql = "SELECT id ".
    	"FROM entries Entries WHERE JSON_CONTAINS(labels, :label, '$.labels')"
    	;
    	$ar[] = $label;
    	$connection = ConnectionManager::get('default');
    	$coll = new \Cake\Collection\Collection ($connection
    	->execute($sql, ['label' => json_encode($ar)
    			])->fetchAll('assoc'));
    	$ar = $coll->extract('id')->toList();
    	return empty($ar) ? [0] : $ar;
    }
    
    public function allLabels(array $cond) {
    	$accum = [];
    	foreach ($this->find()->contain(['Accounts'])->where($cond) as $entry) {
    		$json = json_decode($entry->labels);
    		if (!property_exists($json, 'labels'))
    			continue;
    		foreach ($json->labels as $label) {
    			$accum[$label] = 1;
    		}
    	}
    	return array_keys($accum);
    }
    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('status')
            ->maxLength('status', 2)
            ->allowEmptyString('status');

        $validator
            ->decimal('real_amount', 2)
            ->allowEmptyString('real_amount');

        $validator
            ->decimal('home_amount', 2)
            ->allowEmptyString('home_amount');

        $validator
            ->date('date2')
            ->allowEmptyDate('date2');

        $validator
            ->allowEmptyString('labels');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['account_id'], 'Accounts'), ['errorField' => 'account_id']);
        $rules->add($rules->existsIn(['transaction_id'], 'Transactions'), ['errorField' => 'transaction_id']);

        return $rules;
    }
}
