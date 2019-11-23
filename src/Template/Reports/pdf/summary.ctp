<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="reports index dataTable_wrapper table-responsive">
                    <div class="reports index dataTable_wrapper table-responsive">
					
					<strong><?= __('Monthly Summary Attendance Report') ?></strong><br><br>
					<table id="dataTables-reports"  width='40%'>
						<tr>
                            <td><?= __('Month'); ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $monthselected; ?></td>
						</tr>
					</table>
					<br><br>
					 <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
						<thead>
                            <tr>
                                <th rowspan=2><center><?= __('No.'); ?></center><br/></th>
                                <th rowspan=2><center><?= __('Officer Group'); ?></center><br/></th>
                                <th rowspan=2><center><?= __('Total Officer'); ?></center><br/></th>
                                <th colspan=3><center><?= __('Card Colour'); ?></center></th>
                                <th rowspan=2><center><?= __('Three late in a month (total officer)'); ?></center><br/></th>
                            </tr>
							<tr>
                                <th><center><?= __('Yellow'); ?></center></th>
                                <th><center><?= __('Green'); ?></center></th>
                                <th><center><?= __('Red'); ?></center></th>
                            </tr>
                        </thead>
						
                         <tbody class="ui-sortable">
							<tr class="even">
								<?php 
								if($grade55result[0]['total_officer'] > 0){
									$yellowholder55 = $grade55result[0]['total_officer'] - $grade55result[0]['green'] - $grade55result[0]['red'];
								} else {$yellowholder55 = '-';}
								?>
								<td><?= '1'; ?></td>
								<td><?= __('Higher Management Group') ?></td>
								<td align='center'><?= $grade55result[0]['total_officer']; ?></td>
								<!--<td align='center'><?php if ($grade55result[0]['yellow'] > 0){ echo $grade55result[0]['yellow']; }else{ echo '-'; } ?></td>-->
								<td align='center'><?php echo $yellowholder55;?></td>
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
							</tr>
							<tr class="even">
								<?php 
								if($grade4854result[0]['total_officer'] >0){
									$yellowholder4854 = $grade4854result[0]['total_officer'] - $grade4854result[0]['green'] - $grade4854result[0]['red'];
								} else {$yellowholder4854 = '-';}
								?>
								<td><?= '2'; ?></td>
								<td><?= __('Professional Management Group (Grade 48-54)') ?></td>
								<td align='center'><?= $grade4854result[0]['total_officer']; ?></td>
								<!--<td align='center'><?php if ($grade4854result[0]['yellow'] > 0){ echo $grade4854result[0]['yellow']; }else{ echo '-'; } ?></td>-->
								<td align='center'><?php echo $yellowholder4854;?></td>
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
							</tr>
							<tr class="odd">
								<?php 
								if($grade4144result[0]['total_officer'] > 0){
									$yellowholder4144 = $grade4144result[0]['total_officer'] - $grade4144result[0]['green'] - $grade4144result[0]['red'];
								} else {$yellowholder4144 = '-';}
								?>
								<td><?= '3'; ?></td>
								<td><?= __('Professional Management Group (Grade 41-44)') ?></td>
								<td align='center'><?= $grade4144result[0]['total_officer']; ?></td>
								<!--<td align='center'><?php if ($grade4144result[0]['yellow'] > 0){ echo $grade4144result[0]['yellow']; }else{ echo '-'; } ?></td>-->
								<td align='center'><?php echo $yellowholder4144;?></td>
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
							</tr>
							<tr class="even">
								<?php 
								if($grade1740result[0]['total_officer'] > 0){
									$yellowholder1740 = $grade1740result[0]['total_officer'] - $grade1740result[0]['green'] - $grade1740result[0]['red'];
								} else {$yellowholder1740 = '-';}
								?>
								<td><?= '4'; ?></td>
								<td><?= __('Executing Group (Grade 17-40)') ?></td>
								<td align='center'><?= $grade1740result[0]['total_officer']; ?></td>
								<!--<td align='center'><?php if ($grade1740result[0]['yellow'] > 0){ echo $grade1740result[0]['yellow']; }else{ echo '-'; } ?></td>-->
								<td align='center'><?php echo $yellowholder1740;?></td>
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
							</tr>
							<tr class="odd">
								<?php 
								if($grade116result[0]['total_officer'] > 0){
									$yellowholder116 = $grade116result[0]['total_officer'] - $grade116result[0]['green'] - $grade116result[0]['red'];
								} else {$yellowholder116 = '-';}
								?>
								<td><?= '5'; ?></td>
								<td><?= __('Executing Group (Grade 1-16)') ?></td>
								<td align='center'><?= $grade116result[0]['total_officer']; ?></td>
								<!--<td align='center'><?php if ($grade116result[0]['yellow'] > 0){ echo $grade116result[0]['yellow']; }else{ echo '-'; } ?></td>-->
								<td align='center'><?php echo $yellowholder116;?></td>
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
							</tr>
							<tr>
								<?php 
									$gtotalstaff= $grade116result[0]['total_officer'] + $grade1740result[0]['total_officer'] +$grade4144result[0]['total_officer'] + $grade4854result[0]['total_officer'] +$grade55result[0]['total_officer'];
									
									$gtotalyellow= $yellowholder116 + $yellowholder1740 + $yellowholder4144 + $yellowholder4854 + $yellowholder55;
									
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
							</tr>
            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
