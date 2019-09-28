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
						?>
						<br>
						<div class="form-group">
							<fieldset>
								<legend><?= __('Email Template - English') ?></legend>
								<?php
									echo $this->Form->input('en_subject', ['label'=>__('Subject'), 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'style'=>'width:50%;height:100px;','required'=>true]);
									echo $this->Form->input('en_body', ['label'=>__('Body'), 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'style'=>'width:50%;','required'=>true]);
								?>
							</fieldset>
						</div>
						<br>
						<div class="form-group">
							<fieldset>
								<legend><?= __('Email Template - Malay') ?></legend>
								<?php
									echo $this->Form->input('my_subject', ['label'=>__('Subject'), 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'style'=>'width:50%;height:100px;','required'=>true]);
									echo $this->Form->input('my_body', ['label'=>__('Body'), 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'style'=>'width:50%;','required'=>true]);
								?>
							</fieldset>
						</div>
						<p><i><b>Note:</b><br>This email template must consist [USER_NAME],[PASSWORD],[IC_NUMBER]</i></p>
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
