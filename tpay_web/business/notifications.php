<?php
    session_start();

    # Checkin if The user logged in...
 
    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }


    # Includes...
    require_once '../config/connection.php';

    # Getting Information of Signed in User
    $business_names = $_SESSION['sessionToken']->business_name;
    $bID = $_SESSION['sessionToken']->bID;
    $business_tin = $_SESSION['sessionToken']->business_tin;
    $business_photo = $_SESSION['sessionToken']->photo;
    $_business_balance = $_SESSION['sessionToken']->balance;


    # Getting Business Info. for update form...

    $businessFetchQuery = 'SELECT * FROM `business` WHERE `bID` = :bid';
    $businessFetchStatement = $pdo->prepare($businessFetchQuery);
    $businessFetchStatement->execute([
        'bid' => $bID
    ]);
    $businessResults = $businessFetchStatement->fetch();

    # Storing Updated Information...

    $business_name = $businessResults->business_name;
    $business_photo = $businessResults->photo;
    $business_balance = $businessResults->balance;
    $business_tin = $businessResults->business_tin;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent | Notification</title>

    <?php
        include "includes/header_top.php";
    ?>

                <div class="logo-md text-primary">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_comment"></i></span>&nbsp;<span class="text-dark">Notification</span>
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
                    <i class="icon_comment text-primary"></i>&nbsp;
                    Notifications
                </h1>
                <br>
                    <div class="notification-holder"> 
                        <?php
                            # Fetching Notification...

                            $fetchDateQuery = 'SELECT `date_sent` FROM `notification` WHERE recieverid = :reciever AND `target`= :business GROUP BY `date_sent` ORDER BY date_sent DESC';

                            $fetchDateStatement = $pdo->prepare($fetchDateQuery);
                            $fetchDateStatement->execute([
                                'reciever'  =>  $business_tin,
                                'business'     =>  'business'
                            ]);

                            while($dateArray = $fetchDateStatement->fetch()):
                                $notificationDate = $dateArray->date_sent;
                                $retVal = ($notificationDate == date('Y-m-d')) ? 'Today' : $notificationDate ;
                        ?>
                           <div class="notification ">
                                <div class="date">
                                    <h4 class="text-light ml-1" style="font-family: century gothic;"><?php echo $retVal ?></h4>
                                </div>
                                <?php
                                    $fetchNotificationQuery = 'SELECT * FROM `notification` WHERE recieverid = :reciever AND `date_sent` = :datesent ORDER BY `nID` DESC
                                    ';

                                    $fetchNotificationStatement = $pdo->prepare($fetchNotificationQuery);
                                    $fetchNotificationStatement->execute([
                                        'reciever'  =>  $business_tin,
                                        'datesent'  =>  $notificationDate
                                    ]);


                                    while($notificationArray = $fetchNotificationStatement->fetch()):
                                        $notification_Status = $notificationArray->status;
                                ?>
                                <div class="body-cont">
                                    <div class="notification-message">
                                        <p style="padding: 10px; font-family: century gothic;"><?php echo $notificationArray->message?></p>
                                    </div>
                                    <div class="hov">
                                        <span>
                                            <?php if ($notification_Status == 'unread'): ?>
                                                <a href="read.php?nid=<?php echo $notificationArray->nID ?>" class="indigo-text " style="font-weight: bold;" title="Mark as Read"><i class="icon_mail text-primary ml-4"></i></a>
                                            <?php endif ?>
                                            <?php if ($notification_Status == 'read'): ?>
                                                <a href="#" class="indigo-text" title="Read"><i class="icon_check text-primary ml-4"></i></a>
                                            <?php endif ?>
                                            
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="deleten.php?nid=<?php echo $notificationArray->nID ?>" class="pink-text " style="font-weight: bold;" title="Delete"><i class="icon_trash text-danger"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <?php
                                    endwhile;
                                ?>
                           </div>
                        <?php
                        endwhile;
                        ?>
                        </div>
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
