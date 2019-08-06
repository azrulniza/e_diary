<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Menus'), ['action' => 'index']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Menu Groups'), ['controller' => 'MenuGroups', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Menu Group'), ['controller' => 'MenuGroups', 'action' => 'add']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Menus'), ['controller' => 'Menus', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Menu'), ['controller' => 'Menus', 'action' => 'add']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="menus form">
            <?= $this->Form->create($menu, ['role' => 'form']) ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Add Menu') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('menu_group_id', ['options' => $menuGroups, 'class' => 'form-control']);
                        echo $this->Form->input('parent_id', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('ordering', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('label', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('description', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('controller', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('action', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'menus'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
