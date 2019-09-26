<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
				
                <?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
					<div class="form-group">
						<?php
							$control_html = 
								$this->Form->input(
								  'date_attendance', [
									'label' => 'Date Attendance',
									'type' => 'text',
									'id' => 'dateattendance',
									'class' => 'form-control autosubmit',
									'style'=>'width:40%',
									'value'=>$dateselected
								  ]);

							$date_control = 
								str_replace(
							   'type="text"', 
							   'type="date"', 
							   $control_html
							);
						  
							echo $date_control;
						?>
						<?php if ($userRoles->hasRole(['Master Admin','Admin'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
						<?php endif; ?>
						<?php if (!$userRoles->hasRole(['Staff'])) :?>
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'empty'=>__('All'),'value'=>$userSelected,'style'=>'width:40%']);
							?>
						<?php endif; ?>
					</div>
				<?= $this->Form->end() ?>
                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('Bil') ?></th>
                                <th><?= $this->Paginator->sort('name') ?></th>
                                <th><?= $this->Paginator->sort('card no.') ?></th>
                                <th><?= $this->Paginator->sort('in time') ?></th>
                                <th><?= $this->Paginator->sort('out time') ?></th>
                                <th><?= $this->Paginator->sort('remarks') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						foreach ($result as $key => $user):
						$result = explode("||",$user['attn_time']);
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?= $result[0] ?></td>
                                <td><?= $result[1] ?></td>
                                <td><?= $user['attn_remarks'] ?></td>

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
