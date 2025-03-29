<?php
// config.php
$host = 'localhost';
$dbname = 'mill_tests';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!-- register.php -->
<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);

    echo "Registration successful. <a href='login.php'>Login here</a>";
}
?>
<form method="post">
    <input type="text" name="username" required placeholder="Username">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Register</button>
</form>

<!-- login.php -->
<?php
require 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid credentials";
    }
}
?>
<form method="post">
    <input type="text" name="username" required placeholder="Username">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
</form>

<!-- dashboard.php -->
<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
        .overdue-yes { background-color: red; color: white; }
        .overdue-no { background-color: green; color: white; }
    </style>
</head>
<body>
    <h1>Mill Test Records</h1>
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

<!-- logout.php -->
<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>

<!-- Database: MySQL Schema -->
CREATE DATABASE mill_tests;
USE mill_tests;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE mill_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mill VARCHAR(255) NOT NULL,
    test_date DATE NOT NULL
);
