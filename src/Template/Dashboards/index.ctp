<?php
$this->Html->script('dashboard');

?>
<script type="text/javascript">
	$( document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			$.ajax({
				type : "POST",
				//url  : getAppVars('basepath').basePath + 'dashboards/getUsers' + '?id=' + id, //pass query string to server
				url  : getAppVars('basepath').basePath + 'dashboards/getDetails' + '?id=' + id, //pass query string to server
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
</script>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
			    <?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
				<table id="addTable" class="table-condensed" style="table-layout: auto;">
                    <tbody>
                        <tr>
                            <?php if($userRoles->hasRole(['Master Admin'])) :?>
                            <td align="left"><label for="department"><?php echo $this->Form->label('Department');?></label></td>
                            <td>    
                               
                                
                                <?php echo $this->Form->input('department', ['label'=>false,'options' => $departments, 'empty' => __('-- All --'), 'class' => 'form-control','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$departmentSelected]); ?>
                                 
                            </td>
                            <?php endif; ?>
                            <?php if($userRoles->hasRole(['Master Admin']) OR $userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) :?>
                            <td align="left"><label for="staff"><?php echo $this->Form->label('Staff');?></label></td>
                            <td>                                             
                                
                                <?php echo $this->Form->input('user', ['label'=>false,'options' => $users, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$userSelected]); ?>
                                 
                            </td>
                            <?php endif; ?>
                            
                        </tr>
                       <tr>
                           <td></td>
                       </tr>
                    </tbody>
                </table>
                <?= $this->Form->end() ?>
							
				<div class="col-md-3 col-sm-4 col-xs-8">
					  <div class="small-box bg-aqua">
						<div class="inner">
						  <h3><?php echo $staff_absent ?></h3>

						  <p><?= __('ABSENT')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-close-circled"></i>
						</div>
					  </div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12">
					  <div class="small-box bg-green">
						<div class="inner">
						  <h3><?php echo $staff_working ?></h3>

						  <p><?= __('WORKING')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-checkmark-circled"></i>
						</div>
					  </div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12">
					  <div class="small-box bg-red">
						<div class="inner">
						  <h3><?php echo $notifications ?></h3>

						  <p><?= __('NOTIFICATION')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-information-circled"></i>
						</div>
					  </div>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12">
					  <div class="small-box bg-yellow">
						<div class="inner">
						  <h3><?php echo $staff_timeoff ?></h3>

						  <p><?= __('TIME OFF')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-clock"></i>
						</div>
					  </div>
				</div>
            </div>
            <div class="box-footer">
			    <table class="table table-bordered table-striped" style="table-layout:fixed">							
                    <tr> 
                        <td><h5><strong><?= __('Name') ?></strong></h5></td>
                        <td><h5><?= $user->name ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Email') ?></strong></h5></td>
                        <td><h5><?= $user->email ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Phone No.') ?></strong></h5></td>
                        <td><h5><?= $user->phone ?></h5></td> 
                    </tr>
                </table>
				
            </div>
        </div>
    </div>
</div>