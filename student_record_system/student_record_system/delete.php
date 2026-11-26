<?php
require_once "db.php";

if ($db_error !== "") {
    header("Location: index.php?msg=db_error");
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE StudentID = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php?msg=deleted");
exit();
?>
