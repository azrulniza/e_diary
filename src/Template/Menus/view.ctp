<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('Edit Menu'), ['action' => 'edit', $menu->id]) ?> </li>
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(__('Delete Menu'), ['action' => 'delete', $menu->id], ['confirm' => __('Are you sure you want to delete # {0}?', $menu->id)]) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Menus'), ['action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Menu'), ['action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Menu Groups'), ['controller' => 'MenuGroups', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Menu Group'), ['controller' => 'MenuGroups', 'action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Menus'), ['controller' => 'Menus', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Menu'), ['controller' => 'Menus', 'action' => 'add']) ?> </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="menus view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-menu"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($menu->label) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Menu Group') ?></dt>
                            <dd><?= $menu->has('menu_group') ? $this->Html->link($menu->menu_group->name, ['controller' => 'MenuGroups', 'action' => 'view', $menu->menu_group->id]) : '' ?></dd>
                            <dt><?= __('Label') ?></dt>
                            <dd><?= h($menu->label) ?></dd>
                            <dt><?= __('Description') ?></dt>
                            <dd><?= h($menu->description) ?></dd>
                            <dt><?= __('Controller') ?></dt>
                            <dd><?= h($menu->controller) ?></dd>
                            <dt><?= __('Action') ?></dt>
                            <dd><?= h($menu->action) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($menu->id) ?></dd>
                            <dt><?= __('Parent Id') ?></dt>
                            <dd><?= $this->Number->format($menu->parent_id) ?></dd>
                            <dt><?= __('Ordering') ?></dt>
                            <dd><?= $this->Number->format($menu->ordering) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
