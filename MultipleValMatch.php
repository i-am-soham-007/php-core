<!DOCTYPE html>
<html>
<body>

<?php
$a=array(1,2,3,4,5,8);
//echo array_search(5,$a,true);


$array = array(1,2,3,4,5,8,5,8);
//echo count(array_keys($array, 5));
$second_numbers = array(04,13);
//print_r($array);

$position =array_keys(array_intersect($second_numbers, array(13))); // NUMBERS
print_r($position);
exit();
 if( $matches =array_keys(array_intersect($array, array(5)))){
 	echo "Matched Done";
   	 print_r($matches);
 }else{
 echo "Not Matched";
 }
 
?>

</body>
</html>
