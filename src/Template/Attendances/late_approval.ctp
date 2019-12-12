
<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header">
                   <h4><?php echo __("Late Attendance Approval")?></h4>
            </div>
            <div class="box-body">
                <div class="attendances index dataTable_wrapper table-responsive">
                 
                    <?php //print_r($attendance_in); die;?>
                    <?php if($userRoles->hasRole(['Ketua Pengarah','Master Admin','Admin','Supervisor'])) :?>

                        <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>
                                    <tr> 
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Date');?></label></td>
                                        <td>    
                                            <?= $this->Form->create('card',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
                                            <?php
                                                $control_html = 
                                                    $this->Form->input(
                                                      'dateChoose', [
                                                        'type' => 'text',
                                                        'id' => 'dateChoose',
                                                        'label'=>false,
                                                        'class' => 'form-control autosubmit',
                                                        'onchange' => 'this.form.submit()',
                                                        'style'=>'width:100%',
                                                        'value'=>$dateSelected
                                                      ]);

                                                $date_control = 
                                                    str_replace(
                                                   'type="text"', 
                                                   'type="date"', 
                                                   $control_html
                                                );
                                             
                                                echo $date_control;
                                                echo $this->Form->input('department', ['type'=>'hidden','class' => 'form-control', 'value' => $organizationSelected]);
                                                echo $this->Form->input('status', ['type'=>'hidden','class' => 'form-control', 'value' => $statusSelected]);
                                             ?>
                                            
                                            <?= $this->Form->end() ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                            
                                            <?= $this->Form->create('list',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected]); ?>
                                            <?php echo $this->Form->input('dateChoose', ['type'=>'hidden','class' => 'form-control', 'value' => $dateSelected]); ?>
                                            <?php echo $this->Form->input('status', ['type'=>'hidden','class' => 'form-control', 'value' => $statusSelected]); ?>
                                             <?= $this->Form->end() ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Status');?></label></td>
                                        <td>    
                                            
                                            <?= $this->Form->create('list',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
                                            <?php echo $this->Form->input('status', ['label'=>false,'options' => $attendanceLateStatus, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$statusSelected]); ?>
                                            <?php echo $this->Form->input('dateChoose', ['type'=>'hidden','class' => 'form-control', 'value' => $dateSelected]); ?>
                                            <?php echo $this->Form->input('department', ['type'=>'hidden','class' => 'form-control', 'value' => $organizationSelected]); ?>
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
                                <th><?= __('Date') ?></th>
                                <th><?= __('In') ?></th>
                                
                                <th><?= __('Late Remark') ?></th>
                                <th><?= __('Status') ?></th>
                               
                                <?php if($userRoles->hasRole(['Ketua Pengarah','Master Admin','Admin','Supervisor'])) :?>
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
                                    <?=$dateSelected?>
                                </td>
                                <td><?php if($attendance['in']!=""){ ?>
                                    
                                    <?php if($attendance['status']==1){
                                        $color="#000000";
                                    }else if($attendance['card']=="Yellow"){
                                        $color="#FFC300";
                                    }else if($attendance['card']=="Red"){
                                        $color="red";
                                    }else{
                                        $color="black";
                                    }
                                   ?>
                                    <p style="color:<?php echo $color?>"><?php echo date_format($attendance['in'],"H:i a");?></p> 

                                     
                                <?php } ?>
                                </td>
                                <td><?php if($attendance['late_remark']!=""){ ?>
                                    
                                     <?= $attendance['late_remark'] ?>
                                       
                                     <?php } ?>
                                    
                                </td>
                                <td><?php if($attendance['status']!=""){ ?>
                                    
                                    <?php foreach ($attendanceLateStatus as $key => $value) {
                                        if($attendance['status']==$key){
                                            echo $value;
                                        }
                                     }
                                    ?>
                                       
                                    <?php } ?>
                                
                                </td>
                                
                               <?php if($userRoles->hasRole(['Ketua Pengarah','Master Admin','Admin','Supervisor'])) :?>
                                    <td class="actions">
                                        
                                        <div class="btn-group">
                                        
                                            <?php echo $this->Form->postLink($this->Html->tag('i', __('Approve')), ['action' => 'approve', $attendance['id']], ['escape' => false, 'title' => __('Approve'), 'class' => 'btn btn-success btn-xs','data-toggle'=>'tooltip','confirm' => __('Are you sure you want to APPROVE this late attendance application?')]);

                                                echo $this->Form->postLink($this->Html->tag('i', __('Reject')), ['action' => 'reject', $attendance['id']], ['escape' => false, 'title' => __('Reject'), 'class' => 'btn btn-danger btn-xs','data-toggle'=>'tooltip','confirm' => __('Are you sure you want to REJECT this late attendance application?')]);
                                                ?>
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
