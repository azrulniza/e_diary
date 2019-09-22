<div class="row">
    <div class="col-xs-10">
        <div class="designations form">
            <?= $this->Form->create($userleaves, ['role' => 'form']) ?>
            <div class="box box-deafult">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Apply Time Off') ?></h3>
                </div>
                <div class="box-body">
                    <?php //print_r($leaveTypes);?>
                    <div class="form-group">
                        <?php if($userRoles->hasRole(['Master Admin'])) :?>
                             <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>

                                    <tr>
                                        <td align="left"><label for="deparment"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- Please Select --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected]); ?>
                                             
                                        </td>
                                        <td align="left"><label for="staff"><?php echo $this->Form->label('Staff');?></label></td>
                                        <td>    
                                            <?php echo $this->Form->input('staff', ['label'=>false,'options' => $list_user, 'empty' => __('-- Please Select --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$userSelected]); ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Date');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('cdate', ['type' => 'date','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Leave Type');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('leave_type', ['required'=>true,'label'=>false,'options' => $leaveTypes, 'empty' => __('-- Please Select --'), 'class' => 'form-control','style'=>'width:250px;', 'id'=>'leavetype', 'value'=>$leaveTypeSelected]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Time From');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('from_time', ['type' => 'time','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Time To');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('to_time', ['required'=>true,'type' => 'time','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Remark');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('remark', ['type' => 'textarea','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Upload Attachment');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('attachment', ['type' => 'file','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        <?php endif; ?>
                       
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Apply'), ['class'=>'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'designations'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
