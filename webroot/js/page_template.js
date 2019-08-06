 $(document).ready(function () {
      $("#reseller").change(function () {
	   
		var reseller_id= $(this).val();
         var rows= $(".client_checkbox").find(".checkbox");
		 $.each(rows,function(){
			console.log(reseller_id);
			$(this).find('input').prop('checked', false);
			if(reseller_id == ""){
				$(this).show();
			}else{
				$(this).hide();
				if($(this).is(".reseller-"+reseller_id)){
					$(this).show();
				}
			}
		 })
       });
  });