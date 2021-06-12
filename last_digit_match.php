<!DOCTYPE html> 
<html> 
	<head> 
		<script src= 
"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> 
	</script> 

		
		
	</head> 
	<body> 
		<form> 
			<input type="text" id="input1">
            <div class="" id="open_error"></div>
		</form> 
	</body> 
</html> 

<script> 
			$(document).ready(function () { 
                $("#input").blur(function(){
   $("#open_error").hide();
  });
				$("#input").on('keyup',function(){
                    var val = $(this).val();
                    var len = val.length;
                    var cChar = val.substr(val.length - 1);
                    var lastChar = val.substr(0, val.length - 1);
                  
                    if(len > 3)
                    {
                        $("#open_error").show();
                        $("#open_error").html("<span style='color:red;font-size:10px;'>Only 3 digits allowed !!</span>");
                        $("#input").val("");
                    }else{
                        alert(" Last Charcter " + lastChar);
                        var thirdChar  = val.substr(0, val.length - 2);
                        alert(" third Charcter " + thirdChar);
                            if(cChar >= lastChar)
                            {
                                $("#open_error").html("<span style='color:green;font-size:10px;'>DONE</span>");
                                //$("#input").val("");
                            }else{
                                $("#open_error").html("<span style='color:red;font-size:10px;'>Not Valid!!</span>");
                                $("#input").val("");
                            }
                    }
                });

                $("#input1").on('keyup',function(){
                    
                    var val = $(this).val();
                    var len = val.length;
                    
                    var cChar = val.charAt(len-1);
                   var lastChar = val.charAt(len-2);
                    if( len > 3)
                    {
                        $("#open_error").show();
                        $("#open_error").html("<span style='color:red;font-size:10px;'>Only 3 digits allowed !!</span>");
                        $("#input1").val("");
                    }else{

                        if(cChar >= lastChar)
                        {
                            $("#open_error").html("<span style='color:green;font-size:10px;'>DONE</span>");
                        }else{
                            $("#open_error").html("<span style='color:red;font-size:10px;'>Not Valid!!</span>");
                            $("#input1").val("");
                        }
                    }
                    //alert
                });
			}); 
		</script> 