<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SettingEmail $settingEmail
 */
?>
<div class="row">
    <div class="col-xs-10">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Setting Emails'), ['action' => 'index']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="settingEmails form">
            <?= $this->Form->create($settingEmail, ['role' => 'form']) ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Add Setting Email') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('subject', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('body', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('email_type_id', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('language_id', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('cdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('mdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'settingEmails'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
