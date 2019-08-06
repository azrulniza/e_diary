<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Menu Entity.
 *
 * @property int $id
 * @property int $menu_group_id
 * @property \App\Model\Entity\MenuGroup $menu_group
 * @property int $parent_id
 * @property int $ordering
 * @property string $label
 * @property string $description
 * @property string $controller
 * @property string $action
 * @property \App\Model\Entity\Menu[] $menus
 */
class Menu extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
    
	public function isParent($request){
		
		$menusTable = TableRegistry::get('Menus');
		$menu = $menusTable->find()->select(['parent_id'])->where(['controller'=>$request->controller])->where(['action'=>$request->action])->first();
		
		if($menu){
			return $menu->parent_id;
		}else if($request->controller == $this->controller AND $request->controller == "Dashboards" AND $request->action == "index"){
			return 1;
		}else{
			return 0;
		}
	}
	
    public function isActive($request){
        
        if($this->action == ''){
			if ($this->controller=="Clients" && ($request->controller=="ClientSubscriptions" || $request->controller=="ClientCmses")) { return true; }
			if ($this->controller=="PageTemplates" && ($request->controller=="PageTemplatesClients")){ return true; }
			if ($this->label=="Settings" && ($request->controller=="SubscriptionPackageResellers" || $request->controller=="SubscriptionPackageClients" || $request->controller=="NotificationEmailTemplates")){ return true; }
            return $request->controller == $this->controller;
			
        }
        else{
			
            return $request->controller == $this->controller AND $request->action == $this->action;
		}
    }
    
    public function hasSubmenu($menus){
        if(isset($menus[$this->id]) AND count($menus[$this->id]) > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function path(){
        return \Cake\Routing\Router::url(['controller'=>$this->controller, 'action'=>$this->action]);
    } 
    
    public function iconClass(){
        return $this->icon ? $this->icon: 'fa-square-o';
    }
}
