<html>
<head>
<link rel="stylesheet" href="materialize.min.css">
<link rel="stylesheet" href="form-select2.min.css">
<link rel="stylesheet" href="form-wizard.min.css">
<link rel="stylesheet" href="style.min.css">

</head>
<body>
<div class="row">
    <div class="input-field col m6 s12">
        <input type="text" class="datepicker" name="drop_off" id="drop_off" form="appointment-form">
        <label id="chnge">When would you like to drop off ?</label>
    </div>
    <div class="input-field col m6 s12">
        <input type="text" class="datepicker" name="pickup" id="pickup" form="appointment-form">
        <label id="chnge2">When would you like to Pickup ?</label>
    </div>
</div>
</body>
</html>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="form-editor.min.js"></script>
<script src="form-validation.min.js"></script>
<script src="form-elements.min.js"></script>
<script src="vendor.min.js"></script>
<script>
	$(document).ready(function(){
		var date_input=$('.datepicker'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
		    format: 'dd-mm-yyyy',
			todayHighlight: true,
			autoclose: true,
			changeMonth: true, // this will help you to change month as you like
            changeYear: true, // this will help you to change years as you like
            yearRange: "2000:2021"
		});
/**/
$('#pickup').datepicker({
        autoClose : true,
        format : 'dd mmm yyyy',
        yearRange : 1,
        minDate : new Date(),
        onSelect: function(el) {
        	const ell = new Date(el);
        $("#drop_off").datepicker({ minDate : ell})
        }
      });

      $("#drop_off").datepicker({
        autoClose : true,
        format : 'dd mmm yyyy'
      })
/**/
	});
</script>
