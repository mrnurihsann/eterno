<?php
// Memulai session untuk mengakses data sesi pengguna yang sedang aktif
session_start();

// Menghancurkan sesi yang sedang aktif, menghapus semua data sesi yang ada
session_destroy();

// Mengalihkan pengguna ke halaman index.php setelah berhasil logout
header("Location: index.php");

// Menghentikan eksekusi lebih lanjut setelah pengalihan halaman
exit;
?>
