<!-- Logo -->
<a href="<?= $this->Url->build( '/' ) ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
	<?php if($oem->short_name !=null){?>
		<span class="logo-mini"><?php echo $oem->short_name ;?></span>
	<?php }else{?>
		<span class="logo-mini"><b>eD</b></span>
	<?php } ?>
    
    <!-- logo for regular state and mobile devices -->
	<?php if($oem->name !=null){?>
		<span class="logo-lg"><?php echo $oem->name;?></span>
	<?php }else{?>
		<span class="logo-lg"><b>e-Diary</b></span>
	<?php } ?>
   
</a>

<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <!-- User Account: style can be found in dropdown.less -->
			<li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                  <?php echo __('Language') ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php
                    $langs = \Cake\Core\Configure::read('Languages');
                    $first = true;
                    ?>

                    <?php foreach ($langs as $ref => $lang): ?>
                        <div class="box-body"><?= $this->Html->link($lang, ['controller' => 'Lang', 'action' => 'switch_to', $ref]) ?></div>
                        <?php $first = false ?>
                    <?php endforeach; ?>
                </ul>
              </li>
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="hidden-xs"><?= $logged['email'] ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <p>
                            <?= $logged['email'] ?>
                            <?php if( isset($logged['created']) ) : ?>
                            <small><?= __('Member since') ?> <?= $logged['created'] ?></small>
                            <?php endif ?>
                        </p>
                    </li>
                    <?php if( isset($logged['extra']) ) : ?>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <div class="col-xs-4 text-center">
                            <a href="#">Followers</a>
                        </div>
                        <div class="col-xs-4 text-center">
                            <a href="#">Sales</a>
                        </div>
                        <div class="col-xs-4 text-center">
                            <a href="#">Friends</a>
                        </div>
                    </li>
                    <?php endif ?>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <?= $this->Html->link( __('Profile'), ['controller'=>'users','action'=>'edit',$logged['id']], ['class'=>'btn btn-default btn-flat'] ) ?>
                        </div>
                        <div class="pull-right">
                            <?= $this->Html->link( __('Sign out'), ['controller'=>'users','action'=>'logout'], ['class'=>'btn btn-default btn-flat'] ) ?>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
