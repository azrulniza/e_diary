<?php

namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 */
class UsersTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('users');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('Captcha.Captcha', ['field'=>'<captcha>']);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id'
        ]);
        $this->belongsToMany('Roles', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'users_roles'
        ]);

        $this->belongsToMany('Clients', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'client_id',
            'joinTable' => 'users_clients'
        ]);

        $this->belongsToMany('Resellers', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'reseller_id',
            'joinTable' => 'users_resellers'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
                ->add('id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('id', 'create');

        $validator
                ->requirePresence('password', 'create')
                ->notEmpty('password')
                ->allowEmpty('password', 'update');

        $validator
                ->requirePresence('status', 'create')
                ->notEmpty('status');

        $validator
                ->add('confirm_password', 'compareWith', [
                    'rule' => ['compareWith', 'password'],
                    'message' => 'Passwords not equal.'
        ])
                ->allowEmpty('confirm_password', 'update');

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

        /* $validator->requirePresence('roles', 'create')
                ->requirePresence('roles', 'update')
                ->notEmpty('email');

        $validator->add('roles', 'custom', [
            'rule' => function($value, $context) {
                return (!empty($value['_ids']) && is_array($value['_ids']));
            },
            'message' => 'Please assign a role to user.'
        ]); */

        $validator
                ->allowEmpty('name');

        $validator
                ->allowEmpty('reset_password_key');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->isUnique(['email']));
        //$rules->add($rules->existsIn(['role_id'], 'Roles'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        return $rules;
    }
	
	public function generateRandomString($length = 16) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

}
