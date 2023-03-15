<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }


    # Includes...
    include '../config/connection.php';

    # Getting Information of Signed in User
    $agent_username = $_SESSION['sessionToken']->agent_username;
    $agent_ID = $_SESSION['sessionToken']->aID;

    // $agent_name = $_SESSION['sessionToken']->agent_name;
    // $agent_photo = $_SESSION['sessionToken']->photo;
    // $agent_balance = $_SESSION['sessionToken']->agent_balance;


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

    $user_FetchQuery = 'SELECT * FROM `ucards`';
    $user_FetchStatement = $pdo->prepare($user_FetchQuery);
    $user_FetchStatement->execute([]);
    $user_Result = $user_FetchStatement->fetchAll();

?>

<?php
    if (isset($_POST['add_new_ucard'])) {
        # Variable Declaration...

        $card_id = $_POST['card_id'];
        $card_holder = $_POST['card_holder'];
        $card_tel = $_POST['card_tel'];
        $card_email = $_POST['card_email'];
        $amount = $_POST['amount'];
        $approved = 'Approved';

        # Card Validation ...

        $fetch_cardQuery = 'SELECT * FROM `ucards` WHERE `Card_id` = :cardid';
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

                $user_balance = $card->Balance;
                $user_id = $card->No;
                // $user_name = $card -> Card_holder;

                # Operation...

                $New_balance = $user_balance + $amount;

                $agent_new_balance = $agent_balance - $amount;

                # Updating Data...

                $agent_updateQuery = 'UPDATE `agent`
                                        SET `agent_balance` = :agentbalance
                                        WHERE `aID` = :agentid
                ';

                $agent_updateStatement = $pdo->prepare($agent_updateQuery);
                $agent_updateStatement->execute([
                    'agentbalance'  =>  $agent_new_balance,
                    'agentid'       =>  $agent_ID
                ]);

                # Updating registered user
                $user_updateQuery = 'UPDATE `ucards`
                                        SET
                                            `Card_holder`   = :holder,
                                            `Card_Tel`      = :tel,
                                            `Email`         = :email,
                                            `Balance`       = :balance,
                                            `By_agent`      = :agentn,
                                            `Approve`       = :approved
                                        WHERE `Card_id`     = :cardid
                ';

                $user_updateStatement = $pdo->prepare($user_updateQuery);
                $user_updateStatement -> execute([
                    'holder'   =>  $card_holder,
                    'tel'      =>  $card_tel,
                    'email'    =>  $card_email,
                    'balance'  =>  $New_balance,
                    'agentn'   =>  $agentResults -> agent_username,
                    'approved' =>  $approved,
                    'cardid'   =>  $card_id
                ]);

                # Sending a Notification...

                $date_Sent = date('Y-m-d');
                $time_Sent = date('h:m');
                
                # Recording transactions...

                $trans_InsertQuery = 'INSERT INTO `transfering`(`sender_ID`, `reciever_ID`, `amount`, `date`)  
                                            VALUES ( :agentid, :ucardid, :balance, :cdate)
                ';

                $trans_InsertStatement = $pdo->prepare($trans_InsertQuery);
                $trans_InsertStatement->execute([
                    'agentid'  =>  $agent_ID,
                    'ucardid'  =>  $card_id,
                    'balance'  =>  $amount,
                    'cdate'    =>  $date_Sent
                ]);

                $message = 'You have Recharged a Card with  '.$amount. ' <br/> Done:' . $date_Sent . ',' . $time_Sent;

                sendNotification($agentResults -> agent_pin, $message, 'agent', $pdo);

                if($trans_InsertQuery && $user_updateQuery){
                    header("Location: register_ucard.php");
                }
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent | U-Cards Page</title>

    <?php
        include "includes/header_top.php";
    ?>

                <div class="logo-md text-primary">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_creditcard"></i></span>&nbsp;<span class="text-dark">U-Card</span>
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

        <div class="jumbotron jumbotron-lg bg-white shadow-sm" style="padding: 30px; margin: 10px;">
                <h1 class="logo-md">
                    <i class="icon_creditcard text-dark"></i>
                    U-Cards
                </h1>
                <br>
                <button class="btn-group">
                    <button class="btn-sm btn-info" id="modal-trigger"> <i class="icon_creditcard"></i> &nbsp; Register New U-Card</button>
                    <!-- <button class="btn-sm btn-secondary"><i class="icon_pencil-edit" id="modal-trigger2"></i><?php # echo $user -> Approve ?> &nbsp; Edit</button> -->
                </button>

                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Date Created</th>
                            <th>Card id</th>
                            <th>Card_Holder</th>
                            <th>Card_Tel</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Card Status</th>
                            <!-- <th>Edit</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr>
                        <td>hellp</td>
                        <td>hellp</td>
                    </tr> -->
                        <?php
                            foreach($user_Result as $user){
                            ?>
                            <tr>
                                <td><?php echo $user -> Date_Created ?></td>
                                <td><?php echo $user -> Card_id ?></td>
                                <td><?php echo $user -> Card_holder ?></td>
                                <td><?php echo $user -> Card_Tel ?></td>
                                <td><?php echo $user -> Email ?></td>
                                <td><?php echo number_format($user -> Balance) ?></td>
                                <td><?php echo $user -> Approve ?></td>
                            </tr>
                            <?php
                            }
                        ?>
                    </tbody>
                </table>
                <br>

                <!-- First modal -->

                <div class="modal-content">
                    <div class="modal">
                        <div class="modal-head">
                            <p class="display-1 text-primary">Add New U-Card</p>
                            <i class="icon_close" id="close-modal"></i>
                        </div>
                        <div class="modal-body">
                        <form method="post">
                                <div class="form-group">
                                    <label>Card ID</label>
                                    <?php
                                    $sel = 'SELECT * FROM `ucards` WHERE `Approve` != :approved';
                                    $approved = 'Approved';
                                    $agent_FetchStatement = $pdo->prepare($sel);
                                    $agent_FetchStatement->execute([
                                      'approved' => $approved
                                    ]);
                                    ?>
                                    <select name="card_id" class="form-input-2">
                                        <option>Assign Card</option>
                                    <?php while ( $row = $agent_FetchStatement->fetch() ): ?>
                                        <option value="<?php echo $row->Card_id ?>"><?php echo $row->Card_id ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Card Holder</label>
                                    <input type="text" name="card_holder" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Tel (250 xxx xxx xxx)</label>
                                    <input type="text" maxlength="12" name="card_tel" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Email (sample@domain.com)</label>
                                    <input type="text" name="card_email" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Submit" name="add_new_ucard" class="btn btn-sm btn-info">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Second Modal -->

                <div class="modal-content2">
                    <div class="modal2">
                        <div class="modal-head">
                            <p class="display-1 text-primary"><i class="icon_folder-add"></i> &nbsp; Agent Recharge</p>
                            <i class="icon_close" id="close-modal2"></i>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <div class="form-group">
                                    <label>Card PIN</label>
                                    <input type="text" name="vnumber" class="form-input-2" placeholder="Enter Card PIN">
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-input-2" placeholder="Recharge Amount">
                                </div>
                                <!-- <div class="form-group">
                                    <label>Agent</label>
                                    <?php
                                    $sel = 'SELECT * FROM `agent` ';
                                    $agent_FetchStatement = $pdo->prepare($sel);
                                    $agent_FetchStatement->execute();
                                    ?>
                                    <select name="agentid" class="form-input-2">
                                        <option>Choose Agent</option>
                                    <?php while ( $row = $agent_FetchStatement->fetch() ): ?>
                                        <option value="<?php echo $row->aID ?>"><?php echo $row->agent_username ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                </div> -->
                                <div class="form-group">
                                    <input type="submit" name="recharge" value="Recharge" class="btn btn-sm btn-primary">
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

        executeSuccess('Register New U-Card Successful');

    }
?>
