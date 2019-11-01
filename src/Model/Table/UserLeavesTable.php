<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Userleaves Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\LeaveStatusesTable|\Cake\ORM\Association\BelongsTo $LeaveStatuses
 * @property \App\Model\Table\LeaveTypesTable|\Cake\ORM\Association\BelongsTo $LeaveTypes
 *
 * @method \App\Model\Entity\Userleave get($primaryKey, $options = [])
 * @method \App\Model\Entity\Userleave newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Userleave[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Userleave|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Userleave saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Userleave patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Userleave[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Userleave findOrCreate($search, callable $callback = null, $options = [])
 */
class UserleavesTable extends Table
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

        $this->setTable('user_leaves');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('LeaveStatus', [
            'foreignKey' => 'leave_status_id'
        ]);
        $this->belongsTo('LeaveTypes', [
            'foreignKey' => 'leave_type_id'
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
            ->scalar('date_apply')
            ->maxLength('date_apply', 255)
            ->notEmptyString('date_apply');

        $validator
            ->scalar('start_time')
            ->maxLength('start_time', 255)
            ->notEmptyString('start_time');

        $validator
            ->scalar('end_time')
            ->maxLength('end_time', 255)
            ->notEmptyString('end_time');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 255)
            ->allowEmptyString('reason');

        $validator
            ->scalar('filename')
            ->maxLength('filename', 255)
            ->allowEmptyFile('filename');

        $validator
            ->integer('pic')
            ->requirePresence('pic', 'create')
            ->notEmptyString('pic');

        $validator
            ->dateTime('cdate')
            ->notEmptyDateTime('cdate');

        $validator
            ->dateTime('mdate')
            ->notEmptyDateTime('mdate');

        $validator
            ->integer('status')
            ->allowEmptyString('status');

        $validator
            ->scalar('remark')
            ->maxLength('remark', 255)
            ->allowEmptyString('remark');

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
        $rules->add($rules->existsIn(['leave_status_id'], 'LeaveStatus'));
        $rules->add($rules->existsIn(['leave_type_id'], 'LeaveTypes'));

        return $rules;
    }

    public function overlapInMinutes($startDate1, $endDate1, $startDate2, $endDate2)
    {
        // Figure out which is the later start time
        $lastStart = $startDate1 >= $startDate2 ? $startDate1 : $startDate2;
        // Convert that to an integer
        $lastStart = strtotime($lastStart);

        // Figure out which is the earlier end time
        $firstEnd = $endDate1 <= $endDate2 ? $endDate1 : $endDate2;
        // Convert that to an integer
        $firstEnd = strtotime($firstEnd);

        // Subtract the two, divide by 60 to convert seconds to minutes, and round down
        $overlap = floor( ($firstEnd - $lastStart) / 60 );

        // If the answer is greater than 0 use it.
        // If not, there is no overlap.
        return $overlap > 0 ? $overlap : 0;
    }
}
