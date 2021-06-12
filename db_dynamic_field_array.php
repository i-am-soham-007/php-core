<?php
$con =  mysqli_connect("localhost","root","","test");
$sql = mysqli_query($con,'SELECT * FROM user');

$associativeArray = array();
while($row = mysqli_fetch_array($sql)){
   // Put the values into the array, no other variables needed
   $associativeArray[$row['name']] = $row['name'];
}

foreach($associativeArray as $k => $id){
    echo $k."=>".$id;
}
?>