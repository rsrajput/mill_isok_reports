<?php
session_start();
require 'config.php';

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: dashboard.php");
    exit;
}

// Handle admin toggle
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $userId = $_GET['toggle_admin'];
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user) {
        $newStatus = $user['is_admin'] ? 0 : 1;
        $updateStmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $userId]);
    }
    header("Location: admin.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = $_GET['delete'];
    if ($userId != $_SESSION['user_id']) { // Prevent self-deletion
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->execute([$userId]);
    }
    header("Location: admin.php");
    exit;
}

// Fetch users
$stmt = $pdo->query("SELECT id, username, is_admin FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-admin { background-color: #28a745; color: white; }
        .btn-delete { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= $user['is_admin'] ? 'Admin' : 'User' ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="admin.php?toggle_admin=<?= $user['id'] ?>" class="btn btn-admin">
                                    <?= $user['is_admin'] ? 'Remove Admin' : 'Make Admin' ?>
                                </a>
                                <a href="admin.php?delete=<?= $user['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php else: ?>
                                <span>(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
