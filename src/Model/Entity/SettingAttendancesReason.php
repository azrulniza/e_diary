<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SettingAttendancesReason Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $status
 * @property \Cake\I18n\Time|null $cdate
 * @property \Cake\I18n\Time|null $mdate
 */
class SettingAttendancesReason extends Entity
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
        'description' => true,
        'status' => true,
        'cdate' => true,
        'mdate' => true
    ];
}
