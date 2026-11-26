<?php
require_once "db.php";

if ($db_error !== "") {
    header("Location: index.php?msg=db_error");
    exit();
}

$original_id = intval($_GET['id'] ?? $_POST['original_id'] ?? 0);

if ($original_id <= 0) {
    header("Location: index.php");
    exit();
}

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_id = intval($_POST["new_student_id"] ?? 0);
    $name = trim($_POST["student_name"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($new_id <= 0 || $name === "" || $course === "") {
        $errorMsg = "Student ID, Name, and Course are all required.";
    } elseif ($new_id !== $original_id) {
        $checkStmt = mysqli_prepare($conn, "SELECT StudentID FROM students WHERE StudentID = ?");
        mysqli_stmt_bind_param($checkStmt, "i", $new_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        if (mysqli_fetch_assoc($checkResult)) {
            $errorMsg = "Student ID $new_id is already taken. Please choose another ID.";
        }
    }

    if ($errorMsg === "") {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeCourse = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');

        $stmt = mysqli_prepare($conn, "UPDATE students SET StudentID = ?, Name = ?, Course = ? WHERE StudentID = ?");
        mysqli_stmt_bind_param($stmt, "issi", $new_id, $safeName, $safeCourse, $original_id);

        if (!mysqli_stmt_execute($stmt)) {
            $errorMsg = "Update failed: " . mysqli_error($conn);
        } else {
            header("Location: index.php?msg=updated");
            exit();
        }
    }

    $student = [
        'StudentID' => $new_id > 0 ? $new_id : $original_id,
        'Name' => $name,
        'Course' => $course
    ];
} else {
    $stmt = mysqli_prepare($conn, "SELECT StudentID, Name, Course FROM students WHERE StudentID = ?");
    mysqli_stmt_bind_param($stmt, "i", $original_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        header("Location: index.php");
        exit();
    }
    $student['Name'] = htmlspecialchars_decode($student['Name'], ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Student Records Mini-System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f1f5f9;
            color: #1e293b;
        }
        .toast-wrapper {
            width: 100%;
            max-width: 440px;
            display: flex;
            justify-content: center;
            margin-bottom: 14px;
        }
        .toast {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #b91c1c;
            border: 1px solid #fecaca;
            background: #fef2f2;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.1);
        }
        .toast-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #dc2626;
            flex-shrink: 0;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px 30px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }
        h2 {
            font-size: 1.4rem;
            color: #0b3b75;
            margin-bottom: 6px;
        }
        .subtitle {
            font-size: 0.88rem;
            color: #64748b;
            margin-bottom: 22px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #0b3b75;
            box-shadow: 0 0 0 3px rgba(11, 59, 117, 0.14);
        }
        .btn-primary {
            width: 100%;
            margin-top: 6px;
            padding: 11px 18px;
            background: #0b3b75;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.15s ease;
        }
        .btn-primary:hover {
            background: #082b56;
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .nav-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #0b3b75;
            text-decoration: none;
        }
        .nav-link:hover {
            text-decoration: underline;
        }
        .watermark {
            margin-top: 16px;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            color: rgba(100, 116, 139, 0.65);
            text-align: center;
            user-select: none;
            transition: color 0.2s ease;
        }
        .watermark:hover {
            color: rgba(15, 23, 42, 0.85);
        }
    </style>
</head>
<body>
    <?php if ($errorMsg !== ""): ?>
        <div class="toast-wrapper">
            <div class="toast">
                <span class="toast-dot"></span>
                <span><?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Update Student Record</h2>
        <p class="subtitle">Editing Student ID #<?php echo $original_id; ?></p>
        <form action="edit.php" method="post">
            <input type="hidden" name="original_id" value="<?php echo $original_id; ?>">
            <div class="form-group">
                <label for="new_student_id">Student ID</label>
                <input type="number" id="new_student_id" name="new_student_id" min="1" value="<?php echo intval($student['StudentID']); ?>" required>
            </div>
            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($student['Name'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="course">Course</label>
                <select id="course" name="course" required>
                    <option value="BSCS" <?php echo ($student['Course'] === 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                    <option value="BSIT" <?php echo ($student['Course'] === 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                    <option value="BSRTCS" <?php echo ($student['Course'] === 'BSRTCS') ? 'selected' : ''; ?>>BSRTCS</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
        <a class="nav-link" href="index.php">&larr; Cancel and Go Back</a>
    </div>
    <footer class="watermark">Made by BSRTCS 4A</footer>
</body>
</html>
