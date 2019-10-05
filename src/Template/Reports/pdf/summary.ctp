<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="reports index dataTable_wrapper table-responsive">
                    <div class="reports index dataTable_wrapper table-responsive">
					
					<table id="dataTables-reports"  width='40%'>
						<tr>
                            <td><?= 'Month'; ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $monthselected; ?></td>
						</tr>
					</table>
					<br><br>
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
						<thead>
                            <tr>
                                <th rowspan=2><center><?= 'Bil'; ?></center><br/></th>
                                <th rowspan=2><center><?= 'Officer Group'; ?></center><br/></th>
                                <th rowspan=2><center><?= 'Total Officer'; ?></center><br/></th>
                                <th colspan=3><center><?= 'Card Colour'; ?></center></th>
                                <th rowspan=2><center><?= 'Three late in a month (total officer)'; ?></center><br/></th>
                                <th rowspan=2><center><?= 'Remarks'; ?></center><br/></th>
                            </tr>
							<tr>
                                <th><center><?= 'Yellow'; ?></center></th>
                                <th><center><?= 'Green'; ?></center></th>
                                <th><center><?= 'Red'; ?></center></th>
                            </tr>
                        </thead>
						
                        <tbody class="ui-sortable">
							<tr class="even">
								<td><?= '1'; ?></td>
								<td><?= 'Higher Management Group' ?></td>
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
								<td><?= 'Professional Management Group (Grade 48-54)' ?></td>
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
								<td><?= 'Professional Management Group (Grade 41-44)' ?></td>
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
								<td><?= 'Executing Group (Grade 17-40)' ?></td>
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
								<td><?= 'Executing Group (Grade 1-16)' ?></td>
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
								<td align='center' colspan='2'><b><?= 'Grand Total'?></b></td>
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
        </div>
    </div>
</div>
