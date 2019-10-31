<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;

class BasepathHelper extends Helper
{
    public function getBasePath()
    {
        Configure::load('domainconfig', 'default');
        $domainName = Configure::read('domain.domain_name');
        
        return $domainName;
    }
}

?>