<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SettingEmail Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $subject
 * @property string|null $body
 * @property int|null $status
 * @property int|null $email_type_id
 * @property int|null $language_id
 * @property \Cake\I18n\Time|null $cdate
 * @property \Cake\I18n\Time|null $mdate
 */
class SettingEmail extends Entity
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
        'subject' => true,
        'body' => true,
        'status' => true,
        'email_type_id' => true,
        'language_id' => true,
        'cdate' => true,
        'mdate' => true
    ];
}
