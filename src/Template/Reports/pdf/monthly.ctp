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
						$outputdepartment = __('All');
					}
					if ($userSelected){
						$outputuser = $monthlyresult[0]['name'];
					}else{
						$outputuser = __('All');
					}
					?>
					<strong><?= __('Monthly Attendance Report') ?></strong><br><br>
					<table id="dataTables-reports"  width='40%'>
						<tr>
                            <td><?= __('Department'); ?></td>
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
                            <td><?= $monthselected; ?></td>
						</tr>
					</table>
					<br><br>
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
                        <thead>
                            <tr>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('No.') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Name') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Grade') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Card No.') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Total Late') ?></th>
                                <th colspan=2 style='text-align: center;'><?= __('Officer Approval') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Card Colour') ?></th>
                                <th rowspan=2 style='vertical-align: text-top;'><?= __('Remarks') ?></th>
                           </tr>
						   <tr>
                                <th style='text-align: center;'><?= __('With Approval') ?></th>
                                <th style='text-align: center;'><?= __('Without Approval') ?></th>
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
						$user['card_colour'] = 'Yellow';
						if($user['redcard'] == 3){ $user['card_colour'] = 'Red'; }
						if($user['redcard'] > 3){ $user['card_colour'] = 'Green'; }
						if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
						if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
						if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
						if ($user['total_late'] >= 3){$total3times += 1;}
						
						$late_not_approved = $user['total_late'] - $user['approved_late'];
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['grade'].$user['skim'] ?></td>
                                <td><?= $user['card_no'] ?></td>
                                <td><?php if ($user['total_late']>0){ echo $user['total_late']; }else{ echo '-'; } ?></td>
                                <td align='center'><?php echo $user['approved_late'];?></td>
                                <td align='center'><?php if($late_not_approved > 0) {echo $late_not_approved;} ?></td>
								<td>
									<?php if ($user['card_colour']) {?>
									<b style="color:<?= $user['card_colour'] ?>"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" class="fa fa-square"></span></b>
									<?php } ?>
									<?= __($user['card_colour']) ?>
								</td>
                                <td><?= $user['card_remarks'] ?></td>

                            </tr>
                        <?php endforeach ?>
							<tr>
								<td colspan='5'><b><?= __('Total Officer')?></b></td>
								<td colspan='4'><b><?= $count?></b></td>
							</tr>
							<tr>
								<td colspan='5'><b><?= __('Total Officer Late More Than 2 Times')?></b></td>
								<td colspan='4'><b><?= $total3times?></b></td>
							</tr>
							<!--<tr>
								<td colspan='4'><b><?= __('Total Officer That Hold Yellow Cards')?></b></td>
								<td colspan='3'><b><?= $totalyellow?></b></td>
							</tr>-->
							<tr>
								<td colspan='5'><b><?= __('Total Officer That Hold Red Cards')?></b></td>
								<td colspan='4'><b><?= $totalred?></b></td>
							</tr>
							<tr>
								<td colspan='5'><b><?= __('Total Officer That Hold Green Cards')?></b></td>
								<td colspan='4'><b><?= $totalgreen?></b></td>
							</tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
