<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
				<h3 class="box-title"><?= __('Daily Reports') ?></h3>
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
						<?php if ($userRoles->hasRole(['Master Admin','Ketua Pengarah'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
						<?php endif; ?>
						<?php if ($userRoles->hasRole(['Master Admin','Supervisor','Ketua Pengarah'])) :?>
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'empty'=>__('All'),'value'=>$userSelected,'style'=>'width:40%']);
							?>
						<?php endif; ?>
							<?php
								$filteroption = ['1'=>__('Late In (After 9)'),'2'=>__('Early Out (Before 9 Total Hour)'),'3'=>__('Absent')];
								echo $this->Form->input('filterby',['label' => __('Filter by'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $filteroption, 'empty'=>__('All'),'value'=>$filterSelected,'style'=>'width:40%']);
							?>
					</div>
				<?= $this->Form->end() ?>
					<a href="reports/exportExcelDaily?department=<?= $departmentSelected;?>&
						user=<?= $userSelected;?>&date_attendance=<?= $dateselected;?>&filterby=<?= $filterSelected;?>" class="btn btn-default pull-right">
						<span class="glyphicon glyphicon-download-alt"> </span> 
						<?php echo __('Export to Excel') ?>
					</a>
					<?php if (!$userRoles->hasRole(['Staff'])) :?>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'index','department' => $departmentSelected,
						'user' => $userSelected,'date_attendance' => $dateselected,'filterby' => $filterSelected, 
						'_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<?php endif; ?>
					<br/><br/>
					<div id='dvContainer'>
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
								<th><?= __('No.') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Card No.') ?></th>
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
						
						$totalyellow = 0;
						$totalred = 0;
						$totalgreen = 0;
						
						foreach ($result as $key => $user):
						
						$result = explode("||",$user['attn_time']);
						//count hour
						$diff = strtotime($user['out_time']) - strtotime($user['in_time']);
						$hours = $diff / ( 60 * 60 );
						
						//filtershow
						$showData = 1;
						if($filterSelected == 1){
							$showData = 0;
							if(date('H:i:s',strtotime($user['in_time']))	 > date('H:i:s',strtotime('09:00:00'))){
								$showData = 1;
							}
						}if($filterSelected == 2){
							$showData = 0;
							if($user['out_time'] != '' && $hours < '9'){
								$showData = 1;
							}
						}if($filterSelected == 3){
							$showData = 0;
							if($user['attn_date'] == ''){
								$showData = 1;
							}
						}
						
						//if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
						//if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
						//if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
						
						if($user['redcard'] == 3){
							$totalred += 1; 
						} else if($user['redcard'] < 3){
							$totalyellow += 1;
						} else if($user['redcard'] > 3){
							$totalgreen += 1;
						}

						if ($user['late_status'] != NULL){
							$late_status_approval = __('yes');
							if($user['late_status'] == '2' || $user['late_status']== '0'){
								$late_status_approval = __('no');
							}
						} else {
							$late_status_approval = '';
						}
						if($showData == 1) {
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?php if ($user['in_time'] !=''){ echo date('H:i:s',strtotime($user['in_time']));} ?></td>
                                <td><?php if ($user['out_time'] !=''){ echo date('H:i:s',strtotime($user['out_time']));} ?></td>
                                <td><?= $user['reason'] ?></td>
                                <td><?= $late_status_approval ?></td>
								
                                <td><?php if ($diff > 0){ echo round($hours, 2). ' Hours';} else { } ?></td>
                            </tr>
						<?php } ?>
                        <?php endforeach ?>
							<tr>
								<td colspan=''><b><?= __('Total Officer')?></b></td>
								<td colspan='7'><b><?= $count?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Yellow Cards')?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<!--<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Red Cards')?></b></td>
								<td colspan='4'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Green Cards')?></b></td>
								<td colspan='4'><b><?= $totalgreen?></b></td>
							</tr>-->
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
