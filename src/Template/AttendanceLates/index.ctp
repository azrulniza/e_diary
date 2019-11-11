<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance Late'), ['action' => 'add']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Attendances'), ['controller' => 'Attendances', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Attendance'), ['controller' => 'Attendances', 'action' => 'add']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="attendanceLates index dataTable_wrapper table-responsive">
                    <table id="dataTables-attendanceLates" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id') ?></th>
                                <th><?= $this->Paginator->sort('attendance_id') ?></th>
                                <th><?= $this->Paginator->sort('late_remark') ?></th>
                                <th><?= $this->Paginator->sort('created_by') ?></th>
                                <th><?= $this->Paginator->sort('pic') ?></th>
                                <th><?= $this->Paginator->sort('pic_remark') ?></th>
                                <th><?= $this->Paginator->sort('status') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 0 ?>
                        <?php foreach ($attendanceLates as $key => $attendanceLate): ?>
                            <tr id="<?= $attendanceLate->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $this->Number->format($attendanceLate->id) ?></td>
                                <td>
                                    <?= $attendanceLate->has('attendance') ? $this->Html->link($attendanceLate->attendance->id, ['controller' => 'Attendances', 'action' => 'view', $attendanceLate->attendance->id]) : '' ?>
                                </td>
                                <td><?= h($attendanceLate->late_remark) ?></td>
                                <td><?= $this->Number->format($attendanceLate->created_by) ?></td>
                                <td><?= $this->Number->format($attendanceLate->pic) ?></td>
                                <td><?= h($attendanceLate->pic_remark) ?></td>
                                <td><?= $this->Number->format($attendanceLate->status) ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $attendanceLate->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $attendanceLate->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $attendanceLate->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'attendanceLate')]) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer">
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                    </ul>
                    <p><?= $this->Paginator->counter() ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
