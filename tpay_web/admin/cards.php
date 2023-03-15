<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }


    # Includes...
    require_once '../config/connection.php';

    # Getting Information of Signed in User
    $admin_username = $_SESSION['sessionToken']->admin_username;
    $admin_ID = $_SESSION['sessionToken']->admin_ID;
    $admin_name = $_SESSION['sessionToken']->admin_name;


    # Getting Cards ...

    $card_FetchQuery = 'SELECT * FROM `recharging_card`';
    $card_FetchStatement = $pdo->prepare($card_FetchQuery);
    $card_FetchStatement->execute();
    $card_Result = $card_FetchStatement->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Cards</title>

        <?php
          include "includes/Header_dash_top.php";
         ?>

                <div class="logo-md text-primary">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_creditcard"></i></span>&nbsp;<span class='text-dark'>Cards</span>
                </div>

                <?php
                  include "includes/Header_dash_bot.php";
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
            <div class="jumbotron jumbotron-lg bg-white shadow-sm">
                <h1 class="logo-md">
                    <i class="icon_creditcard text-dark"></i>
                    Cards
                </h1>
                <br>
                <div class="btn-group">
                    <button class="btn-sm btn-secondary" id="modal-trigger"><i class="icon_plus_alt2"></i> &nbsp; Generate Cards</button>
                </div>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Card</th>
                            <th>Amount</th>
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr>
                        <td>hellp</td>
                        <td>hellp</td>
                    </tr> -->
                        <?php
                            foreach($card_Result as $card){
                            ?>
                            <tr>
                                <td><?php echo $card->pin ?></td>
                                <td><?php echo $card->amount ?></td>
                                <td><?php echo $card->date ?></td>
                                <td><?php echo $card->Status ?></td>
                                <td><a href="card_delete.php?aid=<?php echo $card->rcID ?>"><i class="icon_trash btn-delete"></i></a></td>
                            </tr>
                            <?php
                            }
                        ?>
                    </tbody>
                </table>
                <br>

                <!-- First Modal -->

                <div class="modal-content">
                    <div class="modal">
                        <div class="modal-head">
                            <p class="display-1 text-primary"><i class="icon_creditcard"></i> &nbsp; Generate Cards</p>
                            <i class="icon_close" id="close-modal"></i>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Number of Cards</label>
                                    <input type="text" name="cardnumber" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="generate" value="Generate Cards" class="btn btn-sm btn-secondary">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- modal end -->
            </div>
        </div>
    </div>

</body>
<script src="../assets/js/main.js"></script>
</html>

<?php
    # Generating Cards...

    function rand16(){
        $number="";
        for ($i=0; $i < 16; $i++) {
           $min= ($i == 0)? 1:0;
           $number .= mt_rand($min,9);
        }
        return $number;
    }

    if (isset($_POST['generate'])) {

        # Variable Declaration...

        $amount=$_POST['amount'];
        $cnumber=$_POST['cardnumber'];

        $i=1;

        while ($i <= $cnumber) {
            $randnum = rand16();

            $generated_CardQuery = 'SELECT * FROM recharging_card WHERE pin = :pin
            ';
            $generated_CardStatement = $pdo->prepare($generated_CardQuery);
            $generated_CardStatement->execute([
                'pin' => $randnum
            ]);

            $count = $generated_CardStatement->rowCount();

            if ($count > 0) {
                $randnum=rand16();
            }

            $insert=$pdo->query("
                                INSERT INTO recharging_card(amount,pin,`date`,Status)
                                VALUES ('$amount','$randnum',NOW(),'Not used')
            ");

            $i++;
            $nbr = $i-1;
        }
        executeSuccess( $nbr. '  Cards Generated');
    }
?>

<?php

    // Functions...

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

    function sendNotification($reciever, $message, $target, $conn){

        # Variable Declaration...
        $date_Sent=date("d/m/Y");
        $notitfication_Status = 'unread';

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

        executeSuccess('Agent Recharge Successful');

    }
?>
