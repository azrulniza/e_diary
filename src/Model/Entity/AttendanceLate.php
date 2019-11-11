<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AttendanceLate Entity
 *
 * @property int $id
 * @property int|null $attendance_id
 * @property string|null $late_remark
 * @property int|null $created_by
 * @property int|null $pic
 * @property string|null $pic_remark
 * @property int|null $status
 * @property \Cake\I18n\Time|null $cdate
 * @property \Cake\I18n\Time|null $mdate
 *
 * @property \App\Model\Entity\Attendance $attendance
 */
class AttendanceLate extends Entity
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
        'attendance_id' => true,
        'late_remark' => true,
        'created_by' => true,
        'pic' => true,
        'pic_remark' => true,
        'status' => true,
        'cdate' => true,
        'mdate' => true,
        'attendance' => true
    ];
}
