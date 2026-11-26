<?php
require_once "db.php";

$rows = [];
if ($db_error === "") {
    $sql = "SELECT * FROM students ORDER BY StudentID ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    } else {
        $db_error = mysqli_error($conn);
    }
}

$toastMsg = "";
$toastType = "success";
$autoHide = true;

if ($db_error !== "") {
    $toastMsg = "Database not connected (" . $db_error . ")";
    $toastType = "error";
    $autoHide = false;
} elseif (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'db_error') {
        $toastMsg = "Database not connected. Please start MySQL in XAMPP.";
        $toastType = "error";
        $autoHide = false;
    } elseif ($_GET['msg'] === 'added') {
        $toastMsg = "Student added successfully!";
        $toastType = "success";
    } elseif ($_GET['msg'] === 'updated') {
        $toastMsg = "Student updated successfully!";
        $toastType = "success";
    } elseif ($_GET['msg'] === 'deleted') {
        $toastMsg = "Student deleted successfully!";
        $toastType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records - Student Records Mini-System</title>
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
            max-width: 780px;
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
            font-size: 0.9rem;
            font-weight: 600;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.1);
            animation: popDown 0.3s ease forwards;
        }
        .toast.hide {
            animation: fadeUp 0.25s ease forwards;
        }
        .toast-success {
            color: #15803d;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
        }
        .toast-danger, .toast-error {
            color: #b91c1c;
            border: 1px solid #fecaca;
            background: #fef2f2;
        }
        .toast-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .toast-success .toast-dot {
            background: #16a34a;
        }
        .toast-danger .toast-dot, .toast-error .toast-dot {
            background: #dc2626;
        }
        .toast-close {
            margin-left: 6px;
            background: none;
            border: none;
            color: inherit;
            font-size: 1.1rem;
            cursor: pointer;
            opacity: 0.7;
            line-height: 1;
        }
        .toast-close:hover {
            opacity: 1;
        }
        @keyframes popDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-8px); }
        }
        .card {
            width: 100%;
            max-width: 780px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px 30px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 1.45rem;
            color: #0b3b75;
        }
        .subtitle {
            font-size: 0.86rem;
            color: #64748b;
            margin-top: 2px;
        }
        .btn-add {
            padding: 10px 16px;
            background: #0b3b75;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background 0.2s ease, transform 0.15s ease;
        }
        .btn-add:hover {
            background: #082b56;
            transform: translateY(-1px);
        }
        .table-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #0b3b75;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        tbody tr:hover {
            background: #f1f5f9;
        }
        .course-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            background: #e0e7ff;
            color: #1e3a8a;
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        .btn-edit, .btn-delete {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 6px;
            font-size: 0.83rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s ease;
        }
        .btn-edit {
            background: #e0f2fe;
            color: #0369a1;
        }
        .btn-edit:hover {
            background: #bae6fd;
        }
        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
        }
        .btn-delete:hover {
            background: #fecaca;
        }
        .empty-row {
            text-align: center;
            padding: 24px;
            color: #64748b;
            font-size: 0.92rem;
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
    <?php if ($toastMsg !== ""): ?>
        <div class="toast-wrapper">
            <div id="toast" class="toast toast-<?php echo $toastType; ?>">
                <span class="toast-dot"></span>
                <span><?php echo htmlspecialchars($toastMsg, ENT_QUOTES, 'UTF-8'); ?></span>
                <button type="button" class="toast-close" onclick="closeToast()">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="header-bar">
            <div>
                <h2>Student Records</h2>
                <p class="subtitle">View, add, update, or remove student entries.</p>
            </div>
            <a class="btn-add" href="form.html">+ Add Student</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) > 0): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?php echo $row['StudentID']; ?></td>
                                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                <td><span class="course-pill"><?php echo htmlspecialchars($row['Course']); ?></span></td>
                                <td>
                                    <div class="actions">
                                        <a class="btn-edit" href="edit.php?id=<?php echo $row['StudentID']; ?>">Edit</a>
                                        <a class="btn-delete" href="delete.php?id=<?php echo $row['StudentID']; ?>"
                                           onclick="return confirm('Delete this student?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty-row">No student records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="watermark">Made by BSRTCS 4A</footer>

    <script>
        const toast = document.getElementById('toast');
        const autoHide = <?php echo $autoHide ? 'true' : 'false'; ?>;
        if (toast) {
            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            if (autoHide) {
                setTimeout(() => closeToast(), 3500);
            }
        }
        function closeToast() {
            if (!toast) return;
            toast.classList.add('hide');
            setTimeout(() => {
                const wrap = toast.parentElement;
                if (wrap) wrap.remove();
            }, 250);
        }
    </script>
</body>
</html>
