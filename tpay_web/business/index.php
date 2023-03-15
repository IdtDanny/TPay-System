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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business | Dashboard</title>

    <?php
        include "includes/header_top.php";
    ?>

                <div class="logo-md">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_contacts_alt"></i></span>&nbsp;<span class="text-dark">Business</span>
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

            <!-- Profile -->

            <div class="jumbotron bg-white shadow-sm mr-1">
                <div class="badge">
                    <div class="badge-img-head">
                        <img src="../pictures/<?php echo $business_photo ?>" alt="Profile Picture">
                    </div>
                    <br>
                    <div class="badge-body">
                        <label class="text-secondary display-1" style="font-weight: bold;">Business:</label>&nbsp;<?php echo $businessResults -> business_name ?><br>
                        <label class="text-secondary display-1" style="font-weight: bold;">TIN:</label>&nbsp;<?php echo $businessResults -> business_tin ?><br>
                        <label class="text-secondary display-1" style="font-weight: bold;">Balance:</label>&nbsp;<?php echo number_format($businessResults -> balance) . " Rwf" ?><br>
                    </div>
                </div>
                <br>
                <button class="btn-sm btn-info" id="form-trigger"> <i class="icon_pencil-edit"></i> &nbsp; Edit Account</button>
            </div>

            <!-- Greetings Part -->

            <div class="jumbotron bg-white shadow-sm mr-1">
                <h1 class="logo-md text-secondary">
                    <i class="arrow_carrot-2right text-dark"></i>
                    <em class="text-dark">Welcome</em> <?php echo $business_names ?>
                </h1>
            </div>

            <!-- Form modal -->

            <div class="jumbotron bg-white shadow-sm form-modal">
                <h2 class="logo-md text-center">
                    <i class="icon_pencil-edit text-dark"></i> Edit Account
                </h2>
                <br>
                <div class="form-class container">
                    <form method="post">
                        <div class="form-group">
                            <label>Names</label>
                            <input type="text" name="business-name" value="<?php echo $businessResults->business_name ?>" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>TIN</label>
                            <input type="text" name="business-tin" value="<?php echo $businessResults->business_tin ?>" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="business-old-password" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="business-new-password" class="form-input-2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="business-confirm-password" class="form-input-2"pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
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
# Updating business Information...

if (isset($_POST['editinfo'])) {
    $new_Business_Name = $_POST['business-name'];
    $new_Business_Tin = $_POST['business-tin'];
    $business_Old_Password = $_POST['business-old-password'];
    $business_New_Password = $_POST['business-new-password'];
    $business_Confirm_password = $_POST['business-confirm-password'];

    # Checking for Password fields(if they are empty, It will only update the tin or name only)...

    if (empty($business_Old_Password)) {

        # Updating Query...

        $business_Update_Query = 'UPDATE `business`
                                SET `business_name`        = :businessname,
                                    `business_tin`    = :businesstin
                                WHERE `bID` = :businessid
        ';

        $business_Update_stmt = $pdo->prepare($business_Update_Query);
        $business_Update_stmt->execute([
            'businessname'     =>  $new_Business_Name,
            'businesstin' =>  $new_Business_Tin,
            'businessid'       =>  $bID
        ]);
        executeSuccess('TIN Edited Successfully');
    }
    else {

        # Checking if the old password match...

        $fetch_PassQuery='SELECT * FROM `business` WHERE `bID` = :businessid';
        $fetch_PassStatement = $pdo->prepare($fetch_PassQuery);
        $fetch_PassStatement->execute([
            'businessid'   => $bID
        ]);

        $business_Info = $fetch_PassStatement -> fetch();

        $hashedpass = md5($business_Old_Password);

        if ($business_Info->business_password == $hashedpass || $business_Info->business_password == $business_Old_Password) {

            if ($business_New_Password == $business_Confirm_password) {

                # Update Query Including Passwords...

                $business_Update_Query = 'UPDATE `business`
                                        SET `business_name` = :businessname,
                                            `business_tin` = :businesstin,
                                            `business_password` = :businesspassword
                                        WHERE `bID` = :businessid
                ';

                $business_Update_stmt = $pdo->prepare($business_Update_Query);
                $business_Update_stmt->execute([
                    'businessname'     =>  $new_Business_Name,
                    'businesstin' =>  $new_Business_Tin,
                    'businesspassword' =>  md5($business_New_Password),
                    'businessid'       =>  $bID
                ]);
                executeSuccess('Data Edited Successfully');

                if($business_Update_Query){
                    // header("Location: index.php?msg");
                }
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
