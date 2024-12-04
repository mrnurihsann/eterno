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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style/profile.css">
</head>
<body>
    <div class="container mt-5">
        <div class="profile-header">
            <img src="./images/user.webp" alt="User Avatar">
            <h2 class="mt-3"><?= $user['nama']; ?></h2>
            <p class="mb-0">@<?= $user['username']; ?></p>
        </div>
        <br>
        <div class="card mx-auto mt-5 profile-card" style="max-width: 600px;">
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
                </table>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <a href="./chat/chat.php" class="btn btn-success">
                        <i class="fas fa-comments"></i> Chat
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
