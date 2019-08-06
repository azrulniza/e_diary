<?php

namespace App\Auth;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ModifiedTinyAuthorize extends \TinyAuth\Auth\TinyAuthorize{
    
    public function allowed($userIds, $controller, $action) {

        //debug($user);
     //   debug([$controller, $action]);

        if (isset($this->_acl[$controller])) {

          //  debug($this->_acl[$controller]);

            if ($action == '') {
                $action = 'index';
            }

            $groups = [];
            if (isset($this->_acl[$controller]['actions'][$action])) {
                $groups = $this->_acl[$controller]['actions'][$action];
            }  
                
            if (isset($this->_acl[$controller]['actions']['*'])) {

                //debug($this->_acl[$controller]['actions']['*']);

                $groups = array_merge($groups, $this->_acl[$controller]['actions']['*']);
            }

            $groups = array_flip($groups);
            //debug($this->_acl[$controller]['actions']['*']);

           // debug($groups);

            if ($groups) {

                if (isset($groups['*'])) {
              //      debug('alowed to *');
                    return true;
                } else {
                    foreach ($userIds as $roleId) {
                        if (isset($groups[$roleId])) {

                      //      debug('alowed');
                            return true;
                        }
                    }
                }
            }
            else{
            //    debug('group not found');
            }
        }
        //  debug('NOT alowed');
        return false;
    }
}
