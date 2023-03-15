<?php
if(isset($_GET['id'])){
$id=$_GET['id'];
// echo str_replace("ok" , "" , "$id");
//Removing "ok" from the string uid
$rfid = str_replace("ok" , "" , "$id");

//Created a template
$sql= "SELECT cID, name, balance, rfid
              FROM client
              where rfid = ? ";
//Calling the Database
$conn= mysqli_connect("localhost","root","","obedSystem");
//Create a prepared statement
$stmt = mysqli_stmt_init($conn);
//Prepare the prepared statement
if (!mysqli_stmt_prepare($stmt , $sql)) {
  echo "Failure";
}
else {
  //Bind the parameters
  mysqli_stmt_bind_param($stmt , "s" , $rfid);
  //run the parameters inside Database
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  while ($row = mysqli_fetch_assoc($result)) {
    // deducting from the card balance by fee = 100
    $fee = 100;
    $current = $row['balance'];
    if (!($current <= 0)) {
      $remain = $current - $fee;
      //printing out the current balance
      echo "The current Balance: ". $current . " ";
      //printing out the remained balance
      echo "The Remain Balance: ". $remain . " ";
      // updating the current ballance
      $query1 = "UPDATE client SET balance = '$remain' WHERE rfid = '$rfid'";
      $result1 = mysqli_query($conn , $query1);
      exit();
                          }
                          else {
                            echo "Not Enough Balance";
                            exit();
                          }
      }
    }
}

?>
