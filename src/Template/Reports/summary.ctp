<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
				<h3 class="box-title"><?= __('Monthly Summary Reports') ?></h3>
			</div>
            <div class="box-body">
				<?php $this->Form->templates($form_templates['shortForm']); ?>
					<?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
						<!--<div class="form-group">
						<?php if ($userRoles->hasRole(['Master Admin','Supervisor'])) :?>
							<?php
								echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control autosubmit','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
							?>		
						<?php endif; ?>-->
							
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
								'default' => '08',
								'value'=>$monthselected,'style'=>'width:40%']
							);
						?>
						</div>
				<a href="exportExcelSummary?att_month=<?= $monthselected;?>" class="btn btn-default pull-right">
					<span class="glyphicon glyphicon-download-alt"> </span> 
					<?php echo __('Export to Excel') ?>
				</a>
					
				<?= $this->Html->link(__('Export to PDF'), ['action' => 'summary','att_month' => $monthselected, '_ext' => 'pdf'], ['class' => 'btn btn-default pull-right']) ?>

				<br/><br/>
                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan=2><center><?= __('Bil') ?></center><br/></th>
                                <th rowspan=2><center><?= __('Officer Group') ?></center><br/></th>
                                <th rowspan=2><center><?= __('Total Officer') ?></center><br/></th>
                                <th colspan=3><center><?= __('Card Colour') ?></center></th>
                                <th rowspan=2><center><?= __('Three late in a month (total officer)') ?></center><br/></th>
                                <th rowspan=2><center><?= __('Remarks') ?></center><br/></th>
                            </tr>
							<tr>
                                <th><center><?= __('Yellow') ?></center></th>
                                <th><center><?= __('Green') ?></center></th>
                                <th><center><?= __('Red') ?></center></th>
                            </tr>
                        </thead>
						
                        <tbody class="ui-sortable">
							<tr class="even">
								<td><?= '1'; ?></td>
								<td><?= __('Higher Management Group') ?></td>
								<td align='center'><?= $grade55result[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade55result[0]['yellow'] > 0){ echo $grade55result[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade55result[0]['green'] > 0){ echo $grade55result[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade55result[0]['red'] > 0){ echo $grade55result[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count55late = 0;
										foreach ($totallate55result as $key => $value){
											if ($value['total_late'] >= 3){
												$count55late ++;
											}
										}
									if ($count55late > 0){ echo $count55late; } else { echo '-'; }?>
								</td>
								<td><?= $count55late[0]['remarks'];?></td>
							</tr>
							<tr class="even">
								<td><?= '2'; ?></td>
								<td><?= __('Professional Management Group (Grade 48-54)') ?></td>
								<td align='center'><?= $grade4854result[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade4854result[0]['yellow'] > 0){ echo $grade4854result[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4854result[0]['green'] > 0){ echo $grade4854result[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4854result[0]['red'] > 0){ echo $grade4854result[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count4854late = 0;
										foreach ($totallate4854result as $key => $value){
											if ($value['total_late'] >= 3){
												$count4854late ++;
											}
										}
									if ($count4854late > 0){ echo $count4854late; } else { echo '-'; }?>
								</td>
								<td><?= $totallate4854result[0]['remarks'];?></td>
							</tr>
							<tr class="odd">
								<td><?= '3'; ?></td>
								<td><?= __('Professional Management Group (Grade 41-44)') ?></td>
								<td align='center'><?= $grade4144result[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade4144result[0]['yellow'] > 0){ echo $grade4144result[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4144result[0]['green'] > 0){ echo $grade4144result[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4144result[0]['red'] > 0){ echo $grade4144result[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count4144late = 0;
										foreach ($totallate4144result as $key => $value){
											if ($value['total_late'] >= 3){
												$count4144late ++;
											}
										}
									if ($count4144late > 0){ echo $count4144late; } else { echo '-'; }?>
								</td>
								<td><?= $totallate4144result[0]['remarks'];?></td>
							</tr>
							<tr class="even">
								<td><?= '4'; ?></td>
								<td><?= __('Executing Group (Grade 17-40)') ?></td>
								<td align='center'><?= $grade1740result[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade1740result[0]['yellow'] > 0){ echo $grade1740result[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade1740result[0]['green'] > 0){ echo $grade1740result[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade1740result[0]['red'] > 0){ echo $grade1740result[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count1740late = 0;
										foreach ($totallate1740result as $key => $value){
											
											if ($value['total_late'] >= 3){
												$count1740late ++;
												
											}
										}
									if ($count1740late > 0){ echo $count1740late; } else { echo '-'; }?>
								</td>
								<td><?= $totallate1740result[0]['remarks'];?></td>
							</tr>
							<tr class="odd">
								<td><?= '5'; ?></td>
								<td><?= __('Executing Group (Grade 1-16)') ?></td>
								<td align='center'><?= $grade116result[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade116result[0]['yellow'] > 0){ echo $grade116result[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade116result[0]['green'] > 0){ echo $grade116result[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade116result[0]['red'] > 0){ echo $grade116result[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count116late = 0;
										foreach ($totallate116result as $key => $value){
											if ($value['total_late'] >= 3){
												$count116late ++;
											}
										}
									if ($count116late > 0){ echo $count116late; } else { echo '-'; }?>
								</td>
								<td><?= $totallate116result[0]['remarks'];?></td>
							</tr>
							<tr>
								<?php 
									$gtotalstaff= $grade116result[0]['total_officer'] + $grade1740result[0]['total_officer'] +$grade4144result[0]['total_officer'] + $grade4854result[0]['total_officer'] +$grade55result[0]['total_officer'];
									
									$gtotalyellow= $grade116result[0]['yellow'] + $grade1740result[0]['yellow'] +$grade4144result[0]['yellow'] + $grade4854result[0]['yellow'] +$grade55result[0]['yellow'];
									
									$gtotalgreen= $grade116result[0]['green'] + $grade1740result[0]['green'] +$grade4144result[0]['green'] + $grade4854result[0]['green'] +$grade55result[0]['green'];
									
									$gtotalred= $grade116result[0]['red'] + $grade1740result[0]['red'] +$grade4144result[0]['red'] + $grade4854result[0]['red'] +$grade55result[0]['red'];
									
									$gtotal3times = $count116late + $count1740late + $count4144late + $count4854late + $count55late;
								?>
								<td align='center' colspan='2'><b><?= __('Grand Total')?></b></td>
								<td align='center'><b><?= $gtotalstaff ?></b></td>
								<td align='center'><b><?= $gtotalyellow ?></b></td>
								<td align='center'><b><?= $gtotalgreen ?></b></td>
								<td align='center'><b><?= $gtotalred ?></b></td>
								<td align='center'><b><?= $gtotal3times ?></b></td>
								<td align='center'></td>
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
