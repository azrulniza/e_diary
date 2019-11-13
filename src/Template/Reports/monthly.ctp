<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
				<h3 class="box-title"><?= __('Monthly Reports') ?></h3>
			</div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
					<?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
						<div class="form-group">
						<?php if ($userRoles->hasRole(['Master Admin',])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
						<?php endif; ?>
						<?php if (!$userRoles->hasRole(['Staff','Admin'])) :?>
							<?php
								echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control autosubmit','options' => $users, 'empty'=>__('All'),'value'=>$userSelected,'style'=>'width:40%']);
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
								'default' => '08',
								'value'=>$yearselected,'style'=>'width:40%']
							);
						?><?php								
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
								'default' => '08',
								'value'=>$monthselected,'style'=>'width:40%']
							);
						?>
						</div>
				<a href="exportExcelMonthly?department=<?= $departmentSelected;?>&user=<?= $userSelected;?>	&att_month=<?= $monthselected;?>&att_year=<?= $yearselected;?>" class="btn btn-default pull-right">
					<span class="glyphicon glyphicon-download-alt"> </span> 
					<?php echo __('Export to Excel') ?>
				</a>
				<?php if (!$userRoles->hasRole(['Staff'])) :?>
					<?= $this->Html->link(__('Export to PDF'), ['action' => 'monthly','department' => $departmentSelected,
							'user' => $userSelected,'att_month' => $monthselected,'att_year' => $yearselected, '_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
					<br/><br/>
				<?php endif; ?>
				
                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Bil') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Name') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Grade') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Card No.') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Total Late') ?></th>
                                <th colspan=2 style='text-align: center;'><?= __('Officer Approval') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Card Colour') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Remarks') ?></th>
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
						$total3times = 0;
						
						foreach ($monthlyresult as $key => $user):
						if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
						if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
						if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
						if ($user['total_late'] >= 3){$total3times += 1;}
						
						$late_not_approved = $user['total_late'] - $user['approved_late'];
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['grade'].$user['skim'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?php if ($user['total_late']>0){ echo $user['total_late']; }else{ echo '-'; } ?></td>
                                <td align='center'><?php echo $user['approved_late'];?></td>
                                <td align='center'><?php if($late_not_approved > 0) {echo $late_not_approved;} ?></td>
								<td>
									<?php if ($user['card_colour']) {?>
									<b style="color:<?= $user['card_colour'] ?>"><span class="fa fa-square"></span></b>
									<?php } ?>
									<?= __($user['card_colour']) ?>
								</td>
                                <td><?= $user['card_remarks'] ?></td>

                            </tr>
                        <?php endforeach ?>
							<tr>
								<td colspan='5'><b><?= __('Total Officer')?></b></td>
								<td colspan='4'><b><?= $count?></b></td>
							</tr>
							<tr>
								<td colspan='5'><b><?= __('Total Officer Late More Than 3 Times')?></b></td>
								<td colspan='4'><b><?= $total3times?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Yellow Cards')?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<tr>
								<td colspan='5'><b><?= __('Total Officer That Hold Red Cards')?></b></td>
								<td colspan='4'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='5'><b><?= __('Total Officer That Hold Green Cards')?></b></td>
								<td colspan='4'><b><?= $totalgreen?></b></td>
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
