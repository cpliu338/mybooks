<?php
declare(strict_types=1);

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SettingsForm extends Form {

	protected function _buildSchema(Schema $schema): Schema {
		return $schema->addField('bfDate', 'date');
	}
	
	/**
	Validate for individual fields
	public function validationDefault(Validator $validator): Validator {
		$validator->minLength('tran_desc', 2);
		return $validator;
	}
	*/
	
	/**
	Do anything upon executing the form
	@return false if validation fails
	*/
	protected function _execute(array $data): bool {
		return true;
	}

}
 	 