<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
					<?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
						<div class="form-group">
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
								'default' => '08',
								'value'=>$monthselected,'style'=>'width:40%']
							);
						?>
						</div>
				<a href="exportExcelMonthly?department=<?= $departmentSelected;?>&user=<?= $userSelected;?>			&att_month=<?= $monthselected;?>" class="btn btn-default pull-right">
					<span class="glyphicon glyphicon-download-alt"> </span> 
					<?php echo __('Export to Excel') ?>
				</a>
					
				<?= $this->Html->link(__('Export to PDF'), ['action' => 'monthly', '_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>
				<br/><br/>
				
                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('Bil') ?></th>
                                <th><?= $this->Paginator->sort('Name') ?></th>
                                <th><?= $this->Paginator->sort('Grade') ?></th>
                                <th><?= $this->Paginator->sort('Card No.') ?></th>
                                <th><?= $this->Paginator->sort('Total Late') ?></th>
                                <th><?= $this->Paginator->sort('Officer Approval') ?></th>
                                <th><?= $this->Paginator->sort('Card Colour') ?></th>
                                <th><?= $this->Paginator->sort('Remarks') ?></th>
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
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['grade'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?php if ($user['total_late']>0){ echo $user['total_late']; }else{ echo '-'; } ?></td>
                                <td><?php if ($user['total_late']>0){ echo 'yes'; }else{ echo '-'; } ?></td>
                                <td><?= $user['card_colour'] ?></td>
                                <td><?= $user['card_remarks'] ?></td>

                            </tr>
                        <?php endforeach ?>
							<tr>
								<td colspan='5'><b><?= 'Total Officer'?></b></td>
								<td colspan='3'><b><?= $count?></b></td>
							</tr>
							<tr>
								<td colspan='5'><b><?= 'Total Officer Late More Than 3 Times'?></b></td>
								<td colspan='3'><b><?= $total3times?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Yellow Cards'?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<tr>
								<td colspan='5'><b><?= 'Total Officer That Hold Red Cards'?></b></td>
								<td colspan='3'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='5'><b><?= 'Total Officer That Hold Green Cards'?></b></td>
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
