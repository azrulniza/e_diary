<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserCard Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $card_id
 * @property \Cake\I18n\Time $mdate
 * @property \Cake\I18n\Time $cdate
 * @property int $status
 * @property int $pic
 * @property string|null $remarks
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Card $card
 */
class UserCard extends Entity
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
        'card_id' => true,
        'mdate' => true,
        'cdate' => true,
        'status' => true,
        'pic' => true,
        'remarks' => true,
        'user' => true,
        'card' => true
    ];
}
