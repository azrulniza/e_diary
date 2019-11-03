
<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="attendances index dataTable_wrapper table-responsive">
                    <h4><?php echo __("Summary Attendance for today, ").$today_date;?></h4>
                    <?php //print_r($attendance_in)?>
                    <?php if($userRoles->hasRole(['Master Admin'])) :?>

                        <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>

                                    <tr>
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                            <?= $this->Form->create('list',['type' => 'GET','autocomplete' => 'off','method'=>'POST']) ?>
                                            
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected]); ?>
                                             <?= $this->Form->end() ?>
                                        </td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                    <?php endif; ?>
                    <table id="dataTables-attendances" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Department') ?></th>
                                <th><?= __('Status') ?></th>
                                <th><?= __('In') ?></th>
                                <th><?= __('Out') ?></th>
                                
                                <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php //print_r($attendances);?>
                        <?php $count = 0 ?>
        
                        <?php foreach ($attendances as $attendance): ?>
                            
                            <tr id="<?= $attendance['attendance_id']; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $this->Number->format($count) ?></td>
                                <td>
                                    <?= $attendance['username'];?>
                                </td>   
                                <td><?= $attendance['organization_name'] ?></td>
                                <td>
                                    <?php 
                                    if($attendance['status']==2){ 
                                        $color='red';
                                    }else if($attendance['status']==1){
                                        $color='green';
                                    }
                                    ?>
                                    <p style="color:<?php echo $color;?>"><?= $attendance['attendance_code_name'] ?></p>
                                </td>
                                <td><?php if($attendance['in']!=""){ ?>
                                    
                                   
                                    <p style="color:<?php echo $attendance['card']?>"><?php echo date_format($attendance['in'],"H:i a");?></p> 

                                     
                                <?php } ?>
                                </td>
                                <td><?php if($attendance['out']!=""){
                                    //$date_out=date_create($attendance['out']);
                                        echo date_format($attendance['out'],"H:i a");
                                }
                                
                                ?></td>
                               
                               <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                    <td class="actions">
                                        <div class="btn-group">
                                            
                                            <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'update', $attendance['user_id']], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-default btn-xs','data-toggle'=>'tooltip','title'=>__('Update Attendance')]) ?>                    
                        
                                        </div>
                                        
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
