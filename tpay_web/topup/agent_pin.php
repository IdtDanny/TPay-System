<?php

$con = new mysqli("localhost","root","","tpay");
if(!$con){
	die("Error while trying to connect to the db server.");
}


if(isset($_GET['id'])){
	$agent_pin = $_GET['id'];

// $select = $con -> query("INSERT INTO `topup_ref` (`Amount`) VALUES('$amt')") or die(mysqli_error($con));

$update_reference = $con -> query("UPDATE `topup_ref` SET `agent_pin` = '$agent_pin' WHERE `id` = 1 ") or die(mysqli_error($con));

}

?>
