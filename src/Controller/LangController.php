<?php
namespace App\Controller;

use Cake\I18n\I18n;
use App\Controller\AppController;

/**
 * Language Switcher Controller
 *
 */
class LangController extends AppController
{

    /**
     * Switch lang method
     *
     * @return \Cake\Network\Response|null
     */
    public function switch_to($lang)
    {
        $session = $this->request->session();
        
        $langs = \Cake\Core\Configure::read('Languages');

        $cont=$this->request->query('cont');
        $act=$this->request->query('act');
        
        if(isset($langs[$lang])){
            $session->write('Config.language', $lang);
            I18n::locale($lang);
            //$this->redirect('/');
        
            return $this->redirect(['controller'=>$cont,'action' => $act]);
        }
        else{
            //@TODO correctly display error
            $this->viewBuilder()->layout('Element/Flash/error');
        }
    }
}
