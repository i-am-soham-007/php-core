<script>
	$(document).ready(function(){
		var date_input=$('#date'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'dd-mm-yyyy',
			todayHighlight: true,
			autoclose: true,
			changeMonth: true, // this will help you to change month as you like
            changeYear: true, // this will help you to change years as you like
            yearRange: "2000:2021"
		})
	})
</script>