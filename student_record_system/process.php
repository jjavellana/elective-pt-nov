<?php
require_once "db.php";

if ($db_error !== "") {
    header("Location: index.php?msg=db_error");
    exit();
}

$name = trim($_POST["student_name"] ?? "");
$course = trim($_POST["course"] ?? "");

if ($name === "" || $course === "") {
    die("All fields are required. <a href='form.html'>Go back</a>");
}

$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$course = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');

$stmt = mysqli_prepare($conn, "INSERT INTO students (Name, Course) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $name, $course);

if (!mysqli_stmt_execute($stmt)) {
    die("Insert failed: " . mysqli_error($conn));
}

header("Location: index.php?msg=added");
exit();
?>
