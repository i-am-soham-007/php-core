<?php
$fdate = date("Y-m-d");

$duration ="Half Yearly";

if($duration =="Yearly"){
    $duration_val = "+1 year";
}
if($duration =="Half Yearly"){
    $duration_val = "+6 months";
}
if($duration =="Quartly"){
    $duration_val = "+3 months";
}
if($duration =="Monthly"){
    $duration_val = "+1 months";
}
echo $tdate =date("Y-m-d",strtotime($duration_val, strtotime($fdate)));
?>