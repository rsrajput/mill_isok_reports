<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle new record submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mill']) && isset($_POST['test_date']) && isset($_FILES['report'])) {
    $mill = $_POST['mill'];
    $test_date = $_POST['test_date'];
    
    // Handle file upload
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = $_FILES['report']['name'] ? time() . '_' . basename($_FILES['report']['name']) : null;
    $filePath = $fileName ? $uploadDir . $fileName : null;
    if ($filePath) {
        move_uploaded_file($_FILES['report']['tmp_name'], $filePath);
    }
    
    $stmt = $pdo->prepare("INSERT INTO mill_tests (mill, test_date, report_path) VALUES (?, ?, ?)");
    $stmt->execute([$mill, $test_date, $filePath]);
    header("Location: dashboard.php");
    exit;
}

// Handle record deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM mill_tests WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: dashboard.php");
    exit;
}

// Fetch mill test data
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$query = "SELECT id, mill, test_date, DATE_ADD(test_date, INTERVAL 6 MONTH) AS next_due_date, report_path FROM mill_tests WHERE mill LIKE ? ORDER BY test_date DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%"]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mill Test Dashboard</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { cursor: pointer; }
        .overdue-yes { background-color: red; color: white; }
        .overdue-no { background-color: green; color: white; }
        .no-report { background-color: lightcoral; }
    </style>
</head>
<body>
    <h1>Mill Test Records</h1>
    
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="mill" required placeholder="Mill Name">
        <input type="date" name="test_date" required>
        <input type="file" name="report" accept="application/pdf">
        <button type="submit">Add Record</button>
    </form>
    
    <form method="get">
        <input type="text" name="search" placeholder="Search by Mill" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>S No</th>
                <th>Mill</th>
                <th>Isokinetic Test Date</th>
                <th>Next Due Date</th>
                <th>Overdue</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $sno = $offset + 1; foreach ($results as $row): ?>
                <tr>
                    <td><?= $sno++ ?></td>
                    <td><?= htmlspecialchars($row['mill']) ?></td>
                    <td>
                        <a href="<?= htmlspecialchars($row['report_path'] ?? '#') ?>" 
                           target="_blank" 
                           class="<?= $row['report_path'] ? '' : 'no-report' ?>">
                           <?= $row['test_date'] ?>
                        </a>
                    </td>
                    <td><?= $row['next_due_date'] ?></td>
                    <td class="<?= strtotime($row['next_due_date']) < time() ? 'overdue-yes' : 'overdue-no' ?>">
                        <?= strtotime($row['next_due_date']) < time() ? 'Yes' : 'No' ?>
                    </td>
                    <td>
                        <a href="edit_record.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="dashboard.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div>
        <a href="?page=<?= max(1, $page - 1) ?>&search=<?= htmlspecialchars($search) ?>">Previous</a>
        <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>">Next</a>
    </div>
    
    <a href="logout.php">Logout</a>
</body>
</html>

