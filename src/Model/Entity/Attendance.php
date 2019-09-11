<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Attendance Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $attendance_code_id
 * @property string|null $ip_address
 * @property float|null $gps_lat
 * @property float|null $gps_lng
 * @property int|null $pic
 * @property \Cake\I18n\Time $cdate
 * @property \Cake\I18n\Time $mdate
 * @property int|null $status
 * @property string|null $biometric
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\AttendanceCode $attendance_code
 */
class Attendance extends Entity
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
        'attendance_code_id' => true,
        'ip_address' => true,
        'gps_lat' => true,
        'gps_lng' => true,
        'pic' => true,
        'cdate' => true,
        'mdate' => true,
        'status' => true,
        'biometric' => true,
        'user' => true,
        'attendance_code' => true
    ];
}
