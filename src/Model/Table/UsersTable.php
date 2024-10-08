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

        $this->belongsTo('Grades', [
            'foreignKey' => 'grade_id'
        ]);
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
        $this->hasMany('UserDesignations', [
            'foreignKey' => 'user_id'
        ]);
		$this->hasMany('UserOrganizations', [
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
                    'message' => __('Email not equal.')
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
                    'message' => __('Passwords not equal.')
        ])
                ->allowEmpty('confirm_password', 'update');
				
        $validator
            ->allowEmptyString('name');

        $validator
            ->allowEmptyString('ic_number')
			->add('ic_number', 'valid', ['rule' => 'numeric'])
                ->requirePresence('ic_number', 'create')
                ->notEmpty('ic_number')
                ->add('ic_number', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('Sorry. IC already exist')]);
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
				
        $validator
            ->integer('skim')
            ->allowEmptyString('skim');
			
		$validator
            ->integer('card_no')
            ->allowEmptyString('card_no');
    
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
		$rules->add($rules->isUnique(['ic_number']));
		$rules->add($rules->existsIn(['grade_id'], 'Grades'));
        return $rules;
    }
	public function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		
		return $ipaddress;
	}
}
