<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="reports index dataTable_wrapper table-responsive">
					<?php
					if ($departmentSelected){
						$outputdepartment = $monthlyresult[0]['organization_name'];
					}else{
						$outputdepartment = 'All';
					}
					if ($userSelected){
						$outputuser = $monthlyresult[0]['name'];
					}else{
						$outputuser = 'All';
					}
					?>
					<strong>Monthly Attendance Report</strong><br><br>
					<table id="dataTables-reports"  width='40%'>
						<tr>
                            <td><?= 'Department'; ?></td>
                            <td><?= ':'; ?></td>
                            <td><?= $outputdepartment; ?></td>
						</tr>
						<tr>
                            <td><?= "Staff's Name"; ?></td>
							<td><?= ':'; ?></td>
                            <td><?= $outputuser; ?></td>
						</tr>
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
                                <th><?= 'Bil'; ?></th>
                                <th><?= 'Name'; ?></th>
                                <th><?= 'Grade'; ?></th>
                                <th><?= 'Card No.'; ?></th>
                                <th><?= 'Total Late'; ?></th>
                                <th><?= 'Officer Approval'; ?></th>
                                <th><?= 'Card Colour'; ?></th>
                                <th><?= 'Remarks'; ?></th>
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
        </div>
    </div>
</div>
