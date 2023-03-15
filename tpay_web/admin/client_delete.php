<?php
    session_start();
	# Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../config/connection.php';

    # Getting The Sent ID...
    $No=$_GET['No'];

    # Getting The Card ID...
    $getCardQuery = 'SELECT `Card_id` FROM `ucards` WHERE `No` = :No' ;

    $stmt = $pdo->prepare($getCardQuery);
    $stmt->execute([
        'No'   =>  $No
    ]);
    $card = $stmt->fetch();
    $card_id = $card->Card_id;

    // $sql='DELETE FROM recharging_card WHERE pin=:clientid';

    # PDO Prep & Exec..
    // $delete_ClientR = $pdo->prepare($sql);
    // $delete_ClientR->execute([
    //     'clientid'  =>  $Card_id
    // ]);

    $query='DELETE FROM `ucards` WHERE `No`=:No';

    # PDO Prep & Exec..
    $delete_Client = $pdo->prepare($query);
    $delete_Client->execute([
        'No'  =>  $No
    ]);

    # Deleting Card in Recharging Card Table...

    header("location:clients.php");

?>
