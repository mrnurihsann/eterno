<?php
// Menentukan parameter koneksi ke database
$host = "localhost";         // Nama host untuk database, biasanya 'localhost' jika database berada di server yang sama dengan aplikasi.
$username = "root";          // Nama pengguna yang digunakan untuk login ke MySQL, 'root' adalah nama pengguna default di banyak instalasi MySQL.
$password = "";              // Kata sandi untuk login ke MySQL, kosong di sini karena 'root' biasanya tidak memiliki kata sandi secara default di lokal.
$database = "leggero";       // Nama database yang akan digunakan oleh aplikasi. Di sini, nama databasenya adalah 'leggero'.

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Mengecek apakah koneksi berhasil
if ($conn->connect_error) {
    // Jika koneksi gagal, menampilkan pesan error dan menghentikan eksekusi script
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
