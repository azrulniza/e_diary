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
					?>
					<strong><?= __('Staff Working Hour Report')?></strong><br><br>
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
                            <td><?= __('Attendance Month'); ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $monthselected; ?></td>
						</tr>
					</table>
					<br><br>
                   <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
                        <thead>
                            <tr>
                                <th><?= __('Bil') ?></th>
                                <th><?= __('Date') ?></th>
                                <th><?= __('In Time') ?></th>
                                <th><?= __('Out Time') ?></th>
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
									?>
								
										<td><?php if($results[0] !=''){ echo date('H:i:s',strtotime($results[0]));} ?></td>
										<td><?php if($results[1] !=''){ echo date('H:i:s',strtotime($results[1]));} ?></td>
										<td>
										<?php if ($diff > 0){
											if($hours<9){
												echo "<font color='red'>";
											}
											echo round($hours, 2). __('Hour');} else { } ?></td>
									<?php } ?>
								<?php endforeach ?>
								<?php if($latestDate != $currentDate){
									echo '<td></td><td></td><td></td>';
								 } 
								 ?>
							</tr>
                        <?php }?>
							<tr>
                                <td colspan='4' align='right'><strong><?= __('Grand Total')?></strong></td>
								<td><strong><?php $ghours = $grandtotaldiff / ( 60 * 60 ); echo round($ghours, 2). __('Hour'); ?></strong></td>
                               
                            </tr>
                        </tbody>
						
                    </table>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
