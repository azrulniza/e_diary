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
			$.ajax({
				type : "POST",
				url  : getAppVars('basepath').basePath + 'users/getDesignation' + '?id=' + id, //pass query string to server
				success: function(data){
						data = JSON.parse(data);
						console.log(data);
						$("#listdesignation").empty();
						$('#listdesignation').append($('<option value>--Please Select--</option>'));
													
						$.each(data.designations, function(i, p) {
							console.log(p);
							$('#listdesignation').append($('<option></option>').val(p.id).html(p.name));
						});
			}});
		});
	});
</script>
<div class="row">
    <div class="col-xs-10">
        <div class="users form">
            <?= $this->Form->create($user, ['role' => 'form','enctype'=>'multipart/form-data']) ?>
            <div class="box box-primary">
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
						</center>
                        <?php
						echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
						echo $this->Form->input('ic_number', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
						echo $this->Form->input('phone', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
                        echo $this->Form->input('password', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'style'=>'width:50%;']);
						echo $this->Form->input('confirm_password', ['type'=>'password', 'class' => 'form-control', 'placeholder' => __('Enter ...'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false,'style'=>'width:50%;']);                        
                        echo $this->Form->input('report_to', ['class' => 'form-control','placeholder' => __('Enter ...'), 'options' => $reportTo,'style'=>'width:50%;']);
                        echo $this->Form->input('organizations._ids', ['label'=>__('Department'),'id'=>'listdepartment','class' => 'form-control','empty'=>__('--Please Select--'),'options' => $organizations,'multiple' => false,'style'=>'width:50%;']);
						echo $this->Form->input('designations._ids', ['class' => 'form-control','id'=>'listdesignation','empty'=>__('--Please Select--'),'options' => $designations,'multiple' => false,'style'=>'width:50%;']);
                        if ($userRoles->hasRole(['Master Admin'])) :
							echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $userStatus,'style'=>'width:50%;']);
						endif;
                        echo $this->Form->input('roles._ids', ['options' => $roles, 'multiple' => 'checkbox','style'=>'width:50%;']);
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
