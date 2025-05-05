<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ssn = $_POST['ssn'];
    $name = mysqli_real_escape_string($con, $_POST['Name']);
    $phoneNo = mysqli_real_escape_string($con, $_POST['PhoneNo']);
    $bid = mysqli_real_escape_string($con, $_POST['BankID']);
    $bNo = mysqli_real_escape_string($con, $_POST['BANumber']);

    // Insert into ELEC_ADDRESS, ignore errors if it fails
    $insertElecAddress = "INSERT IGNORE INTO ELEC_ADDRESS (SSN, Identifier, Verified, Type)
                          VALUES ('$ssn', '$phoneNo', 1, 'Phone')";

    // Insert into BANK_ACCOUNT, ignore errors if it fails
    $insertBankAccount = "INSERT IGNORE INTO BANK_ACCOUNT (BankID, BANumber, SSN)
                          VALUES ('$bid', '$bNo', '$ssn')";

    // Execute the INSERT queries, ignore success or failure
    mysqli_query($con, $insertElecAddress);
    mysqli_query($con, $insertBankAccount);

    // Perform the UPDATE query
    $updateQuery = "UPDATE WALLET_ACCOUNT
                    SET Name = '$name', PhoneNo = '$phoneNo', BankID = '$bid', BANumber = '$bNo'
                    WHERE SSN = '$ssn'";

    if (mysqli_query($con, $updateQuery)) {
        echo "<script>alert('Information updated successfully.'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Update failed. Please try again.');</script>";
    }
}
?>
