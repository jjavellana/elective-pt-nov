<?php
require_once "db.php";

$sql = "SELECT id, name, course FROM students ORDER BY id ASC";
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
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 16px; color: #222; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        h2 { margin: 0; color: #0b3b75; }
        .btn-add { padding: 9px 14px; background: #0b3b75; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .btn-add:hover { background: #082b56; }
        .alert { padding: 10px 14px; background: #e6f4ea; color: #1e7e34; border: 1px solid #b7dfbf; border-radius: 4px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; }
        th { background: #0b3b75; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        .action-edit { color: #0b3b75; text-decoration: none; margin-right: 10px; font-weight: bold; }
        .action-delete { color: #c0392b; text-decoration: none; font-weight: bold; }
        .action-edit:hover, .action-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header-bar">
        <h2>Student Records</h2>
        <a class="btn-add" href="form.html">+ Add Student</a>
    </div>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert">Student record added successfully!</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert">Student record updated successfully!</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert">Student record deleted successfully!</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td>
                            <a class="action-edit" href="edit.php?id=<?php echo $row['id']; ?>">Update</a>
                            <a class="action-delete" href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this student record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No student records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
