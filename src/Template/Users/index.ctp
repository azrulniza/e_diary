<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="users index dataTable_wrapper table-responsive">
                    <table id="dataTables-users" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo __('No.') ?></th> 
                                <th><?= $this->Paginator->sort('name') ?></th>
                                <th><?= $this->Paginator->sort('email') ?></th>
								<th><?= $this->Paginator->sort('status') ?></th>
                                <th><?= $this->Paginator->sort('date_created') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 1 ?>
                        <?php foreach ($users as $key => $user): ?>
                            <tr id="<?= $user->id; ?>">
                                <td><?= $count ?></td>
                                <td><?= h($user->name) ?></td>
                                <td><?= h($user->email) ?></td>
								<td><?php if($user->status=='1'){ echo __('Active');}
								else if($user->status=='0'){echo __('Disabled');}
								else if($user->status=='2'){echo __('Pending activiation');}
								else if($user->status=='3'){echo __('Pending update profile');}
								else if($user->status=='4'){echo __('Password reset requested');}  ?></td>
								<td><?php if($user->date_created){
											echo h(date("d/m/Y H:i:s",strtotime($user->date_created)));
												}
											?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $user->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $user->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $user->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'user')]) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php $count++; endforeach ?>
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
