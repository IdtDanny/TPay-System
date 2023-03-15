<?php

$con = new mysqli("localhost","root","","tpay");
if(!$con){
	die("Error while trying to connect to the db server.");
}


if(isset($_GET['id'])){
	$amt=$_GET['id'];

$select=$con->query("INSERT INTO `reference` (`amount`) VALUES('$amt')") or die(mysqli_error($con));

// $update_reference = $con -> query("UPDATE `reference` SET `Amount` = '$amt' WHERE `id` = 0 ") or die(mysqli_error($con));

}

?>
