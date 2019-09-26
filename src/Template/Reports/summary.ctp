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

                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan=2><center><?= $this->Paginator->sort('Bil') ?></center><br/></th>
                                <th rowspan=2><center><?= $this->Paginator->sort('Officer Group') ?></center><br/></th>
                                <th rowspan=2><center><?= $this->Paginator->sort('Total Officer') ?></center><br/></th>
                                <th colspan=3><center><?= $this->Paginator->sort('Card Colour') ?></center></th>
                                <th rowspan=2><center><?= $this->Paginator->sort('Three late in a month (total officer)') ?></center><br/></th>
                                <th rowspan=2><center><?= $this->Paginator->sort('Remarks') ?></center><br/></th>
                            </tr>
							<tr>
                                <th><center><?= $this->Paginator->sort('Yellow') ?></center></th>
                                <th><center><?= $this->Paginator->sort('Green') ?></center></th>
                                <th><center><?= $this->Paginator->sort('Red') ?></center></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
							<tr class="even">
								<td><?= '1'; ?></td>
								<td><?= 'Higher Management Group' ?></td>
								<td align='center'><?= $grade55results[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade55results[0]['yellow'] > 0){ echo $grade55results[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade55results[0]['green'] > 0){ echo $grade55results[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade55results[0]['red'] > 0){ echo $grade55results[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count55late = 0;
										foreach ($totallate55result as $key => $value){
											if ($value['total_late'] >= 3){
												$count55late ++;
											}
										}
									if ($count55late > 1){ echo $count55late; } else { echo '-'; }?>
								</td>
								<td></td>
							</tr>
							<tr class="even">
								<td><?= '2'; ?></td>
								<td><?= 'Professional Management Group (Grade 48-54)' ?></td>
								<td align='center'><?= $grade4854results[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade4854results[0]['yellow'] > 0){ echo $grade4854results[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4854results[0]['green'] > 0){ echo $grade4854results[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4854results[0]['red'] > 0){ echo $grade4854results[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count4854late = 0;
										foreach ($totallate4854result as $key => $value){
											if ($value['total_late'] >= 3){
												$count4854late ++;
											}
										}
									if ($count4854late > 1){ echo $count4854late; } else { echo '-'; }?>
								</td>
								<td></td>
							</tr>
							<tr class="odd">
								<td><?= '3'; ?></td>
								<td><?= 'Professional Management Group (Grade 41-44)' ?></td>
								<td align='center'><?= $grade4144results[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade4144results[0]['yellow'] > 0){ echo $grade4144results[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4144results[0]['green'] > 0){ echo $grade4144results[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade4144results[0]['red'] > 0){ echo $grade4144results[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count4144late = 0;
										foreach ($totallate4144result as $key => $value){
											if ($value['total_late'] >= 3){
												$count4144late ++;
											}
										}
									if ($count4144late > 1){ echo $count4144late; } else { echo '-'; }?>
								</td>
								<td></td>
							</tr>
							<tr class="even">
								<td><?= '4'; ?></td>
								<td><?= 'Executing Group (Grade 17-40)' ?></td>
								<td align='center'><?= $grade1740results[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade1740results[0]['yellow'] > 0){ echo $grade1740results[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade1740results[0]['green'] > 0){ echo $grade1740results[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade1740results[0]['red'] > 0){ echo $grade1740results[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count1740late = 0;
										foreach ($totallate1740result as $key => $value){
											if ($value['total_late'] >= 3){
												$count1740late ++;
											}
										}
									if ($count1740late > 1){ echo $count1740late; } else { echo '-'; }?>
								</td>
								<td></td>
							</tr>
							<tr class="odd">
								<td><?= '5'; ?></td>
								<td><?= 'Executing Group (Grade 1-16)' ?></td>
								<td align='center'><?= $grade116results[0]['total_officer']; ?></td>
								<td align='center'><?php if ($grade116results[0]['yellow'] > 0){ echo $grade116results[0]['yellow']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade116results[0]['green'] > 0){ echo $grade116results[0]['green']; }else{ echo '-'; } ?></td>
								<td align='center'><?php if ($grade116results[0]['red'] > 0){ echo $grade116results[0]['red']; }else{ echo '-'; } ?></td>
								<td align='center'>
									<?php 
										$count116late = 0;
										foreach ($totallate116result as $key => $value){
											if ($value['total_late'] >= 3){
												$count116late ++;
											}
										}
									if ($count116late > 1){ echo $count116late; } else { echo '-'; }?>
								</td>
								<td></td>
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
