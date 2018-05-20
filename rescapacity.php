<?php
  $servername = "127.0.0.1:3306";
  $username = "root";
  $password = "1qaz2wsx";
  $dbname = "restaurant_booking";
  $datasent = json_decode($_POST["datasent"],true);
  $resid = $datasent["resid"];
  $npeople = $datasent["npeople"];
  $bookdate = $datasent["bookdate"];
  $booktime = $datasent["booktime"];
  $result = "";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    exit;
  }

  $resquery = "SELECT * FROM (SELECT * FROM restaurant WHERE rid='" .$resid. "') AS r
  LEFT JOIN (SELECT rid, SUM(quantity) AS orderedseats FROM booking WHERE btime ='". $bookdate." ".$booktime."' GROUP BY rid) AS t
  ON r.rid = t.rid WHERE (orderedseats IS NULL AND capacity >=".$npeople.
  ") OR (orderedseats IS NOT NULL AND capacity-orderedseats >=".$npeople.")";
  $resresult = $conn->query($resquery);
  if ($resresult->num_rows > 0) {
    $result = 0;
  } else {
    $result = 1;
  }
  $resresult->free();

  echo $result;
?>
