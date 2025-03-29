<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle new record submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mill']) && isset($_POST['test_date'])) {
    $mill = $_POST['mill'];
    $test_date = $_POST['test_date'];
    $stmt = $pdo->prepare("INSERT INTO mill_tests (mill, test_date) VALUES (?, ?)");
    $stmt->execute([$mill, $test_date]);
    header("Location: dashboard.php");
    exit;
}

// Fetch mill test data
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$query = "SELECT id, mill, test_date, DATE_ADD(test_date, INTERVAL 6 MONTH) AS next_due_date FROM mill_tests WHERE mill LIKE ? ORDER BY test_date DESC LIMIT $limit OFFSET $offset";
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
        .overdue-yes { background-color: #FFB6C1; color: #000000; }
        .overdue-no { background-color: #9AFF9A; color: #000000; }
    </style>
</head>
<body>
    <h1>Mill Test Records</h1>
    
    <form method="post">
        <input type="text" name="mill" required placeholder="Mill Name">
        <input type="date" name="test_date" required>
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
            </tr>
        </thead>
        <tbody>
            <?php $sno = $offset + 1; foreach ($results as $row): ?>
                <tr>
                    <td><?= $sno++ ?></td>
                    <td><?= htmlspecialchars($row['mill']) ?></td>
                    <td><?= $row['test_date'] ?></td>
                    <td><?= $row['next_due_date'] ?></td>
                    <td class="<?= strtotime($row['next_due_date']) < time() ? 'overdue-yes' : 'overdue-no' ?>">
                        <?= strtotime($row['next_due_date']) < time() ? 'Yes' : 'No' ?>
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
