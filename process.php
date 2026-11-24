<?php
require_once "db.php";

$name = trim($_POST["student_name"] ?? "");
$course = trim($_POST["course"] ?? "");

if ($name === "" || $course === "") {
    die("All fields are required. <a href='form.html'>Go back</a>");
}

$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$course = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');

$stmt = mysqli_prepare($conn, "INSERT INTO students (name, course) VALUES (?, ?)");

mysqli_stmt_bind_param($stmt, "ss", $name, $course);

if (!mysqli_stmt_execute($stmt)) {
    die("Insert failed: " . mysqli_error($conn));
}

header("Location: view.php?added=1");
exit();
?>
