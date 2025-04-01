<?php
include 'header.php';
require 'config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// Handle make admin/remove admin
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $user_id = $_GET['toggle_admin'];
    $stmt = $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: admin.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    if ($user_id != $_SESSION['user_id']) { // Prevent self-deletion
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }
    header("Location: admin.php");
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, is_admin FROM users");
$users = $stmt->fetchAll();
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
    <style>
    .btn-dashboard {
        background-color: #007bff;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
    }

    .btn-dashboard:hover {
        background-color: #0056b3;
    }
    </style>
</head>


<body>
    <div class="container">
        <h1>User Administration</h1>
        <a href="dashboard.php" class="btn btn-dashboard">Dashboard</a>

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
                            <a href="admin.php?toggle_admin=<?= $user['id'] ?>" class="btn btn-toggle">
                            <button type="submit" class="btn <?php echo $user['is_admin'] ? 'btn-remove' : 'btn-admin'; ?>">
                                <?= $user['is_admin'] ? 'Remove Admin' : 'Make Admin' ?></button>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="admin.php?delete_user=<?= $user['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                    Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>

</body>
</html>
