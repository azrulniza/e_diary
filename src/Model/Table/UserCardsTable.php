<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserCards Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CardsTable|\Cake\ORM\Association\BelongsTo $Cards
 *
 * @method \App\Model\Entity\UserCard get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserCard newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserCard[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserCard|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserCard saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserCard patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserCard[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserCard findOrCreate($search, callable $callback = null, $options = [])
 */
class UserCardsTable extends Table
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

        $this->setTable('user_cards');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Cards', [
            'foreignKey' => 'card_id',
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
            ->dateTime('mdate')
            ->notEmptyDateTime('mdate');

        $validator
            ->dateTime('cdate')
            ->notEmptyDateTime('cdate');

        $validator
            ->integer('status')
            ->notEmptyString('status');

        $validator
            ->integer('pic')
            ->requirePresence('pic', 'create')
            ->notEmptyString('pic');

        $validator
            ->scalar('remarks')
            ->maxLength('remarks', 255)
            ->allowEmptyString('remarks');

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
        $rules->add($rules->existsIn(['card_id'], 'Cards'));

        return $rules;
    }
}
