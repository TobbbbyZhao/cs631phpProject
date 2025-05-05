<?php
session_start();
include 'loginCheck.php';
include 'connect.php';
$ssn = $_SESSION['ssn'];

// Handle Add/Remove for Phone/Email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_phone_email'])) {
        $type = $_POST['type'];
        $identifier = $_POST['identifier'];
        $sql = "INSERT INTO ELEC_ADDRESS (Identifier, Verified, Type, SSN) VALUES ('$identifier', 1, '$type', '$ssn')";
        mysqli_query($con, $sql);
    } elseif (isset($_POST['remove_phone_email'])) {
        $identifier = $_POST['remove_identifier'];
        $sql = "DELETE FROM ELEC_ADDRESS WHERE Identifier='$identifier' AND SSN='$ssn'";
        mysqli_query($con, $sql);
    }
    // Handle Add/Remove for Bank Account
    if (isset($_POST['add_bank'])) {
        $bankID = $_POST['bankID'];
        $baNumber = $_POST['baNumber'];
        $sql = "INSERT INTO BANK_ACCOUNT (BankID, BANumber, SSN) VALUES ('$bankID', '$baNumber', '$ssn')";
        mysqli_query($con, $sql);
    } elseif (isset($_POST['remove_bank'])) {
        $bankID = $_POST['remove_bankID'];
        $baNumber = $_POST['remove_baNumber'];
        $sql = "DELETE FROM BANK_ACCOUNT WHERE BankID='$bankID' AND BANumber='$baNumber' AND SSN='$ssn'";
        mysqli_query($con, $sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="form.css" />
</head>
<body>
<div class="container">
    <h2>Phone And Emails</h2>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 60%; margin: auto; text-align: left;">
        <?php
        $sql = "SELECT * FROM ELEC_ADDRESS WHERE SSN='$ssn'";
        $result = mysqli_query($con, $sql);
        echo "<tr><th>Identifier</th><th>Verified</th><th>Type</th></tr>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr><td>{$row['Identifier']}</td><td>{$row['Verified']}</td><td>{$row['Type']}</td></tr>";
        }
        ?>
    </table>

    <!-- Add Form for Phone/Email -->
    <form method="POST">
        <h3>Add Phone/Email</h3>
        <label>Type:</label>
        <select name="type" required>
            <option value="Phone">Phone</option>
            <option value="Email">Email</option>
        </select>
        <label>Identifier:</label>
        <input type="text" name="identifier" required>
        <button type="submit" name="add_phone_email">Add</button>
    </form>

    <!-- Remove Form for Phone/Email -->
    <form method="POST">
        <h3>Remove Phone/Email</h3>
        <label>Select Identifier:</label>
        <select name="remove_identifier" required>
            <?php
            $sql = "SELECT Identifier FROM ELEC_ADDRESS WHERE SSN='$ssn'";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                echo "<option value='{$row['Identifier']}'>{$row['Identifier']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="remove_phone_email">Remove</button>
    </form>

    <h2>Bank Accounts</h2>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 60%; margin: auto; text-align: left;">
        <?php
        $sql = "SELECT * FROM BANK_ACCOUNT WHERE SSN='$ssn'";
        $result = mysqli_query($con, $sql);
        echo "<tr><th>BankID</th><th>BANumber</th></tr>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr><td>{$row['BankID']}</td><td>{$row['BANumber']}</td></tr>";
        }
        ?>
    </table>

    <!-- Add Form for Bank Account -->
    <form method="POST">
        <h3>Add Bank Account</h3>
        <label>Bank ID:</label>
        <input type="text" name="bankID" required>
        <label>BA Number:</label>
        <input type="text" name="baNumber" required>
        <button type="submit" name="add_bank">Add</button>
    </form>

    <!-- Remove Form for Bank Account -->
    <form method="POST">
        <h3>Remove Bank Account</h3>
        <label>Select Account:</label>
        <select name="remove_bankID" required>
            <?php
            $sql = "SELECT BankID, BANumber FROM BANK_ACCOUNT WHERE SSN='$ssn'";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $display = "{$row['BankID']} - {$row['BANumber']}";
                echo "<option value='{$row['BankID']}' data-banumber='{$row['BANumber']}'>$display</option>";
            }
            ?>
        </select>
        <input type="hidden" name="remove_baNumber" id="remove_baNumber">
        <button type="submit" name="remove_bank">Remove</button>
    </form>

    <a class="form-button" href="home.php">Back</a>
</div>
<script>
    // Update BANumber based on selected BankID in Bank Account Removal
    const selectBankID = document.querySelector('select[name="remove_bankID"]');
    const inputBANumber = document.getElementById('remove_baNumber');

    selectBankID.addEventListener('change', function() {
        inputBANumber.value = this.options[this.selectedIndex].getAttribute('data-banumber');
    });

    // Trigger initial event
    selectBankID.dispatchEvent(new Event('change'));
</script>
</body>
</html>
