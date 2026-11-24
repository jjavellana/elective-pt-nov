<?php
require_once "db.php";

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
            $errorMsg = "Student ID #$new_id is already in use. Please choose a different ID.";
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
            header("Location: index.php?updated=1");
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
    <title>Update Student - Student Records Mini-System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Inter, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: radial-gradient(circle at top left, #1d1d24 0%, #0d0d11 45%, #050507 100%);
            color: #f4f4f5;
        }
        .card {
            width: 100%;
            max-width: 460px;
            background: linear-gradient(145deg, rgba(26, 26, 32, 0.92), rgba(12, 12, 15, 0.96));
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.78), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
        }
        .card-header {
            margin-bottom: 24px;
        }
        h2 {
            font-size: 1.45rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            background: linear-gradient(180deg, #ffffff 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }
        .subtitle {
            font-size: 0.88rem;
            color: #71717a;
        }
        .error-banner {
            margin-bottom: 18px;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 0.86rem;
            background: rgba(244, 63, 94, 0.14);
            color: #fda4af;
            border: 1px solid rgba(244, 63, 94, 0.32);
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #d4d4d8;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 11px 14px;
            background: #0a0a0d;
            border: 1px solid #27272a;
            border-radius: 10px;
            color: #f4f4f5;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #6366f1;
            background: #0e0e13;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        select option {
            background: #121216;
            color: #f4f4f5;
        }
        .btn-primary {
            width: 100%;
            margin-top: 6px;
            padding: 12px 18px;
            background: linear-gradient(180deg, #2e2e38 0%, #18181f 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(180deg, #3b3b47 0%, #22222b 100%);
            border-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .footer-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: #a1a1aa;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-link:hover {
            color: #ffffff;
        }
        .watermark {
            margin-top: 18px;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            color: rgba(161, 161, 170, 0.45);
            text-align: center;
            user-select: none;
            transition: color 0.2s ease;
        }
        .watermark:hover {
            color: rgba(244, 244, 245, 0.75);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>Update Student Record</h2>
            <p class="subtitle">Editing Student ID #<?php echo $original_id; ?></p>
        </div>

        <?php if ($errorMsg !== ""): ?>
            <div class="error-banner"><?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

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
        <a class="footer-link" href="index.php">&larr; Cancel and Go Back</a>
    </div>
    <footer class="watermark">Made by John Paul D. Javellana - BSRTCS 4A</footer>
</body>
</html>