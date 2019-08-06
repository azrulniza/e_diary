<?php 

$this->loadHelper('Tools.AuthUser');

$em = Cake\ORM\TableRegistry::get('Users');

$user = $em->get(1, [
            'contain' => ['Roles']
        ]);

//debug($user->Roles->find());

//debug($this->AuthUser->user());



debug($this->AuthUser->roles());


debug($this->AuthUser->hasRole(SUPER_ADMIN));

