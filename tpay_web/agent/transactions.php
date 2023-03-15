<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }


    # Includes...
    require_once '../config/connection.php';

    # Getting Information of Signed in User
    $agent_username = $_SESSION['sessionToken']->agent_username;
    $agent_ID = $_SESSION['sessionToken']->aID;

    $agent_name = $_SESSION['sessionToken']->agent_name;
    $agent_photo = $_SESSION['sessionToken']->photo;
    $agent_balance = $_SESSION['sessionToken']->agent_balance;


    # Getting Agent Info. for update form...

    $agentFetchQuery = 'SELECT * FROM `agent` WHERE `aID` = :agentid';
    $agentFetchStatement = $pdo->prepare($agentFetchQuery);
    $agentFetchStatement->execute([
        'agentid' => $agent_ID
    ]);
    $agentResults = $agentFetchStatement->fetch();

    # Storing Updated Information...

    $agent_name = $agentResults->agent_name;
    $agent_photo = $agentResults->photo;
    $agent_balance = $agentResults->agent_balance;
    $agent_aID = $agentResults->aID;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent | Transactions</title>

    <?php
        include "includes/header_top.php";
    ?>

                <div class="logo-md text-primary">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_contacts_alt"></i></span>&nbsp;<span class="text-dark">Transactions</span>
                </div>

    <?php
        include "includes/header_bot.php";
    ?>

    <!-- Navigation End Here!! -->

    <!-- Dashboard Body!!! -->
    <br>
    <div id="error-holder">
    <!-- <div class="error">
        <i class="icon_error-circle_alt"></i>
        <p>Passwords Does not Match</p>
        <i class="icon_close" id="close-btn" title="Dismiss"></i>
    </div> -->
    </div>
    <div class="container" >
        <div class="row">

        <div class="jumbotron jumbotron-lg bg-white shadow-sm" style="padding: 35px;">
                <h1 class="logo-md">
                    <i class="icon_documents_alt text-dark"></i>
                    Transactions
                </h1>
                <br>

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>U-Card</th>
                            <th>Date</th>
                            <th class="text-center">Amount </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        # Getting Payments Made By The Logged in agent...

                        $fetchTransactionQuery = 'SELECT * from `transfering` WHERE `sender_ID` = :agentid ORDER BY `date` DESC';
                        $fetchTransactionStatement = $pdo->prepare($fetchTransactionQuery);
                        $fetchTransactionStatement->execute([
                            'agentid'   =>  $agent_aID
                        ]);
                        $transaction = $fetchTransactionStatement->fetchAll();

                        foreach($transaction as $transfer){

                    ?>
                        <tr>
                            <td><?php echo  $transfer  -> reciever_ID?> </td>
                            <td><?php echo  $transfer -> date?></td>
                            <td class="text-center"><?php echo  number_format($transfer -> amount);?></td>
                        </tr>
                        <?php 
                    }
                    ?>
                        </tbody>
                    </table>
                <br>
            </div>

        </div>
    </div>

</body>
<script src="../assets/js/main.js"></script>
</html>

<?php

    function executeError($messageToShow){
        $errorMessage = $messageToShow;
        ?>
                <script>
                    dangerErrorMessage('<?php echo $errorMessage ?>');
                </script>
        <?php
    }
    function executeSuccess($messageToShow){
        $successMessage = $messageToShow;
        ?>
                <script>
                    successMessage('<?php echo $successMessage ?>');
                </script>
        <?php
    }

    // Send Notification Function...

    function sendNotification($reciever, $message, $target, $conn){

        # Variable Declaration...
        $notitfication_Status = 'unread';
        $date_Sent=date("d/m/Y");

        # Inserting Notification...

        $notification_InsertQuery = 'INSERT INTO notification(`recieverid`,`message`,`date_sent`,`status`,`target`)
                                        VALUES (:reciever, :messagebody, :datesent, :notificationstatus, :notificationtarget)
        ';

        $notification_InsertStatement = $conn->prepare($notification_InsertQuery);
        $notification_InsertStatement->execute([
            'reciever'              =>  $reciever,
            'messagebody'           =>  $message,
            'datesent'              =>  $date_Sent,
            'notificationstatus'    =>  $notitfication_Status,
            'notificationtarget'    =>  $target
        ]);

        executeSuccess('Client Recharge Successful');

    }
?>
