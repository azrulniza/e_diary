<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Designation Entity
 *
 * @property int $id
 * @property string $name
 * @property string $gred
 * @property int|null $organization_id
 * @property int|null $status
 * @property \Cake\I18n\Time|null $cdate
 * @property \Cake\I18n\Time|null $mdate
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\UserDesignation[] $user_designations
 */
class Designation extends Entity
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
        'gred' => true,
        'organization_id' => true,
        'status' => true,
        'cdate' => true,
        'mdate' => true,
        'organization' => true,
        'user_designations' => true
    ];
}
