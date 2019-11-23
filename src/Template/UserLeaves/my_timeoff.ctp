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
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Year');?></label></td>
                                        <td>    
                                            
                                            <?php
                                                for($year = date('Y') ; $year <= date('Y',strtotime('+3 year')); $year++){
                                                    $arr_year[$year] = $year;
                                                }
                                                echo $this->Form->input('att_year', ['label'=>false,'options' => $arr_year, 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'att_year', 'value'=>$yearselected]); 
                                                
                                             ?>
                                            
                                           
                                        </td>

                                    </tr>
                                    <tr>
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Month');?></label></td>
                                        <td>    
                                           
                                            <?php
                                                $month = array(
                                                  '01' => __('January'), 
                                                  '02' => __('February'), 
                                                  '03' => __('March'), 
                                                  '04' => __('April'), 
                                                  '05' => __('May'), 
                                                  '06' => __('June'), 
                                                  '07' => __('July'), 
                                                  '08' => __('August'), 
                                                  '09' => __('September'), 
                                                  '10' => __('October'), 
                                                  '11' => __('November'), 
                                                  '12' => __('December'));

                                                echo $this->Form->input('att_month', ['label'=>false,'options' => $month, 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'att_month', 'value'=>$monthselected]); 
                                                

                                             ?>
                                            
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        
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
                                <th><?= __('No.') ?></th>
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
                                            <a href="<?php echo $this->Basepath->getBasePath().$userLeave['filename']?>" target="__blank"><?php $str=explode("/",$userLeave['filename']); echo $str[4];?></a>

                                    <?php    }
                                    ?>
                                </td>
                                <td><?= h($userLeave['date_start']) ?></td>
                                <td><?= h($userLeave['date_end']) ?></td>
                                <td><?= h($userLeave['start_time']) ?></td>
                                <td><?= h($userLeave['end_time']) ?></td>
                                <td><b style="color:<?php if($userLeave['leave_status_id']==1){echo "orange";}elseif($userLeave['leave_status_id']==2){echo "green";}elseif($userLeave['leave_status_id']==3){echo "red";}elseif($userLeave['leave_status_id']==4){echo "default";} elseif($userLeave['leave_status_id']==5){echo "default";} ?>">
                                    
                                    <?php echo __($userLeave['leave_status_name']) ?>
                                     </b>
                                    <?php if(!empty($userLeave['remark'])){ ?>

                                        <br/><?= h($userLeave['remark']) ?><br/>
                                        
                                    <?php } ?>
                                </td>
                                
                                <td class="actions">
                                    <div class="btn-group">
                                        <?php if($userLeave['leave_status_id']==1){
                                         
                                            echo $this->Form->postLink($this->Html->tag('i', __('Cancel')), ['action' => 'cancel', $userLeave['id']], ['escape' => false, 'title' => __('Cancel'), 'class' => 'btn btn-default btn-xs', 'data-toggle'=>'tooltip', 'confirm' => __('Are you sure you want to CANCEL this leave application?')]);
                                           
                                        }?>
                                    
                                       
                                        <?php if($userLeave['leave_status_id']==2){
                                            
                                            echo $this->Form->postLink($this->Html->tag('i', __('Void')), ['action' => 'void', $userLeave['id']], ['escape' => false, 'title' => __('Void'), 'class' => 'btn btn-default btn-xs', 'data-toggle'=>'tooltip', 'confirm' => __('Are you sure you want to VOID this leave application?')]);
                                            

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
