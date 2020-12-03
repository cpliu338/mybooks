<?php
declare(strict_types=1);

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class TransactionForm extends Form {

	protected function _buildSchema(Schema $schema): Schema {
		return $schema->addField('tran_date', 'date')
			->addField('tran_desc', 'string');
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
 	 