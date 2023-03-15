<?php

$con = new mysqli("localhost","root","","tpay");
if(!$con){
	die("Error while trying to connect to the db server.");
}


if(isset($_GET['id'])){
	$business_pin = $_GET['id'];

// $select = $con -> query("INSERT INTO `topup_ref` (`Amount`) VALUES('$amt')") or die(mysqli_error($con));

$update_reference = $con -> query("UPDATE `reference` SET `business_pin` = '$business_pin' WHERE `id` = 0 ") or die(mysqli_error($con));

}

?>
