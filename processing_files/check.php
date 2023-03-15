<?php

$conn=new mysqli("localhost","root","","obedSystem");
if(!$conn){
	die("Error while trying to connect to the db server.");
}

if(isset($_GET['id'])){
	$id=$_GET['id'];
	if(!empty($id)){
		$check=$conn->query("SELECT cID FROM client where rfid='$id' LIMIT 1");
		if($check){
			if($check->num_rows>0){
				echo $id;
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
