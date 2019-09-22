<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header with-border">
                    <h3 class="box-title"><?= __('List Time Off') ?></h3>
                </div>
            <div class="box-body">
                <div class="designations index dataTable_wrapper table-responsive">
                
                        <?= $this->Form->create('list',['type' => 'GET','autocomplete' => 'off','method'=>'POST']) ?>
                        <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>
                                    <tr>
                                        <?php if($userRoles->hasRole(['Master Admin'])) :?>
                                        <td align="left"><label for="department"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                           
                                            
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected]); ?>
                                             
                                        </td>
                                        <?php endif; ?>
                                        <?php if($userRoles->hasRole(['Master Admin']) OR $userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) :?>
                                        <td align="left"><label for="staff"><?php echo $this->Form->label('Staff');?></label></td>
                                        <td>                                             
                                            
                                            <?php echo $this->Form->input('staff', ['label'=>false,'options' => $list_user, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$staffSelected]); ?>
                                             
                                        </td>
                                        <?php endif; ?>
                                        <td align="left"><label for="status"><?php echo $this->Form->label('Status');?></label></td>
                                        <td>    
                    
                                            <?php echo $this->Form->input('status', ['label'=>false,'options' => $list_status, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$statusSelected]); ?>
                                            
                                        </td>
                                    </tr>
                                   <tr>
                                       <td></td>
                                   </tr>
                                </tbody>
                            </table>
                             <?= $this->Form->end() ?>                   

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
                                <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor','Staff'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php //print_r($userLeaves);?>
                        <?php $count = 0 ?>
                        <?php foreach ($userLeaves as $userLeave): ?>
                            <tr id="<?= $userLeaves['id']; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $count ?></td>
                                <td><?php $str_date=explode(" ", $userLeave['cdate']); echo $str_date[0];?></td>
                                <td><?= h($userLeave['user_name']) ?></td>
                                <td><b><?= h($userLeave['leave_type_name']) ?></b><br/><?= h($userLeave['reason']) ?><br/>
                                    <?php 
                                        if(!empty($userLeave['filename'])){ ?>
                                            <a href="<?php echo $this->Url->build( '/' ).$userLeave['filename']?>" target="__blank"><?php $str=explode("/",$userLeave['filename']); echo $str[4];?></a>

                                    <?php    }
                                    ?>
                                </td>
                                <td><?= h($userLeave['date_apply']) ?></td>
                                <td><?= h($userLeave['start_time']) ?></td>
                                <td><?= h($userLeave['end_time']) ?></td>
                                <td><h4><span class="label label-<?php if($userLeave['leave_status_id']==1){echo "warning";}elseif($userLeave['leave_status_id']==2){echo "success";}elseif($userLeave['leave_status_id']==3){echo "danger";}elseif($userLeave['leave_status_id']==4){echo "default";}    ?>"><?= h($userLeave['leave_status_name']) ?></span></h4></td>
                                <td class="actions">
                                        <div class="btn-group">
                                            
                                            <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'update', $userLeave['id']], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-default btn-xs','data-toggle'=>'tooltip','title'=>'Update']) ?>
                        
                                        </div>
                                    </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--<div class="box-footer">
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                    </ul>
                    <p><?= $this->Paginator->counter() ?></p>
                </div>
            </div>-->   
        </div>
    </div>
</div>
