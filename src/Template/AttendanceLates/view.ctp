<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('Edit Attendance Late'), ['action' => 'edit', $attendanceLate->id]) ?> </li>
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(__('Delete Attendance Late'), ['action' => 'delete', $attendanceLate->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attendanceLate->id)]) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendance Lates'), ['action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance Late'), ['action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendances'), ['controller' => 'Attendances', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance'), ['controller' => 'Attendances', 'action' => 'add']) ?> </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="attendanceLates view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-attendanceLate"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($attendanceLate->id) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Attendance') ?></dt>
                            <dd><?= $attendanceLate->has('attendance') ? $this->Html->link($attendanceLate->attendance->id, ['controller' => 'Attendances', 'action' => 'view', $attendanceLate->attendance->id]) : '' ?></dd>
                            <dt><?= __('Late Remark') ?></dt>
                            <dd><?= h($attendanceLate->late_remark) ?></dd>
                            <dt><?= __('Pic Remark') ?></dt>
                            <dd><?= h($attendanceLate->pic_remark) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($attendanceLate->id) ?></dd>
                            <dt><?= __('Created By') ?></dt>
                            <dd><?= $this->Number->format($attendanceLate->created_by) ?></dd>
                            <dt><?= __('Pic') ?></dt>
                            <dd><?= $this->Number->format($attendanceLate->pic) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $this->Number->format($attendanceLate->status) ?></dd>
                            <dt><?= __('Cdate') ?></dt>
                            <dd><?= h($attendanceLate->cdate) ?></dd>
                            <dt><?= __('Mdate') ?></dt>
                            <dd><?= h($attendanceLate->mdate) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
