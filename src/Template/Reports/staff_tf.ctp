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
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'value'=>$userSelected,'style'=>'width:40%']);
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
						<?php								
							  $leaveType = array(
							  '1' => 'Personal Matters',
							  '2' => 'Work Affairs');
							echo $this->Form->input(
								'leaveType',
								['label' => __('Leave Type'),
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
				<a href="exportExcelStafftf?department=<?= $departmentSelected;?>&
					user=<?= $userSelected;?>&att_month=<?= $monthselected;?>&leaveType=<?= $leaveTypeselected;?>" class="btn btn-default pull-right">
						<span class="glyphicon glyphicon-download-alt"> </span> 
						<?php echo __('Export to Excel') ?>
					</a>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'staff_tf','department' => $departmentSelected,
						'user' => $userSelected,'att_month' => $monthselected,'leaveType' => $leaveTypeselected, 
						'_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<br/><br/>
					<div id='dvContainer'>
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('Bil') ?></th>
                                <th><?= $this->Paginator->sort('Leave Type') ?></th>
                                <th><?= $this->Paginator->sort('Leave Date') ?></th>
                                <th><?= $this->Paginator->sort('Leave Time') ?></th>
                                <th><?= $this->Paginator->sort('Leave Status') ?></th>
                                <th><?= $this->Paginator->sort('Reason') ?></th>
                                <th><?= $this->Paginator->sort('Total Hours') ?></th>
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
							
							//calculate total hour
							$dateend = $user['date_end'].' '.$user['end_time'].':00';
							$datestart = $user['date_start'].' '.$user['start_time'].':00';
							$dateDiff = strtotime($dateend)-strtotime($datestart);
							
							$totalhourOutput='';
							if($dateDiff >= 2592000){
								$M = floor($dateDiff/2592000);
								$totalhourOutput.= $M.'Month ';
							}
							if($dateDiff >= 86400){
								$d = floor(($dateDiff%2592000)/86400);
								$totalhourOutput.= $d.'Day ';
							}
							if($dateDiff >= 3600){
								$h = floor(($dateDiff%86400)/3600);
								$totalhourOutput.= $h.'Hours ';
							}
							if($dateDiff >= 60){
								$m = floor(($dateDiff%3600)/60);
								$totalhourOutput.= $m.'Minutes ';
							}
							
							
							$grandTotaldateDiff += $dateDiff;
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['leave_type'] ?></td>
                                <td><?= $leave_date ?></td>								
                                <td><?= 'Start Time : '.$user['start_time'].'<br>'.'End Time : '.$user['end_time'] ?></td>
                                <td><?= $user['leave_status'] ?></td>
                                <td><?= $user['reason'] ?></td>
                                <td><?= $totalhourOutput ?></td>
                            </tr>
                        <?php endforeach ?>
							<tr>
                                <td colspan='6' align='right'><strong><?= 'Grand Total Hour'?></strong></td>
                                <td><strong>
									<?php
										if($grandTotaldateDiff >= 2592000){
											$M = floor($grandTotaldateDiff/2592000);
											$gtotalhourOutput.= $M.'Month ';
										}
										if($grandTotaldateDiff >= 86400){
											$d = floor(($grandTotaldateDiff%2592000)/86400);
											$gtotalhourOutput.= $d.'Day ';
										}
										if($grandTotaldateDiff >= 3600){
											$h = floor(($grandTotaldateDiff%86400)/3600);
											$gtotalhourOutput.= $h.'Hours ';
										}
										if($grandTotaldateDiff >= 60){
											$m = floor(($grandTotaldateDiff%3600)/60);
											$gtotalhourOutput.= $m.'Minutes ';
										}
										echo $gtotalhourOutput;
									?>
								
								</strong></td>
                            </tr>
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
