<div class="row">
    <div class="col-xs-12">
        <div class="settingAttendancesReasons form">
            <?= $this->Form->create($settingAttendancesReason, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Setting Attendances Reason') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'required'=>true]);
                        echo $this->Form->input('description', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'required'=>true]);
						echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $status,'required'=>true]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'settingAttendancesReasons'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
