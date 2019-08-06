<?php
$this->Html->script('dashboard', ['block' => 'script']);

?>

<div class="row">
    <div class="col-md-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h2 class="box-title"><?= __('Profile') ?></h2>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">							
                    <tr> 
                        <td width="30%"><h5><strong><?= __('Company Name') ?></strong></h5></td>
                        <td><h5><?= $reseller->company_name ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Contact Person') ?></strong></h5></td>
                        <td><h5><?= $user->name ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Email') ?></strong></h5></td>
                        <td><h5><?= $user->email ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Phone No.') ?></strong></h5></td>
                        <td><h5><?= $reseller->phone_number ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Address') ?></strong></h5></td>
                        <td><h5><?= $reseller->address ?></h5></td> 
                    </tr>
                </table>
            </div>
            <div class="box-footer">

                <?php //if ($reseller->cmp_management == 1): ?>
                    <div class="btn-group">

                        <?= $this->Html->link(__('Add New Client (CMP)'), ['controller' => 'Clients', 'action' => 'add'], ['class' => 'btn btn-success pull-left'], array('style' => 'color:black !important;')) ?>
                        <?= $this->Html->link(__('View Keycodes'), ['controller' => 'ProductKeys', 'action' => 'oem_keylist'], ['class' => 'btn btn-primary pull-left'], array('style' => 'color:black !important;')) ?>

                    </div>
                <?php //endif; ?>

                <?= $this->Html->link(__('Update Profile'), ['controller' => 'Resellers', 'action' => 'edit', $reseller->id], ['class' => 'btn btn-default pull-right'], array('style' => 'color:black !important;')) ?>
				<br>
				<br>
            </div>
        </div>
    </div>
</div>
