<?php
//Makes DB connection
$servername = "sql1.njit.edu";
$username = "xz39";
$password = "100000Zxz.";
$dbname = "xz39";
$con = mysqli_connect($servername,$username,$password,$dbname);
if (mysqli_connect_errno())
{
	echo "connect fail";
die( mysqli_connect_error() );
}
?>