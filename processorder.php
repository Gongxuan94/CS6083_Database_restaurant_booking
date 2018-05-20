<!DOCTYPE html>
<?php
  // create short variable names
  $cname = $_POST['cname']; // this is the cid of the customer
  $rname = $_POST['rname']; // this is the cid of the restaurant
  $npeople = $_POST['npeople'];
  $bookdate = $_POST['datebooked'];
  $booktime = $_POST['timebooked'];
?>
<html>
<head>
<title>Restaurant Booking System</title>
<link rel="stylesheet" href="spectre.css">
<style>
.center-in-up{
            width: 90%;
            margin: 0 auto;
            padding: 1.5rem 2rem;
            top: 10%;
            left: 50%;
            align: center;
}
</style>
<script type="text/javascript" src="jquery-3.2.1.js"></script>
<script type="text/javascript">
  function goHomePage() {
    var currenturl = document.location.toString();
    var url = currenturl.substring(0,currenturl.lastIndexOf("/"))+"/homepage.php";
    window.location.href = url;
  }
</script>
</head>
<body>
<div class= "center-in-up"><h1 align= "center">Restaurant Booking Results</h1>
<?php
	$servername = "127.0.0.1:3306";
	$username = "root";
	$password = "1qaz2wsx";
	$dbname = "restaurant_booking";
  echo "<button class= 'btn' style='align-self:center' onclick= 'goHomePage()'>Go to HomePage</button><br/>";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
      exit;
	}

  // calculate existed order numbers
  $ordercount = "SELECT COUNT(*) AS ordernumber FROM booking";
  $countresult = $conn->query($ordercount);
  $ordernumber = 0;
  if ($countresult->num_rows > 0) {
    while ($resrow = $countresult->fetch_assoc()) {
      $ordernumber = $resrow['ordernumber'];
    }
  }
  $ordernumber++;
  $countresult->free();

  // get customer name
  $cusinfo = "SELECT * FROM customer WHERE cid ='".$cname."'";
  $cusresult = $conn->query($cusinfo);
  if ($cusresult->num_rows > 0) {
    while ($resrow = $cusresult->fetch_assoc()) {
      echo "<br/><p>Dear ". $resrow['cname']. " (Tel:". $resrow['phone']. ")</p>";
    }
  }
  $cusresult->free();

  // get restaurant name and address
  $resaddress = "";
  $resname = "";
  $resinfo = "SELECT rname,raddress FROM restaurant WHERE rid ='".$rname."'";
  $resresult = $conn->query($resinfo);
  if ($resresult->num_rows > 0) {
    while ($resrow = $resresult->fetch_assoc()) {
      $resname = $resrow['rname'];
      $resaddress = $resrow['raddress'];
    }
  }

  // insert the new order into booking
  $neworder = "INSERT INTO booking (bid, cid, rid, btime, quantity) VALUES ('". $ordernumber ."','".
  $cname ."', '". $rname ."','". $bookdate." ".$booktime. "','". $npeople ."')";
  $orderinsert = $conn->query($neworder);
  if ($orderinsert === TRUE) {
    echo "<p>Your booking is processed successfully at ". date('H:i, jS F Y'). ".</p><br/><p>Your New Order:</p>";
    echo "<table class= 'table table-striped table-hover'><tr><th align='center'>Restaurant</th><th align='center'>Address</th><th align='center'>Time</th>
    <th align='center'>Seats</th></tr><tr>";
    echo "<td align='center'>". $resname ."</td>";
    echo "<td align='center'>". $resaddress ."</td>";
    echo "<td align='center'>". $bookdate." ".$booktime ."</td>";
    echo "<td align='center'>". $npeople ."</td>";
    echo "</tr></table>";
  } else {
    echo "Error: " . $neworder . "<br>" . $conn->error;
    exit;
  }

  // query history order
  $oldorder = "SELECT * FROM booking NATURAL JOIN restaurant WHERE cid='".$cname."' AND bid<>'".$ordernumber."'";
  $historyresult = $conn->query($oldorder);
  if ($historyresult->num_rows > 0) {
    echo "<br/><p>Your History Orders:</p>";
    echo "<table class= 'table table-striped table-hover'><tr><th align='center'>Restaurant</th><th align='center'>Address</th><th align='center'>Time</th>
    <th align='center'>Seats</th></tr>";
    while ($resrow = $historyresult->fetch_assoc()) {
      echo "<tr>";
  		echo "<td align='center'>" . $resrow['rname'] . "</td>";
  		echo "<td align='center'>" . $resrow['raddress'] . "</td>";
  		echo "<td align='center'>" . $resrow['btime'] . "</td>";
      echo "<td align='center'>" . $resrow['quantity'] . "</td>";
  		echo "</tr>";
    }
    echo "</table></div>";
  } else {
    echo "<br/><p>You don't have any history orders.</p>";
  }
  $historyresult->free();

	$conn->close();
?>
</body>
</html>
