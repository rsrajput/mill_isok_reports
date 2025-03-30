<?php
session_start();
require 'config.php'; // Database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: admin.php");
    exit();
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, is_admin FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle admin toggle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $is_admin = $_POST['is_admin'] == 1 ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
    $stmt->execute([$is_admin, $user_id]);
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; }
        .btn-admin { background-color: #28a745; color: white; }
        .btn-remove { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <h2>Admin Panel - Manage Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user) { ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="is_admin" value="<?php echo $user['is_admin']; ?>">
                        <button type="submit" class="btn <?php echo $user['is_admin'] ? 'btn-remove' : 'btn-admin'; ?>">
                            <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
