<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property |\Cake\ORM\Association\HasMany $AttendanceLogs
 * @property |\Cake\ORM\Association\HasMany $Attendances
 * @property |\Cake\ORM\Association\HasMany $UserCards
 * @property |\Cake\ORM\Association\HasMany $UserCardsLogs
 * @property |\Cake\ORM\Association\HasMany $UserLeaves
 * @property |\Cake\ORM\Association\HasMany $UserLeavesLogs
 * @property |\Cake\ORM\Association\HasMany $UserLoginLogs
 * @property |\Cake\ORM\Association\HasMany $UserRoleLogs
 * @property \App\Model\Table\OrganizationsTable|\Cake\ORM\Association\BelongsToMany $Organizations
 * @property \App\Model\Table\RolesTable|\Cake\ORM\Association\BelongsToMany $Roles
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
		
		$this->addBehavior('Timestamp');
		$this->addBehavior('Captcha.Captcha', ['field'=>'<captcha>']);

        $this->hasMany('AttendanceLogs', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Attendances', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserCards', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserCardsLogs', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserLeaves', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserLeavesLogs', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserLoginLogs', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserRoleLogs', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsToMany('Organizations', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'organization_id',
            'joinTable' => 'users_organizations'
        ]);
        $this->belongsToMany('Roles', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'users_roles'
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
                ->add('id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('id', 'create');

        $validator
                ->add('email', 'valid', ['rule' => 'email'])
                ->requirePresence('email', 'create')
                ->notEmpty('email')
                ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('Sorry. The email address you entered is already in use.')]);
		$validator
                ->add('reconfirm_email', 'compareWith', [
                    'rule' => ['compareWith', 'email'],
                    'message' => 'Email not equal.'
				])
				->requirePresence('email', 'create')
                ->notEmpty('reconfirm_email');
				
		$validator
				->requirePresence('password', 'create')
				->notEmpty('password')
				->allowEmpty('password', 'update');

		$validator
                ->add('confirm_password', 'compareWith', [
                    'rule' => ['compareWith', 'password'],
                    'message' => 'Passwords not equal.'
        ])
                ->allowEmpty('confirm_password', 'update');
				
        $validator
            ->allowEmptyString('name');

        $validator
            ->allowEmptyString('ic_number');

        $validator
            ->allowEmptyString('phone');

        $validator
            ->integer('report_to')
            ->allowEmptyString('report_to');

        $validator
            ->allowEmptyString('reset_password_key');

        $validator
                ->requirePresence('status', 'create')
                ->notEmpty('status');

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
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }
}
