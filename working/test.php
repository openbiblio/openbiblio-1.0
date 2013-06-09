<?php
session_start();
include 'connection.php';
$name = date('YmdHis');
$newname="images/".$name.".jpg";
$file = file_put_contents( $newname, file_get_contents('php://input') );
if (!$file) {
	print "ERROR: Failed to write data to $filename, check permissions\n";
	exit();
}
else
{
    $sql="Insert into entry(images) values('$newname')";
    $result=mysqli_query($con,$sql)
            or die("Error in query");
    $value=mysqli_insert_id($con);
    $_SESSION["myvalue"]=$value;
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $newname;
print "$url\n";

?>
