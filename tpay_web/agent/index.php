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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent | Dashboard</title>

    <?php
        include "includes/header_top.php";
    ?>

                <div class="logo-md">
                    <a href="index.php" style="text-decoration: none;">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_contacts_alt"></i></span>&nbsp;<span class="text-dark">Agent</span>
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
                        <img src="../pictures/<?php echo $agent_photo ?>" alt="Profile Picture">
                    </div>
                    <br>
                    <div class="badge-body">
                        <label class="text-secondary display-1" style="font-weight: bold;">Names:</label>&nbsp;<?php echo $agentResults -> agent_name ?><br>
                        <label class="text-secondary display-1" style="font-weight: bold;">Username:</label>&nbsp;<?php echo $agentResults -> agent_username ?><br>
                        <label class="text-secondary display-1" style="font-weight: bold;">Balance:</label>&nbsp;<?php echo $agentResults -> agent_balance ?><br>
                    </div>
                </div>
                <br>
                <button class="btn-sm btn-info" id="form-trigger"> <i class="icon_pencil-edit"></i> &nbsp; Edit Account</button>
            </div>

            <!-- Greetings Part -->

            <div class="jumbotron bg-white shadow-sm mr-1">
                <h1 class="logo-md text-secondary">
                    <i class="arrow_carrot-2right text-dark"></i>
                    <em class="text-dark">Welcome</em> <?php echo $agent_name ?>
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
                            <input type="text" name="agent-name" value="<?php echo $agentResults->agent_name ?>" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="agent-username" value="<?php echo $agentResults->agent_username ?>" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="agent-old-password" class="form-input-2">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="agent-new-password" class="form-input-2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="agent-confirm-password" class="form-input-2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
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
# Updating Agent Information...

if (isset($_POST['editinfo'])) {
    $new_Agent_Name = $_POST['agent-name'];
    $new_Agent_Username = $_POST['agent-username'];
    $agent_Old_Password = $_POST['agent-old-password'];
    $agent_New_Password = $_POST['agent-new-password'];
    $agent_Confirm_password = $_POST['agent-confirm-password'];

    # Checking for Password fields(if they are empty, It will only update the username or name only)...

    if (empty($agent_Old_Password)) {

        # Updating Query...

        $agent_Update_Query = 'UPDATE `agent`
                                SET `agent_name`        = :agentname,
                                    `agent_username`    = :agentusername
                                WHERE `aID` = :agentid
        ';

        $agent_Update_stmt = $pdo->prepare($agent_Update_Query);
        $agent_Update_stmt->execute([
            'agentname'     =>  $new_Agent_Name,
            'agentusername' =>  $new_Agent_Username,
            'agentid'       =>  $agent_ID
        ]);
        executeSuccess('Username Edited Successfully');
    }
    else {

        # Checking if the old password match...

        $fetch_PassQuery='SELECT * FROM `agent` WHERE `aID` = :agentid';
        $fetch_PassStatement = $pdo->prepare($fetch_PassQuery);
        $fetch_PassStatement->execute([
            'agentid'   => $agent_ID
        ]);

        $agent_Info = $fetch_PassStatement -> fetch();

        $hashedpass = md5($agent_Old_Password);

        if ($agent_Info->agent_password == $hashedpass || $agent_Info->agent_password == $agent_Old_Password) {

            if ($agent_New_Password == $agent_Confirm_password) {

                # Update Query Including Passwords...

                $agent_Update_Query = 'UPDATE `agent`
                                        SET `agent_name` = :agentname,
                                            `agent_username` = :agentusername,
                                            `agent_password` = :agentpassword
                                        WHERE `aID` = :agentid
                ';

                $agent_Update_stmt = $pdo->prepare($agent_Update_Query);
                $agent_Update_stmt->execute([
                    'agentname'     =>  $new_Agent_Name,
                    'agentusername' =>  $new_Agent_Username,
                    'agentpassword' =>  md5($agent_New_Password),
                    'agentid'       =>  $agent_ID
                ]);
                executeSuccess('Data Edited Successfully');

                if($agent_Update_Query){
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
