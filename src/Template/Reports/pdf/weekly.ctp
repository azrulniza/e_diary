<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="reports index dataTable_wrapper table-responsive">
					<?php
					if ($departmentSelected){
						$outputdepartment = $weeklyresult[0]['organization_name'];
					}else{
						$outputdepartment = 'All';
					}
					if ($userSelected){
						$outputuser = $weeklyresult[0]['name'];
					}else{
						$outputuser = 'All';
					}
					?>
					<strong>Weekly Attendance Report</strong><br><br>
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
                            <td><?= 'Date Range'; ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $thisweekStart.' to '.$thisweekEnd; ?></td>
						</tr>
					</table>
					<br><br>
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
						<thead>
                            <tr>
                                <th><?= 'Bil'; ?></th>
                                <th><?= 'name'; ?></th>
                                <th><?= 'card no.'; ?></th>
                                <th><?= 'red card in a week'; ?></th>
                                <th><?= 'card colour for end week'; ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						$totalyellow = 0;
						$totalred = 0;
						$totalgreen = 0;
						
						foreach ($weeklyresult as $key => $user):
						if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
						if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
						if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?= $user['total_late'] ?></td>
                                <td><?= $user['card_colour'] ?></td>

                            </tr>
							
                        <?php endforeach ?>
							<tr>
								<td colspan='4'><b><?= 'Total Officer'?></b></td>
								<td colspan='1'><b><?= $count?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Yellow Cards'?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Red Cards'?></b></td>
								<td colspan='1'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Green Cards'?></b></td>
								<td colspan='1'><b><?= $totalgreen?></b></td>
							</tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
