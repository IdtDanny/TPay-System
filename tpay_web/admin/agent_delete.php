<?php  
    session_start();
	# Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../config/connection.php';

    # Getting The Sent ID...
    $aid=$_GET['aid'];
    

    $sql='DELETE FROM agent WHERE aID=:aid';

    # PDO Prep & Exec..
    $delete_ClientR = $pdo->prepare($sql);
    $delete_ClientR->execute([
        'aid'  =>  $aid
    ]);

    
    # Deleting Card in Recharging Card Table...

    header("location:agents.php");
    
?>