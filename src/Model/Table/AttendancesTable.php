<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Attendances Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AttendanceCodesTable|\Cake\ORM\Association\BelongsTo $AttendanceCodes
 *
 * @method \App\Model\Entity\Attendance get($primaryKey, $options = [])
 * @method \App\Model\Entity\Attendance newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Attendance[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Attendance|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attendance saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attendance patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Attendance[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Attendance findOrCreate($search, callable $callback = null, $options = [])
 */
class AttendancesTable extends Table
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

        $this->setTable('attendances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('AttendanceCodes', [
            'foreignKey' => 'attendance_code_id',
            'joinType' => 'INNER'
        ]);

        $this->hasOne('AttendanceLates', [
            'foreignKey' => 'attendance_id',
            'joinType' => 'INNER'
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
            ->scalar('ip_address')
            ->maxLength('ip_address', 255)
            ->allowEmptyString('ip_address');

        $validator
            ->numeric('gps_lat')
            ->allowEmptyString('gps_lat');

        $validator
            ->numeric('gps_lng')
            ->allowEmptyString('gps_lng');

        $validator
            ->integer('pic')
            ->allowEmptyString('pic');

        $validator
            ->dateTime('cdate')
            ->allowEmptyDateTime('cdate');

        $validator
            ->dateTime('mdate')
            ->allowEmptyDateTime('mdate');

        $validator
            ->integer('status')
            ->allowEmptyString('status');

        $validator
            ->scalar('biometric')
            ->allowEmptyString('biometric');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['attendance_code_id'], 'AttendanceCodes'));

        return $rules;
    }
}
