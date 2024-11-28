<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit;
}
require 'db.php';

// Fetch user profile data
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Profile</h1>
        <p class="text-center">Selamat datang, <?= $user['nama']; ?>!</p>
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h5 class="card-title text-center">Informasi Profil</h5>
                <table class="table">
                    <tr>
                        <th>Nama</th>
                        <td><?= $user['nama'] ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?= $user['username'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $user['email'] ?></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?= $user['role'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
         <div class="d-flex justify-content-center mt-3">
            <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
