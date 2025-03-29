<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $username = $user['username'] ?? 'User';
}
?>
<div class="topbar">
    <?php if (isset($_SESSION['user_id'])): ?>
        <span class="username">Welcome, <?= htmlspecialchars($username) ?></span>
        <a href="logout.php" class="btn-logout">Logout</a>
    <?php endif; ?>
</div>

<style>
.topbar {
    background: #333;
    color: white;
    padding: 10px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.username {
    margin-right: 15px;
    font-weight: bold;
}
.btn-logout {
    background: red;
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
}
.btn-logout:hover {
    background: darkred;
}
</style>
