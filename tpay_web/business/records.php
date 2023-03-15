<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }


    # Includes...
    include '../config/connection.php';

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

    # Getting Clients ...

    $user_FetchQuery = 'SELECT * FROM `records`';
    $user_FetchStatement = $pdo->prepare($user_FetchQuery);
    $user_FetchStatement->execute([]);
    $user_Result = $user_FetchStatement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent | Records Page</title>

    <?php
        include "includes/header_top.php";
    ?>

                <div class="logo-md text-primary">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_documents_alt"></i></span>&nbsp;<span class="text-dark">Records</span>
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

        <div class="jumbotron jumbotron-lg bg-white shadow-sm" style="padding: 25px;">
                <h1 class="logo-md">
                    <i class="icon_documents_alt text-dark"></i>&nbsp;
                    Records
                </h1>

                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Card id</th>
                            <th>Card_Holder</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <!-- <th>Edit</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($user_Result as $user){
                            ?>
                            <tr>
                                <td><?php echo $user -> Date ?></td>
                                <td><?php echo $user -> Card_id ?></td>
                                <td><?php echo $user -> Card_holder ?></td>
                                <td><?php echo $user -> Amount_paid ?></td>
                                <td><?php echo $user -> Status ?></td>
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

        executeSuccess('Register New U-Card Successful');

    }
?>
