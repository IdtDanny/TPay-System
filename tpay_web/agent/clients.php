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

    # Getting Clients ...

    $client_FetchQuery = 'SELECT * FROM `client`';
    $client_FetchStatement = $pdo->prepare($client_FetchQuery);
    $client_FetchStatement->execute();
    $client_Result = $client_FetchStatement->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent | Clients Page</title>

    <!-- Icon Header -->
    <link rel="shortcut icon" type="image/png" href="../assets/img/Card_Header.png">

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/css/style.min.css">

    <!-- Icons CSS -->
    <link rel="stylesheet" href="../assets/icons/style.css">
</head>
<body class="bg-darken-light">


    <!-- Dashboard Navigation  -->

    <nav class="dashboard-nav bg-dark">
        <div class="container">
            <div class="navigation">
                <div class="logo-md text-primary">
                    <a href="index.php" style="text-decoration: none;color: #07ac82;">Dashboard</a><span>&nbsp; | &nbsp;<i class="icon_contacts_alt"></i></span>&nbsp;Agent
                </div>
                <div class="navs">
                    <ul class="nav-links">
                        <li>
                          <a href="register_ucard.php"><span><i class="icon_group"></i></span>&nbsp;U-Cards</a>
                        </li>
                        <li>
                            <a href="transactions.php"><span><i class=" icon_documents_alt"></i></span>&nbsp;Transactions</a>
                        </li>
                        <li>
                            <a href="notifications.php"><span><i class="icon_comment"></i></span>&nbsp;Notification</a>
                        </li>
                        <li>
                            <a href="logout.php"><span><i class="arrow_triangle-left_alt2"></i></span>&nbsp;Logout</a>
                        </li>
                    </ul>
                    <div class="burger">
                        <div class="line1"></div>
                        <div class="line2"></div>
                        <div class="line3"></div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

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

        <div class="jumbotron jumbotron-lg bg-white shadow-sm">
                <h1 class="logo-md">
                    <i class="icon_group text-dark"></i>
                    Client Cards
                </h1>
                <br>
                <button class="btn-sm btn-info" id="modal-trigger"> <i class="icon_creditcard"></i> &nbsp; Client Recharge</button>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Card id</th>
                            <th>Balance</th>
                            <th>Card Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr>
                        <td>hellp</td>
                        <td>hellp</td>
                    </tr> -->
                        <?php
                            foreach($client_Result as $client){
                            ?>
                            <tr>
                                <td><?php echo $client->card_id ?></td>
                                <td><?php echo $client->balance ?></td>
                                <td><?php echo $client->status ?></td>
                            </tr>
                            <?php
                            }
                        ?>
                    </tbody>
                </table>
                <br>
                <div class="modal-content">
                    <div class="modal">
                        <div class="modal-head">
                            <p class="display-1 text-primary">Add New Client</p>
                            <i class="icon_close" id="close-modal"></i>
                        </div>
                        <div class="modal-body">
                        <form method="post">
                                <div class="form-group">
                                    <label>Card ID</label>
                                    <input type="text" name="card_id" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Recharge" name="Recharge" class="btn btn-sm btn-info">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
<script src="../assets/js/main.js"></script>
</html>

<?php
    if (isset($_POST['Recharge'])) {
        # Variable Declaration...

        $card_id = $_POST['card_id'];
        $amount = $_POST['amount'];

        # Card Validation ...

        $fetch_cardQuery = 'SELECT * FROM client WHERE card_id = :cardid';
        $fetch_cardStatement = $pdo->prepare($fetch_cardQuery);
        $fetch_cardStatement->execute([
            'cardid'    =>  $card_id
        ]);

        $card = $fetch_cardStatement->fetch();

        # Counting ...

        $cardCount = $fetch_cardStatement->rowCount();

        if ($cardCount >= 0) {

            # Validation of Amount to be sent ...

            if ($amount <= $agent_balance) {

                # Client Details...

                // No longer in use..... $client_username=$row['username'];

                $client_balance = $card->balance;
                $client_id = $card->cID;

                # Operation...

                $client_new_balance = $client_balance + $amount;

                $agent_new_balance = $agent_balance - $amount;

                # Updating Data...

                $agent_updateQuery = 'UPDATE `agent`
                                        SET agent_balance = :agentbalance
                                        WHERE aID = :agentid
                ';

                $agent_updateStatement = $pdo->prepare($agent_updateQuery);
                $agent_updateStatement->execute([
                    'agentbalance'  =>  $agent_new_balance,
                    'agentid'       =>  $agent_ID
                ]);

                # Updating Client...

                $client_updateQuery = 'UPDATE client
                                        SET balance = :balance
                                        WHERE card_id = :cardid
                ';

                $client_updateStatement = $pdo->prepare($client_updateQuery);
                $client_updateStatement->execute([
                    'balance'   =>  $client_new_balance,
                    'cardid'    =>  $card_id
                ]);

                # Sending a Notification...

                $date_Sent=date("d/m/Y");
                $time_Sent=date("h:m");

                $message = 'You have Recharged a Card with  '.$amount. ' <br/> Done:'. $date_Sent. ',' . $time_Sent;

                sendNotification($agent_ID, $message, 'agent', $pdo);
            }
            else{
                executeError('Amount Exceeded');
            }
        }
        else{
            executeError('Invalid Card');
        }

    }
?>



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
