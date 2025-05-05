<?php
session_start();
include 'loginCheck.php';
	?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="sql.css" />

</head>
<body>
    <form action="#" method="post" class="container" id="">
        <label for="sqlcomm"><strong>Type Your Sql and Run</strong></label>
        <br>
            <textarea 
            autocomplete="on"
            name="sqlcomm" 
            class="table_column" 
            id="sqlcomm" 
            style="width: 500px; height: 200px; resize: none;"
        ></textarea>
                    <input type="submit" class="submit_button" value="Submit">
                    <td><a id='nav-home' href='index.php'>Logout</a></td>

                    <a style="text-decoration: none; padding: 10px 20px; background-color: #dc3545; color: #fff; border-radius: 5px;" href='search.php'>Search and Statement</a>
    </form>
    <table>
            <?php
if ($_POST){
    $sql = $_POST['sqlcomm'];
$servername = "sql1.njit.edu";
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$dbname = $_SESSION['db'];
$con = mysqli_connect($servername,$username,$password,$dbname);
$result = mysqli_query($con, $sql);
if (mysqli_connect_errno())
{
	echo "connect fail";
die( mysqli_connect_error() );
}else{
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
}}
?>
</table>
</body>
</html>