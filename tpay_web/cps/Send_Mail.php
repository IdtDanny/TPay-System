<?php
    # PHP Mailer Sender
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\composer\vendor\autoload.php';

  $to_id = 'idtdanny@gmail.com';
  $subject =  'PAYMENT CONFIRMATION';

  include_once "PHPMailer\PHPMailer\src\PHPMailer.php";
  include_once "PHPMailer\PHPMailer\src\Exception.php";

  $mail = new PHPMailer(true);

  $mail->isSMTP();
                  // set mailer to use SMTP
  $mail->Host = 'smtp.gmail.com';                           // specify main and backup smtp servers
  $mail->SMTPAuth = true;                                   //enable smtp auuthentication
  $mail->Username = 'tap.pay.holder@gmail.com';                       //smtp username
  $mail->Password = 'ydxgyjesvahyjhik';                         //smtp password
  $mail->SMTPSecure = 'tls';                                //enable tls encryption, `ssl   :`
  $mail->Port = 587;

  $mail->setFrom('tap.pay.holder@gmail.com', 'TAP AND PAY SYSTEM');
  $mail->addAddress($to_id);

  $mail->IsHTML(true);

  $mail->Subject = $subject;
  $mail->Body = $message;


  // if (!$mail->isSMTP()) {
  //   $error = 'Internet Connection Problem';
  //   echo "<div class='text-white p-3 badge-danger' style='border-radius: 5px;'> $error
  //         <a href='submit_page.php' style='font-size: 19px;font-weight: bold;' class='text-white text-center' title='Return Back'>&nbsp&times;</a> </div>";
  // }

    if(!$mail->send()){
      // $error = 'Message was not Sent';
        $error = "Mailer Error: " .$mail->ErrorInfo;
        echo "<div class='text-danger p-3 blue' style='border-radius: 5px;'> $error </div>";
      }else
          {
            $msg = 'Report Submitted';
            // header("Location: ../agent/submit_page.php?msg=$msg");
            // echo " <div class='text-white p-3 blue' style='border-radius: 5px;'> Message Sent
            //        &nbsp;<span class='fs1' aria-hidden='true' style='font-size: 19px; font-weight: bold;' data-icon='&#x4e;' ></span> </div>";
          }

?>