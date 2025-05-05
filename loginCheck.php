<?php
if($_SESSION['login']!=true){
	echo "<script>alert('You need to login first')</script>";
				echo "<script> location.href='index.php'; </script>";
        exit;
}
?>