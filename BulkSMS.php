<?php
//Enter your login username 
$username="BusyBoy0510";

//Enter your login password 
$password="123456";

//Enter your text message 
$message="Hii Friends Free SMS Worked ";

//Enter your Sender ID
$sender="ADMITN";

//Enter your receiver mobile number
$mobile_number="9265243821";

//Don't change below code use as it is
$url="https://www.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&
mobile=".urlencode($mobile_number)."&message=".urlencode($message)."&sender=".urlencode($sender)."&type=".urlencode('3');

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_scraped_page = curl_exec($ch);

curl_close($ch);


?>
