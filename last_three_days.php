<?php

echo "Today date ".$cdate = date('Y-m-d');
echo" <br><br>Last three days date". $last_three_day = date('Y-m-d', strtotime("-3 days", strtotime($cdate)));

?>