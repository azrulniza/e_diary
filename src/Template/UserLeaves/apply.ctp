<script type="text/javascript">
    $(document ).ready(function() {
        $('#listdepartment').change(function(){
            var id = $(this).val();
            $.ajax({
                type : "POST",
                url  : getAppVars('basepath').basePath + 'user_leaves/getDetails' + '?id=' + id, //pass query string to server
                success: function(data){
                        data = JSON.parse(data);
                        console.log(data);
                                   
                        
                        $("#listuser").empty();
                        $('#listuser').append($('<option value>--Please Select--</option>'));
                                                    
                        $.each(data.users, function(i, p) {
                            console.log(p);
                            $('#listuser').append($('<option></option>').val(p.id).html(p.name));
                        });
            }});
        });
    });
    $(document ).ready(function() {
        $('#leavetype').change(function(){
            var id = $(this).val();
            
            if (id == 1) {
                $('#label_from_time').show();
                $('#input_from_time').show();
                $('#label_to_time').show();
                $('#input_to_time').show();

                $('#label_end_date').hide();
                $('#input_end_date').hide();

            }else{
                $('#label_from_time').hide();
                $('#input_from_time').hide();
                $('#label_to_time').hide();
                $('#input_to_time').hide();

                $('#label_end_date').show();
                $('#input_end_date').show();
                
            }
        });
    });
    
</script>
<div class="row">
    <div class="col-xs-10">
        <div class="designations form">
            <?= $this->Form->create($userleaves, ['role' => 'form','enctype'=>'multipart/form-data']) ?>
            <div class="box box-deafult">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Apply Time Off') ?></h3>
                </div>
                <div class="box-body">
                    <?php //print_r($last_id);?>
                    <?php //echo $total_time_off_hour . $from_day;?>
                    <div class="form-group">
                        
                             <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>

                                    <tr>
                                        <?php if($userRoles->hasRole(['Master Admin'])) :?>
                                        <td align="left"><label for="deparment"><?php echo $this->Form->label('Department');?></label></td>
                                        <td>    
                                            <?php echo $this->Form->input('department', ['label'=>false,'options' => $list_organization, 'empty' => __('-- Please Select --'), 'class' => 'form-control','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$organizationSelected,'required'=>true,]); ?>
                                             
                                        </td>
                                        <?php endif; ?>
                                        <?php if($userRoles->hasRole(['Master Admin']) OR $userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) :?>
                                        <td align="left"><label for="staff"><?php echo $this->Form->label('Staff');?></label></td>
                                        <td>    
                                            <?php echo $this->Form->input('staff', ['label'=>false,'options' => $list_user, 'empty' => __('-- Please Select --'), 'class' => 'form-control','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$staffSelected,'required'=>true,]); ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($userRoles->hasRole(['Staff'])) :?>
                                            <?php 
                                                echo $this->Form->input('department', ['type'=>'hidden', 'class' => 'form-control', 'value' => $user_organization_id]);
                                                echo $this->Form->input('staff', ['type'=>'hidden','class' => 'form-control', 'value' => $user_id]); 
                                            ?>
                                        <?php endif; ?>
                                    </tr>
                                    

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Leave Type');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('leave_type', ['required'=>true,'label'=>false,'options' => $leaveTypes, 'empty' => __('-- Please Select --'), 'class' => 'form-control','style'=>'width:250px;', 'id'=>'leavetype', 'value'=>$leaveTypeSelected]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Date Start');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('apply_date', ['type' => 'date','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>
                                   

                                    <tr>
                                        <td align="left" style='display:none' id='label_end_date'><label for="date"><?php echo $this->Form->label('Date End');?></label></td>
                                        <td style='display:none' id='input_end_date'>
                                          <?php echo $this->Form->input('date_end', ['type' => 'date','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" style='display:none' id='label_from_time'><label for="date"><?php echo $this->Form->label('Time From');?></label></td>
                                        <td style='display:none' id='input_from_time'>
                                          <?php echo $this->Form->input('from_time', ['id'=>'id_start_time','type' => 'time','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>
                                   
                                    <tr>
                                        <td align="left" style='display:none' id='label_to_time'><label for="date"><?php echo $this->Form->label('Time To');?></label></td>
                                        <td style='display:none' id='input_to_time'>
                                          <?php echo $this->Form->input('to_time', ['id'=>'id_to_time','required'=>true,'type' => 'time','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Remark');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('remark', ['required'=>true,'type' => 'textarea','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
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
