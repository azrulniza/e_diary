<div class="row">
    <div class="col-xs-10">
        <div class="box box-default">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="grades index dataTable_wrapper table-responsive">
                    <table id="dataTables-grades" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= __('Grade') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 0 ?>
                        <?php foreach ($grades as $key => $grade): ?>
                            <tr id="<?= $grade->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count ?></td>
                                <td><?= h($grade->name) ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $grade->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $grade->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this?')]) ?>
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
