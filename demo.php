
<html>
<input type="text" name ="pass"  placeholder="Enter Password" id="pass">
<input type="submit" name="" id="submit" class="btn-submit" value="Password Generate">
<p id="show"></p>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
     function getRandomString(length)
     {
        var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var result = '';
        for ( var i = 0; i < length; i++ ) {
            result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
        }
        return result;
    }

    $("#submit").on('click',function(){
        var pass = getRandomString(8);
        $("#pass").val(pass);
    });
});
</script>