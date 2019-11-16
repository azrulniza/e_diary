<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
					<div id='dvContainer'>
                    <?php
					if ($departmentSelected){
						$outputdepartment = $result[0]['organization_name'];
					}else{
						$outputdepartment = __('All');
					}
					if ($userSelected){
						$outputuser = $result[0]['user_name'];
					}else{
						$outputuser = __('All');
					}
					if ($monthselected){
						$outputmonth = $monthselected;
					}else{
						$outputmonth = __('All');
					}
					if ($leaveTypeselected){
						$outputleavetype = $result[0]['leave_type'];
					}else{
						$outputleavetype = __('All');
					}
					?>
					<strong><?= __('Staff Time Off Report')?></strong><br><br>
					<table id="dataTables-reports"  width='40%'>
						<tr>
                            <td><?= __('Departments'); ?></td>
                            <td><?= ':'; ?></td>
                            <td><?= $outputdepartment; ?></td>
						</tr>
						<tr>
                            <td><?= __("Staff's Name"); ?></td>
							<td><?= ':'; ?></td>
                            <td><?= $outputuser; ?></td>
						</tr>
						<tr>
                            <td><?= __('Month'); ?></td>
							<td><?= ':'; ?></td>
                            <td><?= $outputmonth; ?></td>
						</tr>
						<tr>
                            <td><?= __('Leave type'); ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $outputleavetype; ?></td>
						</tr>
					</table>
					<br><br>
					
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
                        <thead>
                            <tr>
                                <th><?= __('No.') ?></th>
                                <th><?= __('Leave Type') ?></th>
                                <th><?= __('Leave Date') ?></th>
                                <th><?= __('Leave Time') ?></th>
                                <th><?= __('Leave Status') ?></th>
                                <th><?= __('Reason') ?></th>
                                <th><?= __('Total Hours') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						foreach ($result as $key => $user):
						
							if($user['date_start'] == $user['date_end']){
								$leave_date = date('Y-m-d',$user['date_start']);
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
								$totalhourOutput.= $h.__('Hour');
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
           
        </div>
    </div>
</div>
