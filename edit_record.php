<?php include 'header.php'; ?>

<?php
//session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if record ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid record ID.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM mill_tests WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    die("Record not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mill = $_POST['mill'];
    $test_date = $_POST['test_date'];
    $filePath = $record['report_path'];

    // Handle file upload
    if (!empty($_FILES['report']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['report']['name']);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['report']['tmp_name'], $filePath);
    }
    
    $stmt = $pdo->prepare("UPDATE mill_tests SET mill = ?, test_date = ?, report_path = ? WHERE id = ?");
    $stmt->execute([$mill, $test_date, $filePath, $id]);
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Mill Test Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-save {
            background-color: #28a745;
            color: white;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Mill Test Record</h2>
        <form method="post" enctype="multipart/form-data">
            <label>Mill Name:</label>
            <input type="text" name="mill" value="<?= htmlspecialchars($record['mill']) ?>" required>
            <br>
            <label>Isokinetic Test Date:</label>
            <input type="date" name="test_date" value="<?= $record['test_date'] ?>" required>
            <br>
            <label>Upload New Report (optional):</label>
            <input type="file" name="report" accept="application/pdf">
            <br>
            <?php if ($record['report_path']): ?>
                <p>Current Report: <a href="<?= $record['report_path'] ?>" target="_blank">View Report</a></p>
            <?php endif; ?>
            <br>
            <button type="submit" class="btn btn-save">Save Changes</button>
            <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
