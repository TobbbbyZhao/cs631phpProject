

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Page</title>
    <link rel="stylesheet" href="form.css" />
</head>
<body>
    <div class="container">
        <h1>Submit Request</h1>

        <?php if ($message): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label class="form-label" for="identifier">Identifier (Phone or Email):</label>
            <input placeholder="example@gmail.com or 1234567890" class="form-input" type="text" name="identifier" id="identifier" required>

            <label class="form-label" for="percentage">Percentage:</label>
            <input placeholder="50.00" class="form-input" type="number" step="0.01" min="0.01" name="percentage" id="percentage" required>

            <label class="form-label" for="amount">Amount:</label>
            <input placeholder="100.00" class="form-input" type="number" step="0.01" min="0.01" name="amount" id="amount" required>

            <label class="form-label" for="memo">Memo (Optional):</label>
            <input placeholder="Request memo" class="form-input" type="text" name="memo" id="memo">

            <div style="margin-top: 20px;">
                <button class="form-button" type="submit">Submit</button>
                <button class="form-button" type="reset">Reset</button>
                <a class="form-button" href="home.php">Back</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php
session_start();
include 'connect.php'; // Include database connection file

// Check if SSN exists in session
if (!isset($_SESSION['ssn'])) {
    die("Unauthorized access. SSN not found in session.");
}

// Initialize error/success messages
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $identifier = trim($_POST['identifier']);
    $percentage = floatval($_POST['percentage']);
    $amount = floatval($_POST['amount']);
    $memo = isset($_POST['memo']) ? trim($_POST['memo']) : null;
    $ssn = $_SESSION['ssn'];
    $currentTime = date('Y-m-d H:i:s'); // Current Date-Time

    $insertElecAddress = "INSERT IGNORE INTO ELEC_ADDRESS (SSN, Identifier, Verified, Type)
                          VALUES (NULL, '$identifier', 0, 'New')";
    mysqli_query($con, $insertElecAddress);
    // Validate required fields
    if (empty($identifier) || $percentage <= 0 || $amount <= 0) {
        $message = "Please fill in all required fields with valid values.";
    } else {
        // Start transaction to ensure atomic operations
        $con->begin_transaction();

        try {
            // 1. Insert into REQUEST_TRANSACTION table
            $stmt1 = $con->prepare("INSERT INTO REQUEST_TRANSACTION (Amount, RDateTime, Memo, SSN)
                                   VALUES (?, ?, ?, ?)");
            $stmt1->bind_param("dsss", $amount, $currentTime, $memo, $ssn);
            $stmt1->execute();

            // Retrieve the auto-generated RTid
            $rtid = $con->insert_id;

            // 2. Insert into FROM table using the RTid
            $stmt2 = $con->prepare("INSERT INTO `REQUESTED_FROM` (RTid, Identifier, Percentage)
                                   VALUES (?, ?, ?)");
            $stmt2->bind_param("isd", $rtid, $identifier, $percentage);
            $stmt2->execute();

            // Commit transaction
            $con->commit();
            echo "<script>alert('Request submitted successfully!'); window.location.href='home.php';</script>";
            exit();
        } catch (Exception $e) {
            // Rollback if any error occurs
            $con->rollback();
            $message = "Transaction failed: " . $e->getMessage();
        }

        // Close statements
        $stmt1->close();
        $stmt2->close();
    }
}
?>