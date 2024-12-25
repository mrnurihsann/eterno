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
    $id = $_GET['id'];  // Menyimpan ID pengguna yang ingin dihapus dari URL ke dalam variabel $id

    // Membuat query SQL untuk menghapus data pengguna berdasarkan ID yang diberikan
    $query = "DELETE FROM users WHERE id = ?";
    
    // Menyiapkan pernyataan (prepared statement) untuk menghindari SQL Injection
    $stmt = $conn->prepare($query);
    
    // Mengikat parameter $id ke dalam pernyataan yang telah disiapkan, tipe data 'i' menunjukkan integer
    $stmt->bind_param("i", $id);

    // Mengeksekusi pernyataan
    if ($stmt->execute()) {
        // Jika eksekusi berhasil, pengguna akan diarahkan ke halaman dashboard.php
        header("Location: dashboard.php");
        exit;  // Menghentikan eksekusi lebih lanjut setelah pengalihan
    } else {
        // Jika terjadi kesalahan dalam eksekusi query, menampilkan pesan kesalahan
        echo "Terjadi kesalahan saat menghapus data.";
    }
} else {
    // Jika parameter 'id' tidak ada pada URL, pengguna akan diarahkan kembali ke halaman dashboard.php
    header("Location: dashboard.php");
    exit;  // Menghentikan eksekusi lebih lanjut setelah pengalihan
}
?>
