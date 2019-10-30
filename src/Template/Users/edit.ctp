<script type="text/javascript">
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
	 function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				
				$('#img_prev')
					.attr('src', e.target.result)
					.width(100)
					.height(100)
					.show();
				$('#none_prev').hide();
				$('#prev_image').hide();
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
	$(document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			$.ajax({
				type : "POST",
				url  : getAppVars('basepath').basePath + 'users/getDetails' + '?id=' + id, //pass query string to server
				success: function(data){
						data = JSON.parse(data);
						console.log(data);
						$("#listdesignation").empty();
						$('#listdesignation').append($('<option value>--Please Select--</option>'));
													
						$.each(data.designations, function(i, p) {
							console.log(p);
							$('#listdesignation').append($('<option></option>').val(p.id).html(p.name));
						});				
						
						$("#listuser").empty();
						$('#listuser').append($('<option value>--Please Select--</option>'));
													
						$.each(data.users, function(i, p) {
							console.log(p);
							$('#listuser').append($('<option></option>').val(p.id).html(p.name));
						});
			}});
		});
		$('#listrole').change(function(){
			var id = $(this).val();
			if(id == 2 || id == 1){
				$("#listuser").hide();
				$("label[for='listuser']").hide();
				$("#listuser").prop('required',false);
				$("#listuser").val(1);
			}else{
				$("#listuser").show();
				$("label[for='listuser']").show();				
			}
		});
		if($( "#listrole" ).val() == 2 || $( "#listrole" ).val() == 1){
			$("#listuser").hide();
			$("label[for='listuser']").hide();
			$("#listuser").prop('required',false);
			$("#listuser").val(1);
			console.log($("#listuser"));
		}
	});
</script><div class="row">
    <div class="col-xs-10">
        <div class="users form">
            <?= $this->Form->create($user, ['role' => 'form','enctype'=>'multipart/form-data']) ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Edit User') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
						<center>
							<div class="input file">
								<label for="image">
									<img id="img_prev" class="img-circle" src="#" style="display:none;"/>
									<?php if($user->image != null) :?>
									<img id="prev_image" class="img-circle" style="width:100px;height:100px;" src="<?php echo $this->Url->build( '/' ).$user->image; ?>"/>
									<?php else: ?>
									<i id="none_prev" class="fa fa-user fa-5x" aria-hidden="true"></i>
									<?php endif;?>
								</label>
								<input type="file" name="image" class="form-control" id="image" style="width:40%;" onchange="readURL(this);">
								<input type="hidden" name="previous_image" value="<?php echo $user->image ;?>">
							</div>
							<span class="instruction"><?php echo __('Image file type: '); ?><strong>JPG</strong> <?php echo __('or'); ?><strong> PNG</strong> <?php echo __('only'); ?><br/>
                                <?php echo __('Maximum file size:') ?><strong> <?php echo __('1MB') ?></strong>
                            </span>
						</center>
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
						echo $this->Form->input('ic_number', ['onkeypress'=>'return isNumber(event)','maxlength'=>12,'title'=>'12-Digit IC Number','class' => 'form-control', 'placeholder' => __('Enter ...'),'disabled'=>true,'style'=>'width:50%;']);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'disabled'=>true,'style'=>'width:50%;']);
						echo $this->Form->input('phone', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
                        echo $this->Form->input('new_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter new password'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false,'style'=>'width:50%;']);
                        echo $this->Form->input('confirm_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter password confirmation'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false,'style'=>'width:50%;']);
						if ($userRoles->hasRole(['Master Admin','Supervisor'])) :
							echo $this->Form->input('role', ['class' => 'form-control','id'=>'listrole','options' => $roles, 'multiple'=>false,'style'=>'width:50%;','empty'=>__('--Please Select--'),'required'=>true,'value'=> $selected_role]);
						endif;
						if ($userRoles->hasRole(['Master Admin'])) :
							echo $this->Form->input('organization', ['label'=>__('Department'),'id'=>'listdepartment','class' => 'form-control','empty'=>__('--Please Select--'),'options' => $organizations,'multiple' => false,'style'=>'width:50%;','value'=>$selected_dept]);
							echo $this->Form->input('designation', ['class' => 'form-control','id'=>'listdesignation','empty'=>__('--Please Select--'),'options' => $designations,'multiple' => false,'style'=>'width:50%;','value'=>$selected_designation]); 
							echo $this->Form->input('report_to', ['class' => 'form-control','id'=>'listuser','empty'=>__('--Please Select--'),'placeholder' => __('Enter ...'), 'options' => $reportTo,'style'=>'width:50%;','required'=>true,'value'=>$selected_reportTo]);						
							echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $userStatus,'style'=>'width:50%;']);
						endif;
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-success']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'users'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
