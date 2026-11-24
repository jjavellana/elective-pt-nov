<?php
require_once "db.php";

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: view.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST["student_name"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name === "" || $course === "") {
        die("All fields are required. <a href='edit.php?id=$id'>Go back</a>");
    }

    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $course = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');

    $stmt = mysqli_prepare($conn, "UPDATE students SET name = ?, course = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $course, $id);

    if (!mysqli_stmt_execute($stmt)) {
        die("Update failed: " . mysqli_error($conn));
    }

    header("Location: view.php?updated=1");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT id, name, course FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header("Location: view.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student - Student Records Mini-System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; color: #222; }
        .card { border: 1px solid #ddd; border-radius: 6px; padding: 24px; background: #fff; }
        h2 { margin-top: 0; color: #0b3b75; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 18px; background: #0b3b75; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #082b56; }
        .nav-link { display: inline-block; margin-top: 16px; color: #0b3b75; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Update Student Record</h2>
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars_decode($student['name'], ENT_QUOTES); ?>" required>
            </div>
            <div class="form-group">
                <label for="course">Course</label>
                <select id="course" name="course" required>
                    <option value="BSCS" <?php echo ($student['course'] === 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                    <option value="BSIT" <?php echo ($student['course'] === 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                    <option value="BSRTCS" <?php echo ($student['course'] === 'BSRTCS') ? 'selected' : ''; ?>>BSRTCS</option>
                </select>
            </div>
            <button type="submit">Save Changes</button>
        </form>
        <a class="nav-link" href="view.php">&larr; Cancel and Go Back</a>
    </div>
</body>
</html>
