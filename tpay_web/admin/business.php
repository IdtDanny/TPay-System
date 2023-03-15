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

    $business_FetchQuery = 'SELECT * FROM `business`';
    $business_FetchStatement = $pdo->prepare($business_FetchQuery);
    $business_FetchStatement->execute();
    $business_Result = $business_FetchStatement->fetchAll();

        # Getting Admin Info. for update form...

        $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
        $adminFetchStatement = $pdo->prepare($adminFetchQuery);
        $adminFetchStatement->execute([
            'adminid' => $admin_ID
        ]);
        $adminResults = $adminFetchStatement->fetch();


?>

<?php

    if (isset($_POST['addbusiness'])) {
        $business_names = $_POST['business_names'];
        $business_tin = $_POST['business_tin'];
        $password= $business_tin;
        $hashed_Password=md5($password);
        $photo=$_FILES['photo']['name'];
        $date_Sent = date("Y-m-d");
        $business_pin = rand(1000,9999);
        $folder="../pictures/".basename($photo=$_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $folder) or die(error_reporting())){

            # Inserting Agent...

            $sql_insert="  INSERT INTO `business`(`Date`,`business_name`,`business_tin`,`business_password`,`business_pin`,`balance`,`status`,`photo`)
                                VALUES(:bdate, :businessname, :businesstin, :businesspass, :businesspin, :balance, :bstatus, :photo)
                ";

            $agent_InsertStatement = $pdo->prepare($sql_insert);
            $agent_InsertStatement->execute([
                'bdate'          =>  $date_Sent,
                'businessname'   =>  $business_names,
                'businesstin'    =>  $business_tin,
                'businesspass'   =>  $hashed_Password,
                'businesspin'    =>  $business_pin,
                'balance'        =>  '0',
                'bstatus'        =>  'Active',
                'photo'          =>  $photo
            ]);

            if ($sql_insert) {
                # code...
            header('Location: business.php?m=');
            executeSuccess('Business Registered, TIN:'.$business_tin);
          }
        }
        else{
            executeError('Something Went Wrong');
        }
    }
?>

<!-- Edit Business... -->

<?php

# Edit Business Operation...

if (isset($_POST['editBusiness'])) {

    $businessTin = $_POST['btin'];
    $business_newTin = $_POST['ntin'];
    $business_newName = $_POST['nname'];

    # Checking for businessTin ...

        $fetch_UserQuery='SELECT * FROM `business` WHERE `business_tin` = :pin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'pin'       => $businessTin
        ]);

        $business_Info = $fetch_UserStatement -> fetch();

        $businessCount = $fetch_UserStatement->rowCount();

      if ($businessCount > 0 ) {

        # Modifying Agent ...

        $business_UpdateQuery = ' UPDATE `business`
                                SET `business_name` = :business_NewName,
                                    `business_tin` = :business_NewTin
                                WHERE `business_tin` = :businesstin
        ';

        $business_UpdateStatement = $pdo->prepare($business_UpdateQuery);
        $business_UpdateStatement->execute([
            'business_NewName'      =>  $business_newName,
            'business_NewTin'       =>  $business_newTin,
            'businesstin'           =>  $businessTin
        ]);

        if ($business_UpdateQuery) {
          header('Location: business.php');
            }
        }
      else {
        executeError('Unknown Tin');
        header("Location: business.php?UnknownTin");
      }

}

?>

<!-- Withdraw... -->

<?php

# Withdraw Operation...

if (isset($_POST['withdraw'])) {

    $businessTin = $_POST['btin'];
    $confirmTin = $_POST['ctin'];
    $balance = $_POST['amount'];
    $withdrawNumber = $_POST['wnumber'];
    // $agentid = $_POST['agentid'];

    // header("location: business.php?".+$businessTin)

    // $aID = $agentid;
    # Checking for cardpin ...

    if($businessTin == $confirmTin){

        $fetch_UserQuery='SELECT * FROM `business` WHERE `business_tin` = :pin AND `business_pin` = :withdraw';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'pin'       => $businessTin,
            'withdraw'  => $withdrawNumber
        ]);

        $business_Info = $fetch_UserStatement -> fetch();

        $businessCount = $fetch_UserStatement->rowCount();

      if ($businessCount > 0 ) {

        # User Balance ...

        $current_BusinessBalance = $business_Info -> balance;
        
        if($current_BusinessBalance > 0){
            
        $new_BusinessBalance = $current_BusinessBalance - $balance;

        # Updating Business ...

        $business_UpdateQuery = ' UPDATE `business`
                                SET `balance` = :business_NewBalance
                                WHERE `business_tin` = :businesstin
        ';

        $business_UpdateStatement = $pdo->prepare($business_UpdateQuery);
        $business_UpdateStatement->execute([
            'business_NewBalance'  =>  $new_BusinessBalance,
            'businesstin'           =>  $businessTin
        ]);

        # Updating Admin ...

        $admin_balance = $adminResults -> Balance;
        $admin_name = $adminResults -> admin_name;
        $admin_newBalance = $admin_balance + $balance;

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

        $message = 'Withdraw Successful , Amount: '.$balance. '<br/> Done:'. $date_Sent. ',' . $time_Sent;
        sendNotification($businessTin, $message, 'business', $pdo);

        if ($business_UpdateQuery) {
          header('Location: business.php');
            }
        }
        else{
            executeError('Amount Exceed');
          header('Location: business.php?exceed');
        }
      }
      else {
        executeError('Unknown Pin');
      }
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

        executeSuccess('Withdraw Successful');

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Business Client</title>

    <?php
      include "includes/Header_dash_top.php";
     ?>
                <div class="logo-md">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark" >&nbsp; | &nbsp;<i class="icon_building"></i></span><span class="text-dark">&nbsp;Business Clients</span>
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
                    <i class="icon_building text-dark"></i>
                    Business Clients
                </h1>
                <br>
                <div class="btn-group">
                    <button class="btn-sm btn-primary" id="modal-trigger"> <i class="icon_plus_alt2"></i> &nbsp; Register</button>
                    <button class="btn-sm btn-secondary" id="modal-trigger2"><i class="icon_pencil-edit_alt"></i> &nbsp; Edit</button>
                    <button class="btn-sm btn-dark" id="modal-trigger3"><i class="icon_wallet"></i> &nbsp; Withdraw</button>
                </div>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Business Names</th>
                            <th class="text-center">Business TIN</th>
                            <th class="text-center">Withdraw_Pin</th>
                            <th class="text-center">Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr>
                        <td>hellp</td>
                        <td>hellp</td>
                    </tr> -->
                        <?php
                            foreach($business_Result as $business){
                            ?>
                            <tr>
                                <td><img  src="../pictures/<?php echo $business -> photo ?>" style="width: 40px;height: 40px;object-fit: cover;border-radius: 100%;"/></td>
                                <td><?php echo $business -> business_name ?></td>
                                <td class="text-center"><?php echo $business -> business_tin ?></td>
                                <td class="text-center"><?php echo $business -> business_pin ?></td>
                                <td class="text-center"><?php echo number_format($business -> balance) ?></td>
                                <td class="text-center"><a href="business_delete.php?bid=<?php echo $business -> bID ?>"><i class="icon_trash" style="color: #A74A47"></i></a></td>
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
                            <p class="display-1 text-primary"><i class="icon_plus_alt2"></i> &nbsp;Register Business</p>
                            <i class="icon_close" id="close-modal"></i>
                        </div>
                        <div class="modal-body">
                            <form method="post" enctype="multipart/form-data">
                                <p class="muted">* The Default Password Will be like (TIN)</p>
                                <div class="form-group">
                                    <label>Business Name</label>
                                    <input type="text" name="business_names" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Business TIN</label>
                                    <input type="text" name="business_tin" class="form-input-2" required>
                                </div>
                                <div class="form-group">
                                    <label>Photo</label>
                                    <input type="file" name="photo" class="form-file-2" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="addbusiness" value="Register Business" class="btn btn-sm btn-primary">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Second Modal -->

                <div class="modal-content2">
                    <div class="modal2">
                        <div class="modal-head">
                            <p class="display-1 text-primary"><i class="icon_pencil-edit_alt"></i> &nbsp; Edit Business</p>
                            <i class="icon_close" id="close-modal2"></i>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                            <div class="form-group">
                                    <label>Business TIN</label>
                                    <?php
                                    $sel = 'SELECT * FROM `business` ';
                                    $business_FetchStatement = $pdo->prepare($sel);
                                    $business_FetchStatement->execute();
                                    ?>
                                    <select name="btin" class="form-input-2">
                                        <option>Choose TIN</option>
                                    <?php while ( $row = $business_FetchStatement->fetch() ): ?>
                                        <option value="<?php echo $row->business_tin ?>"><?php echo $row->business_tin ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>New TIN</label>
                                    <input type="text" name="ntin" class="form-input-2" placeholder="New TIN" required>
                                </div>
                                <div class="form-group">
                                    <label>New Business Name</label>
                                    <input type="text" name="nname" class="form-input-2" placeholder="New Business Name" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="editBusiness" value="Edit" class="btn btn-sm btn-primary">
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
                                    <label>Business TIN</label>
                                    <?php
                                    $sel = 'SELECT * FROM `business` ';
                                    $business_FetchStatement = $pdo->prepare($sel);
                                    $business_FetchStatement->execute();
                                    ?>
                                    <select name="btin" class="form-input-2">
                                        <option>Choose TIN</option>
                                    <?php while ( $row = $business_FetchStatement->fetch() ): ?>
                                        <option value="<?php echo $row->business_tin ?>"><?php echo $row->business_tin ?></option>
                                    <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Confirm TIN</label>
                                    <input type="text" name="ctin" class="form-input-2" placeholder="Confirm TIN" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-input-2" placeholder="Withdraw Amount" required>
                                </div>
                                <div class="form-group">
                                    <label>Withdraw Pin</label>
                                    <input type="text" name="wnumber" class="form-input-2" placeholder="Enter PIN" required>
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
