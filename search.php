<?php
session_start();
include 'loginCheck.php';
include 'connect.php';

$ssn = $_SESSION['ssn'];

// Initialize messages
$message = "";
$result_data = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search & Statement</title>
    <link rel="stylesheet" href="form.css" />
</head>
<body>
    <div class="container">
        <h1>Search Transaction</h1>
        <form method="POST" action="search.php">
            <label for="searchField">Search Field:</label>
            <select name="searchField" id="searchField" onchange="toggleDateFields()" required>
                <option value="">-- Select Field --</option>
                <option value="I_DTime">Time-Date Range</option>
                <option value="SSN">SSN</option>
                <option value="Identifier">Email Address / Phone Number</option>
                <option value="CType">Type of Transaction</option>
            </select>

            <div id="inputFields">
                <label id="fieldLabel" for="searchValue">Enter Value:</label>
                <input type="text" name="searchValue" id="searchValue" >
            </div>

            <div id="dateRangeFields" style="display: none;">
                <label for="startDate">Start Date:</label>
                <input type="date" name="startDate" id="startDate">
                <label for="endDate">End Date:</label>
                <input type="date" name="endDate" id="endDate">
            </div>

            <button type="submit" name="searchTransaction">Search</button>
        </form>

        <?php
       if (isset($_POST['searchTransaction'])) {
    $field = $_POST['searchField'];
    $value = $_POST['searchValue'] ?? null;

    if ($field == "I_DTime") {
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $query = "SELECT * FROM SEND_TRANSACTION WHERE I_DTime BETWEEN '$startDate' AND '$endDate';";
    } else {
        $query = "SELECT * FROM SEND_TRANSACTION WHERE $field LIKE '%$value%'";
    }

    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<h3>Search Results:</h3>";
        echo "<table border='1'><tr>";
        $fields = mysqli_fetch_fields($result);
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found for your query.</p>";
    }
}
        ?>

        <h1>Statement</h1>
        <form method="POST" action="search.php">
            <label for="statementType">Select Statement Type:</label>
            <select name="statementType" id="statementType" onchange="toggleStatementFields()" required>
                <option value="">-- Select Statement --</option>
                <option value="total_amount_range">Total Amount Received/Sent (Date Range)</option>
                <option value="avg_monthly">Total/Average Amount Per Month</option>
                <option value="max_transaction">Transactions with Maximum Amount Per Month</option>
                <option value="best_users">Best Users (Highest Total Amount)</option>
            </select>

            <div id="statementFields"></div>

            <div id="userFields" style="display: none;">
            <label id="fieldLabel" for="stateValue">Enter User SSN:</label>
            <input type="text" name="stateValue" id="stateValue">
            </div>

            <button type="submit" name="generateStatement">Generate Statement</button>
            <a style="text-decoration: none;   border-radius: 5px;" href='index.php'>Logout</a>
        </form>
        <table border='1'>
        <?php
        if (isset($_POST['generateStatement'])) {
            $type = $_POST['statementType'];
            $val = $_POST['stateValue'];
            if ($type == "total_amount_range") {
                $start = $_POST['startDate'];
                $end = $_POST['endDate'];
                $query = "SELECT SUM(Amount) AS TotalAmount FROM SEND_TRANSACTION WHERE SSN='$val' AND I_DTime BETWEEN '$start' AND '$end'";
            } elseif ($type == "avg_monthly") {
                $query = "SELECT SUM(Amount) AS TotalAmount, AVG(Amount) AS AvgAmount, MONTH(I_DTime) AS Month
                          FROM SEND_TRANSACTION
                          WHERE SSN='$val'
                          GROUP BY MONTH(I_DTime) ";
            } elseif ($type == "max_transaction") {
                $query = "SELECT MAX(Amount) AS MaxAmount, MONTH(I_DTime) AS Month
                          FROM SEND_TRANSACTION
                          GROUP BY MONTH(I_DTime)";
            } elseif ($type == "best_users") {
                $query = "SELECT SSN, SUM(Amount) AS TotalAmount
                          FROM SEND_TRANSACTION
                          GROUP BY SSN
                          ORDER BY TotalAmount DESC LIMIT 1";
            }

            $result = mysqli_query($con, $query);
            if ($result) {
                echo "<h3>Statement Results:</h3>";
                echo "<tr>";
                $fields = mysqli_fetch_fields($result);
                foreach ($fields as $field) {
                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                echo "</tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<p>No data found.</p>";
            }
        }
        ?>

        </table>
    </div>
    <script>
        function toggleDateFields() {
            const field = document.getElementById('searchField').value;
            if (field === 'I_DTime') {
                document.getElementById('inputFields').style.display = 'none';
                document.getElementById('dateRangeFields').style.display = 'block';
            } else {
                document.getElementById('inputFields').style.display = 'block';
                document.getElementById('dateRangeFields').style.display = 'none';
            }
        }

        function toggleStatementFields() {
            const type = document.getElementById('statementType').value;
            const fields = document.getElementById('statementFields');
            fields.innerHTML = '';

            if (type === 'total_amount_range') {
                fields.innerHTML = `
                    <label>Start Date:</label>
                    <input type="date" name="startDate" required>
                    <label>End Date:</label>
                    <input type="date" name="endDate" required>
                `;
                document.getElementById('userFields').style.display = 'block';
            } else if (type === 'avg_monthly'){
                document.getElementById('userFields').style.display = 'block';
            }
            else{
                document.getElementById('userFields').style.display = 'none';
            }
        }
    </script>
</body>
</html>
