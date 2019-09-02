<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Organization Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property int|null $phone
 * @property string|null $email
 * @property \Cake\I18n\Time|null $cdate
 * @property \Cake\I18n\Time $mdate
 * @property int|null $status
 *
 * @property \App\Model\Entity\UserOrganization[] $user_organizations
 */
class Organization extends Entity
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
        'address' => true,
        'phone' => true,
        'email' => true,
        'cdate' => true,
        'mdate' => true,
        'status' => true,
        'user_organizations' => true
    ];
}
