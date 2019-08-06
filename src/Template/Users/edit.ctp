<?php
$this->Html->script('users', ['block' => 'script']);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="users form">
            <?= $this->Form->create($user, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit User') ?></h3>
                </div>
				<div class="box-body">
				<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
				<script type="text/javascript">
					//select all checkboxes
					$( document ).ready(function() {
						$('.hide').hide();
					});
				</script>
				<div class="box-body">
						<div class="form-group">
						<input class="prevent-autofill hide" type="password" >
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter fullname')]);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter e-mail')]);
                        echo $this->Form->input('new_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter new password'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false]);
                        echo $this->Form->input('confirm_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter password confirmation'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false]);
                        echo $this->Form->input('status', ['class' => 'form-control', 'options' => $userStatus]);
						if($user_level == 1 OR $user_level == 2){
							echo $this->Form->input('roles._ids', ['options' => $roles, 'multiple' => 'checkbox']);
							echo $this->Form->input('resellers._ids', ['options' => $resellers, 'multiple' => 'checkbox']);
						}
                       ?>
						<!--<b>Clients</b><br>
						<?php foreach($clients as $id=>$user){
                        ?><br>
						<input type="checkbox" name="client[<?php echo $id; ?>][]" value="<?php echo $id; ?>" <?php if ($selected_user[$id]==true){?> checked <?php } ?>><?php echo $user; } ?>-->
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'),['class' => 'btn btn-success' , 'controller' => 'dashboards']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'dashboards'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
