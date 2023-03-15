<?php

$conn = new mysqli("localhost","root","","tpay");
if(!$conn){
	die("Error while trying to connect to the db server.");
}


if(isset($_GET['id'])){
	$card_id=$_GET['id'];

$select = $conn -> query("SELECT * FROM `ucards` where `Card_id`='$card_id'");
$row = mysqli_num_rows($select);
// $name = $row['name'];
// echo $name;

	if(!empty($card_id)){
		$check = $conn -> query("SELECT `No` FROM `ucards` where `Card_id`='$card_id' LIMIT 1");
		if($check){
			if($check->num_rows>0){
				echo $card_id;
				echo "ok";
			}else{
				echo "not found";
			}
		}else{
			echo "Error in sql query";
		}
	}else{
		echo "id not sent";
	}
}

?>