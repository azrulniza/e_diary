<?php 

$this->loadHelper('Tools.AuthUser');

$em = Cake\ORM\TableRegistry::get('Users');

$user = $em->get($this->request->session()->read('Auth.User.id'), [
            'contain' => ['Roles']
        ]);
?>
<?php if( !empty($menus[0]) ) : ?>
    <?php foreach( $menus[0] as $menu ) : ?>
        <li class="<?= ($menu->id == $menu->isParent($this->request)) ? 'active' : '' ?>  <?= $menu->hasSubmenu($menus) ? '' : 'treeview' ?>">
				<a href="<?=$menu->path() ?>" title="<?php echo __($menu->description);?>">
					<i class="fa <?=$menu->iconClass()?>"></i>
					<span><?= __( $menu->label ) ?></span>
					<?php if( !empty($menu->hasSubmenu($menus)) ) : ?>
						<i class="fa fa-angle-left pull-right"></i>
					<?php endif ?>
				</a>
				<?php if( !empty($menu->hasSubmenu($menus)) ) : ?>
					<ul class="treeview-menu">
						<?php foreach( $menus[$menu->id] as $menu_li ) : ?>
							<li class="<?= $menu_li->isActive($this->request) ? 'active' : '' ?>">
								<a href="<?=$menu_li->path()// $this->Url->build( $subitem['controller'] ) ?>" title="<?php echo __($menu_li->description);?>"><i class="fa <?= $menu_li->iconClass() ?>"></i> <?= __( $menu_li->label ) ?></a>
							</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
        </li>
    <?php endforeach ?>
<?php endif ?>
