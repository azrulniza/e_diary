<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsersLog Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $email
 * @property string|null $password
 * @property string|null $name
 * @property string|null $ic_number
 * @property string|null $phone
 * @property int|null $report_to
 * @property string|null $reset_password_key
 * @property int|null $status
 * @property \Cake\I18n\Time $cdate
 * @property \Cake\I18n\Time $mdate
 * @property string|null $image
 *
 * @property \App\Model\Entity\User $user
 */
class UsersLog extends Entity
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
        'email' => true,
        'password' => true,
        'name' => true,
        'ic_number' => true,
        'phone' => true,
        'report_to' => true,
        'reset_password_key' => true,
        'status' => true,
        'cdate' => true,
        'mdate' => true,
        'image' => true,
        'user' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];
}
