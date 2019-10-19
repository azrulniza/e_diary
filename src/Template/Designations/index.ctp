<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header">
				<?php if($userRoles->hasRole(['Master Admin'])) :?>
					<?php $this->Form->templates($form_templates['shortForm']); ?>
                    <?= $this->Form->create('list',['type' => 'GET','autocomplete' => 'off']) ?>
					
					<?php if($userRoles->hasRole(['Master Admin'])):
                    echo $this->Form->input('organization', ['label' => __('Department'), 'type'=>'select','id'=>'listorganization','class' => 'form-control autosubmit','options' => $organizations, 'empty'=>'All', 'value' => $organizationSelected]); echo "<br><br><br>";
					endif; ?>
					
					<div class="form-group">
						<label class="col-md-2 control-label" for="search"></label>
						<div class="col-md-10"><button class="btn btn-primary" type="submit"><?php echo __('Search') ;?></button></div>
					</div>
					<?= $this->Form->end() ?>
			<?php endif; ?>
			</div>
            <div class="box-body">
                <div class="designations index dataTable_wrapper table-responsive">
                    <table id="dataTables-designations" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Gred') ?></th>
                                <th><?= __('Organization') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php $count = 0 ?>
                        <?php foreach ($designations as $key => $designation): ?>
                            <tr id="<?= $designation->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count ?></td>
                                <td><?= h($designation->name) ?></td>
                                <td><?= h($designation->gred) ?></td>
                                <td>
                                    <?= $designation->has('organization') ? $this->Html->link($designation->organization->name, ['controller' => 'Organizations', 'action' => 'view', $designation->organization->id]) : '' ?>
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $designation->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $designation->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                        <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $designation->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'designation')]) ?>
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
