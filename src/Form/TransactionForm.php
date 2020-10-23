<?php
declare(strict_types=1);

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class TransactionForm extends Form {

	protected function _buildSchema(Schema $schema): Schema {
		return $schema->addField('tran_date', 'date')
			->addField('tran_desc', 'string')
			->addField('tran_id', 'integer')
			->addField('entry1_id', 'integer')
			->addField('entry1_accountcode', 'string')
			->addField('entry1_dbcr', [
					'type'=>'string', 'length'=>2])
			->addField('entry1_status', [
					'type'=>'string', 'default'=>'n'])
			->addField('entry1_realamount', [
					'type'=>'decimal', 'precision'=>2])
			->addField('entry1_homeamount', [
					'type'=>'decimal', 'precision'=>2])
			->addField('entry2_id', 'integer')
			->addField('entry2_accountcode', 'string')
			->addField('entry2_dbcr', [
					'type'=>'string', 'length'=>2])
			->addField('entry2_status', [
					'type'=>'string', 'default'=>'n'])
			->addField('entry2_realamount', [
					'type'=>'decimal', 'precision'=>2])
			->addField('entry2_homeamount', [
					'type'=>'decimal', 'precision'=>2]);
	}
	
	/**
	Validate for individual fields
	*/
	public function validationDefault(Validator $validator): Validator {
		$validator->minLength('tran_desc', 2);
		return $validator;
	}
	
	/**
	Do anything upon executing the form
	@return false if validation fails
	*/
	protected function _execute(array $data): bool {
		return true;
	}

}
 	 