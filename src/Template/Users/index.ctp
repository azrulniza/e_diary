<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header">
			<?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
					<?php $this->Form->templates($form_templates['shortForm']); ?>
                    <?= $this->Form->create('list',['type' => 'GET','autocomplete' => 'off']) ?>
					
					<?php if($userRoles->hasRole(['Master Admin'])):
                    echo $this->Form->input('organization', ['label' => __('Department'), 'type'=>'select','id'=>'listorganization','class' => 'form-control autosubmit','options' => $organizations, 'empty'=>'All', 'value' => $organizationSelected]); echo "<br><br><br>";
					endif; ?>
					
					<?php
                    echo $this->Form->input('search', ['label' => __('Name'),'id'=>'myInput', 'type'=>'text','class' => 'form-control','style'=>'width:345px; height:32px;','value' => $search_name]);
					?><br><br>
					<div class="form-group">
						<label class="col-md-2 control-label" for="search"></label>
						<div class="col-md-10"><button class="btn btn-primary" type="submit"><?php echo __('Search') ;?></button></div>
					</div>
					<?= $this->Form->end() ?>
			<?php endif; ?>
			</div>
			<?php if ($users->count() > 0){ ?>
            <div class="box-body">
                <div class="users index dataTable_wrapper table-responsive">
                    <table id="dataTables-users" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= __('Email') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Department') ?></th>
                                <th><?= __('Designation') ?></th>
                                <th><?= __('Report To') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php foreach ($users as $key => $user): ?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= h($user->email) ?></td>
                                <td><?= h($user->name) ?></td>
                                <td><?= h($user->user_organizations[0]->organization->name) ?></td>
                                <td><?= h($user->user_designations[0]->designation->name) ?></td>
                                <td><?php foreach($reportTo as $key => $report_to): 
											if($key == $user->report_to){
												echo $report_to;
											}
									?>
								
								<?php endforeach; ?>
								</td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $user->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $user->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $user->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'user')]) ?>
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
			<?php }else {?>
                    <div class="box-body">
                        <?php echo __('No user found.') ?>
                    </div>

            <?php } ?>
        </div>
    </div>
</div>
