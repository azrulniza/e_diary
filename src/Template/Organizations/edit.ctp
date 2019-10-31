<div class="row">
    <div class="col-xs-10">
        <div class="organizations form">
            <?= $this->Form->create($organization, ['role' => 'form']) ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Department') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'required'=>true]);
                        echo $this->Form->input('description', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
						echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $status,'required'=>true]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'organizations'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
