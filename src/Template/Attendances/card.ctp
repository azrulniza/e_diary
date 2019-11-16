
<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header">
                   <h4><?php echo __("Change Card Color")?></h4>
            </div>
            <div class="box-body">
                <div class="attendances index dataTable_wrapper table-responsive">
                 
                    <?php //print_r($attendance_in); die;?>
                    <?php if($userRoles->hasRole(['Master Admin'])) :?>
                
                        <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>
                                    <tr> 
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Year');?></label></td>
                                        <td>    
                                            <?= $this->Form->create('card',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
                                            <?php
                                                for($year = date('Y') ; $year <= date('Y',strtotime('+3 year')); $year++){
                                                    $arr_year[$year] = $year;
                                                }
                                                $control_html = 
                                                    $this->Form->input(
                                                      'att_year', [
                                                        'type' => 'select',
                                                        'label'=>false,
                                                        'id' => 'att_year',
                                                        'class' => 'form-control autosubmit',
                                                        'onchange' => 'this.form.submit()',
                                                        'style'=>'width:100%',
                                                        'options' => $arr_year, 
                                                        'value'=>$yearselected
                                                      ]);

                                                $date_control = 
                                                    str_replace(
                                                   'type="text"', 
                                                   'type="date"', 
                                                   $control_html
                                                );
                                             
                                                echo $date_control;
                                                echo $this->Form->input('department', ['type'=>'hidden','class' => 'form-control', 'value' => $organizationSelected]);
                                                echo $this->Form->input('att_month', ['type'=>'hidden','class' => 'form-control', 'value' => $monthselected]);
                                                echo $this->Form->input('card', ['type'=>'hidden','class' => 'form-control', 'value' => $cardselected]);
                                             ?>
                                            
                                            <?= $this->Form->end() ?>
                                        </td>

                                    </tr>
                                    <tr> 
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Month');?></label></td>
                                        <td>    
                                            <?= $this->Form->create('card',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
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

                                                $control_html = 
                                                    $this->Form->input(
                                                      'att_month', [
                                                        'type' => 'select',
                                                        'label'=>false,
                                                        'id' => 'att_month',
                                                        'class' => 'form-control autosubmit',
                                                        'onchange' => 'this.form.submit()',
                                                        'style'=>'width:100%',
                                                        'options' => $month, 
                                                        'value'=>$monthselected
                                                      ]);

                                                $date_control = 
                                                    str_replace(
                                                   'type="text"', 
                                                   'type="date"', 
                                                   $control_html
                                                );
                                             
                                                echo $date_control;
                                                echo $this->Form->input('department', ['type'=>'hidden','class' => 'form-control', 'value' => $organizationSelected]);
                                                echo $this->Form->input('att_year', ['type'=>'hidden','class' => 'form-control', 'value' => $yearselected]);
                                                echo $this->Form->input('card', ['type'=>'hidden','class' => 'form-control', 'value' => $cardselected]);

                                             ?>
                                            
                                            <?= $this->Form->end() ?>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                            
                                            <?= $this->Form->create('list',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected]); ?>
                                            <?php echo $this->Form->input('attyear', ['type'=>'hidden','class' => 'form-control', 'value' => $yearselected]); ?>
                                            <?php echo $this->Form->input('att_month', ['type'=>'hidden','class' => 'form-control', 'value' => $monthselected]); ?>
                                            <?php echo $this->Form->input('card', ['type'=>'hidden','class' => 'form-control', 'value' => $cardselected]); ?>
                                             <?= $this->Form->end() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="product-key"><?php echo $this->Form->label('Card');?></label></td>
                                        <td>    
                                            
                                            <?= $this->Form->create('list',['type'=>'GET','autocomplete' => 'off', 'method'=>'POST']) ?>
                                            <?php echo $this->Form->input('card', ['label'=>false,'options' => $list_card, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listcard', 'value'=>$cardselected]); ?>
                                            <?php echo $this->Form->input('attyear', ['type'=>'hidden','class' => 'form-control', 'value' => $yearselected]); ?>
                                            <?php echo $this->Form->input('att_month', ['type'=>'hidden','class' => 'form-control', 'value' => $monthselected]); ?>
                                            <?php echo $this->Form->input('att_month', ['type'=>'hidden','class' => 'form-control', 'value' => $organizationSelected]); ?>
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
                                <th><?= __('Month') ?></th>
                                <th><?= __('Year') ?></th>
                                <th><?= __('Card Color') ?></th>
                                <?php if($userRoles->hasRole(['Master Admin'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php //print_r($attendances);?>
                        <?php $count = 0 ?>
        
                        <?php foreach ($attendances as $attendance): ?>
                            <?php $count_red=0; $array_card_id = array(); 
                                foreach($attendance['user_cards'] as $data){
                                   if($data->card_id==3){
                                        $count_red++;
                                   }

                                    $array_card_id[]=$data->id;
                                }
                            ?>
                            <tr id="<?= $attendance['attendance_id']; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $this->Number->format($count) ?></td>
                                <td>
                                    <?= $attendance['username'];?>
                                </td>   
                                <td><?= $attendance['organization_name'] ?></td>
                            
                                <td><?php 
                                        foreach ($month as $key => $value) {
                                           if($key==$monthselected){
                                                echo $value;
                                           }
                                        }
                                    ?>
                                </td>
                                <td><?=$yearselected?></td>
                                
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
                               <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                    <td class="actions">
                                        
                                        <div class="btn-group">
                
                                            <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-exchange','data-toggle'=>'tooltip','title' => __('Change Card Color')]), ['action' => '#modal-container-171766'.$attendance['user_id']], ['escape' => false, 'class' => 'btn btn-default btn-xs','data-toggle'=>'modal']); ?>

                                           <div class="modal fade" id="modal-container-171766<?php echo $attendance['user_id'];?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?= __('Change Card Color'); ?>
                                                                </h4> 
                                                            </div>
                                                            <?= $this->Form->create('list', ['action' => 'change_card_month', 'role' => 'form','enctype'=>'multipart/form-data']) ?>
                                                            <div class="modal-body">
                                                                <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                                                    <tbody>

                                                                        <tr>
                                                                            <td align="left"><b><?php echo __('Name');?></b></td>
                                                                            <td>    
                                                                               <?php echo $attendance['username'];?>
                                                                            </td>   
                                                                        </tr>

                                                                        <tr>
                                                                            <td align="left"><b><?php echo __('Card Color');?></b></td>
                                                                            <td>    
                                                                            
                                                                               <?php
                                                                                if($count_red==3){ $current_card_color=3;?>
                                                                                    <b style="color:red"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" , class="fa fa-square"></span></b> <?= __('Red')?>

                                                                                <?php }else if($count_red > 3){ $current_card_color=1;?>
                                                                                    <b style="color:green"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" , class="fa fa-square"></span></b> <?= __('Green')?>
                                                                                <?php }else{ $current_card_color=2;?>
                                                                                    <b style="color:yellow"><span style="border-radius:25%; border: 0.5px solid #000000; !important;" , class="fa fa-square"></span></b> <?= __('Yellow')?>
                                                                                <?php } ?>
                                                                            </td>   
                                                                        </tr>
                                                                        </tr>

                                                                        <tr>
                                                                            <td align="left"><label for="date"><?php echo $this->Form->label('Change Card Color');?></label></td>
                                                                            <td>
                                                                              <?php echo $this->Form->input('card_color', ['required'=>true,'label'=>false,'options' => $list_card, 'empty' => __('-- Please Select --'), 'class' => 'form-control','style'=>'width:350px;', 'id'=>'leavetype', 'value'=>$current_card_color]); ?>
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td align="left"><label for="date"><?php echo $this->Form->label('Remark');?><a style='color:red'>*</a></label></td>
                                                                            <td>
                                                                              <?php echo $this->Form->input('remark', ['required'=>true,'type' => 'textarea','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>
                                                                </table>

                                                                <?php 
                                                                   
                                                                    echo $this->Form->input('user_id', ['type'=>'hidden', 'value'=>$attendance['user_id']]);
                                                                    echo $this->Form->input('card_ids', ['type'=>'hidden', 'value'=>$array_card_id]);
                                                                    echo $this->Form->input('month', ['type'=>'hidden', 'value'=>$monthselected]);
                                                                    echo $this->Form->input('year', ['type'=>'hidden', 'value'=>$yearselected]);
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
