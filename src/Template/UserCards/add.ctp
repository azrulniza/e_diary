<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UserCard $userCard
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List User Cards'), ['action' => 'index']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Cards'), ['controller' => 'Cards', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Card'), ['controller' => 'Cards', 'action' => 'add']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="userCards form">
            <?= $this->Form->create($userCard, ['role' => 'form']) ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Add User Card') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('user_id', ['options' => $users, 'class' => 'form-control']);
                        echo $this->Form->input('card_id', ['options' => $cards, 'class' => 'form-control']);
                        echo $this->Form->input('mdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('cdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('pic', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('remarks', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'userCards'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
