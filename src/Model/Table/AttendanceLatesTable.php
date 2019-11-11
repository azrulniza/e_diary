<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AttendanceLates Model
 *
 * @property \App\Model\Table\AttendancesTable|\Cake\ORM\Association\BelongsTo $Attendances
 *
 * @method \App\Model\Entity\AttendanceLate get($primaryKey, $options = [])
 * @method \App\Model\Entity\AttendanceLate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AttendanceLate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AttendanceLate|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AttendanceLate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AttendanceLate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AttendanceLate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AttendanceLate findOrCreate($search, callable $callback = null, $options = [])
 */
class AttendanceLatesTable extends Table
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

        $this->setTable('attendance_lates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Attendances', [
            'foreignKey' => 'attendance_id'
        ]);
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
            ->scalar('late_remark')
            ->maxLength('late_remark', 255)
            ->allowEmptyString('late_remark');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('pic')
            ->allowEmptyString('pic');

        $validator
            ->scalar('pic_remark')
            ->maxLength('pic_remark', 255)
            ->allowEmptyString('pic_remark');

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

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['attendance_id'], 'Attendances'));

        return $rules;
    }
}
