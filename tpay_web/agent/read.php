<?php
    $con = new mysqli("localhost","root","","tpay_db");

    if(!$con){
        die("Error while trying to connect to the db server.");
    }


    if(isset($_GET['nid'])){
        $nid = $_GET['nid'];

        $update_notification = $con -> query("UPDATE `notification` SET `status` = 'read' WHERE `nid` = '$nid' ");
        if($update_notification){
            header('Location: notifications.php');
        }
    }
    
?>