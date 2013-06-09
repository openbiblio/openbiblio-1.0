<?php
session_start();
if(isset ($_POST["send"])) {
    $getname=$_POST["myname"];
    include 'connection.php';
    //$idvalue=$_SESSION["myvalue"];
    $idvalue=123456789;
    $sql="update entry set name='$getname' where id='$idvalue'";
    $result=mysqli_query($con,$sql)
            or die("error in query");
    if($result) {
        echo "Uploaded $_SESSION[myvalue] re ..... ";
		} else {
        echo "$_SESSION[myvalue] nahi hua";
    }
}
?>
<!doctype html>
<html class="no-js" lang="en" >
<head>
	<meta charset="utf-8" >

	<script type="text/javascript" src="webcam.js"></script>

	<script language="JavaScript">
		"use strict"
if(navigator.mozGetUserMedia==null) {
	console.log('Sorry your browser doesnt support "GetUserMedia"');
} else {
	console.log('"GetUserMedia" is supported here');
}
    webcam.set_api_url( 'test.php' );
		webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		webcam.set_shutter_sound( true ); // play shutter click sound
		webcam.set_hook( 'onComplete', 'my_completion_handler' );

		function take_snapshot(){
			// take snapshot and upload to server
			document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
			webcam.snap();
		}

		function my_completion_handler(msg) {
			// extract URL out of PHP output
			if (msg.match(/(http\:\/\/\S+)/)) {
				// show JPEG image in page
				document.getElementById('upload_results').innerHTML ='<h1>Upload Successful!</h1>';
				// reset camera for another shot
				webcam.reset();
			} else {
				alert("PHP Error: " + msg);
			}
		}
	</script>
</head>

<body>
	<p>
		This demo is courtsy of
		<a href="http://www.vivekmoyal.in/webcam-in-php-how-to-use-webcam-in-php/">
		Vivek Moyal
		</a>
	</p>
	<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
    <input type="text" name="myname" id="myname" value="<?php echo $_SERVER["PHP_SELF"];?>" />
    <input type="submit" name="send" id="send">
	</form>

	<script language="JavaScript">
		document.write( webcam.get_html(320, 240) );
	</script>

	<form>
		<input type=button value="Configure..." onClick="webcam.configure()">
		&nbsp;&nbsp;
		<input type=button value="Take Snapshot" onClick="take_snapshot()">
	</form>

	<div id="upload_results" style="background-color:#eee;"></div>
  </body>
</html>
