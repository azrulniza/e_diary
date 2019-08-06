<?php if($keyError): ?>
<div class="row">
    <div class="col-xs-8">
        <?= $this->Html->link( __('Reset Password'), '/users/reset_password' ) ?>
    </div>
</div>

<?php elseif ( ! $success): ?>

<p class="login-box-msg"><?= __('Enter new password for <strong>{0}</strong>', [$user->email]) ?></p>
<?= $this->Form->create($user) ?>

<div class="form-group has-feedback">
    <?= $this->Form->input('password', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => __('New Password'),
        'value' => ''
    ]) ?>
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
<div class="form-group has-feedback">
    <?= $this->Form->input('confirm_password', [
        'label' => false,
        'class' => 'form-control',
        'type' => 'password',
        'placeholder' => __('Repeat Password')
    ]) ?>
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
<div class="row">
    <div class="col-xs-8">
        <?= $this->Html->link( __('Sign-in'), '/users/login' ) ?>
    </div><!-- /.col -->
    <div class="col-xs-4">
        <?= $this->Form->button( __('Reset'), [ 'class' => 'btn btn-primary btn-block btn-flat' ]) ?>
    </div><!-- /.col -->
</div>
<?= $this->Form->end() ?>
<?php else: ?>
<div class="row">
    <div class="col-xs-4"></div>
    <div class="col-xs-8">
        <?= $this->Html->link( __('Sign-in'), '/users/login', ['class' => 'btn btn btn-info', 'role' => 'button'] ) ?>
    </div><!-- /.col -->
</div>

<?php endif; ?>