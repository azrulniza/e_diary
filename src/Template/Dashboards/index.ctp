<?php
$this->Html->script('dashboard');

?>
<script type="text/javascript">
	$( document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			$.ajax({
				type : "POST",
				url  : getAppVars('basepath').basePath + 'dashboards/getUsers' + '?id=' + id, //pass query string to server
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
			    <?php //$this->Form->templates($form_templates['shortForm']); ?>
                <?php //$this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
					<div class="form-group" style="padding-left:30%;">
					<?php if ($userRoles->hasRole(['Master Admin'])) :?>
						<?php
 							echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control','options' => $departments, 'empty'=>__('All'),'value'=>$departmentSelected,'style'=>'width:40%']);
						?>		
					<?php endif; ?>
					<?php if (!$userRoles->hasRole(['Staff'])) :?>
						<?php
							echo $this->Form->input('user', ['label' => __('Staffs'), 'type'=>'select', 'id'=>'listuser','class' => 'form-control','options' => $users, 'empty'=>__('All'),'value'=>$userSelected,'style'=>'width:40%']);
						?>
					<?php endif; ?>
					</div>
				<?php //$this->Form->end() ?>
							
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