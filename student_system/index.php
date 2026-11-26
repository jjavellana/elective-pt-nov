<?php
require_once "db.php";

$sql = "SELECT * FROM students ORDER BY StudentID ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
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
            max-width: 820px;
            background: linear-gradient(145deg, rgba(26, 26, 32, 0.92), rgba(12, 12, 15, 0.96));
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.78), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            background: linear-gradient(180deg, #ffffff 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .subtitle {
            font-size: 0.85rem;
            color: #71717a;
            margin-top: 4px;
        }
        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            background: linear-gradient(180deg, #2e2e38 0%, #18181f 100%);
            color: #ffffff;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            transition: all 0.2s ease;
        }
        .btn-add:hover {
            background: linear-gradient(180deg, #3b3b47 0%, #22222b 100%);
            border-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        }
        .table-wrapper {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            overflow-x: auto;
            background: rgba(9, 9, 12, 0.65);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        th {
            background: linear-gradient(180deg, #1e1e25 0%, #131318 100%);
            color: #a1a1aa;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tbody tr {
            transition: background 0.18s ease;
        }
        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        .id-cell {
            color: #71717a;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 0.88rem;
        }
        .course-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            background: rgba(255, 255, 255, 0.06);
            color: #e4e4e7;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        .btn-action {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.18s ease;
        }
        .action-edit {
            background: rgba(56, 189, 248, 0.12);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.25);
        }
        .action-edit:hover {
            background: rgba(56, 189, 248, 0.22);
            border-color: rgba(56, 189, 248, 0.45);
            color: #bae6fd;
            transform: translateY(-1px);
        }
        .action-delete {
            background: rgba(244, 63, 94, 0.12);
            color: #fda4af;
            border: 1px solid rgba(244, 63, 94, 0.25);
        }
        .action-delete:hover {
            background: rgba(244, 63, 94, 0.22);
            border-color: rgba(244, 63, 94, 0.45);
            color: #fecdd3;
            transform: translateY(-1px);
        }
        .empty-state {
            text-align: center;
            padding: 32px 16px;
            color: #71717a;
            font-size: 0.92rem;
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

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 280px;
            max-width: 380px;
            padding: 14px 18px;
            background: linear-gradient(145deg, #1f1f26, #111115);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            color: #f4f4f5;
            font-size: 0.9rem;
            animation: slideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast.hide {
            animation: slideOut 0.3s ease forwards;
        }
        .toast-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .toast-success .toast-dot {
            background: #34d399;
            box-shadow: 0 0 10px rgba(52, 211, 153, 0.7);
        }
        .toast-danger .toast-dot {
            background: #fb7185;
            box-shadow: 0 0 10px rgba(251, 113, 133, 0.7);
        }
        .toast-close {
            margin-left: auto;
            background: none;
            border: none;
            color: #71717a;
            font-size: 1.1rem;
            cursor: pointer;
            line-height: 1;
        }
        .toast-close:hover {
            color: #ffffff;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-14px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes slideOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-10px) scale(0.96); }
        }
    </style>
</head>
<body>
    <?php
    $toastMsg = "";
    $toastType = "toast-success";
    if (isset($_GET['added'])) {
        $toastMsg = "Student record added successfully.";
    } elseif (isset($_GET['updated'])) {
        $toastMsg = "Student record updated successfully.";
    } elseif (isset($_GET['deleted'])) {
        $toastMsg = "Student record deleted.";
        $toastType = "toast-danger";
    }
    ?>

    <?php if ($toastMsg !== ""): ?>
        <div id="toast" class="toast <?php echo $toastType; ?>">
            <span class="toast-dot"></span>
            <span><?php echo $toastMsg; ?></span>
            <button type="button" class="toast-close" onclick="closeToast()">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="header-bar">
            <div>
                <h2>Student Records</h2>
                <p class="subtitle">Manage enrolled students and course assignments.</p>
            </div>
            <a class="btn-add" href="form.html">+ Add Student</a>
        </div>

        <div class="table-wrapper">
            <table border="1" cellpadding="8">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="id-cell">#<?php echo $row['StudentID']; ?></td>
                            <td><?php echo htmlspecialchars($row['Name']); ?></td>
                            <td><span class="course-badge"><?php echo htmlspecialchars($row['Course']); ?></span></td>
                            <td>
                                <div class="actions">
                                    <a class="btn-action action-edit" href="edit.php?id=<?php echo $row['StudentID']; ?>">Update</a>
                                    <a class="btn-action action-delete" href="delete.php?id=<?php echo $row['StudentID']; ?>"
                                       onclick="return confirm('Delete this student?')">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="empty-state">No student records found. Click "+ Add Student" to create one.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="watermark">Made by John Paul D. Javellana - BSRTCS 4A</footer>

    <script>
        const toast = document.getElementById('toast');
        if (toast) {
            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            setTimeout(() => closeToast(), 3500);
        }
        function closeToast() {
            if (!toast) return;
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }
    </script>
</body>
</html>