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


    # Calculating Each Number of Users, Cards...
    $sql_agent = 'SELECT * FROM agent';
    $sql_ucards = 'SELECT * FROM ucards';
    $sql_business = 'SELECT * FROM business';
    // $usedCardsSql = 'SELECT * FROM `ucards` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_ucards = $pdo->prepare($sql_ucards);
    $statement_ucards -> execute();

    $statement_business = $pdo->prepare($sql_business);
    $statement_business -> execute();

    // $stmt = $pdo->prepare($usedCardsSql);
    // $stmt2 = $pdo->prepare($usedCardsSql);
    // $stmt->execute([
        // 'approve' => 'Approved'
    // ]);
    // $stmt2->execute([
        // 'approve' => 'Approved'
    // ]);

    # Getting The number of Agents & Cards...
    $agentsCount = $statement->rowCount();
    $registered_ucards = $statement_ucards->rowCount();
    $registered_business = $statement_business -> rowCount();


    # Getting Admin Info. for update form...

    $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
    $adminFetchStatement = $pdo->prepare($adminFetchQuery);
    $adminFetchStatement->execute([
        'adminid' => $admin_ID
    ]);
    $adminResults = $adminFetchStatement->fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>

                <?php
                  include "includes/Header_dash_top.php";
                 ?>


                <div class="logo-md">
                    <a href="index.php" style="text-decoration: none; /* color: #37adf6; */ ">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_adjust-vert"></i></span><span class="text-dark">&nbsp;Admin</span>
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
            <div class="jumbotron bg-white shadow-sm" style="padding: 50px 50px; margin: 30px;">
                <h1 class="logo-md text-dark">
                    <i class="arrow_carrot-2right text-dark"></i>
                    <em class="text-top-dash">Welcome</em>&nbsp;&nbsp; <?php echo $adminResults -> admin_name ?>
                </h1>
                <br>
                <div class="info-holder" style="padding: 5px; margin-left: 50px;">
                    <div class="info-desc" style="padding: 5px;">
                        <h2>
                            <span>
                                <i class="icon_contacts_alt"></i>
                            </span>
                            &nbsp;Agents: &nbsp; <em class="text-primary"><?php echo number_format($agentsCount)?></em>
                        </h2>
                    </div>
                    <div class="info-desc" style="padding: 5px;">
                        <h2>
                            <span>
                                <i class="icon_creditcard text-danger"></i>
                            </span>
                            &nbsp;U-Cards: &nbsp; <em class="text-primary"><?php echo number_format($registered_ucards)?></em>
                        </h2>
                    </div>
                    <div class="info-desc" style="padding: 5px;">
                        <h2>
                            <span>
                                <i class="icon_building "></i>
                            </span>
                            &nbsp;Business: &nbsp; <em class="text-primary"><?php echo number_format($registered_business)?></em>
                        </h2>
                    </div>
                    <div class="info-desc" style="padding: 5px;">
                        <h2>
                            <span>
                                <i class="icon_wallet "></i>
                            </span>
                            &nbsp;Balance: &nbsp; <em class="text-primary"><?php echo number_format($adminResults -> Balance). ' Rwf'?></em>
                        </h2>
                    </div>
                </div>
                <br>
                <button class="btn btn-rounded btn-primary" id="form-trigger">Edit Account</button>
            </div>
            <div class="jumbotron bg-white shadow-sm form-modal">
                <h2 class="logo-md text-center">
                    <i class="icon_adjust-vert text-dark"></i> Edit Account
                </h2>
                <br>
                <div class="form-class container">
                    <form method="post">
                        <div class="form-group">
                            <label>Names</label>
                            <input type="text" name="admin-name" value="<?php echo $adminResults->admin_name ?>" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="admin-username" value="<?php echo $adminResults->admin_username ?>" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="old-password" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password"  name="new-password" class="form-input-2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm-password" class="form-input-2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="editinfo" value="Edit Information" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="../assets/js/main.js"></script>
</html>

<?php
# Updating Admin Information...

if (isset($_POST['editinfo'])) {
    $new_Admin_Name = $_POST['admin-name'];
    $new_Admin_Username = $_POST['admin-username'];
    $admin_Old_Password = $_POST['old-password'];
    $admin_New_Password = $_POST['new-password'];
    $admin_Confirm_password = $_POST['confirm-password'];

    # Checking for Password fields(if they are empty, It will only update the username or name only)...

    if (empty($admin_Old_Password)) {

        # Updating Query...

        $admin_Update_Query = 'UPDATE `admin`
                                SET `admin_name` = :adminname,
                                    `admin_username` = :adminusername
                                WHERE `admin_ID` = :adminid
        ';

        $admin_Update_stmt = $pdo->prepare($admin_Update_Query);
        $admin_Update_stmt->execute([
            'adminname'     =>  $new_Admin_Name,
            'adminusername' =>  $new_Admin_Username,
            'adminid'       =>  $admin_ID
        ]);
        executeSuccess('Username Edited Successfully');
    }
    else {

        # Checking if the old password match...

        $hashedpass = md5($admin_Old_Password);
        
        // $hashedpass = $admin_Old_Password;

        if ($adminResults->admin_password == $hashedpass || $adminResults->admin_password == $admin_Old_Password ) {

            if ($admin_New_Password == $admin_Confirm_password) {

                # Update Query Including Passwords...

                $admin_Update_Query = 'UPDATE `admin`
                                        SET `admin_name` = :adminname,
                                            `admin_username` = :adminusername,
                                            `admin_password` = :adminpassword
                                        WHERE `admin_ID` = :adminid
                ';

                $admin_Update_stmt = $pdo->prepare($admin_Update_Query);
                $admin_Update_stmt->execute([
                    'adminname'     =>  $new_Admin_Name,
                    'adminusername' =>  $new_Admin_Username,
                    'adminpassword' =>  md5($admin_New_Password),
                    'adminid'       =>  $admin_ID
                ]);
                executeSuccess('Data Edited Successfully');
            }
            else{
                executeError('New Password Does not Match');
            }

        }
        else{
            executeError('Current Password is Incorrect');
        }

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
?>
