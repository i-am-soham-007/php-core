<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>


<form id="myform" action="" method="post">
  <div>
    <label>GSTTIN #</label>
    <div>
      <input type="text" name="gst" value="" id="input-gst" />
    </div>
  </div>
  <button type="submit">Register</button>
</form>

<script>

$(document).ready(function() {
  $.validator.addMethod("gst", function(value3, element3) {
    var gst_value = value3.toUpperCase();
    var reg = /^([0-9]{2}[a-zA-Z]{4}([a-zA-Z]{1}|[0-9]{1})[0-9]{4}[a-zA-Z]{1}([a-zA-Z]|[0-9]){3}){0,15}$/;
    if (this.optional(element3)) {
      return true;
    }
    if (gst_value.match(reg)) {
      return true;
    } else {
      return false;
    }

  }, "Please specify a valid GSTTIN Number");

  $('#myform').validate({ // initialize the plugin
    rules: {
      gst: {
        required: true,
        gst: true
      }

    },
    submitHandler: function(form) {
      alert('valid form submitted');
      return false;
    }
  });
});
</script>