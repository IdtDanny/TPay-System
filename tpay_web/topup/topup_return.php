<?php
if(isset($_GET['id'])){
$id = $_GET['id'];
// echo str_replace("ok" , "" , "$id");
//Removing "ok" from the string uid
$card_id = str_replace("ok" , "" , "$id");

//Created a template
$sql= "SELECT * FROM `ucards` WHERE `Card_id` = ? ";

//Calling the Database
$conn = mysqli_connect("localhost","root","","tpay");

//Select The reference money
$select_reference = $conn -> query("SELECT * FROM `topup_ref` WHERE `id` = 1") or die(mysqli_error($conn));
$row_reference = mysqli_fetch_array($select_reference);
$topup_fee = $row_reference['Amount'];
$agent_pin = $row_reference['agent_pin'];

//Select The Agent
$select_agent = $conn -> query("SELECT * FROM `agent` WHERE `agent_pin` = '$agent_pin'");
$row_agent = mysqli_fetch_array($select_agent);

if($row_agent){
  $agent_balance = $row_agent['agent_balance'];

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
    // Topping the client balance
    $current = $row['Balance'];
    $name = $row['Card_holder'];
    if ($agent_balance >= $topup_fee) {
      $new_clientBalance = $current + $topup_fee;
      // Client Name
      echo "Client: ". $name . " ";
      //printing out the current balance
      echo "\nBalance : ". number_format($current) . " ";
      //printing out the charging amount
      echo "\nRecharge : ". number_format($topup_fee) . " ";
      //printing out the remained balance
      echo "\nNew Balance : ". number_format($new_clientBalance) . " ";
      // updating the current ballance
      $query1 = "UPDATE `ucards` SET `Balance` = '$new_clientBalance' WHERE `Card_id` = '$card_id'";
      $result1 = mysqli_query($conn , $query1);

      // Modifying the agent balance
      $agent_newBalance = $agent_balance - $topup_fee;
      $query_agent = "UPDATE `agent` SET `agent_balance` = '$agent_newBalance' WHERE `agent_pin` = '$agent_pin'";
      $result_agent = mysqli_query($conn , $query_agent);

      # Sending Notification...
      $date_Sent = date("Y-m-d");
      $time_Sent = date("h:m");
      
      $message = 'Card Top-up to <b>'. $name .'</b> Successful , Amount: '.$topup_fee. '<br/> Done:'. $date_Sent. ',' . $time_Sent;
      
      $insert_notification = $conn->query("INSERT INTO `notification` (`recieverid`,`message`,`date_sent`,`status`,`target`) VALUES('$agent_pin', '$message', '$date_Sent', 'unread', 'agent')");      

      exit();
                          }
                          else {
                            echo "You do not have enough balance to topup card?";
                            exit();
                          }
      }
    }
  }
  else{
    echo "Unknown Pin?";
  }
}

?>
