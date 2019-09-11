<p class="login-box-msg"><?= __('Sign in to start your session') ?></p>
<?= $this->Form->create() ?>
<div class="form-group has-feedback">
    <?= $this->Form->input('ic_number', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => __('Identity Card No.'),
        'required' => true
    ]) ?>
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
</div>
<div class="form-group has-feedback">
    <?= $this->Form->input('password', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => __('Password'),
        'required' => true
    ]) ?>
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="checkbox icheck">
            <!-- <label>
                <?= $this->Form->checkbox('remember', [
                    'label' => false,
                ]) ?>
                <?= __('Remember Me') ?>
            </label> -->
        </div>
    </div><!-- /.col -->
</div>	
<div class="row">
    <div class="col-xs-12">	
	<?php

	echo $this->Captcha->create('<captcha>');

	?>
	</div><!-- /.col -->
</div>
<br>

<div class="col-md-12">
        <button type="submit" class="btn btn-success btn-block btn-flat"><?= __('Sign In') ?></button><br/>
</div><!-- /.col -->
	

<?= $this->Form->end() ?>
	
	
	
<?= $this->Html->link( __('Forgot Password?'), '/users/reset_password' ) ?>	
	
	
    

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
