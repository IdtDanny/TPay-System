<?php

$conn = new mysqli("localhost","root","","tpay");
if(!$conn){
	die("Error while trying to connect to the db server.");
}


if(isset($_GET['card_id'])){
	$card_id=$_GET['card_id'];

$select = $conn -> query("SELECT * FROM `ucards` where `card_id` = '$card_id'");
$row = mysqli_num_rows($select);
// $name = $row['name'];
// echo $name;

	if(!empty($card_id)){
		$check = $conn -> query("SELECT `No` FROM `ucards` where `card_id`='$card_id' LIMIT 1");
		if($check){
			if($check->num_rows>0){
				echo "Registered";
			}else{
						$record_u_card = $conn -> query("INSERT INTO `ucards` (`Date_Created`, `Card_id`, `Card_holder`, `Status`, `Approve`)
																									VALUES (NOW(), '$card_id', '', '', '')");

						if($record_u_card){
									echo $card_id;
									echo "ok";
								}else{
									echo "Could not Record!";
								}
			}
		}else{
			echo "Error in sql query";
		}
	}else{
		echo "id not sent";
	}
}

?>
