<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SettingAttendancesReasons Model
 *
 * @method \App\Model\Entity\SettingAttendancesReason get($primaryKey, $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SettingAttendancesReason findOrCreate($search, callable $callback = null, $options = [])
 */
class SettingAttendancesReasonsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('setting_attendances_reasons');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        $validator
            ->integer('status')
            ->allowEmptyString('status');

        $validator
            ->dateTime('cdate')
            ->allowEmptyDateTime('cdate');

        $validator
            ->dateTime('mdate')
            ->allowEmptyDateTime('mdate');

        return $validator;
    }
}
