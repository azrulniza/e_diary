<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SettingEmail Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $en_subject
 * @property string|null $my_subject
 * @property string|null $en_body
 * @property string|null $my_body
 * @property int|null $status
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
        'en_subject' => true,
        'my_subject' => true,
        'en_body' => true,
        'my_body' => true,
        'status' => true,
        'cdate' => true,
        'mdate' => true
    ];
}
