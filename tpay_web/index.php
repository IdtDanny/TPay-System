<?php

    session_start();

    # Including The Connection...
    require_once 'config/connection.php';

    # Variable Declaration...
    $sessionToken='';
    $error_message='';

    # Getting Data From Form...

    if (isset($_POST['signin'])) {

        # Form Variables...
        $username = $_POST['username'];
        $password = $_POST['password'];

        # Getting The Hashed Password...
        $hashedPassword = md5($password);

        $sessionToken = addslashes($sessionToken);

        # Checking for User Existence...
        $query = 'SELECT * FROM `admin` WHERE `admin_username` = :username';

        # PDO Prepare & Execution of the query...
        $statement = $pdo->prepare($query);
        $statement->execute([
            'username' => $username
        ]);
        $usersCount = $statement->rowCount();

        if ($usersCount > 0) {
            $admin = $statement->fetch();
            if ($username == $admin->admin_username && ($password == $admin->admin_password || $hashedPassword == $admin->admin_password)) {
                $page = 'admin/#dashboard';
                $_SESSION['sessionToken'] = $admin;
                header('location:'.$page);
            }
            else {
                $error_message="* Incorrect Username or Password";
            }
        }
        else if($usersCount == 0) {

            # Checking for Agent Account Existence...
            $query_2 = 'SELECT * FROM `agent` WHERE `agent_username` = :username';

            # PDO Prepare & Execution of the query...
            $statement = $pdo->prepare($query_2);
            $statement->execute([
                'username' => $username
            ]);
            $agentCount = $statement->rowCount();

            if ($agentCount > 0) {
                $agent = $statement->fetch();
                if ($username == $agent->agent_username && ($password == $agent->agent_password || $hashedPassword == $agent->agent_password)) {
                    $page = 'agent/#dashboard';
                    $_SESSION['sessionToken'] = $agent;
                    header('location:'.$page);
                }
                else {

                    $error_message="* Incorrect Username or Password";
                }

            }
            // else {
            //        $error_message="* Incorrect Username or Password";
            // }

        // }
        else {
            # Checking for Business Account Existence...
            $query_3 = 'SELECT * FROM `business` WHERE `business_tin` = :businesstin';
            
            $businessname = $username; 

            # PDO Prepare & Execution of the query...
            $statement = $pdo->prepare($query_3);
            $statement->execute([
                'businesstin' => $businessname
            ]);
            $businessCount = $statement->rowCount();
        
            if ($businessCount > 0) {
                $business = $statement->fetch();
                if ($username == $business -> business_tin && ($password == $business -> business_password || $hashedPassword == $business -> business_password)) {
                    $page = 'business/#dashboard';
                    $_SESSION['sessionToken'] = $business;
                    header('location:'.$page);
                }
                else {
        
                    $error_message="* Incorrect TIN or Password";
                }
        
            }
            else {
                $error_message="* Incorrect TIN or Password";
            }
        }
        
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap & Pay</title>

    <!-- Icon Header -->
    <link rel="shortcut icon" type="image/png" href="assets/img/Card_Header.png">

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/css/style.min.css">

    <!-- Icons CSS -->
    <link rel="stylesheet" href="assets/icons/style.css">

</head>
<body>
    <div class="holder">
        <div class="container">
            <nav>
                <div class="logo">
                    Tap & Pay
                </div>
                <div class="nav-btn">
                    <a href="#explore" class="btn btn-light btn-rounded">Sign in</a>
                </div>
            </nav>
            <div class="content">
                <div class="section1">
                    <h1>Welcome To Tap And Pay System</h1>
                    <p> &raquo; <em>Make The Place of Convenient, Convenience for All</em></p>
                    <br>
                    <a href="#explore"><button class="btn btn-outline-light btn-block">Explore &nbsp;  <i class="animatable arrow_carrot-2down"></i></button></a>
                </div>
                <div class="section2">
                    <div class="section-img">
                        <img src="assets/img/image.png" alt="tap&pay image">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="holder-2">
        <div class="container-2" id="explore">
            <div class="explore-section">
                <div class="item">
                    <div class="well-sm bg-primary ">
                        <i class="icon_clock_alt medium-text text-center text-light"></i>
                        </div>
                        <h1>Time Saving</h1>
                </div>
                <div class="item mt-1">
                    <div class="well-sm bg-primary ">
                        <i class="icon_creditcard medium-text text-center text-light"></i>
                    </div>
                    <h1>Pay By Tapping</h1>
                </div>
                <div class="item mt-1">
                    <div class="well-sm bg-primary ">
                        <i class="icon_lock_alt medium-text text-center text-light"></i>
                        </div>
                        <h1>Secured</h1>
                </div>
                <!--  -->
            </div>
            <style type="text/css">
                .bg-t{
                    background-color: #A74A47;
                }
            </style>
            <div class="sign-in-section">
                <div class="well-sm bg-primary mt-3">
                    <i class="icon_id medium-text text-center text-light"></i>
                </div>
                <h1 class="text-primary">SIGN IN</h1>
                <div class="form-class shadow-sm" style="padding: 50px;">
                    <form method="post">
                         <div>
                            <i style="font-size:14px;color:#d11a2c;text-align:center;" class="text-danger"><?php echo $error_message ?></i>
                        </div>
                        <br>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-input-1">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-input-1">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="signin" value="Sign in" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="assets/js/main.js"></script>
</html>
