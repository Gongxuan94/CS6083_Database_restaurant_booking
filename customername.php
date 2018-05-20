<?php
  $servername = "127.0.0.1:3306";
  $username = "root";
  $password = "1qaz2wsx";
  $dbname = "restaurant_booking";
  $nameErr = "";
  $cname = $_POST["q"];
  $errnum = 0;
  $result = "";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    exit;
  }

// test the user's input
  if (empty($cname)) {
    $nameErr = "Name is required";
    $errnum = 1;
  } else {
    // is the customer name in the db
    $cusids = "SELECT cid FROM customer where cname = '". $cname ."'";
    $cusresult = $conn->query($cusids);
    if ($cusresult->num_rows <= 0) {
      $nameErr = "Invalid name";
      $errnum = 1;
    }
    $cusresult->free();
  }

  $result = $nameErr. "," .$errnum;
  echo $result;
?>
