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
						$outputdepartment = 'All';
					}
					if ($userSelected){
						$outputuser = $result[0]['name'];
					}else{
						$outputuser = 'All';
					}
					?>
					<table id="dataTables-reports"  width='40%'>
						<tr>
                            <td><?= 'Department'; ?></td>
                            <td><?= ':'; ?></td>
                            <td><?= $outputdepartment; ?></td>
						</tr>
						<tr>
                            <td><?= 'User'; ?></td>
							<td><?= ':'; ?></td>
                            <td><?= $outputuser; ?></td>
						</tr>
						<tr>
                            <td><?= 'Date'; ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $dateselected; ?></td>
						</tr>
					</table>
					<br><br>
					
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
                        <thead>
                            <tr>
                                <th><?= 'Bil'; ?></th>
                                <th><?= 'name'; ?></th>
                                <th><?= 'card no.'; ?></th>
                                <th><?= 'in time'; ?></th>
                                <th><?= 'out time'; ?></th>
                                <th><?= 'remarks'; ?></th>
                                <th><?= 'total hour'; ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php 
						
						$totalyellow = 0;
						$totalred = 0;
						$totalgreen = 0;
						
						foreach ($result as $key => $user):
						
						$result = explode("||",$user['attn_time']);
						//count hour
						$diff = strtotime($result[1]) - strtotime($result[0]);
						$hours = $diff / ( 60 * 60 );
						
						//filtershow
						$showData = 1;
						if($filterSelected == 1){
							$showData = 0;
							if(date('H:i:s',strtotime($result[0]))	 > date('H:i:s',strtotime('09:00:00'))){
								$showData = 1;
							}
						}if($filterSelected == 2){
							$showData = 0;
							if($user['attn_time'] != '' && $hours < '9'){
								$showData = 1;
							}
						}if($filterSelected == 3){
							$showData = 0;
							if($user['attn_time'] == ''){
								$showData = 1;
							}
						}
						
						if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
						if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
						if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
						if($showData == 1) {
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?php if ($result[0] !=''){ echo date('H:i:s',strtotime($result[0]));} ?></td>
                                <td><?php if ($result[1] !=''){ echo date('H:i:s',strtotime($result[1]));} ?></td>
                                <td><?= $user['attn_remarks'] ?></td>
                                <td><?php if ($diff > 0){ echo round($hours, 2). ' Hours';} else { } ?></td>
                            </tr>
						<?php } ?>
                        <?php endforeach ?>
							<tr>
								<td colspan='4'><b><?= 'Total Officer'?></b></td>
								<td colspan='3'><b><?= $count?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Yellow Cards'?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Red Cards'?></b></td>
								<td colspan='3'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='4'><b><?= 'Total Officer That Hold Green Cards'?></b></td>
								<td colspan='3'><b><?= $totalgreen?></b></td>
							</tr>
                        </tbody>
                    </table>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
