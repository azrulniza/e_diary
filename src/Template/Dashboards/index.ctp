<?php
$this->Html->script('dashboard');

?>
<script type="text/javascript">
	$( document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			$.ajax({
				type : "POST",
				url  : getAppVars('basepath').basePath + 'dashboards/getUsersByDepartment' + '?id=' + id, //pass query string to server
				success: function(data){
						console.log(data);
			}});
		});
	});
	
	$( document ).ready(function() {
		
		$(".expiry").change(function(){
		var id = $(this).val();
		var package_id = $(".package").val();
		$.ajax({
			type : "POST",
			url  : getAppVars('basepath').basePath + 'product_keys/getExpiryKeycode' + '?id=' + id + '&package_id=' + package_id, //pass query string to server
			success: function(data){
				$("input[name='total_key']").val(data);
				$(".keys").html('<?php echo __('Available key'). " : "?>' + data);
			}});
		});
	});
	
</script>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
				<?php
                    echo $this->Form->input('department', ['label' => __('Departments'), 'type'=>'select','id'=>'listdepartment','class' => 'form-control','options' => $departments, 'empty'=>__('All'),'style'=>'width:40%']);
				?>				
				<?php
                    echo $this->Form->input('department', ['label' => __('Users'), 'type'=>'select', 'onchange'=>'leaveChange(this.value)','id'=>'listuser','class' => 'form-control','options' => $users, 'empty'=>__('All'),'style'=>'width:40%']);
				?>
            </div>
            <div class="box-body">

            </div>
            <div class="box-footer">
            </div>
        </div>
    </div>
</div>
