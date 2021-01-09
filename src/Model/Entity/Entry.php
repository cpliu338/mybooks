<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Entry Entity
 *
 * @property int $id
 * @property int $account_id
 * @property int $transaction_id
 * @property string|null $status
 * @property string|null $real_amount
 * @property string|null $home_amount
 * @property \Cake\I18n\FrozenDate|null $date2
 * @property array|null $tags
 *
 * @property \App\Model\Entity\Account $account
 * @property \App\Model\Entity\Transaction $transaction
 */
class Entry extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'account_id' => true,
        'transaction_id' => true,
        'status' => true,
        'real_amount' => true,
        'home_amount' => true,
        'date2' => true,
        'labels' => true,
        'account' => true,
        'transaction' => true,
    ];
    
    public function getAsList($property) {
    	$x = json_decode($this->labels);
    	/*$labels =* / json_decode('{"labels":["a","b"]}');*/
    	if (json_last_error() !== JSON_ERROR_NONE) {
    		return 'error';
    	}
    	if (!property_exists($x, $property))
    		return '';
    	return implode(' ', $x->$property);
    }
}
