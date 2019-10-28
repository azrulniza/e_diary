<script type="text/javascript">
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
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
	$(document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			if($( "#listdepartment" ).val() < 1){
				$("#listdesignation").prop('disabled',true);
				$("#listuser").prop('disabled',true);
			}else{
				$("#listdesignation").prop('disabled',false);
			}
			$.ajax({
				type : "POST",
				url  : getAppVars('basepath').basePath + 'users/getDetails' + '?id=' + id, //pass query string to server
				success: function(data){
						data = JSON.parse(data);
						//console.log(data);
						$("#listdesignation").empty();
						$('#listdesignation').append($('<option value>--Please Select--</option>'));
													
						$.each(data.designations, function(i, p) {
							//console.log(p);
							$('#listdesignation').append($('<option></option>').val(p.id).html(p.name));
						});				
						
						$("#listuser").empty();
						$('#listuser').append($('<option value>--Please Select--</option>'));
													
						$.each(data.users, function(i, p) {
							//console.log(p);
							$('#listuser').append($('<option></option>').val(p.id).html(p.name));
						});
			}});
		});
		$('#listdesignation').change(function(){
			if($( "#listdesignation" ).val() < 1){
				$("#listuser").prop('disabled',true);
				$( "#listuser" ).val('');
			}else{
				$("#listuser").prop('disabled',false);
			}
		});
		$('#listrole').change(function(){
			var id = $(this).val();
			if(id == 2 || id == 1){
				$("#listuser").hide();
				$("label[for='listuser']").hide();
				$("#listuser").prop('required',false);
			}else{
				$("#listuser").show();
				$("label[for='listuser']").show();				
			}
		});
	});
</script>
<div class="row">
    <div class="col-xs-10">
        <div class="users form">
            <?= $this->Form->create($user, ['role' => 'form','enctype'=>'multipart/form-data']) ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Add User') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
						<center>
							<div class="input file">
								<label for="image">
									<img id="img_prev" class="img-circle" src="#" style="display:none;"/>
									<i id="none_prev" class="fa fa-user fa-5x" aria-hidden="true"></i>
								</label>
								<input type="file" name="image" class="form-control" id="image" style="width:40%;" onchange="readURL(this);">
							</div>
							<span class="instruction"><?php echo __('Image file type: '); ?><strong>JPG</strong> <?php echo __('or'); ?><strong> PNG</strong> <?php echo __('only'); ?><br/>
                                <?php echo __('Maximum file size:') ?><strong> <?php echo __('1MB') ?></strong>
                            </span>
						</center>
                        <?php
						echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
						echo $this->Form->input('ic_number', ['pattern'=>'[0-9]{12,12}','title'=>'12-Digit IC Number','class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
						echo $this->Form->input('phone', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
                        echo $this->Form->input('password', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
						echo $this->Form->input('confirm_password', ['type'=>'password', 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false,'style'=>'width:50%;','required'=>true]);                        
                        if ($userRoles->hasRole(['Master Admin','Supervisor'])) :
							echo $this->Form->input('role', ['class' => 'form-control','id'=>'listrole','options' => $roles, 'multiple'=>false,'style'=>'width:50%;','empty'=>__('--Please Select--'),'required'=>true]);
							echo $this->Form->input('organization', ['label'=>__('Department'),'id'=>'listdepartment','class' => 'form-control','empty'=>__('--Please Select--'),'options' => $organizations,'multiple' => false,'style'=>'width:50%;','required'=>true]);
							echo $this->Form->input('designation', ['class' => 'form-control','id'=>'listdesignation','empty'=>__('--Please Select--'),'options' => $designations,'multiple' => false,'style'=>'width:50%;','required'=>true,'disabled'=>true]);
							echo $this->Form->input('report_to', ['class' => 'form-control','id'=>'listuser','empty'=>array('0'=>__('--Please Select--')),'placeholder' => __('Enter ...'), 'options' => $reportTo,'style'=>'width:50%;','required'=>true,'disabled'=>true]);							
							echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $userStatus,'style'=>'width:50%;','required'=>true]);
						endif;
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'users'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
