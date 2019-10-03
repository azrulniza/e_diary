<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="reports index dataTable_wrapper table-responsive">
                    <table id="dataTables-reports" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th colspan='5'><?= 'Date from :'.$thisweekStart.' to '.$thisweekEnd ?></th>
							</tr>
                        </thead>
						<thead>
                            <tr>
                                <th><?= $this->Paginator->sort('Bil') ?></th>
                                <th><?= $this->Paginator->sort('name') ?></th>
                                <th><?= $this->Paginator->sort('card no.') ?></th>
                                <th><?= $this->Paginator->sort('red card in a week') ?></th>
                                <th><?= $this->Paginator->sort('card colour for end week') ?></th>
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
            <div class="box-footer">
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                    </ul>
                    <p><?= $this->Paginator->counter() ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
