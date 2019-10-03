<div class="row">
    <div class="col-xs-12">
        <div class="users form">
		<?php if(isset($language_id) OR isset($this->request['url']['switch_to'])){?>
			<p class="login-box-msg"><?= __('Reset Password') ?></p>
			<div class="login-box-body">
				<?= $this->Form->create() ?>
					<div class="form-group has-feedback">
						<?= $this->Form->input('password', [
						'label' => false,
						'class' => 'form-control',
						'placeholder' => __('Set Password'),
						'required' => true
						]) ?>
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<?= $this->Form->input('confirm_password', [
						'type'=>'password',
						'label' => false,
						'class' => 'form-control',
						'placeholder' => __('Re-confirm Password'),
						'autocomplete' => 'off',
						'required' => true
						]) ?>
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>	
					<div class="col-xs-12">
						<input type="hidden" name="param1" value=<?php echo $_GET['param1']?>>
						<button type="submit" class="btn btn-primary btn-block btn-flat"><?= __('Verify') ?></button>
					</div>
			</div>
		<?php }else { ?>
					<div class="box-body">
                        <?php echo __('Parameter not valid.') ?>
                    </div>
		<?php } ?>
		</div>
	</div>
</div>

<?= $this->Form->end() ?>
	
	
	
<?php ?>	
	
	
    

<?php
/*
<a href="#">I forgot my password</a><br>
<a href="register.html" class="text-center">Register a new membership</a>
*/
?>



<?php
$this->Html->scriptStart(['block' => true]);
echo '$(function () {
        $("input").iCheck({
            checkboxClass: "icheckbox_square-blue",
        });
    });';
$this->Html->scriptEnd();
?>