<?php
namespace App\Model\Table;

use App\Model\Entity\Role;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Users
 */
class RolesTable extends Table
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

        $this->table('roles');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Users', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_roles'
        ]);
        
        $this->belongsToMany('Menus', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'menu_id',
            'joinTable' => 'menus_permissions'
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
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->requirePresence('alias', 'create')
            ->notEmpty('alias');

        return $validator;
    }
    
    public function hasRole($keywords){
        foreach ($this->currentUserRoles as $role){
            if(in_array($role->name, $keywords)){
                return true;
            }
        }
        return false;
    }
    
    private $currentUserRoles = [];
    public function initRolesChecker($roles){
        $this->currentUserRoles = $roles;
        return $this;
    }
        
    
}
