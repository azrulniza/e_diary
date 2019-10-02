<div class="row">
    <div class="col-xs-12">
        <div class="settingEmails form">
            <?= $this->Form->create($settingEmail, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Setting Email') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'readonly'=>true]);
                        echo $this->Form->input('subject', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'required'=>true]);
                        echo $this->Form->input('body', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'required'=>true]);
                        echo $this->Form->hidden('email_type_id', ['value'=>$email_type_id]);
                        echo $this->Form->hidden('language_id', ['value'=>$language_id]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'settingEmails'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
