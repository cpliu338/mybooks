<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
                            
use App\Model\Entity\Account;

/**
 * Accounts Model
 *
 * @property \App\Model\Table\EntriesTable&\Cake\ORM\Association\HasMany $Entries
 * @property \App\Model\Table\TagsTable&\Cake\ORM\Association\BelongsToMany $Tags
 *
 * @method \App\Model\Entity\Account newEmptyEntity()
 * @method \App\Model\Entity\Account newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Account[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Account get($primaryKey, $options = [])
 * @method \App\Model\Entity\Account findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Account patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Account[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Account|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Account saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AccountsTable extends Table
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

        $this->setTable('accounts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Entries', [
            'foreignKey' => 'account_id',
        ]);
        $this->belongsToMany('Tags', [
            'foreignKey' => 'account_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'accounts_tags',
        ]);
    }
    
    public function findUnderCode(Query $query, array $options) {
    	$code = $options['code'];
    	return $query->where(['code LIKE'=>'%' . $code . '%']);
    }
    
    public function findNextChildCode(string $code1) {
    	$fragments = explode('-', $code1);
    	$x =
    	$this->find()->select('code')->where(['code LIKE'=>  $code1.'-%'])
    		->order('code ASC')->filter(function ($act, $key, $iterator) use ($fragments) {
				return count(explode('-', $act->code))==count($fragments)+1;
    		})->each(function ($act, $key){
    			echo $act->code . "\n";
    		})->reduce(function($lastCode, $act){
    			$frags = explode('-', $act->code);
    			return intval(array_pop($frags));
    		}, 0);
    		/*
    	$rootCode = substr($code1, 0, $lastIndex+1);
    	$lastIndex = stripos($code1, '-');
    	if (count($fragments === 1) return '';
    	echo $rootCode . "\n";
    	$act = $this->find()->where(['code LIKE'=>  $rootCode.'%'])->order('code DESC')->last();
    	if (empty($act)) return '';
    	echo $act->code . "\n";
    	$subCode2 = substr($act->code, $lastIndex+1);
    	return $rootCode . ($subCode2+1);
    	*/
    	return $code1 . sprintf('-%02d',$x+1);
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
            ->scalar('name')
            ->maxLength('name', 32)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('code')
            ->maxLength('code', 32)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('db_label')
            ->maxLength('db_label', 32)
            ->requirePresence('db_label', 'create')
            ->notEmptyString('db_label');

        $validator
            ->scalar('cr_label')
            ->maxLength('cr_label', 32)
            ->requirePresence('cr_label', 'create')
            ->notEmptyString('cr_label');

        $validator
            ->scalar('currency')
            ->maxLength('currency', 8)
            ->requirePresence('currency', 'create')
            ->notEmptyString('currency');

        return $validator;
    }
}
