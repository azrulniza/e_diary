
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
                                <th><?= __('No.') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Department') ?></th>
                                <th><?= __('Status') ?></th>
                                <th><?= __('In') ?></th>
                                <th><?= __('Out') ?></th>
                                <th><?= __('Remark') ?></th>
                                <th><?= __('Superior Approval') ?></th>
                                <th><?= __('Card Color') ?></th>
                                <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor','Staff'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php //print_r($attendances);?>
                        <?php $count = 0 ?>
        
                        <?php foreach ($attendances as $attendance): ?>
                                
                            <?php $count_red=0; foreach($attendance['user_cards'] as $data){
                                   if($data->card_id==3){
                                        $count_red++;
                                   }
                            }
                            ?>

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
                                    
                                    <?php if($attendance['late_remark_status']==1){
                                        $time_color="#000000";
                                    }else{
                                        $time_color=$attendance['card'];
                                        if($time_color=='Yellow'){
                                            $time_color="#000000";
                                        }
                                    }?> 
                                    <p style="color:<?php echo $time_color?>"><?php echo date_format($attendance['in'],"H:i a");?></p> 
                                   
                                <?php } ?>
                                </td>
                                <td><?php if($attendance['out']!=""){
                                    //$date_out=date_create($attendance['out']);
                                        echo date_format($attendance['out'],"H:i a");
                                }
                                
                                ?></td>
                               
                                <td><?= $attendance['late_remark'] ?></td>
                                <td><?php if($attendance['late_remark_id'] > 0 AND $attendance['late_remark_status']==1){
                                        echo __("Yes");
                                    }else{
                                        echo __("No");
                                    } ?>
                                    
                                </td> 
                                <td>
                                    <?php
                                        if($count_red==3){ ?>
                                            <b style="color:red"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" , class="fa fa-square"></span></b> <?= __('Red')?>

                                        <?php }else if($count_red > 3){ ?>
                                            <b style="color:green"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" , class="fa fa-square"></span></b> <?= __('Green')?>
                                        <?php }else{?>
                                            <b style="color:yellow"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" , class="fa fa-square"></span></b> <?= __('Yellow')?>
                                        <?php } ?>
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                        <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'update', $attendance['user_id']], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-default btn-xs','data-toggle'=>'tooltip','title'=>__('Update Attendance')]) ?>                    
                                        <?php endif; ?>
                                    </div>
                                    <?php if($attendance['in']!="" AND $attendance['card']=='Red' AND $attendance['late_remark_status']!='1'){?>
                                    <div class="btn-group">
                                        <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor','Staff'])) :?>
                                            <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-book','data-toggle'=>'tooltip','title' => __('Late Remark')]), ['action' => '#modal-container-171766'.$attendance['id']], ['escape' => false, 'class' => 'btn btn-default btn-xs','data-toggle'=>'modal']); ?>

                                            <div class="modal fade" id="modal-container-171766<?php echo $attendance['id'];?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?= __('Late Remark'); ?>
                                                                </h4> 
                                                            </div>
                                                            <div class="modal-body">
                                                                <dl class="dl-horizontal">
                                                                    <dt style='text-align:left;'><?= __('Name') ?></dt>
                                                                    <dd><?= h($attendance['username']) ?></dd>
                                                                    <dt style='text-align:left;'><?= __('Date') ?></dt>
                                                                    <dd><?= h($today_date) ?></dd>
                                                                    <dt style='text-align:left;'><?= __('In') ?></dt>
                                                                    <dd><?php if($attendance['in']!=""){ ?>
                                   
                                                                        <p style="color:<?php echo $attendance['card']?>"><?php echo date_format($attendance['in'],"H:i a");?></p> 
                       
                                                                    <?php } ?>
                                                                    </dd>
                                                                    
                                                                    <dt style='text-align:left;'><?= __('Out') ?></dt>
                                                                    <dd><?php if($attendance['out']!=""){
                                                                            echo date_format($attendance['out'],"H:i a");
                                                                        }
                                                                        
                                                                        ?>
                                                                    </dd>
                                                                    
                                                                </dl>

                                                                <?php 
                                                                    echo $this->Form->create(null, ['url' => ['controller' => 'Attendances', 'action' => 'index'] ]);
                                                                    echo $this->Form->input('remark', ['type'=>'textarea','required'=>true,'class' => 'form-control', 'placeholder' => __(''),'value'=>$attendance['late_remark']]);
                                                                    echo $this->Form->input('attendance_id', ['type'=>'hidden', 'value'=>$attendance['attendance_id']]);
                                                                    
                                                                   
                                                                ?>
                                                               
                                                            </div>
                                                            <div class="modal-footer">
                                                                 <?= $this->Form->button( __('Submit'), ['class'=>'btn btn-primary']) ?>
                                                                
                                                                 <?= $this->Form->end() ?>
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    <?= __('Close') ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php } ?>
                                </td>
                                
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
