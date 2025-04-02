<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        $success = "Password changed successfully.";
        // Set a flash message for success
        $_SESSION['flash_message'] = "Password changed successfully!";
        header("Location: dashboard.php");
        exit;

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            width: 100%;
            margin-top: 10px;
            color: white;
            text-align: center;
            transition: 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"> <?= $error ?> </p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p style="color: green;"> <?= $success ?> </p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="password" name="current_password" placeholder="Current Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="new_password" placeholder="New Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <button type="submit" class="btn">Change Password</button>
        </form>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

</body>
</html>
