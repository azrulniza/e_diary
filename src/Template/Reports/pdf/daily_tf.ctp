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
					if ($leaveTypeselected){
						$outputleavetype = $result[0]['leave_type'];
					}else{
						$outputleavetype = __('All');
					}
					?>
					<strong>Daily Time Off Report</strong><br><br>
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
                            <td><?= __('Date'); ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $dateselected; ?></td>
						</tr>
						<tr>
                            <td><?= __('Leave Type'); ?></td>
							<td><?= ':'; ?></td>	
                            <td><?= $outputleavetype; ?></td>
						</tr>
					</table>
					<br><br>
					
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
                        <thead>
                            <tr>
                                <th><?= __('Bil') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Leave Type') ?></th>
                                <th><?= __('Leave Date') ?></th>
                                <th><?= __('Leave Time') ?></th>
                                <th><?= __('Leave Status') ?></th>
                                <th><?= __('Reason') ?></th>
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
						?>
                            <tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['user_name'] ?></td>
                                <td><?= $user['leave_type'] ?></td>
                                <td><?= $leave_date ?></td>								
                                <td><?= __('Start Time : ').$user['start_time'].'<br>'.__('End Time : ').$user['end_time'] ?></td>
                                <td><?= $user['leave_status'] ?></td>
                                <td><?= $user['reason'] ?></td>
                            </tr>
                        <?php endforeach ?>
							
                        </tbody>
                    </table>
					</div>
                </div>
            </div>
           
        </div>
    </div>
</div>
