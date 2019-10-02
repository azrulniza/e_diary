<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="settingEmails index dataTable_wrapper table-responsive">
                    <table id="dataTables-settingEmails" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('no') ?></th>
                                <th><?= $this->Paginator->sort('name') ?></th>
                                <th><?= $this->Paginator->sort('#') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 0 ?>
                        <?php foreach ($settingEmails as $key => $settingEmail): ?>
                            <tr id="<?= $settingEmail->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count ?></td>
                                <td><?= h($settingEmail->name) ?></td>
                                <td><?= $this->Html->link('English', ['controller' => 'SettingEmails', 'action' => 'edit', $settingEmail->email_type_id , 1])?><br>
								<?= $this->Html->link('Malay', ['controller' => 'SettingEmails', 'action' => 'edit', $settingEmail->email_type_id , 2])?>
								</td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $settingEmail->email_type_id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
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
