<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $password
 * @property string|null $name
 * @property int|null $ic_number
 * @property int|null $phone
 * @property int|null $report_to
 * @property string|null $reset_password_key
 * @property int|null $status
 * @property \Cake\I18n\Time $cdate
 * @property \Cake\I18n\Time $mdate
 *
 * @property \App\Model\Entity\AttendanceLog[] $attendance_logs
 * @property \App\Model\Entity\Attendance[] $attendances
 * @property \App\Model\Entity\UserCard[] $user_cards
 * @property \App\Model\Entity\UserCardsLog[] $user_cards_logs
 * @property \App\Model\Entity\UserLeave[] $user_leaves
 * @property \App\Model\Entity\UserLeavesLog[] $user_leaves_logs
 * @property \App\Model\Entity\UserLoginLog[] $user_login_logs
 * @property \App\Model\Entity\UserOrganization[] $user_organizations
 * @property \App\Model\Entity\UserRoleLog[] $user_role_logs
 * @property \App\Model\Entity\Role[] $roles
 */
class User extends Entity
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
        '*' => true,
        'id' => false,
    ];

	protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
/*     protected $_hidden = [
        'password'
    ]; */
}
