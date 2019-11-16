<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
				<h3 class="box-title"><?= __('Weekly Reports') ?></h3>
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
						<?php if ($userRoles->hasRole(['Master Admin'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
						<?php endif; ?>
						<?php if (!$userRoles->hasRole(['Staff','Admin'])) :?>
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'empty'=>__('All'),'value'=>$userSelected,'style'=>'width:40%']);
							?>
						<?php endif; ?>
						
						</div>
				<?= $this->Form->end() ?>
				<a href="exportExcelWeekly?department=<?= $departmentSelected;?>&user=<?= $userSelected;?>&date_attendance=<?= $dateselected;?>" class="btn btn-default pull-right">
					<span class="glyphicon glyphicon-download-alt"> </span> 
					<?php echo __('Export to Excel') ?>
				</a>
				<?php if (!$userRoles->hasRole(['Staff'])) :?>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'weekly','department' => $departmentSelected,
							'user' => $userSelected,'date_attendance' => $dateselected, '_ext' => 'pdf'], 
							['class' => 'btn btn-default pull-right']) ?>
					<br/><br/>
				<?php endif; ?>
                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th colspan='6'><?= 'Date from :'.$thisweekStart.' to '.$thisweekEnd ?></th>
							</tr>
                        </thead>
						<thead>
                            <tr>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('No.') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Name') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Card No.') ?></th>
                                <th colspan=2 style='text-align: center;'><?= __('Total Late in a week') ?></th>
                               <!-- <th rowspan=2 style='vertical-align: text-top;'><?= __('Card colour for end week') ?></th>-->
                            </tr>
                            <tr>
                                <th style='text-align: center;'><?= __('With Approval') ?></th>
                                <th style='text-align: center;'><?= __('Without Approval') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						$totalyellow = 0;
						$totalred = 0;
						$totalgreen = 0;
						
						foreach ($weeklyresult as $key => $user):
						$user['card_colour'] = 'Yellow';
						if($user['redcard'] == 3){ $user['card_colour'] = 'Red'; }
						if($user['redcard'] > 3){ $user['card_colour'] = 'Green'; }
						if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
						if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
						if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
						
						$late_not_approved = $user['total_late'] - $user['approved_late'];
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td align='center'><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td  align='center'><?= $user['card_no'] ?></td>
                               <!--<td><?php if($user['total_late'] >=3 ) {echo '1';} ?></td>-->
                                <td  align='center'><?php echo $user['approved_late'];?></td>
                                <td  align='center'><?php if($late_not_approved > 0) {echo $late_not_approved;} ?></td>
								<!--<td>
									<?php if ($user['card_colour']) {?>
									<b style="color:<?= $user['card_colour'] ?>"><span class="fa fa-square"></span></b>
									<?php } ?>
									<?= __($user['card_colour']) ?>
								</td>-->

                            </tr>
							
                        <?php endforeach ?>
							<tr>
								<td colspan='4'><b><?= __('Total Officer') ?></b></td>
								<td colspan='3'><b><?= $count?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Yellow Cards'?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Red Cards') ?></b></td>
								<td colspan='3'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Green Cards') ?></b></td>
								<td colspan='3'><b><?= $totalgreen?></b></td>
							</tr>
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
