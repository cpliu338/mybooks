<?php
declare(strict_types=1);

namespace App\Controller\Component;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\Controller\Component;

/**
 * Transactions Controller
 *
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SessionComponent extends Component
{
	private $defaultValues;
	private $defaultArrays;
	
    public function initialize(array $config): void
    {
    	$this->defaultValues = [
		'bfDate' => (FrozenDate::now()->subDays(100))->i18nFormat('yyyy-MM-dd'),
		'transactionDate' => (FrozenDate::now()->subDays(100))->i18nFormat('yyyy-MM-dd'),
		'tagFilter' => "0",
		];
		$this->defaultArrays = [
			'account_tray' => [],
		];
    }	
	public function set(string $key, string $value) {
		if (!array_key_exists($key, $this->defaultValues)) {
			return false;
		}
		else {
			$this->getController()->getRequest()->getSession()->write($key, $value);
			return "$key : $value";
		}
	}
	public function get(string $key) {
		return (array_key_exists($key, $this->defaultValues)) ?
			$this->getController()->getRequest()->getSession()->read($key, $this->defaultValues[$key])
		:
			false;
	}
	public function getArray(string $key) {
		return (array_key_exists($key, $this->defaultArrays)) ?
			$this->getController()->getRequest()->getSession()->read($key, $this->defaultArrays[$key])
		:
			[];
	}
	public function pushArray(string $key, string $value) {
		$ar = $this->getArray($key);
		$max = 5;
		$index = array_search($value, $ar);
		if ($index !== false) {
			$b = array_splice($ar, $index, 1);
			$ar = array_merge($ar, $b);
		}
		else {
			if (count($ar) > $max) {
				array_shift($ar);
			}
			array_push($ar, $value);
		}
		$this->getController()->getRequest()->getSession()->write($key, $ar);
		return $ar;
	}
}
