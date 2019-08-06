<div class="row">
    <div class="col-xs-12">
        <div class="departments form">
            <?= $this->Form->create($department, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Department') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('date_created', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('date_modified', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'departments'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
