<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Cache\Cache;

class ToolsController extends Controller
{

    function clear_cache()
    {
		apc_clear_cache();
    }
    
    function clear_cache_b()
    {
        Cache::clear(false);
        Cache::delete('tiny_auth_acl','_cake_core_');
    }

    function version()
    {
        
    }
}
