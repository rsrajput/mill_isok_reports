<?php
include 'header.php';
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Handle role updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['role'])) {
    $userId = $_POST['user_id'];
    $role = $_POST['role'];

    if ($userId != $_SESSION['user_id']) { // Prevent admin from changing their own role
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $userId]);
        $_SESSION['flash_message'] = "User role updated successfully.";
    }
    header("Location: admin.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = $_GET['delete'];

    if ($userId != $_SESSION['user_id']) { // Prevent admin from deleting themselves
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['flash_message'] = "User deleted successfully.";
    }
    header("Location: admin.php");
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY username ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        /* .btn { padding: 5px 10px; border-radius: 5px; text-decoration: none; cursor: pointer; border: none; } */
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-update { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; color: white; }

        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-add {
            background-color: #28a745;
            color: white;
        }
        .dashboard-heading {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            color: #007bff;
            text-transform: uppercase;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
    <h1 class="dashboard-heading">User Management</h1>
        <a href="dashboard.php" class="btn btn-add">Back to Dashboard</a>
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <p style="color: green;"><?= $_SESSION['flash_message'] ?></p>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

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
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role">
                                    <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                                    <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-update">Update</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="admin.php?delete=<?= $user['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php else: ?>
                                <span>(Cannot delete yourself)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
