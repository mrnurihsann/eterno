<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
require 'db.php';

// Fetch all admin data
$query = "SELECT * FROM users WHERE role = 'user'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="./style/dashboard.css">
</head>
<body class="light-theme">
    <header>
        <div class="theme-toggler p-2">
            <span class="material-icons-sharp active">light_mode</span>
            <span class="material-icons-sharp">dark_mode</span>
        </div>
    </header>
    <div class="container mt-5">
        <h1 class="text-center">Dashboard Admin</h1>
        <p class="text-center">Selamat datang, Admin!</p>
         
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['role'] ?></td>
                        <td>
                             <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
         <div class="d-flex justify-content-center">
            <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/script.js"></script>
</body>
</html>
