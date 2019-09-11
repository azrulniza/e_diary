<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('Edit Attendance'), ['action' => 'edit', $attendance->id]) ?> </li>
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(__('Delete Attendance'), ['action' => 'delete', $attendance->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attendance->id)]) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendances'), ['action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance'), ['action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendance Codes'), ['controller' => 'AttendanceCodes', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance Code'), ['controller' => 'AttendanceCodes', 'action' => 'add']) ?> </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="attendances view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-attendance"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($attendance->id) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('User') ?></dt>
                            <dd><?= $attendance->has('user') ? $this->Html->link($attendance->user->name, ['controller' => 'Users', 'action' => 'view', $attendance->user->id]) : '' ?></dd>
                            <dt><?= __('Attendance Code') ?></dt>
                            <dd><?= $attendance->has('attendance_code') ? $this->Html->link($attendance->attendance_code->name, ['controller' => 'AttendanceCodes', 'action' => 'view', $attendance->attendance_code->id]) : '' ?></dd>
                            <dt><?= __('Ip Address') ?></dt>
                            <dd><?= h($attendance->ip_address) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($attendance->id) ?></dd>
                            <dt><?= __('Gps Lat') ?></dt>
                            <dd><?= $this->Number->format($attendance->gps_lat) ?></dd>
                            <dt><?= __('Gps Lng') ?></dt>
                            <dd><?= $this->Number->format($attendance->gps_lng) ?></dd>
                            <dt><?= __('Pic') ?></dt>
                            <dd><?= $this->Number->format($attendance->pic) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $this->Number->format($attendance->status) ?></dd>
                            <dt><?= __('Cdate') ?></dt>
                            <dd><?= h($attendance->cdate) ?></dd>
                            <dt><?= __('Mdate') ?></dt>
                            <dd><?= h($attendance->mdate) ?></dd>
                            <dt><?= __('Biometric') ?></dt>
                            <dd><?= $this->Text->autoParagraph(h($attendance->biometric)); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
