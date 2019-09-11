<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Designations Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\UserDesignationsTable&\Cake\ORM\Association\HasMany $UserDesignations
 *
 * @method \App\Model\Entity\Designation get($primaryKey, $options = [])
 * @method \App\Model\Entity\Designation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Designation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Designation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Designation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Designation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Designation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Designation findOrCreate($search, callable $callback = null, $options = [])
 */
class DesignationsTable extends Table
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

        $this->setTable('designations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id'
        ]);
        $this->hasMany('UserDesignations', [
            'foreignKey' => 'designation_id'
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->notEmptyString('name');

        $validator
            ->scalar('gred')
            ->maxLength('gred', 255)
            ->notEmptyString('gred');

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
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));

        return $rules;
    }
}
