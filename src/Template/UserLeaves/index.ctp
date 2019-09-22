<div class="row">
    <div class="col-xs-10">
        <div class="box box-default">
            <div class="box-header with-border">
                    <h3 class="box-title"><?= __('List Time Off') ?></h3>
                </div>
            <div class="box-body">
                <div class="designations index dataTable_wrapper table-responsive">
                    <?php if($userRoles->hasRole(['Master Admin'])) :?>

                        <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>

                                    <tr>
                                        <td align="left"><label for="department"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                            <?= $this->Form->create('list',['type' => 'GET','autocomplete' => 'off','method'=>'POST']) ?>
                                            
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected]); ?>
                                             <?= $this->Form->end() ?>
                                        </td>
                                    
                                        <td align="left"><label for="staff"><?php echo $this->Form->label('Staff');?></label></td>
                                        <td>    
                                            <?= $this->Form->create('list',['type' => 'GET','autocomplete' => 'off','method'=>'POST']) ?>
                                            
                                            <?php echo $this->Form->input('staff', ['label'=>false,'options' => $list_user, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$staffSelected]); ?>
                                             <?= $this->Form->end() ?>
                                        </td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                    <?php endif; ?>

                    <table id="dataTables-userLeave" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= __('Date Request') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Time Off Details') ?></th>
                                <th><?= __('Date Time Off') ?></th>
                                <th><?= __('Time Off From') ?></th>
                                <th><?= __('Time Off To') ?></th>
                                <th><?= __('Status') ?></th>
                                <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                            <?php? print_r($userLeaves);?>
                        <?php $count = 0 ?>
                        <?php foreach ($userLeaves as $userLeave): ?>
                            <tr id="<?= $userLeaves['id']; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count ?></td>
                                <td><?= h($userLeave['date_apply']) ?></td>
                                <td><?= h($userLeave['user_name']) ?></td>
                                <td><?= h($userLeave['leave_type_name']) ?></td>
                                
                            </tr>
                        <?php endforeach ?>
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
