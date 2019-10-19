<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
				
                <?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
					<div class="form-group">
						
						<?php if ($userRoles->hasRole(['Master Admin','Supervisor'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'value'=>$departmentSelected,'empty'=>__('All'),'style'=>'width:40%']);
							?>		
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'value'=>$userSelected,'empty'=>__('All'),'style'=>'width:40%']);
							?>
						<?php endif; ?>
						<?php								
							  $month = array(
							  '01' => 'January', 
							  '02' => 'Fabruary', 
							  '03' => 'March', 
							  '04' => 'April', 
							  '05' => 'May', 
							  '06' => 'June', 
							  '07' => 'July', 
							  '08' => 'August', 
							  '09' => 'September', 
							  '10' => 'October', 
							  '11' => 'November', 
							  '12' => 'December');
							echo $this->Form->input(
								'att_month',
								['label' => __('Attendance Month'),
								'type' => 'select',
								'id' => 'attmonth',
								'class' => 'form-control autosubmit','style'=>'width:40%',
								'options' => $month, 
								'empty'=>__('All'),
								'value'=>$monthselected,'style'=>'width:40%']
							);
						?>
						
							
					</div>
				<?= $this->Form->end() ?>
				<a href="exportExcelLatein?department=<?= $departmentSelected;?>&
					user=<?= $userSelected;?>&att_month=<?= $monthselected;?>" class="btn btn-default pull-right">
						<span class="glyphicon glyphicon-download-alt"> </span> 
						<?php echo __('Export to Excel') ?>
					</a>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'late_in','department' => $departmentSelected,
						'user' => $userSelected,'att_month' => $monthselected, 
						'_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<br/><br/>
					<div id='dvContainer'>
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('Bil') ?></th>
                                <th><?= $this->Paginator->sort('Name') ?></th>
                                <th><?= $this->Paginator->sort('Date') ?></th>
                                <th><?= $this->Paginator->sort('In Time') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						
						foreach ($result as $key => $user):
						
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['user_name'] ?></td>
                                <td><?= date('Y-m-d',strtotime($user['cdate'])) ?></td>								
                                <td><?= date('H:i:s',strtotime($user['cdate'])) ?></td>
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
