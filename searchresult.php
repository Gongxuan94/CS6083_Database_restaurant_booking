<!DOCTYPE html>
<?php
  // Get global variable from homepage
  $npeople = trim($_POST['npeople']);
  $keyword = trim($_POST['keyword']);
  $bookdate = $_POST['bookdate'];
  $booktime = substr($_POST['booktime'], 0, strpos($_POST['booktime'],":")).":00:00"; // make sure the time is in the form of xx:00:00
  $cname = trim($_POST['cname']);
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

    function changeinputStyle() {
      document.forms["orderSubmit"]["npeople"].style = "border:1px solid red;";
    }

    function validateForm2() {

      // npeople has to be an integer and cannot be null
      var npeople = document.forms["orderSubmit"]["npeople"].value;
      if (npeople =="") {
        document.getElementById("numerr").innerHTML = " Please input a valid number of people";
        changeinputStyle();
        return false;
      }
      if (!isNaN(npeople)) {
        if (npeople <= 0 || npeople % 1 != 0) {
          document.getElementById("numerr").innerHTML = " Please input a valid number of people";
          changeinputStyle();
          return false;
        }
      } else {
        document.getElementById("numerr").innerHTML = " Please input a valid number of people";
        changeinputStyle();
        return false;
      }

      // the number should be less than the capacity
      var resselect = document.getElementById("resid");
      var resindex = resselect.selectedIndex;
      var resid = resselect.options[resindex].value; // rid of the chosen restaurant
      var bookdate = document.forms["orderSubmit"]["datebooked"].value;
      var booktime = document.forms["orderSubmit"]["timebooked"].value;

      var errnum = 0;
      var datasent = {"resid":resid,"npeople":npeople,"bookdate":bookdate,"booktime":booktime};
      $.ajax({  type: "post",
                url: "rescapacity.php",
                data: { "datasent" : JSON.stringify(datasent)},
                async: false,
                success: function(data){
                  errnum = data;
                }
             });
      if (errnum != 0) {
        document.getElementById("numerr").innerHTML = " Not enough seats!";
        return false;
      }
    }
  </script>
</head>
<body>
<div class= "center-in-up">
  <h1 align= "center">Search Results</h1>
<?php
	// echo "<p>Order processed at ". date('H:i, jS F Y'). "</p>";
	$servername = "127.0.0.1:3306";
	$username = "root";
	$password = "1qaz2wsx";
	$dbname = "restaurant_booking";
  $cusinfo = array(array());   // store customer information
  $cidnums = 0; // how many customer in $cusinfo
  $resinfo = array(array()); // store restaurant information

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
      exit;
	}

  // don't get the value of cname
  echo "<p>Welcome!". trim($cname) . "</p>";
  if ($cname =="") {
    echo "<p>You have to Log in first.</p>";
    echo "<input class= 'btn' type= 'button' onclick= 'goHomePage()' value= 'Back to HomePage'/>";
    exit;
  }

  echo "<p>You are looking for $npeople seats at $booktime on $bookdate. ";
  if ($keyword != "") {
    echo "(Keyword: ".$keyword.")</p>";
  } else {
    echo "</p>";
  }

  if($npeople == "") {
    $npeople = 0;
  }

  //search the customer cid
  $cusinfos = "SELECT * FROM customer where cname = '". trim($cname) ."'";
  $cusresult = $conn->query($cusinfos);
  if ($cusresult->num_rows > 0) {
    while ($resrow = $cusresult->fetch_assoc()) {
      $cusinfo[$cidnums][0] = $resrow['cid'];
      $cusinfo[$cidnums][1] = $resrow['cname'];
      $cusinfo[$cidnums][2] = $resrow['phone'];
      $cidnums++;
    }
  } else {
    exit;
  }
  $cusresult->free();

  /* SELECT r.rid, rname, raddress, description, (CASE WHEN orderedseats IS NULL THEN capacity ELSE capacity-orderedseats END) AS seatsleft
     FROM (SELECT * FROM restaurant WHERE description LIKE '%%' OR rname LIKE '%%' ) AS r LEFT JOIN
          (SELECT rid, SUM(quantity) AS orderedseats
          FROM booking
          WHERE btime = '2017-11-05 12:00:00'
          GROUP BY rid) AS t ON r.rid = t.rid
     WHERE (orderedseats IS NULL AND capacity >= 20) OR (orderedseats IS NOT NULL AND capacity-orderedseats >= 20);
  */
  // query booking using btime, and query restaurant using keyword, join them to calculate how many seats are left.
  $resquery = "SELECT r.rid AS rid, rname, raddress, description,
  (CASE WHEN orderedseats IS NULL THEN capacity ELSE capacity-orderedseats END) AS seatsleft
  FROM (SELECT * FROM restaurant WHERE description LIKE '%" .$keyword. "%' OR rname LIKE '%".$keyword."%') AS r
  LEFT JOIN (SELECT rid, SUM(quantity) AS orderedseats FROM booking WHERE btime ='". $bookdate." ".$booktime."' GROUP BY rid) AS t
  ON r.rid = t.rid WHERE (orderedseats IS NULL AND capacity >=".$npeople.
  ") OR (orderedseats IS NOT NULL AND capacity-orderedseats >=".$npeople.")";
  $resresult = $conn->query($resquery);
  if ($resresult->num_rows > 0) {
    echo "<br/><h2 align= 'center'>Restaurants Available</h2>";
    echo "<table class= 'table table-striped table-hover'><tr><th align='center'>Id</th><th align='center'>Name</th><th align='center'>Address</th>
    <th align='center'>Description</th><th align='center'>Capacity</th></tr>";
    // output data of each row
    $count = 0;
    while ($resrow = $resresult->fetch_assoc()) {
      $resinfo[$count][0] = $resrow['rid'];
      $resinfo[$count][1] = $resrow['rname'];
      $resinfo[$count][2] = $resrow['raddress'];
      $resinfo[$count][3] = $resrow['description'];
      $resinfo[$count][4] = $resrow['seatsleft'];
    	echo "<tr>";
  		echo "<td align='center'>" . $resinfo[$count][0] . "</td>";
  		echo "<td align='center'>" . $resinfo[$count][1] . "</td>";
  		echo "<td align='center'>" . $resinfo[$count][2] . "</td>";
      echo "<td align='center'>" . $resinfo[$count][3] . "</td>";
      echo "<td align='center'>" . $resinfo[$count][4] . "</td>";
  		echo "</tr>";
      $count++;
    }
    echo "</table><br/>";

    echo "<form class= 'form-horizontal' name= 'orderSubmit' onsubmit= 'return validateForm2();' action= 'processorder.php' method= 'post'>";

    // choose a restaurant
    echo "<div class= 'form-group'><div class= 'col-6'><p>Please choose a restaurant from the above</p></div>
    <div class= 'col-6'><select class= 'form-select' name= 'rname' id= 'resid'>";
    for ($i = 0; $i < $count; $i++) {
      echo "<option value ='" . $resinfo[$i][0] . "'>".$resinfo[$i][0]." ".$resinfo[$i][1]."</option>";
    }
    echo "</select></div></div>";

    // choose your information
    echo "<div class= 'form-group'><div class= 'col-6'><p>Confirm your personal information</p></div><div class= 'col-6'><select class= 'form-select' name= 'cname'>";
    for ($i = 0; $i < $cidnums; $i++) {
      echo "<option value ='".$cusinfo[$i][0]. "'>".$cusinfo[$i][0]." ".$cusinfo[$i][1]." ".$cusinfo[$i][2]. "</option>";
    }
    echo "</select></div></div>";

    // other information
    echo "<div class= 'form-group'><div class= 'col-6'><p>How many seats</p></div><div class= 'col-6'>
    <input class= 'form-input' type= 'text' name= 'npeople' value= '". $npeople ."' size= '3' maxlength= '3' />";
    echo "<span class= 'error' style= 'color:#FF5733' id= 'numerr'></span><br/></div></div>";
    echo "<input type= 'hidden' name= 'datebooked' value= '". $bookdate ."'/>";
    echo "<input type= 'hidden' name= 'timebooked' value= '". $booktime ."'/>";
    echo "<div class= 'form-group'><input style= 'margin-left:auto;margin-right:auto;' class= 'btn' type= 'submit' value= 'Submit Request'/></div><br/>";
    echo "<div class= 'form-group'><input style= 'margin-left:auto;margin-right:auto;' class= 'btn' type= 'button' onclick= 'goHomePage()' value= 'Go to HomePage'/></div>";
    echo "</form></div>";

  } else {
    echo "<p style= 'color: #FF5733'>Sorry, no restaurant available.</p><br/><br/>";
    echo "<input class= 'btn' type= 'button' style= 'margin-left:auto;margin-right:auto;' onclick= 'goHomePage()' value= 'Go to HomePage'/>";
  }

  $resresult->free();
	$conn->close();
?>
</body>
</html>
