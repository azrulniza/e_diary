<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New User Card'), ['action' => 'add']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Cards'), ['controller' => 'Cards', 'action' => 'index']) ?></li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Card'), ['controller' => 'Cards', 'action' => 'add']) ?></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="userCards index dataTable_wrapper table-responsive">
                    <table id="dataTables-userCards" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id') ?></th>
                                <th><?= $this->Paginator->sort('user_id') ?></th>
                                <th><?= $this->Paginator->sort('card_id') ?></th>
                                <th><?= $this->Paginator->sort('mdate') ?></th>
                                <th><?= $this->Paginator->sort('cdate') ?></th>
                                <th><?= $this->Paginator->sort('status') ?></th>
                                <th><?= $this->Paginator->sort('pic') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 0 ?>
                        <?php foreach ($userCards as $key => $userCard): ?>
                            <tr id="<?= $userCard->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $this->Number->format($userCard->id) ?></td>
                                <td>
                                    <?= $userCard->has('user') ? $this->Html->link($userCard->user->name, ['controller' => 'Users', 'action' => 'view', $userCard->user->id]) : '' ?>
                                </td>
                                <td>
                                    <?= $userCard->has('card') ? $this->Html->link($userCard->card->name, ['controller' => 'Cards', 'action' => 'view', $userCard->card->id]) : '' ?>
                                </td>
                                <td><?= h($userCard->mdate) ?></td>
                                <td><?= h($userCard->cdate) ?></td>
                                <td><?= $this->Number->format($userCard->status) ?></td>
                                <td><?= $this->Number->format($userCard->pic) ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $userCard->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $userCard->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $userCard->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'userCard')]) ?>
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
