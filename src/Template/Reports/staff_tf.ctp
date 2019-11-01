<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
           <div class="box-header with-border">
				<h3 class="box-title"><?= __('Staff Time Off') ?></h3>
			</div>
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
							  for($year = 2019 ; $year <= date('Y',strtotime('+1 year')); $year++){
								  $arr_year[$year] = $year;
							  }
							  
							  echo $this->Form->input(
								'att_year',
								['label' => __('Attendance Year'),
								'type' => 'select',
								'id' => 'attyear',
								'class' => 'form-control autosubmit','style'=>'width:40%',
								'options' => $arr_year,
								'value'=>$yearselected,'style'=>'width:40%']
							);
						?>
						<?php								
							  $month = array(
							  '01' => __('January'), 
							  '02' => __('February'), 
							  '03' => __('March'), 
							  '04' => __('April'), 
							  '05' => __('May'), 
							  '06' => __('June'), 
							  '07' => __('July'), 
							  '08' => __('August'), 
							  '09' => __('September'), 
							  '10' => __('October'), 
							  '11' => __('November'), 
							  '12' => __('December'));
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
					user=<?= $userSelected;?>&att_month=<?= $monthselected;?>&att_year=<?= $yearselected;?>&leaveType=<?= $leaveTypeselected;?>" class="btn btn-default pull-right">
						<span class="glyphicon glyphicon-download-alt"> </span> 
						<?php echo __('Export to Excel') ?>
					</a>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'staff_tf','department' => $departmentSelected,
						'user' => $userSelected,'att_month' => $monthselected,'att_year' => $yearselected,'leaveType' => $leaveTypeselected, 
						'_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<br/><br/>
					<div id='dvContainer'>
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('Bil') ?></th>
                                <th><?= __('Leave Type') ?></th>
                                <th><?= __('Leave Date') ?></th>
                                <th><?= __('Leave Time') ?></th>
                                <th><?= __('Leave Status') ?></th>
                                <th><?= __('Reason') ?></th>
                                <th><?= __('Total Hour') ?></th>
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
								$totalhourOutput.= $M.__('Month').' ';
							}
							if($dateDiff >= 86400){
								$d = floor(($dateDiff%2592000)/86400);
								$totalhourOutput.= $d.__('Day').' ';
							}
							if($dateDiff >= 3600){
								$h = floor(($dateDiff%86400)/3600);
								$totalhourOutput.= $h.__('Hour').' ';
							}
							if($dateDiff >= 60){
								$m = floor(($dateDiff%3600)/60);
								$totalhourOutput.= $m.__('Minute').' ';
							}
							
							
							$grandTotaldateDiff += $dateDiff;
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['leave_type'] ?></td>
                                <td><?= $leave_date ?></td>								
                                <td><?= __('Start Time : ').$user['start_time'].'<br>'.__('End Time : ').$user['end_time'] ?></td>
                                <td><?= $user['leave_status'] ?></td>
                                <td><?= $user['reason'] ?></td>
                                <td><?= $totalhourOutput ?></td>
                            </tr>
                        <?php endforeach ?>
							<tr>
                                <td colspan='6' align='right'><strong><?= __('Grand Total')?></strong></td>
                                <td><strong>
									<?php
										if($grandTotaldateDiff >= 2592000){
											$M = floor($grandTotaldateDiff/2592000);
											$gtotalhourOutput.= $M.__('Month').' ';
										}
										if($grandTotaldateDiff >= 86400){
											$d = floor(($grandTotaldateDiff%2592000)/86400);
											$gtotalhourOutput.= $d.__('Day').' ';
										}
										if($grandTotaldateDiff >= 3600){
											$h = floor(($grandTotaldateDiff%86400)/3600);
											$gtotalhourOutput.= $h.__('Hour').' ';
										}
										if($grandTotaldateDiff >= 60){
											$m = floor(($grandTotaldateDiff%3600)/60);
											$gtotalhourOutput.= $m.__('Minute').' ';
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
