<div class="row">
    <div class="col-xs-10">
        <div class="grades form">
            <?= $this->Form->create($grade, ['role' => 'form']) ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Grade') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'grades'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
