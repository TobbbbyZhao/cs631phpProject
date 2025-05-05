<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Welcome to WALLET</title>
	<link rel="stylesheet" href="form.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
	<?php
		session_start();
		session_destroy();
		?>
	<form action="#" method="POST" class="container" id="">
		<h1><strong>WALLET Login</strong></h1>
		<table class="selection">
			<tr>
				<td class="table_element">
					<p class="menu_name">Select Regular or DB login:</p>
					<select name="loginMethod" id="the_selection" style="margin-top: 10px" onchange="changeInterface()">
						<option value="regular">
							Regular User
						</option>
						<option value="dbuser">
							DB user
						</option>
					</select>
				</td>
			</tr>
		</table>
		<table class="table_container">
			<tr class="input_content">
				<td class="input_name">Username:</td>
				<td class="input_box">
					<input id="first_name" name="userName" class="input_box" type="text" placeholder="Example: Jane" />
				</td>
				<td class="input_requirement">Required</td>
			</tr>
			<tr class="input_content">
				<td class="input_name">Password:</td>
				<td class="input_box">
					<input id="the_password" name="passWord" class="input_box" type="password" placeholder="Example: OT@1" />
				</td>
				<td class="input_requirement">
					<img src="https://cdn0.iconfinder.com/data/icons/basic-glyph/1024/lock-512.png" alt="icon" style="width: 15px"
						onclick="showPassword()" />Required
				</td>
			</tr>
			<tr id="dbinput" class="input_content" style="display: none;">
				<td class="input_name">DB name:</td>
				<td class="input_box">
					<input id="dbname" name="dbName" class="input_box" type="text" placeholder="Example: zz" />
				</td>
				<td class="input_requirement">Required</td>
			</tr>
		</table>
		<br />
		<br />
		<div class="buttons">
			<input onclick="return validate()" type="submit" class="end_button" value="Login">
			</input>
			<input type="button" value="Register" class="end_button" onclick="window.location.href='register.php';" />
			<input type="reset" value="Reset" class="end_button" />
		</div>
	</form>

	<?php
if ($_POST){
	$firstName = $_POST['userName'];
	$passWord = $_POST['passWord'];
	$type=$_POST['loginMethod'];
		switch ($type) {
			case 'regular':
				include 'connect.php';
				$sql = "SELECT * FROM `LOGIN_ACCOUNT` WHERE username='$firstName' AND password='$passWord'";
				$result = mysqli_query($con,$sql);
				if (!mysqli_num_rows($result)) {
				echo '<script>alert("Username or password incorrect. Please re-enter")</script>';
				unset($_POST);
				} else {
				$row = mysqli_fetch_array($result);
				session_start();
				$_SESSION['ssn']=$row['ssn'];
				$_SESSION['login']=true;
				unset($_POST);
				header("Location: home.php");
				}
				break;
			case 'dbuser':
				$dbname=$_POST['dbName'];
				if($dbname==null || $firstName==null || $passWord==null){
					echo "<script>alert('Miss information')</script>";
					unset($_POST);
					echo "<script> location.href='index.php'; </script>";
					exit();
				}
				$servername = "sql1.njit.edu";
				$con = mysqli_connect($servername,$firstName,$passWord,$dbname);
				if (mysqli_connect_errno())
				{
					echo "<script>alert('Connection error, check your info')</script>";
					unset($_POST);
					echo "<script> location.href='index.php'; </script>";
					exit();
				}else{
					session_start();
					$_SESSION['username']=$firstName;
					$_SESSION['password']=$passWord;
					$_SESSION['db']=$dbname;
					$_SESSION['login']=true;
					unset($_POST);
					header("Location: sql.php");
				}
				break;
			default:
			unset($_POST);
				break;
		}
	}
		unset($_POST);
		?>
	<script src="handler.js"></script>
</body>

</html>