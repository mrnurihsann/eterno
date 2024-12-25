<?php
// Memulai sesi untuk mengakses data yang tersimpan pada sesi pengguna yang sedang login
session_start();

// Mengecek apakah sesi 'role' sudah ada dan apakah role pengguna adalah 'user' 
// Jika tidak, maka pengguna akan dialihkan ke halaman index.php (halaman login atau beranda)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php"); // Pengalihan ke halaman index.php
    exit; // Menghentikan eksekusi kode lebih lanjut
}

// Mengimpor koneksi ke database yang sudah dibuat pada file db.php
require 'db.php';

// Mengambil nama pengguna dari sesi dan menggunakan query untuk mengambil data profil pengguna
$username = $_SESSION['username']; // Menyimpan username dari sesi
$query = "SELECT * FROM users WHERE username = '$username'"; // Query untuk mengambil data pengguna berdasarkan username
$result = $conn->query($query); // Menjalankan query ke database
$user = $result->fetch_assoc(); // Mengambil hasil query dan menyimpannya dalam array asosiatif $user
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>
    <!-- Mengimpor file CSS Bootstrap untuk desain antarmuka yang responsif dan menarik -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mengimpor font-awesome untuk ikon-ikon yang digunakan dalam aplikasi -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Mengimpor file CSS khusus untuk halaman profil -->
    <link rel="stylesheet" href="./style/profile.css">
</head>
<body>
    <div class="container mt-5">
        <div class="profile-header">
            <!-- Menampilkan gambar avatar pengguna -->
            <img src="./images/user.webp" alt="User Avatar">
            <h2 class="mt-3"><?= $user['nama']; ?></h2> <!-- Menampilkan nama pengguna dengan potensi kerentanan XSS -->
            <p class="mb-0">@<?= $user['username']; ?></p> <!-- Menampilkan username pengguna dengan potensi kerentanan XSS -->
        </div>
        <br>
        <div class="card mx-auto mt-5 profile-card" style="max-width: 600px;">
            <div class="card-body">
                <h5 class="card-title text-center">Informasi Profil</h5>
                <!-- Menampilkan informasi profil pengguna dalam tabel -->
                <table class="table">
                    <tr>
                        <th>Nama</th>
                        <td><?= $user['nama'] ?></td> <!-- Menampilkan nama pengguna dengan potensi kerentanan XSS -->
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?= $user['username'] ?></td> <!-- Menampilkan username pengguna dengan potensi kerentanan XSS -->
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $user['email'] ?></td> <!-- Menampilkan email pengguna dengan potensi kerentanan XSS -->
                    </tr>
                    <tr>
                        <th>Saldo</th>
                        <td><?= $user['saldo'] ?></td> <!-- Menampilkan saldo pengguna dengan potensi kerentanan XSS -->
                    </tr>
                </table>
                
                <!-- Tombol untuk logout, top-up dana, dan mengakses fitur chat -->
                <div class="d-flex justify-content-between mt-4">
                    <!-- Tombol untuk logout, mengarah ke halaman logout.php -->
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <!-- Tombol untuk melakukan top-up dana, mengarah ke halaman topup.php -->
                    <a href="./topup/topup.php" class="btn btn-primary">
                        <i class="fas fa-wallet"></i> Top Up Dana
                    </a>
                    <!-- Tombol untuk membuka fitur chat, mengarah ke halaman chat.php -->
                    <a href="./chat/chat.php" class="btn btn-success">
                        <i class="fas fa-comments"></i> Chat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mengimpor file JavaScript Bootstrap untuk fungsionalitas antarmuka -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Mengimpor file JavaScript font-awesome untuk ikon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
