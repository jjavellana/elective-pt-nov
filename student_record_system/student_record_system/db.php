<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect("localhost", "root", "", "school_db");
$db_error = "";

if (!$conn) {
    $db_error = mysqli_connect_error() ?: "Unable to connect to MySQL database.";
}
?>
