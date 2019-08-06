<?php if ( ! $success):?>
<p class="login-box-msg"><?= __('Fill in e-mail address for your account.') ?></p>
<?= $this->Form->create($user) ?>
<div class="form-group has-feedback">
    <?= $this->Form->input('email', [
        'label' => false,
        'class' => 'form-control',
        'placeholder' => __('E-mail')
    ]) ?>
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
</div>

<div class="row">
    <div class="col-xs-4">
        <?= $this->Form->button( __('Reset'), [ 'class' => 'btn btn-primary btn-block btn-flat' ]) ?>
    </div><!-- /.col -->
</div>
<?= $this->Form->end() ?>

<?php else: ?>
<?= $this->Form->create($user) ?>
<div class="row">
    <div class="col-xs-8">
        <?= $this->Html->link( __('Sign-in'), '/users/login' ) ?>
    </div><!-- /.col -->
</div>
<?= $this->Form->end() ?>
<?php endif; ?>
