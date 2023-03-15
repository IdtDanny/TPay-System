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

    $client_FetchQuery = 'SELECT * FROM `ucards`';
    $client_FetchStatement = $pdo->prepare($client_FetchQuery);
    $client_FetchStatement->execute();
    $client_Result = $client_FetchStatement->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Clients</title>

    <!-- Icon Header -->
    <link rel="shortcut icon" type="image/png" href="../assets/img/Card_Header.png">

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/css/style.min.css">

    <!-- Icons CSS -->
    <link rel="stylesheet" href="../assets/icons/style.css">
</head>
<body class="bg-darken-light">

            <?php
              include "includes/Header_dash_top.php";
             ?>

                <div class="logo-md">
                    <a href="index.php" style="text-decoration: none; /* color: #37adf6; */ ">Dashboard</a><span class="text-dark">&nbsp; | &nbsp;<i class="icon_group"></i></span><span class="text-dark">&nbsp;Clients</span>
                </div>

                <?php
                  include "includes/Header_dash_bot.php";
                 ?>

    <!-- Dashboard Body!!! -->
    <br>
    <div class="container" >
        <div class="row">
            <div class="jumbotron jumbotron-lg bg-white shadow-sm" style="padding: 30px;">
                <h1 class="logo-md">
                    <i class="icon_creditcard text-dark"></i>
                    Client Cards
                </h1>
                <br>
                <!-- <button class="btn-sm btn-primary" id="modal-trigger">Add New Client</button>
                <br><br> -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Card id</th>
                            <th>Names</th>
                            <th>Balance</th>
                            <th>By Agent</th>
                            <th>Card Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- <tr>
                        <td>hellp</td>
                        <td>hellp</td>
                    </tr> -->
                        <?php
                            foreach($client_Result as $client){
                            ?>
                            <tr>
                                <td><?php echo $client->Date_Created ?></td>
                                <td><?php echo $client->Card_id ?></td>
                                <td><?php echo $client->Card_holder ?></td>
                                <td><?php echo number_format($client->Balance) ?></td>
                                <td><?php echo $client->By_agent ?></td>
                                <td><?php echo $client->Status ?></td>
                                <td><a href="client_delete.php?No=<?php echo $client->No ?>"><i class="icon_trash"style="color: #A74A47;"></i></a></td>
                            </tr>
                            <?php
                            }
                        ?>
                    </tbody>
                </table>
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
                                    <input type="text" name="card_id" class="form-input-2">
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="addclient" value="Add New Client" class="btn btn-sm btn-primary">
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

    # Adding New Card

    if (isset($_POST['addclient'])) {
        $card_id = $_POST['card_id'];
        $status = 'Usable';

        # Checking if the Card is valid...

        $get_CardQuery = 'SELECT * FROM `recharging_card` WHERE `pin` = :card_id';
        $card_Statement = $pdo->prepare($get_CardQuery);
        $card_Statement->execute([
            'card_id'   =>  $card_id
        ]);

        $cardCount = $card_Statement->rowCount();

        if ($cardCount > 0) {
            # Fetching Data...
            $card = $card_Statement->fetch();
            $balance = $card->amount;
            // var_dump($card);
            // die();

            # Inserting to the Clients Table...
            $client_InsertQuery = ' INSERT INTO client(`card_id`, `balance`, `status`)
                                    VALUES(:card_id, :balance, :card_status)
            ';
            $client_InsertStatement = $pdo->prepare($client_InsertQuery);
            $client_InsertStatement->execute([
                'card_id'       =>  $card_id,
                'balance'       =>  $balance,
                'card_status'   =>  $status
            ]);

            # Updating From The Recharging Card Table ...
            $client_UpdateQuery = 'UPDATE `recharging_card` SET `Status` = :newstatus WHERE `pin` = :pin';
            $client_UpdateStatement = $pdo->prepare($client_UpdateQuery);
            $client_UpdateStatement->execute([
                'newstatus'     =>  'Used',
                'pin'           =>  $card_id
            ]);
        }
        else{
            echo "no card shown";
        }

    }

?>
