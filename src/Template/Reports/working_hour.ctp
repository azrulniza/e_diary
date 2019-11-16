<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
				<h3 class="box-title"><?= __('Staff Working Hour') ?></h3>
			</div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
				
                <?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
					<div class="form-group">
						
						<?php if ($userRoles->hasRole(['Master Admin','Supervisor'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments,'empty'=>__('Please Select'), 'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users,'empty'=>__('Please Select'), 'value'=>$userSelected,'style'=>'width:40%']);
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
								'value'=>$monthselected,'style'=>'width:40%']
							);
						?>
							
					</div>
				<?= $this->Form->end() ?>
				<a href="exportExcelworkinghour?department=<?= $departmentSelected;?>&
					user=<?= $userSelected;?>&att_month=<?= $monthselected;?>&att_year=<?= $yearselected;?>" class="btn btn-default pull-right">
						<span class="glyphicon glyphicon-download-alt"> </span> 
						<?php echo __('Export to Excel') ?>
					</a>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'working_hour','department' => $departmentSelected,
						'user' => $userSelected,'att_month' => $monthselected,'att_year' => $yearselected, 
						'_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<br/><br/>
					<div id='dvContainer'>
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No.') ?></th>
                                <th><?= __('Date') ?></th>
                                <th><?= __('In Time') ?></th>
                                <th><?= __('Out Time') ?></th>
                                <th><?= __('Time Off') ?></th>
								<th><?= __('Late (With Approval)') ?></th>
                                <th><?= __('Total Hour') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						for($daymonth=01;$daymonth<=$lastDayMonth;$daymonth++){
							if ($daymonth<10){
								$daymonth = '0'.$daymonth;
							}
							$currentDate = date('Y-'.$monthselected.'-'.$daymonth);
							$daynow = date('l', strtotime($currentDate));
							?>
							<tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $currentDate.' ('.$daynow.')' ?></td>
							
								<?php foreach ($result as $key => $user):?>
									<?php 
										$results = explode("||",$user['attn_time']);
										
										if (date('Y-m-d',strtotime($results[0])) == $currentDate){ 
											$diff = strtotime($results[1]) - strtotime($results[0]);
											$hours = $diff / ( 60 * 60 );
											$latestDate = date('Y-m-d',strtotime($results[0]));
											
											if ($diff > 0){ $grandtotaldiff+= $diff; }
										
										if ($user['late_status'] != NULL){
											$late_status_approval = __('yes');
											if($user['late_status'] == '2' || $user['late_status']== '0'){
												$late_status_approval = __('no');
											}
										} else {
											$late_status_approval = '';
										}
									?>
								
										<td><?php if($results[0] !=''){ echo date('H:i:s',strtotime($results[0]));} ?></td>
										<td><?php if($results[1] !=''){ echo date('H:i:s',strtotime($results[1]));} ?></td>
										<td><?php echo $user['reason'];?></td>
										<td><?php echo $late_status_approval;?></td>
										<td>
											<?php if ($diff > 0){
												if($hours<9){
													echo "<font color='red'>";
												}
												echo round($hours, 2). __('Hour');} else { } ?>
										</td>
									<?php } ?>
								<?php endforeach ?>
								<?php if($latestDate != $currentDate){
									echo '<td></td><td></td><td></td><td></td><td></td>';
								 } 
								 ?>
							</tr>
                        <?php }?>
							<tr>
                                <td colspan='6' align='right'><strong><?= __('Grand Total') ?></strong></td>
								<td><strong><?php $ghours = $grandtotaldiff / ( 60 * 60 ); echo round($ghours, 2). __('Hour'); ?></strong></td>
                               
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
