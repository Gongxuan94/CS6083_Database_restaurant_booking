<!DOCTYPE html>
<html>
<head>
<title>Restaurant Booking System</title>
<link rel="stylesheet" href="spectre.css">
<style>
.center-in-up{
            position: absolute;
            top: 15%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -o-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
}
.center-in-down{
						width: 70%;
						margin: 0 auto;
						padding: 1.5rem 2rem;
						background-color: white;
						border-radius: 8px;
						box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -o-transform: translate(-50%, -50%);
            transform: translate(-50%, -40%);
}
</style>
<script type="text/javascript" src="jquery-3.2.1.js"></script>
<script type="text/javascript">
  // make sure the form is reasonable
  function validateForm() {
		var errnum = 0;
		var errnum2 = 0;
    //Initialize all the error
    document.getElementById("nameerr").innerHTML = "* Required";
    document.getElementById("numerr").innerHTML = "";
    document.getElementById("dateerr").innerHTML = "* Required";
    document.getElementById("timeerr").innerHTML = "* Required";
		document.forms["searchRes"]["cname"].style = "border: .05rem solid #caced7;";
		document.forms["searchRes"]["npeople"].style = "border: .05rem solid #caced7;";
		document.forms["searchRes"]["bookdate"].style = "border: .05rem solid #caced7;";
		document.forms["searchRes"]["booktime"].style = "border: .05rem solid #caced7;";

    // the customer name should be in the database (AJAX)
    var str = document.getElementById("getcname").value;
    $.ajax({  type: "post",
              url: "customername.php",
              data: {'q':str},
              async: false,
              success: function(data){
								if (data.split(",")[0] != "") {
									document.getElementById("nameerr").innerHTML = "* "+data.split(",")[0];
								}
                errnum = data.split(",")[1];
              }
           });

    // judge if the input is a number
    var x = document.forms["searchRes"]["npeople"].value;
    if (!isNaN(x)) {
      if (x < 0 || x % 1 != 0) {
        document.getElementById("numerr").innerHTML = "Please input a valid number of people";
				document.forms["searchRes"]["npeople"].style = "border:1px solid red;";
        errnum2 = 1;
      }
    } else {
      document.getElementById("numerr").innerHTML = "Please input a valid number of people";
			document.forms["searchRes"]["npeople"].style = "border:1px solid red;";
      errnum2 = 1;
    }

    var y = document.forms["searchRes"]["bookdate"].value;
    if (y == null || y == "") {
      document.getElementById("dateerr").innerHTML = "*  You must choose a date";
			document.forms["searchRes"]["bookdate"].style = "border:1px solid red;";
      errnum2 = 1;
    }

    var z = document.forms["searchRes"]["booktime"].value;
    if (z == null || z == "") {
    	document.getElementById("timeerr").innerHTML = "*  You must choose a time";
			document.forms["searchRes"]["booktime"].style = "border:1px solid red;";
      errnum2 = 1;
    }

    if (errnum != 0) {
			document.forms["searchRes"]["cname"].style = "border:1px solid red;";
      return false;
    } else if (errnum2 != 0 ){
			return false;
		}

  }
</script>
</head>
<body>
<div class= "center-in-up">
  <h1 align= "center">Restaurant Booking System</h1>
</div>
<div class= "center-in-down">
<form class= "form-horizontal" name= "searchRes" action= "searchresult.php" onsubmit= "return validateForm();" method= "post">
	<h2 align= "center">Search For Seats</h2>
	<div class= "form-group">
		<div class= "col-3">
			<label class= "form-label" for= "getcname">Name</label>
		</div>
		<div class= "col-9">
			<input class= "form-input" type= "text" id= "getcname" name= "cname" maxlength= "20" placeholder="Please Input Your Name"/>
			<span class= "error" style= "color: #FF5733" id= "nameerr">* Required</span>
		</div>
	</div>
	<div class= "form-group">
		<div class= "col-3">
    	<label class= "form-label" for= "getkeyword">Keyword</label>
		</div>
		<div class= "col-9">
			<input class= "form-input" type= "text" id= "getkeyword" name= "keyword" maxlength= "30" /><br/>
		</div>
	</div>
	<div class= "form-group">
		<div class= "col-3">
			<label class= "form-label" for= "getnpeople">Number of People</label>
		</div>
		<div class= "col-9">
			<input class= "form-input" type= "text" id= "getnpeople" name= "npeople" maxlength= "3"/>
    	<span class= "error" style= "color: #FF5733" id= "numerr"></span><br/>
		</div>
	</div>
	<div class= "form-group">
		<div class= "col-3">
  		<label class= "form-label" for= "getnpeople">Booking Time</label>
		</div>
		<div class= "col-9">
			<input type= "date" value= "<?= isset($_POST['bookdate']) ? $_POST['bookdate'] : ''; ?>" name= "bookdate"
         min= "<?= date('Y-m-d'); ?>"/><br/>
    	<span class= "error" style= "color: #FF5733" id= "dateerr">* Required</span><br/>
			<input type= "time" value= "16:00" name = "booktime"/><br/>
    	<span class= "error" style= "color: #FF5733" id= "timeerr">* Required</span>
		</div>
	</div>
	<div class= "form-group">
    	<input style= "margin-left:auto;margin-right:auto;" class= "btn" type= "submit" value= "Search For Seats"/>
	</div>
</form>
</div>
</body>
</html>
