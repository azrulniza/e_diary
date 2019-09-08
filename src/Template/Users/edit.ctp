<div class="row">
    <div class="col-xs-12">
        <div class="users form">
            <?= $this->Form->create($user, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit User') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('new_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter new password'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false]);
                        echo $this->Form->input('confirm_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter password confirmation'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false]);
                        echo $this->Form->input('ic_number', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('phone', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('report_to', ['class' => 'form-control','placeholder' => __('Enter ...'), 'options' => $reportTo]);
						echo $this->Form->input('organizations._ids', ['class' => 'form-control','options' => $organizations,'multiple'=>false]);                        
						echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $userStatus]);
						echo $this->Form->input('roles._ids', ['options' => $roles,'multiple' => 'checkbox']);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'users'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
