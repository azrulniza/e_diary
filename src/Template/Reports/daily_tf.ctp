<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
				<h3 class="box-title"><?= __('Daily Time Off') ?></h3>
			</div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
				
                <?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
					<div class="form-group">
						<?php
							$control_html = 
								$this->Form->input(
								  'date_attendance', [
									'label' => __('Date Attendance'),
									'type' => 'text',
									'id' => 'dateattendance',
									'class' => 'form-control autosubmit',
									'onchange' => 'this.form.submit()',
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
						<?php if ($userRoles->hasRole(['Master Admin','Supervisor','Ketua Pengarah'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'empty'=>__('All'),'value'=>$userSelected,'style'=>'width:40%']);
							?>
						<?php endif; ?>
						<?php								
							  $leaveType = array(
							  '1' => __('Personal Matters'),
							  '2' => __('Work Affairs'));
							echo $this->Form->input(
								'leaveType',
								['label' => __('Purpose'),
								'type' => 'select',
								'id' => 'attmonth',
								'class' => 'form-control autosubmit','style'=>'width:40%',
								'options' => $leaveType, 
								'empty'=>__('All'),
								'value'=>$leaveTypeselected,'style'=>'width:40%']
							);
						?>
							
					</div>
				<?= $this->Form->end() ?>
				<a href="exportExcelDailytf?department=<?= $departmentSelected;?>&
					user=<?= $userSelected;?>&date_attendance=<?= $dateselected;?>&leaveType=<?= $leaveTypeselected;?>" class="btn btn-default pull-right">
						<span class="glyphicon glyphicon-download-alt"> </span> 
						<?php echo __('Export to Excel') ?>
					</a>
					<?php if (!$userRoles->hasRole(['Staff'])) :?>
						<?= $this->Html->link(__('Export to PDF'), ['action' => 'daily_tf','department' => $departmentSelected,
							'user' => $userSelected,'date_attendance' => $dateselected,'leaveType' => $leaveTypeselected, 
							'_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<?php endif; ?>
					<br/><br/>
					<div id='dvContainer'>
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No.') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Purpose') ?></th>
                                <th><?= __('Date') ?></th>
                                <th><?= __('Time') ?></th>
                                <th><?= __('Status') ?></th>
                                <th><?= __('Reason') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						
						foreach ($result as $key => $user):
						
							if($user['date_start'] == $user['date_end']){
								$leave_date = date('Y-m-d',strtotime($user['date_start']));
							} else {
							$leave_date = date('Y-m-d',strtotime($user['date_start'])).' To '.date('Y-m-d',strtotime($user['date_end']));
							}
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['user_name'] ?></td>
                                <td><?= $user['leave_type'] ?></td>
                                <td><?= $leave_date ?></td>								
                                <td><?= __('Start Time : ').$user['start_time'].'<br>'.__('End Time : ').$user['end_time'] ?></td>
                                <td><?= $user['leave_status'] ?></td>
                                <td><?= $user['reason'] ?></td>
                            </tr>
                        <?php endforeach ?>
							
                        </tbody>
                    </table>
					</div>
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
