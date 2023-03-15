<?php  
    session_start();
	# Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../config/connection.php';

    # Getting The Sent ID...
    $bid=$_GET['bid'];
    

    $sql='DELETE FROM `business` WHERE bID=:bid';

    # PDO Prep & Exec..
    $delete_BusinessR = $pdo->prepare($sql);
    $delete_BusinessR->execute([
        'bid'  =>  $bid
    ]);

    
    # Deleting Card in Recharging Card Table...

    header("location: business.php");
    
?>