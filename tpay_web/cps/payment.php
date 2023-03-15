<?php

      # PHP Mailer

      use PHPMailer\PHPMailer\PHPMailer;
      use PHPMailer\PHPMailer\SMTP;
      use PHPMailer\PHPMailer\Exception;
      
      require 'require/PHPMailer.php';
      require 'require/SMTP.php';
      require 'require/Exception.php';

if(isset($_GET['id'])){
  
$id=$_GET['id'];
// echo str_replace("ok" , "" , "$id");
//Removing "ok" from the string uid
$card_id = str_replace("ok" , "" , "$id");

//Created a template
$sql= "SELECT * FROM `ucards` WHERE `Card_id` = ? ";
//Calling the Database
$conn = mysqli_connect("localhost","root","","tpay");

//Select The reference money
$select_reference = $conn -> query("SELECT * FROM `reference` WHERE `id` = 0");
$row_reference = mysqli_fetch_array($select_reference);

if (!$row_reference) {
  echo "Payment Done Already!";
    exit();
}

else{
$business_pin = $row_reference['business_pin'];

$select_business = $conn -> query("SELECT * FROM `business` WHERE `business_pin` = '$business_pin'");
$row_business = mysqli_fetch_array($select_business);

if($row_business){

$business_balance = $row_business['balance'];
$business_tin = $row_business['business_tin'];
$business_name = $row_business['business_name'];

$fee = $row_reference['amount'];

//Create a prepared statement
$stmt = mysqli_stmt_init($conn);
//Prepare the prepared statement
if (!mysqli_stmt_prepare($stmt , $sql)) {
  echo "Failure";
}
else {
  //Bind the parameters
  mysqli_stmt_bind_param($stmt , "s" , $card_id);
  //run the parameters inside Database
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  while ($row = mysqli_fetch_assoc($result)) {
    // deducting from the card balance by fee = 100
    $current = $row['Balance'];
    $name = $row['Card_holder'];
    $card_email = $row['Email'];

    if(($current == 0) || ($current < 0)){
      echo "\nPayment failed! Not Enough balance!";
      exit();
    }
    else{
      $remain = $current - $fee;
      // Client Name
      echo "Paid Successful!\nThe Client: ". $name . " ";
      //printing out the current balance
      echo "\nBalance: ". number_format($current) . " ";
      //printing out the charging amount
      echo "\nPaid: ". number_format($fee) . " ";
      //printing out the remained balance
      echo "\nRemain: ". number_format($remain) . " ";
      // updating the current ballance
      $query1 = "UPDATE `ucards` SET `Balance` = '$remain' WHERE `Card_id` = '$card_id'";
      $query_deleteReference = $conn -> query("DELETE FROM `reference` WHERE `id` = 0");
      $result1 = mysqli_query($conn , $query1);

      // Updating business balance
      $business_newBalance = $business_balance + $fee;
      $query_business = $conn -> query("UPDATE `business` SET `balance` = '$business_newBalance' WHERE `business_pin` = '$business_pin'");

      # Sending Notification...
      $date_Sent = date("Y-m-d");
      $time_Sent = date("h:i");
      
      $message = 'You have been paid by <b>'. $name .'</b> , Amount: '.$fee. '<br/> Done: '. $date_Sent. ',' . $time_Sent;
      $mail_message = 'Payment successful to <b>'. $business_name .'</b> , Amount: '.$fee. ' Rwf <br/> <br/>Thank you for using Tap and Pay Service';
      
      $insert_notification = $conn->query("INSERT INTO `notification` (`recieverid`,`message`,`date_sent`,`status`,`target`) VALUES('$business_tin', '$message', '$date_Sent', 'unread', 'business')");
      
      # Recording the payment
      $insert_record = $conn -> query("INSERT INTO `records` (`Date`, `rID`, `Card_id`, `Card_holder`, `Amount_paid`, `Status`) VALUES('$date_Sent', '$business_tin', '$card_id', '$name' , '$fee', 'Paid')");

      if(empty($card_email)){

        echo "\nNo Email Registered\n";

      } else {

            $mail = new PHPMailer();
            $mail->isSMTP();

            $mail->SMTPDebug = 0;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = '587';
            $mail->Username = 'tap.pay.holder@gmail.com';
            $mail->Password = 'hphhfygslhgszibu';

            $mail->isHTML(true);
            $mail->Subject = 'PAYMENT CONFIRMATION';
            $mail->setFrom('tap.pay.holder@gmail.com', 'Tap and Pay');
            $mail->addAddress($card_email);

            $mail->Body = $mail_message;

            if ($mail->Send()) {
              echo "\nSent successful";
            } else {
              echo "\nCouldnot send\n" . $mail->ErrorInfo;
            }
          }

      exit();
                          }
                          // else {
                          //   echo "Payment Failed\nNot Enough Balance";
                          //   exit();
                          // }
      }
    }
  }
  else{
    echo "Unknown Business";
    exit();
  }
}

}
?>
