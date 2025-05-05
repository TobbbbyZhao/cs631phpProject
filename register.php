<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WALLET</title>
    <link rel="stylesheet" href="form.css" />
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="POST" action="register.php">
            <label class="form-label" for="Username">Username:</label>
            <input placeholder="Johndan" class="form-input" type="text" name="Username" id="Username" value="<?php echo htmlspecialchars($name); ?>" required>

            <label class="form-label" for="Password">Password:</label>
            <input placeholder="abcd1234@" class="form-input" type="password" name="Password" id="Password" value="<?php echo htmlspecialchars($email); ?>" required>

            <label class="form-label" for="SSN">SSN:</label>
            <input placeholder="12-345-5678" class="form-input" type="text" name="SSN" id="SSN" maxlength="11" value="<?php echo htmlspecialchars($ssn); ?>" required>

            <label class="form-label" for="Name">Name:</label>
            <input placeholder="John Dan" class="form-input" type="text" name="Name" id="Name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label class="form-label" for="Email">Email:</label>
            <input placeholder="abc@gmail.com" class="form-input" type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label class="form-label" for="PhoneNo">Phone Number:</label>
            <input placeholder="7189098810" class="form-input" type="text" name="PhoneNo" id="PhoneNo" maxlength="10" value="<?php echo htmlspecialchars($phoneNo); ?>" required>

            <label class="form-label" for="Balance">Balance:</label>
            <input placeholder="10000.00" class="form-input" type="number" step="0.01" name="Balance" id="Balance" value="<?php echo htmlspecialchars($balance); ?>" required>

            <label class="form-label" for="BankID">Bank ID:</label>
            <input placeholder="6543" class="form-input" type="number" name="BankID" id="BankID" maxlength="4" value="<?php echo htmlspecialchars($bankID); ?>" required>

            <label class="form-label" for="BANumber">Bank Account Number:</label>
            <input placeholder="6543556455654" class="form-input" type="text" name="BANumber" id="BANumber" maxlength="12" value="<?php echo htmlspecialchars($baNumber); ?>" required>

                <button class="form-button" type="submit">Register</button>
                <a class="form-button" href="index.php">Home</a>
        </form>
    </div>
</body>
</html>

<?php
include 'connect.php';

// Initialize variables for form fields
$password=$username=$ssn = $name = $email = $phoneNo = $balance = $bankID = $baNumber = '';

if ($_POST) {
    // Retrieve and sanitize inputs
    $password=$_POST['Password'];
    $username=$_POST['Username'];
    $ssn = $_POST['SSN'];
    $name =  $_POST['Name'];
    $email = $_POST['Email'];
    $phoneNo = $_POST['PhoneNo'];
    $balance = $_POST['Balance'];
    $bankID = $_POST['BankID'];
    $baNumber = $_POST['BANumber'];

    mysqli_begin_transaction($con);

    try {
        $sqlElecE = "INSERT INTO ELEC_ADDRESS (Identifier, Verified, Type,SSN) VALUES ('$email', 1,'Email','$ssn')";
        $sqlElecP = "INSERT INTO ELEC_ADDRESS (Identifier, Verified, Type,SSN) VALUES ('$phoneNo', 1,'Phone','$ssn')";
        mysqli_query($con, $sqlElecE);
        mysqli_query($con, $sqlElecP);

        $sqlBank = "INSERT INTO BANK_ACCOUNT (BankID, BANumber) VALUES ('$bankID', '$baNumber')";
        if (!mysqli_query($con, $sqlBank)) {
            throw new Exception("Error in BANK_ACCOUNT: " . mysqli_error($con));
        }else{
            $sqlWallet = "INSERT INTO WALLET_ACCOUNT (SSN, Name, PhoneNo, Balance, BankID, BANumber, BAVerified)
                          VALUES ('$ssn', '$name', '$phoneNo', $balance, '$bankID', '$baNumber', 0)";
            if (!mysqli_query($con, $sqlWallet)) {
                throw new Exception("Error in WALLET_ACCOUNT: " . mysqli_error($con));
            }else{
                $sqlAccount = "INSERT INTO LOGIN_ACCOUNT (username, password, ssn) VALUES ('$username', '$password','$ssn')";
                if (!mysqli_query($con, $sqlAccount)) {
                    throw new Exception("Error in LOGIN_ACCOUNT: " . mysqli_error($con));
                }else{
                $sqlEmail = "INSERT INTO EMAIL_ADDRESS (EmailAdd, SSN) VALUES ('$email', '$ssn')";
                if (!mysqli_query($con, $sqlEmail)) {
                    throw new Exception("Error in EMAIL_ADDRESS: " . mysqli_error($con));
                }

                // Commit transaction
                mysqli_commit($con);
                $sql = "SELECT * FROM `LOGIN_ACCOUNT` WHERE username='$username' AND password='$password'";
				$result = mysqli_query($con,$sql);
                $row = mysqli_fetch_array($result);
				session_start();
				$_SESSION['ssn']=$row['ssn'];
				$_SESSION['login']=true;
                echo "<script>alert('Registration successful!'); window.location.href='home.php';</script>";}
            }

        }

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo "<script>alert('Registration failed: " . addslashes($e->getMessage()) . "'); window.location.href='register.php';</script>";
    }
}
?>