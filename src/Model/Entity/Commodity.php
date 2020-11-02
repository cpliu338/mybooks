<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Commodity Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $remark
 * @property string|null $tags
 * @property string|null $home_amount
 * @property string|null $real_amount
 */
class Commodity extends Entity
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
        'remark' => true,
        'tags' => true,
        'home_amount' => true,
        'real_amount' => true,
    ];
}
