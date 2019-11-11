<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AttendanceLate $attendanceLate
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $attendanceLate->id],
                    ['confirm' => __('Are you sure you want to delete this {0}?', 'attendanceLate')]
                ) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendance Lates'), ['action' => 'index']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendances'), ['controller' => 'Attendances', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance'), ['controller' => 'Attendances', 'action' => 'add']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="attendanceLates form">
            <?= $this->Form->create($attendanceLate, ['role' => 'form']) ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit Attendance Late') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('attendance_id', ['options' => $attendances, 'empty' => true, 'class' => 'form-control']);
                        echo $this->Form->input('late_remark', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('created_by', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('pic', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('pic_remark', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('cdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('mdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'attendanceLates'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
