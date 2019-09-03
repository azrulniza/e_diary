<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserOrganization Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $organization_id
 * @property \Cake\I18n\Time $cdate
 * @property \Cake\I18n\Time $mdate
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Organization $organization
 */
class UserOrganization extends Entity
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
        'user_id' => true,
        'organization_id' => true,
        'cdate' => true,
        'mdate' => true,
        'user' => true,
        'organization' => true
    ];
}
