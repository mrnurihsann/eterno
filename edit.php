<?php
// Memulai session untuk melacak status login pengguna
session_start();

// Mengecek apakah pengguna sudah login sebagai admin
// Jika tidak ada session 'role' atau role bukan 'admin', maka pengguna akan diarahkan ke halaman index.php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");  // Mengarahkan ke halaman index.php jika pengguna bukan admin
    exit;  // Menghentikan eksekusi lebih lanjut setelah pengalihan
}

// Menyertakan file koneksi ke database
require 'db.php';

// Mengecek apakah ada parameter 'id' pada URL (menggunakan metode GET)
if (isset($_GET['id'])) {
    $id = $_GET['id'];  // Mengambil ID pengguna yang ingin diedit dari URL

    // Rentan terhadap SQL Injection: Query ini langsung menggabungkan ID dalam query tanpa pengamanan
    $query = "SELECT * FROM users WHERE id = $id";  // Query SQL untuk mengambil data pengguna berdasarkan ID
    $result = $conn->query($query);  // Menjalankan query
    $user = $result->fetch_assoc();  // Mengambil hasil query dan menyimpannya dalam bentuk array asosiatif

    // Mengecek apakah pengguna dengan ID tersebut ditemukan
    if (!$user) {
        echo "User tidak ditemukan.";  // Jika tidak ada pengguna dengan ID tersebut, tampilkan pesan error
        exit;  // Menghentikan eksekusi jika pengguna tidak ditemukan
    }
}

// Mengecek jika ada request POST (formulir telah disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari formulir yang disubmit
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Rentan terhadap SQL Injection: Query ini langsung menggabungkan input pengguna tanpa pengamanan
    $query = "UPDATE users SET nama = '$nama', username = '$username', email = '$email', role = '$role' WHERE id = $id";  // Query SQL untuk memperbarui data pengguna

    // Mengeksekusi query untuk memperbarui data pengguna
    if ($conn->query($query)) {
        header("Location: dashboard.php");  // Jika update berhasil, mengalihkan ke halaman dashboard
        exit;  // Menghentikan eksekusi setelah pengalihan
    } else {
        echo "Terjadi kesalahan saat memperbarui data.";  // Jika terjadi kesalahan saat update, tampilkan pesan error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <!-- Menyertakan link CSS Bootstrap untuk desain yang responsif dan menarik -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style/edit.css">  <!-- Link ke file CSS tambahan -->
</head>
<body>
    <!-- Membuat layout menggunakan Bootstrap untuk menampilkan form dengan posisi di tengah halaman -->
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 rounded" style="max-width: 500px; width: 100%;">
            <h2 class="text-center mb-4">Edit User</h2>
            <form method="POST">  <!-- Formulir yang akan mengirim data dengan metode POST -->
                <!-- Input untuk nama pengguna -->
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $user['nama'] ?>" required>
                </div>
                <!-- Input untuk username pengguna -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
                </div>
                <!-- Input untuk email pengguna -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
                </div>
                <!-- Dropdown untuk memilih role pengguna -->
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <!-- Menentukan opsi role yang dipilih sesuai data pengguna yang ada -->
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <!-- Tombol submit untuk memperbarui data pengguna -->
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </form>
        </div>
    </div>
    
    <!-- Menyertakan JavaScript Bootstrap untuk interaktivitas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
