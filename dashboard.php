<?php include 'header.php'; ?>

<?php
require 'config.php';
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

// Handle new record submission
if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['mill']) &&
    isset($_POST['test_date']) &&
    isset($_FILES['report'])
) {
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

$query = "SELECT id, mill, test_date, DATE_ADD(test_date, INTERVAL 6 MONTH) AS next_due_date, report_path FROM mill_tests ORDER BY test_date DESC";
$stmt = $pdo->query($query);
#$stmt->execute(["%$search%"]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mill Test Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .logout {
            position: absolute;
            top: 10px;
            right: 20px;
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        .overdue {
            padding: 5px;
            border-radius: 5px;
            color: white;
            display: inline-block;
        }
        .overdue.yes {
            background-color: red;
        }
        .overdue.no {
            background-color: green;
        }
        .no-report {
            background-color: lightcoral;
            padding: 5px;
            border-radius: 5px;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
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
        #searchBox {
            margin-bottom: 10px;
            padding: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-admin {
            background-color: #ff9800;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }

    </style>
    <script>
        function sortTable(n) {
            let table = document.getElementById("millTable");
            let rows = Array.from(table.rows).slice(1);
            let ascending = table.rows[0].cells[n].getAttribute("data-order") !== "asc";
            rows.sort((rowA, rowB) => {
                let cellA = rowA.cells[n].innerText.trim().toLowerCase();
                let cellB = rowB.cells[n].innerText.trim().toLowerCase();
                return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });
            rows.forEach(row => table.appendChild(row));
            table.rows[0].cells[n].setAttribute("data-order", ascending ? "asc" : "desc");
        }

        function searchTable() {
            let input = document.getElementById("searchBox").value.toLowerCase();
            let rows = document.querySelectorAll("#millTable tbody tr");
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="change_password.php" class="btn btn-add">Change Password</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="admin.php" class="btn btn-admin">Admin Panel</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="logout">Logout</a>
        <?php else: ?>
            <a href="login.php" class="logout">Login</a>
        <?php endif; ?>
        <h1>Mill Isokinetic Test Records</h1>
 
         <form method="post" enctype="multipart/form-data">
             <input type="text" name="mill" required placeholder="Mill Name">
             <input type="date" name="test_date" required>
             <input type="file" name="report" accept="application/pdf">
             <button type="submit" class="btn btn-add">Add Record</button>
         </form>
        <input type="text" id="searchBox" onkeyup="searchTable()" placeholder="Search records...">
        <table id="millTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">S No</th>
                    <th onclick="sortTable(1)">Mill</th>
                    <th onclick="sortTable(2)">Isokinetic Test Date</th>
                    <th onclick="sortTable(3)">Next Due Date</th>
                    <th onclick="sortTable(4)">Overdue</th>
                    <?php if (isset($_SESSION['user_id'])): ?> 
                        <th>Actions</th>
                    <?php endif; ?>
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
                        <td class="overdue <?= strtotime($row['next_due_date']) < time() ? 'yes' : 'no' ?>">
                            <?= strtotime($row['next_due_date']) < time() ? 'Yes' : 'No' ?>
                        </td>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <td>
                                <a href="edit_record.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="dashboard.php?delete=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
