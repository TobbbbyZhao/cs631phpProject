<?php
session_start();
include 'loginCheck.php';
include 'connect.php';

$ssn = $_SESSION['ssn'];

// Fetch user details from the database
$sql = "SELECT * FROM WALLET_ACCOUNT  WHERE SSN = '$ssn'";
$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Unable to retrieve user information. Please try again.'); window.location.href='index.php';</script>";
    exit();
}

$userData = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home - WALLET</title>
    <link rel="stylesheet" href="form.css" />

</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($userData['Name']); ?>!</h1>
        <form action="update.php" method="POST">
            <table border="1" cellpadding="10" cellspacing="0" style="width: 60%; margin: auto; text-align: left;">
                <?php
                // Define a field map
                $fieldMap = [
                    'Name' => 'Name',
                    'PhoneNo' => 'Phone Number',
                    'Balance' => 'Balance',
                    'BankID' => 'Bank ID',
                    'BANumber' => 'Bank Account Number',
                    'BAVerified' => 'Bank Verified',
                ];

                echo "<tr>";
                foreach ($fieldMap as $key => $label) {
                    echo "<td><strong>" . $label . "</strong></td>";
                }
                echo "</tr>";
                echo "<tr>";
                foreach ($fieldMap as $key => $label) {
                    if ($key === 'Name' || $key === 'PhoneNo' ||$key === 'BankID' || $key === 'BANumber') {
                        // Editable fields
                        $value = $userData[$key]? htmlspecialchars($userData[$key]):"NULL";
                        echo "<td>
                                <div class='editable' onclick='makeEditable(this)' style='text-decoration: none; color: #000000;' >$value</div>
                                <input type='hidden' name='$key' value='$value'>
                              </td>";
                    } else if ($key === 'BAVerified') {
                        // Display Yes/No for BAVerified
                        echo "<td>" . ($userData[$key] ? 'Yes' : 'No') . "</td>";
                    } else {
                        // Non-editable fields
                        echo "<td>" . htmlspecialchars($userData[$key]) . "</td>";
                    }

                }
                echo "</tr>";
                ?>
                <tr>

                        <table border="1" cellpadding="10" cellspacing="0"  style="font-size:medium;  text-align: left;">
                            <h4>Recent Sent Transaction</h4>
                        <?php
                         $sql = "SELECT * FROM SEND_TRANSACTION WHERE SSN='$ssn'";
                         $result = mysqli_query($con, $sql);
                         $sqltotal="SELECT SUM(Amount) AS TotalAmount FROM SEND_TRANSACTION WHERE I_DTime >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)  AND SSN='$ssn'; ";
                         $resulttotal = mysqli_query($con, $sqltotal);
                         $total = mysqli_fetch_assoc($resulttotal);
                         echo "<span>Total Amount Last Month: ".htmlspecialchars($total["TotalAmount"]). "</span>";
                         echo "<tr>";
                    $fields = mysqli_fetch_fields($result);
                    foreach ($fields as $field) {
                        echo "<th>" . htmlspecialchars($field->name) . "</th>";
                    }
                    echo "</tr>";
                while($row = mysqli_fetch_assoc($result)){
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo '</tr>';
                }
                        ?>
                        </table>
                    </tr>
                    <tr>

                        <table border="1" cellpadding="10" cellspacing="0"  style="font-size:medium;  text-align: left;">
                            <h4>Recent Received Transaction</h4>
                        <?php
                         $sql = "SELECT * FROM SEND_TRANSACTION WHERE Identifier=(SELECT PhoneNo FROM WALLET_ACCOUNT WHERE SSN='$ssn') OR Identifier=(SELECT EmailAdd FROM EMAIL_ADDRESS WHERE SSN='$ssn') ";
                         $result = mysqli_query($con, $sql);

                         $sqltotal="SELECT SUM(Amount) AS TotalAmount FROM SEND_TRANSACTION WHERE I_DTime >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)  AND (Identifier=(SELECT PhoneNo FROM WALLET_ACCOUNT WHERE SSN='$ssn') OR Identifier=(SELECT EmailAdd FROM EMAIL_ADDRESS WHERE SSN='$ssn')); ";
                         $resulttotal = mysqli_query($con, $sqltotal);
                         $total = mysqli_fetch_assoc($resulttotal);
                         echo "<span>Total Amount Last Month: ".htmlspecialchars($total["TotalAmount"]). "</span>";

                         echo "<tr>";
                    $fields = mysqli_fetch_fields($result);
                    foreach ($fields as $field) {
                        echo "<th>" . htmlspecialchars($field->name) . "</th>";
                    }
                    echo "</tr>";
                while($row = mysqli_fetch_assoc($result)){
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo '</tr>';
                }
                        ?>
                        </table>
                    </tr>
                    <tr>


                    <table border="1" cellpadding="10" cellspacing="0"  cellpadding="10" style="font-size:medium;  text-align: left;">
                            <h4>Recent Open Request</h4>
                        <?php
                         $sql = "SELECT * FROM REQUESTED_FROM NATURAL JOIN REQUEST_TRANSACTION WHERE Identifier=(SELECT PhoneNo FROM WALLET_ACCOUNT WHERE SSN='$ssn') OR Identifier=(SELECT EmailAdd FROM EMAIL_ADDRESS WHERE SSN='$ssn') ";
                         $result = mysqli_query($con, $sql);
                         echo "<tr>";
                    $fields = mysqli_fetch_fields($result);
                    foreach ($fields as $field) {
                        echo "<th>" . htmlspecialchars($field->name) . "</th>";
                    }
                    echo "</tr>";
                while($row = mysqli_fetch_assoc($result)){
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo '</tr>';
                }
                        ?>
                        </table>

                </tr>
            </table>
            <input type="hidden" name="ssn" value="<?php echo htmlspecialchars($userData['SSN']); ?>">
            <div style="text-align: center;">
                <button style=" padding: 10px 20px; font-size: large; background-color: #dc3545; color: #fff; border-radius: 5px;" href='index.php' type="submit">Update</button>
                <a style="text-decoration: none; padding: 10px 20px; background-color: #dc3545; color: #fff; border-radius: 5px;" href='index.php'>Logout</a>
                <a style="text-decoration: none; padding: 10px 20px; background-color: #dc3545; color: #fff; border-radius: 5px;" href='payment.php'>Pay</a>
                <a style="text-decoration: none; padding: 10px 20px; background-color: #dc3545; color: #fff; border-radius: 5px;" href='request.php'>Request</a>
                <a style="text-decoration: none; padding: 10px 20px; background-color: #dc3545; color: #fff; border-radius: 5px;" href='personal.php'>Account and Wallet</a>
            </div>
        </form>
    </div>

    <script>
        function makeEditable(element) {
            if (element.querySelector('input')) return;

            const originalValue = element.textContent;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = originalValue;

            input.onblur = function() {
                element.textContent = input.value;
                element.nextElementSibling.value = input.value;
            };

            element.textContent = '';
            element.appendChild(input);
            input.focus();
        }
    </script>
</body>
</html>
