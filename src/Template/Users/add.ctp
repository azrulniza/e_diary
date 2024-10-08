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
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
	$(document ).ready(function() {        
		  $('form').bind('submit', function () {
			$(this).find(':input').prop('disabled', false);
		  });
		$('#listdepartment').change(function(){
			var id = $(this).val();
			var role_id = $('#listrole').val();
			
			$("#listuser").prop('disabled',true);
			$("#listdesignation").prop('disabled',false);
			$.ajax({
				type : "POST",
				url  : '<?php echo $this->Basepath->getBasePath() ; ?>' + 'users/getDetails' + '?id=' + id + '&role_id=' + role_id, //pass query string to server
				success: function(data){
						data = JSON.parse(data);
						console.log(data);
						$("#listdesignation").empty();
						$('#listdesignation').append($('<option value><?php echo __("--Please Select--") ?></option>'));
													
						$.each(data.designations, function(i, p) {
							//console.log(p);
							$('#listdesignation').append($('<option></option>').val(p.id).html(p.name));
						});				
						
						$("#listuser").empty();
						$('#listuser').append($('<option value><?php echo __("--Please Select--") ?></option>'));
													
						$.each(data.users, function(i, p) {
							//console.log(p);
							$('#listuser').append($('<option></option>').val(p.id).html(p.name));
						});
			}});
		});
 		$('#listrole').change(function(){
			if($( "#listrole" ).val() < 1){
				$("#listdepartment").prop('disabled',true);
			}else{
				$("#listdepartment").prop('disabled',false);
			}
			if ($("#listrole").val() == 5 ){
				$("#listuser").hide();
                $("label[for='listuser']").hide();
                $("#listuser").prop('required',false);
			}else{
				$("#listuser").show();
                $("label[for='listuser']").show();   
                $("#listuser").prop('required',true);				
			}
			$("#listdepartment" ).val('');
			$("#listuser").prop('disabled',true);
			$("#listdesignation").prop('disabled',true);
			$("#listuser").val('');
			$("#listdesignation").val('');

		});
		$('#listdesignation').change(function(){
			if($( "#listdesignation" ).val() < 1){
				$("#listuser").prop('disabled',true);
				$( "#listuser" ).val('');
			}else{
				$("#listuser").prop('disabled',false);
			}
		});
		$('#ic-number').keyup(function(e) { 
			if(this.value.length < 12){
				this.setCustomValidity('<?php echo __("Please enter 12 digit identity card no.")?>');
			}
		});
		$('#phone').keyup(function(e) { 
			if(this.value.length < 10){
				this.setCustomValidity('<?php echo __("Please enter phone number 10-12 digits.")?>');
			}
		});
		$('#email').keyup(function(e) { 
			if (this.value.indexOf('@') < 0)
			{
				this.setCustomValidity("<?php echo __('Please include an \'@\' in email address.')?>");
			}else if (this.value.indexOf('.') < 0){
				this.setCustomValidity("<?php echo __('Please include an \'.\' in email address.')?>");
			}else if (this.value.slice(this.value.length - 1) == '.'){
				this.setCustomValidity("<?php echo __('\'.\' cannot be used at last position.')?>");
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
						echo $this->Form->input('ic_number', ['onkeypress'=>'return isNumber(event)','maxlength'=>12,'class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
						echo $this->Form->input('phone', ['onkeypress'=>'return isNumber(event)','maxlength'=>12,'class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
                        echo $this->Form->input('password', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;','required'=>true]);
						echo $this->Form->input('confirm_password', ['type'=>'password', 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false,'style'=>'width:50%;','required'=>true]);                        
                        if ($userRoles->hasRole(['Master Admin','Supervisor'])) :
							echo $this->Form->input('role', ['label'=>__('Roles'),'class' => 'form-control','id'=>'listrole','options' => $roles, 'multiple'=>false,'style'=>'width:50%;','empty'=>__('--Please Select--'),'required'=>true]);
							echo $this->Form->input('organization', ['label'=>__('Department'),'id'=>'listdepartment','class' => 'form-control','empty'=>__('--Please Select--'),'options' => $organizations,'multiple' => false,'style'=>'width:50%;','required'=>true,'disabled'=>true]);
							echo $this->Form->label(__('Grade'));
							echo '<span class="mark-required" style="color:red;"> * </span>';
							echo '<div>';
							echo '<div style="float: left;">';
							echo $this->Form->input('grade_id', ['label'=>false,'class' => 'form-control','options' => $grades, 'empty'=>__('--Please Select--'), 'multiple' => false,'required'=>true]);
							echo '</div>';
							echo '<div></div>';
							echo '<div style="float: left;">';
							echo $this->Form->input('skim', ['label'=>false,'class' => 'form-control','style'=>'width:50%;', 'min'=>1,'placeholder' => __('Enter ...'),'required'=>true]);
							echo '</div>';
							echo '<div style="clear: both;"></div>';
							echo '</div>';
							echo $this->Form->input('designation', ['class' => 'form-control','id'=>'listdesignation','empty'=>__('--Please Select--'),'options' => $designations,'multiple' => false,'style'=>'width:50%;','required'=>true,'disabled'=>true]);
							echo $this->Form->input('report_to', ['class' => 'form-control','id'=>'listuser','empty'=>__('--Please Select--'),'placeholder' => __('Enter ...'), 'options' => $reportTo,'style'=>'width:50%;','required'=>true,'disabled'=>true]);
							echo $this->Form->input('card_no', ['label'=>__('Card No.'),'class' => 'form-control','style'=>'width:50%;', 'min'=>1, 'placeholder' => __('Enter ...'),'required'=>true]);							
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
