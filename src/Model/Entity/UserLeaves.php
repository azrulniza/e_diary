<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsersLeave Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $date_apply
 * @property string $start_time
 * @property string $end_time
 * @property string|null $reason
 * @property string|null $filename
 * @property int $pic
 * @property int|null $leave_status_id
 * @property \Cake\I18n\Time $cdate
 * @property \Cake\I18n\Time $mdate
 * @property int|null $status
 * @property int|null $leave_type_id
 * @property string|null $remark
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\LeaveStatus $leave_status
 * @property \App\Model\Entity\LeaveType $leave_type
 */
class UserLeaves extends Entity
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
        'date_apply' => true,
        'start_time' => true,
        'end_time' => true,
        'reason' => true,
        'filename' => true,
        'pic' => true,
        'leave_status_id' => true,
        'cdate' => true,
        'mdate' => true,
        'status' => true,
        'leave_type_id' => true,
        'remark' => true,
        'user' => true,
        'leave_status' => true,
        'leave_type' => true
    ];
}
