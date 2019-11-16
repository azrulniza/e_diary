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
					?>
					<strong><?= __('Late In Report')?></strong><br><br>
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
					</table>
					<br><br>
					
                    <table id="dataTables-reports" border="1" style="border-collapse:collapse;" width='100%'>
                        <thead>
                            <tr>
                                <th><?= __('No.') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Date') ?></th>
                                <th><?= __('In Time') ?></th>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php (isset($this->request['url']['page'])) ? $count = $this->request['url']['page'] * $this->Paginator->param('perPage') : $count =  1 *$this->Paginator->param('perPage');?>

                        <?php foreach ($result as $key => $user):?>
							<tr id="<?= $user->id; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count-$this->Paginator->param('perPage')?></td>
                                <td><?= $user['user_name'] ?></td>
                                <td><?= date('Y-m-d',strtotime($user['cdate'])) ?></td>								
                                <td><?= date('H:i:s',strtotime($user['cdate'])) ?></td>
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
