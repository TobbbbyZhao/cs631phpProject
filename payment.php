<?php
session_start();
include 'connect.php'; // Include database connection file

// Check if SSN exists in session
if (!isset($_SESSION['ssn'])) {
    die("Unauthorized access. SSN not found in session.");
}

// Initialize error/success messages
$message = "";

// Function to generate random transaction ID
function generateRandomID() {
    return rand(10000000000, 99999999999); // Random 11-digit integer
}


if ($_POST) {
    $identifier = $_POST['identifier'];
    $memo = isset($_POST['memo']) ? trim($_POST['memo']) : null;
    $cardType = $_POST['card_type'];
    $amount = $_POST['amount'];
    $ssn = $_SESSION['ssn'];
    $currentTime = date('Y-m-d H:i:s'); // Current Date-Time

     $insertElecAddress = "INSERT IGNORE INTO ELEC_ADDRESS (SSN, Identifier, Verified, Type)
                          VALUES (NULL, '$identifier', 0, 'New')";
    mysqli_query($con, $insertElecAddress);

    if (empty($identifier) || empty($amount) || empty($cardType)) {
        $message = "Please fill in all required fields.";
    } else {
    // trigger in SQL already
        $stmt = $con->prepare("INSERT INTO SEND_TRANSACTION (Identifier, I_DTime, C_DTime, Memo, CReason, CType, Amount, SSN)
                                VALUES ( ?, ?, NULL, ?, NULL, ?, ?, ?)");
        $stmt->bind_param("ssssss",  $identifier, $currentTime, $memo, $cardType, $amount, $ssn);

        if ($stmt->execute()) {
            echo "<script>alert('Transaction successful!'); window.location.href='home.php';</script>";
            exit();
        } else {
            $message = "Transaction failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="form.css" />
</head>
<body>
    <div class="container">
    <h1>Send Payment</h1>

    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label class="form-label" for="identifier">Identifier (Phone or Email):</label>
        <input placeholder="example@gmail.com or 1234567890" class="form-input" type="text" name="identifier" id="identifier" required>

        <label class="form-label" for="memo">Memo (Optional):</label>
        <input placeholder="Payment memo" class="form-input" type="text" name="memo" id="memo">

        <label class="form-label" for="card_type">Card Type:</label>
        <select class="form-input" id="card_type" name="card_type" required>
            <option value="Credit">Credit</option>
            <option value="Debit">Debit</option>
        </select>

        <label class="form-label" for="amount">Amount:</label>
        <input placeholder="100.00" class="form-input" type="number" step="0.01" name="amount" id="amount" min="0.01" required>

        <div style="margin-top: 20px;">
            <button class="form-button" type="submit">Submit</button>
            <button class="form-button" type="reset">Reset</button>
            <a class="form-button" href="home.php">Back</a>
        </div>
    </form>
</div>
</body>
</html>
