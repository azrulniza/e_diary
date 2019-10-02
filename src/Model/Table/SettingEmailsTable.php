<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SettingEmails Model
 *
 * @property |\Cake\ORM\Association\BelongsTo $EmailTypes
 * @property |\Cake\ORM\Association\BelongsTo $Languages
 *
 * @method \App\Model\Entity\SettingEmail get($primaryKey, $options = [])
 * @method \App\Model\Entity\SettingEmail newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SettingEmail[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SettingEmail|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SettingEmail saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SettingEmail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SettingEmail[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SettingEmail findOrCreate($search, callable $callback = null, $options = [])
 */
class SettingEmailsTable extends Table
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

        $this->setTable('setting_emails');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Languages', [
            'foreignKey' => 'language_id'
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('subject')
            ->allowEmptyString('subject');

        $validator
            ->scalar('body')
            ->allowEmptyString('body');

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
        return $rules;
    }
}
