<?php
    $con = new mysqli("localhost","root","","tpay");

    if(!$con){
        die("Error while trying to connect to the db server.");
    }


    if(isset($_GET['nid'])){
        $nid = $_GET['nid'];

        $delete_notification = $con -> query("DELETE FROM `notification` WHERE `nid` = '$nid' ");
        if($delete_notification){
            header('Location: notifications.php');
        }
    }
    
?>