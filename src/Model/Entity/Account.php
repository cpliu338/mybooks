<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Account Entity
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $db_label
 * @property string $cr_label
 * @property string $currency
 *
 * @property \App\Model\Entity\Entry[] $entries
 * @property \App\Model\Entity\Tag[] $tags
 */
class Account extends Entity
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
        'name' => true,
        'code' => true,
        'db_label' => true,
        'cr_label' => true,
        'currency' => true,
        'entries' => true,
        'tags' => true,
    ];
}
