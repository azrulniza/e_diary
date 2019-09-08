<div class="row">
    <div class="col-xs-10">
        <div class="designations form">
            <?= $this->Form->create($designation, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Designation') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('gred', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('organization_id', ['options' => $organizations, 'empty' => __("--Please Select--"), 'class' => 'form-control']);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'designations'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
