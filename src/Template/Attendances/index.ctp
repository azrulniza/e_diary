<div class="row">
    <div class="col-xs-10">
        <div class="box box-default">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="attendances index dataTable_wrapper table-responsive">
                    <table id="dataTables-attendances" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= $this->Paginator->sort('user_id', 'Name') ?></th>
                                <th><?= $this->Paginator->sort('attendance_code_id','Status') ?></th>
                                <th><?= __('Department') ?></th>
                                <th><?= __('In') ?></th>
                                <th><?= __('Out') ?></th>
                                <th><?= $this->Paginator->sort('gps_lng') ?></th>
                                <th><?= $this->Paginator->sort('pic') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 0 ?>
                        <?php foreach ($attendances as $key => $attendance): ?>
                            
                            <tr id="<?= $attendance->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $this->Number->format($count) ?></td>
                                <td>
                                    <?= $attendance->has('user') ? $this->Html->link($attendance->user->name, ['controller' => 'Users', 'action' => 'view', $attendance->user->id]) : '' ?>
                                </td>
                                <td>
                                    <?= $attendance->has('attendance_code') ? $this->Html->link($attendance->attendance_code->name, ['controller' => 'AttendanceCodes', 'action' => 'view', $attendance->attendance_code->id]) : '' ?>
                                </td>
                                <td><?= h($attendance->ip_address) ?></td>
                                <td><?= $this->Number->format($attendance->gps_lat) ?></td>
                                <td><?= $this->Number->format($attendance->gps_lng) ?></td>
                                <td><?= $this->Number->format($attendance->pic) ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $attendance->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $attendance->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $attendance->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'attendance')]) ?>
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
