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
				$('#prev_image').hide();
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
</script><div class="row">
    <div class="col-xs-10">
        <div class="users form">
            <?= $this->Form->create($user, ['role' => 'form','enctype'=>'multipart/form-data']) ?>
            <div class="box box-success">
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
						</center>
                        <?php
                        echo $this->Form->input('name', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('email', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'disabled'=>true]);
                        echo $this->Form->input('new_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter new password'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false]);
                        echo $this->Form->input('confirm_password', ['class' => 'form-control', 'type'=>'password', 'placeholder' => __('Enter password confirmation'), 'autocomplete' => 'off', 'value'=>'', 'required'=>false]);
                        echo $this->Form->input('ic_number', ['class' => 'form-control', 'placeholder' => __('Enter ...'),'disabled'=>true]);
                        echo $this->Form->input('phone', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('report_to', ['class' => 'form-control','placeholder' => __('Enter ...'), 'options' => $reportTo]);
						echo $this->Form->input('organization', ['class' => 'form-control','options' => $organizations,'value'=>$selected_dept]);
                        echo $this->Form->input('designation', ['class' => 'form-control','options' => $designations,'value'=>$selected_designation]);                        
						echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...'), 'options' => $userStatus]);
						echo $this->Form->input('roles._ids', ['options' => $roles,'multiple' => 'checkbox']);
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
