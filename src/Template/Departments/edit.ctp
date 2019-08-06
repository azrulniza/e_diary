<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Department $department
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $department->id],
                    ['confirm' => __('Are you sure you want to delete this {0}?', 'department')]
                ) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Departments'), ['action' => 'index']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="departments form">
            <?= $this->Form->create($department, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Department') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('date_created', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('date_modified', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'departments'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
