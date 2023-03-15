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


    # Getting Clients ...

    $agent_FetchQuery = 'SELECT * FROM `agent`';
    $agent_FetchStatement = $pdo->prepare($agent_FetchQuery);
    $agent_FetchStatement->execute();
    $agent_Result = $agent_FetchStatement->fetchAll();

        # Getting Admin Info. for update form...

        $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
        $adminFetchStatement = $pdo->prepare($adminFetchQuery);
        $adminFetchStatement->execute([
            'adminid' => $admin_ID
        ]);
        $adminResults = $adminFetchStatement->fetch();

?>

<?php

    if (isset($_POST['addagent'])) {
        $names = $_POST['agent_names'];
        $username = $_POST['agent_username'];
        $agent_pin = rand(1000,9999);
        $password= $username.'-'.$agent_pin;
        $hashed_Password=md5($password);
        $photo=$_FILES['photo']['name'];
        $folder="../pictures/".basename($photo=$_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $folder) or die(error_reporting())){

            # Inserting Agent...

            $sql_insert="  INSERT INTO `agent`(`agent_name`,`agent_username`,`agent_password`,`agent_pin`,`photo`,`agent_balance`)
                                VALUES(:agentname, :agentusername, :agentpassword, :agentpin, :photo, :agentbalance)
                ";

            $agent_InsertStatement = $pdo->prepare($sql_insert);
            $agent_InsertStatement->execute([
                'agentname'         =>  $names,
                'agentusername'     =>  $username,
                'agentpassword'     =>  $hashed_Password,
                'agentpin'          =>  $agent_pin,
                'photo'             =>  $photo,
                'agentbalance'      =>  '0'
            ]);

            if ($sql_insert) {
                # code...
            header('Location: agents.php?m');
            executeSuccess('Agent Added, PIN:'.$agent_pin);
          }
        }
        else{
            executeError('Something Went Wrong');
        }
    }
?>

<!-- Recharge... -->

<?php

# Recharging Operation...

if (isset($_POST['recharge'])) {

    $cardpin = $_POST['vnumber'];
    $balance = $_POST['amount'];
    // $agentid = $_POST['agentid'];

    // $aID = $agentid;
    # Checking for cardpin ...

        $fetch_UserQuery='SELECT * FROM `agent` WHERE `agent_pin` = :pin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'pin'   => $cardpin
        ]);

        $agent_Info = $fetch_UserStatement -> fetch();

        $cardCount = $fetch_UserStatement->rowCount();

      if ($cardCount == 1) {

        # User Balance ...

        $agent_name = $agent_Info -> agent_name;
        $current_AgentBalance = $agent_Info -> agent_balance;
        $new_AgentBalance = $current_AgentBalance + $balance;

        # Updating Agent ...

        $agent_UpdateQuery = ' UPDATE `agent`
                                SET `agent_balance` = :agent_NewBalance
                                WHERE `agent_pin` = :agentid
        ';

        $agent_UpdateStatement = $pdo->prepare($agent_UpdateQuery);
        $agent_UpdateStatement->execute([
            'agent_NewBalance'  =>  $new_AgentBalance,
            'agentid'           =>  $cardpin
        ]);

        # Updating Admin ...

        $admin_balance = $adminResults -> Balance;
        $admin_name = $adminResults -> admin_name;
        $admin_newBalance = $admin_balance - $balance;

        $admin_UpdateQuery = ' UPDATE `admin`
                                SET `Balance` = :admin_balance
                                WHERE `admin_name` = :admin_name
        ';

        $admin_UpdateStatement = $pdo->prepare($admin_UpdateQuery);
        $admin_UpdateStatement->execute([
            'admin_balance'     =>  $admin_newBalance,
            'admin_name'           =>  $admin_name
        ]);

        # Sending Notification...
        $date_Sent = date("d/m/Y");
        $time_Sent = date("h:m");

        $messageAgent = 'You have been recharged , Amount: '.number_format($balance). '<br/> Done: '. $date_Sent. ', ' . $time_Sent;
        sendNotification($cardpin, $messageAgent, 'agent', $pdo);

        $messageAdmin = 'Recharge Successfully , Amount: '.number_format($balance). '<br/> To: '. $agent_name . '<br/> Done: '. $date_Sent. ', ' . $time_Sent;
        sendNotificationAdmin($cardpin, $messageAdmin, 'agent', $pdo);

        if ($agent_UpdateQuery) {
          header('Location: agents.php');
        }
      }
      else {
        executeError('Unknown Pin');
      }

}

?>

<?php

# Withdraw Operation...

if (isset($_POST['withdraw'])) {

    $agent_name = $_POST['agent_name'];
    $agent_pin = $_POST['apin'];
    $balance = $_POST['amount'];
    // $agentid = $_POST['agentid'];

    // header("location: business.php?".+$businessTin)

    // $aID = $agentid;
    # Checking for cardpin ...

        $fetch_UserQuery='SELECT * FROM `agent` WHERE `agent_name` = :aname AND `agent_pin` = :agent_pin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'aname'       => $agent_name,
            'agent_pin'  => $agent_pin
        ]);

        $agentModify_Info = $fetch_UserStatement -> fetch();

        $agentModifyCount = $fetch_UserStatement->rowCount();

      if ($agentModifyCount > 0 ) {

        # User Balance ...

        $current_AgentBalance = $agentModify_Info -> agent_balance;
        
        if($current_AgentBalance > 0){
            
        $new_AgentBalance = $current_AgentBalance - $balance;

        # Updating Agent ...

        $agent_UpdateQuery = ' UPDATE `agent`
                                SET `agent_balance` = :new_AgentBalance
                                WHERE `agent_pin` = :agent_pin
        ';

        $agent_UpdateStatement = $pdo->prepare($agent_UpdateQuery);
        $agent_UpdateStatement->execute([
            'new_AgentBalance'  =>  $new_AgentBalance,
            'agent_pin'           =>  $agent_pin
        ]);

        #Updating admin balance
        $admin_balance = $adminResults -> Balance;
        $admin_name = $adminResults -> admin_name;
        $admin_newBalance = $admin_balance + $balance;

        $admin_UpdateQuery = ' UPDATE `admin`
                                SET `balance` = :new_AdminBalance
                                WHERE `admin_name` = :admin_name
        ';

        $admin_UpdateStatement = $pdo->prepare($admin_UpdateQuery);
        $admin_UpdateStatement->execute([
            'new_AdminBalance'  =>  $admin_newBalance,
            'admin_name'           =>  $admin_name
        ]);

        # Sending Notification...
        $date_Sent = date("d/m/Y");
        $time_Sent = date("h:m");

        $message = 'Withdraw Successful , Amount: '.$balance. ' Rwf<br/> Done:'. $date_Sent. ',' . $time_Sent;

        $message_admin = 'Recharged with , Amount: '.$balance. ' Rwf from'. $agent_name . '<br/> Done:'. $date_Sent. ',' . $time_Sent;
        
        sendNotification($agent_pin, $message, 'agent', $pdo);

        sendNotificationAdmin($agent_pin, $message_admin, 'agent', $pdo);

        if ($agent_UpdateQuery && $agent_UpdateQuery) {
          header('Location: agents.php');
            }
        }
        else{
            executeError('Amount Exceed');
          header('Location: agents.php?exceed');
        }
      }
      else {
        executeError('Unknown Pin');
      }
    

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
        $date_Sent = date("Y-m-d");
        $notification_Status = 'unread';

        # Inserting Notification...

        $notification_InsertQuery = 'INSERT INTO notification(`recieverid`,`message`,`date_sent`,`status`,`target`)
                                        VALUES (:reciever, :messagebody, :datesent, :notificationstatus, :notificationtarget)
        ';

        $notification_InsertStatement = $conn->prepare($notification_InsertQuery);
        $notification_InsertStatement->execute([
            'reciever'              =>  $reciever,
            'messagebody'           =>  $message,
            'datesent'              =>  $date_Sent,
            'notificationstatus'    =>  $notification_Status,
            'notificationtarget'    =>  $target
        ]);

        executeSuccess('Agent Recharge Successful');

    }

    function sendNotificationAdmin($reciever, $message, $target, $conn){

        # Variable Declaration...
        $date_Sent = date("Y-m-d");
        $notification_Status = 'unread';

        # Inserting Notification...

        $notification_InsertQuery = 'INSERT INTO `notification_admin`(`recieverid`,`message`,`date_sent`,`status`,`target`)
                                        VALUES (:reciever, :messagebody, :datesent, :notificationstatus, :notificationtarget)
        ';

        $notification_InsertStatement = $conn->prepare($notification_InsertQuery);
        $notification_InsertStatement->execute([
            'reciever'              =>  $reciever,
            'messagebody'           =>  $message,
            'datesent'              =>  $date_Sent,
            'notificationstatus'    =>  $notification_Status,
            'notificationtarget'    =>  $target
        ]);

        executeSuccess('Agent Recharge Successful');

    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Agents</title>

    <?php
      include "includes/Header_dash_top.php";
     ?>
                <div class="logo-md">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark" >&nbsp; | &nbsp;<i class="icon_contacts_alt"></i></span><span class="text-dark">&nbsp;Agents</span>
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
                    <i class="icon_contacts_alt text-dark"></i>
                    Agents
                </h1>
                <br>
                <div class="btn-group">
                    <button class="btn-sm btn-primary" id="modal-trigger"> <i class="icon_plus_alt2"></i> &nbsp; Add New Agent</button>
                    <button class="btn-sm btn-secondary" id="modal-trigger2"><i class="icon_folder-add_alt"></i> &nbsp; Agent Recharge</button>
                    <button class="btn-sm btn-dark" id="modal-trigger3"><i class="icon_wallet"></i> &nbsp; Withdraw</button>
                </div>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Agent Names</th>
                            <th class="text-center">Agent Pin</th>
                            <th class="text-center">Agent Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr>
                        <td>hellp</td>
                        <td>hellp</td>
                    </tr> -->
                        <?php
                            foreach($agent_Result as $agent){
                            ?>
                            <tr>
                                <td><img  src="../pictures/<?php echo $agent -> photo ?>" style="width: 40px;height: 40px;object-fit: cover;border-radius: 100%;"/></td>
                                <td><?php echo $agent -> agent_name ?></td>
                                <td class="text-center"><?php echo $agent -> agent_pin ?></td>
                                <td class="text-center"><?php echo number_format($agent -> agent_balance) ?></td>
                                <td class="text-center"><a href="agent_delete.php?aid=<?php echo $agent -> aID ?>"><i class="icon_trash" style="color: #A74A47"></i></a></td>
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
                            <p class="display-1 text-primary"><i class="icon_plus_alt2"></i> &nbsp;Add New Agent</p>
                            <i class="icon_close" id="close-modal"></i>
                        </div>
                        <div class="modal-body">
                            <form method="post" enctype="multipart/form-data">
                                <p class="muted">* The Default Password Will be like (username-pin)</p>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="agent_names" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="agent_username" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Photo</label>
                                    <input type="file" name="photo" class="form-file-2" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="addagent" value="Add New Agent" class="btn btn-sm btn-primary">
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


                <!-- Third modal -->

                <div class="modal-content3">
                    <div class="modal3">
                        <div class="modal-head">
                            <p class="display-1 text-primary"><i class="icon_wallet_alt"></i> &nbsp;Withdraw</p>
                            <i class="icon_close" id="close-modal3"></i>
                        </div>
                        <div class="modal-body">
                        <form method="post">
                                <div class="form-group">
                                    <label>Agent Name</label>
                                    <?php
                                    $sel = 'SELECT * FROM `agent` ';
                                    $agent_FetchStatement = $pdo->prepare($sel);
                                    $agent_FetchStatement->execute();
                                    ?>
                                    <select name="agent_name" class="form-input-2">
                                        <option>Select Agent</option>
                                    <?php while ( $row = $agent_FetchStatement->fetch() ): ?>
                                        <option value="<?php echo $row-> agent_name ?>"><?php echo $row->agent_name ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Agent PIN</label>
                                    <input type="text" name="apin" class="form-input-2" placeholder="Agent PIN" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-input-2" placeholder="Withdraw Amount" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="withdraw" value="Withdraw" class="btn btn-sm btn-primary">
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
