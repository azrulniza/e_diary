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
                                <th><?= __('Date Start') ?></th>
                                <th><?= __('Date End') ?></th>
                                <th><?= __('Time Off From') ?></th>
                                <th><?= __('Time Off To') ?></th>
                                <th><?= __('Status') ?></th>
                                <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor','Staff'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php //echo $sql_leave;?>
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
                                <td><?= h($userLeave['date_start']) ?></td>
                                <td><?= h($userLeave['date_end']) ?></td>
                                <td><?= h($userLeave['start_time']) ?></td>
                                <td><?= h($userLeave['end_time']) ?></td>
                                <td><b style="color:<?php if($userLeave['leave_status_id']==1){echo "orange";}elseif($userLeave['leave_status_id']==2){echo "green";}elseif($userLeave['leave_status_id']==3){echo "red";}elseif($userLeave['leave_status_id']==4){echo "default";} elseif($userLeave['leave_status_id']==5){echo "default";} ?>">
                                    
                                    <?= h($userLeave['leave_status_name']) ?>
                                     </b>
                                    <?php if(!empty($userLeave['remark'])){ ?>

                                        <br/><?= h($userLeave['remark']) ?><br/>
                                        
                                    <?php } ?>
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?php if($userLeave['leave_status_id']==1){
                                         
                                            if($userRoles->hasRole(['Staff'])){
                                                echo $this->Form->postLink($this->Html->tag('i', __('Cancel')), ['action' => 'cancel', $userLeave['id']], ['escape' => false, 'title' => __('Cancel'), 'class' => 'btn btn-default btn-xs', 'data-toggle'=>'tooltip', 'confirm' => __('Are you sure you want to CANCEL this leave application {0}?', $userLeave['reason'])]);
                                            }

                                            if($userRoles->hasRole(['Master Admin','Supervisor'])){
                                                echo $this->Form->postLink($this->Html->tag('i', __('Approve')), ['action' => 'approve', $userLeave['id']], ['escape' => false, 'title' => __('Approve'), 'class' => 'btn btn-success btn-xs','data-toggle'=>'tooltip','confirm' => __('Are you sure you want to APPROVE this leave application {0}?', $userLeave['reason'])]);

                                                //echo $this->Html->link($this->Html->tag('i', __('Reject')), ['action' => 'update', $userLeave['id']], ['escape' => false, 'title' => __('Reject'), 'class' => 'btn btn-danger btn-xs','data-toggle'=>'tooltip']);
                                                echo $this->Html->link($this->Html->tag('i', __('Reject')), ['action' => '#modal-container-171766'.$userLeave['id']], ['escape' => false, 'title' => __('Reject'), 'class' => 'btn btn-danger btn-xs','data-toggle'=>'modal', 'role'=>'button']);
                                            ?>
                                                <div class="modal fade" id="modal-container-171766<?php echo $userLeave['id'];?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?= __('Reject Time Off Application'); ?>
                                                                </h4> 
                                                            </div>
                                                            <div class="modal-body">
                                                                <dl class="dl-horizontal">
                                                                    <dt style='text-align:left;'><?= __('Name') ?></dt>
                                                                    <dd><?= h($userLeave['user_name']) ?></dd>
                                                                    <dt style='text-align:left;'><?= __('Time Off Details') ?></dt>
                                                                    <dd><b><?= h($userLeave['leave_type_name']) ?></b><br/><?= h($userLeave['reason']) ?><br/>
                                                                        <?php 
                                                                            if(!empty($userLeave['filename'])){ ?>
                                                                                <a href="<?php echo $this->Url->build( '/' ).$userLeave['filename']?>" target="__blank"><?php $str=explode("/",$userLeave['filename']); echo $str[4];?></a>

                                                                        <?php    }
                                                                        ?>
                                                                    </dd>
                                                                    <dt style='text-align:left;'><?= __('Date Start') ?></dt>
                                                                    <dd><?= h($userLeave['date_start']) ?></dd>
                                                                    <dt style='text-align:left;'><?= __('Date End') ?></dt>
                                                                    <dd><?= h($userLeave['date_end']) ?></dd>
                                                                    <dt style='text-align:left;'><?= __('Time Off From') ?></dt>
                                                                    <dd><?= h($userLeave['start_time']) ?></dd>
                                                                    <dt style='text-align:left;'><?= __('Time Off To') ?></dt>
                                                                    <dd><?= h($userLeave['end_time']) ?></dd>
                                                                </dl>

                                                                <?php 
                                                                    echo $this->Form->create(null, ['url' => ['controller' => 'UserLeaves', 'action' => 'reject'] ]);
                                                                    echo $this->Form->input('remark', ['type'=>'textarea','required'=>true,'class' => 'form-control', 'placeholder' => __('Enter ...')]);
                                                                    echo $this->Form->input('user_leave_id', ['type'=>'hidden', 'value'=>$userLeave['id']]);
                                                                    echo $this->Form->input('user_id', ['type'=>'hidden', 'value'=>$userLeave['user_id']]);
                                                                   
                                                                ?>
                                                               
                                                            </div>
                                                            <div class="modal-footer">
                                                                 <?= $this->Form->button(__('Reject'), ['class'=>'btn btn-danger']) ?>
                                                                
                                                                 <?= $this->Form->end() ?>
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    <?= __('Close') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                            <?php 
                                            }
                                        }?>
                                    
                                       
                                        <?php if($userLeave['leave_status_id']==2){
                                            if($userRoles->hasRole(['Master Admin','Supervisor','Staff'])){
                                             echo $this->Form->postLink($this->Html->tag('i', __('Void')), ['action' => 'void', $userLeave['id']], ['escape' => false, 'title' => __('Void'), 'class' => 'btn btn-default btn-xs', 'data-toggle'=>'tooltip', 'confirm' => __('Are you sure you want to VOID this leave application {0}?', $userLeave['reason'])]);
                                            }

                                        }?>

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
