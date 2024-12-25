<?php
// Memulai session untuk menyimpan informasi sesi pengguna
session_start();
// Mengimpor file koneksi database untuk dapat berinteraksi dengan database
require '../db.php';

// Mengecek apakah pengguna sudah login dan memiliki role sebagai 'user' 
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    // Jika belum login atau bukan user, arahkan kembali ke halaman index.php
    header("Location: index.php");
    exit;
}

// Variabel untuk menyimpan pesan yang akan ditampilkan ke pengguna
$message = "";
// Mengecek apakah form telah disubmit (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mendapatkan id pengirim dari session
    $pengirim_id = $_SESSION['id']; 
    // Mengambil jenis transaksi yang dipilih pengguna
    $jenis_transaksi = $_POST['jenis_transaksi']; 
    // Mengambil username penerima yang dimasukkan pengguna (ini rentan terhadap SQL Injection)
    $username_penerima = $_POST['username'];  
    // Mengambil jumlah dana yang akan ditransfer/top-up
    $jumlah = (float) $_POST['jumlah'];
    // Mengambil catatan (opsional) dari pengguna
    $catatan = $_POST['catatan'];  

    // Rentan terhadap SQL Injection karena input pengguna langsung dimasukkan ke dalam query
    $result = $conn->query("SELECT id, saldo FROM users WHERE username = '$username_penerima'");
    // Jika username penerima ditemukan di database
    if ($result->num_rows > 0) {
        // Mengambil data penerima (id dan saldo)
        $row = $result->fetch_assoc();
        $penerima_id = $row['id'];
        $saldo_penerima = $row['saldo'];

        // Ambil saldo pengirim berdasarkan id pengirim
        $result_pengirim = $conn->query("SELECT saldo FROM users WHERE id = $pengirim_id");
        // Jika saldo pengirim ditemukan
        if ($result_pengirim->num_rows > 0) {
            $pengirim_data = $result_pengirim->fetch_assoc();
            $saldo_pengirim = $pengirim_data['saldo'];

            // Jika jenis transaksi adalah top-up
            if ($jenis_transaksi === 'topup') {
                // Rentan terhadap SQL Injection di query INSERT
                $stmt = $conn->prepare("INSERT INTO topup (pengirim_id, penerima_id, jumlah, catatan, status) VALUES ('$pengirim_id', '$penerima_id', '$jumlah', '$catatan', 'pending')");
                $stmt->execute();  // Eksekusi query

                // Rentan terhadap XSS karena menampilkan pesan tanpa sanitasi
                $message = "Permintaan top-up sebesar Rp " . number_format($jumlah, 2) . " telah dikirim dan menunggu persetujuan admin. <script>alert('XSS Attack');</script>";
            } elseif ($jenis_transaksi === 'transfer') {
                // Rentan terhadap SQL Injection: Langsung menggunakan input pengguna di dalam query
                if ($saldo_pengirim >= $jumlah) {
                    // Mengurangi saldo pengirim jika transfer berhasil
                    $conn->query("UPDATE users SET saldo = saldo - $jumlah WHERE id = $pengirim_id");

                    // Menambahkan saldo penerima jika transfer berhasil
                    $conn->query("UPDATE users SET saldo = saldo + $jumlah WHERE id = $penerima_id");

                    // Query INSERT rentan terhadap SQL Injection
                    $stmt = $conn->prepare("INSERT INTO transfer (pengirim_id, penerima_id, jumlah, catatan) VALUES ('$pengirim_id', '$penerima_id', '$jumlah', '$catatan')");
                    $stmt->execute();  // Eksekusi query

                    // Rentan terhadap XSS: Menampilkan pesan tanpa sanitasi
                    $message = "Transfer berhasil dikirim ke $username_penerima sebesar Rp " . number_format($jumlah, 2) . "<script>alert('XSS Attack');</script>";
                } else {
                    // Jika saldo pengirim tidak mencukupi
                    $message = "Saldo tidak mencukupi untuk melakukan transfer.";
                }
            }
        } else {
            // Jika saldo pengirim tidak ditemukan
            $message = "Saldo pengirim tidak ditemukan.";
        }
    } else {
        // Jika username penerima tidak ditemukan
        $message = "Username penerima tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANA Top-Up</title>
    <!-- Mengimpor Bootstrap untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mengimpor FontAwesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        <?php include '../style/topup.css'; ?>
    </style>
</head>

<body>
    <div class="container">
        
        <header class="header">
            <button onclick="history.back()" class="back-button">Kembali</button>
            <div class="refresh-icon">🔄</div>
        </header>

        <div class="top-section">
            <img src="../images/danalogo.png" alt="DANA Logo" class="dana-logo">
            <div class="transfer-animation">
                <div class="circle">
                    <img src="../images/dana.png" alt="User Icon" class="user-logo">
                </div>
                <img src="../images/arrow.webp" alt="Arrow Icon" class="arrow-logo">
            </div>
        </div>

       <div class="content">
            <h2>Transaksi</h2>
            <p>Pilih jenis transaksi, masukkan informasi penerima, dan jumlah dana.</p>

            <!-- Formulir transaksi -->
            <form method="POST">
                <?php if ($message): ?>
                    <!-- Menampilkan pesan jika ada -->
                    <div class="alert"><?= $message ?></div>
                <?php endif; ?>

                <div class="input-group">
                    <label for="jenis_transaksi">Jenis Transaksi: </label>
                    <select name="jenis_transaksi" id="jenis_transaksi" required>
                        <option value="topup">Top-Up           </option>
                        <option value="transfer">Transfer       </option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="username">Username Penerima: </label>
                    <input type="text" name="username" id="username" placeholder="Masukkan username penerima" required>
                </div>

                <div class="input-group">
                    <label for="jumlah">Jumlah (Rp):</label>
                    <input type="number" name="jumlah" id="jumlah" placeholder="Masukkan jumlah dana" required>
                </div>

                <div class="input-group">
                    <label for="catatan">Catatan (Opsional):</label>
                    <textarea name="catatan" id="catatan" placeholder="Tambahkan catatan..."></textarea>
                </div>

                <p class="protection">Dijamin aman oleh <strong>DANA Protection</strong></p>
                <p class="footer-text">
                    Dengan melanjutkan, Anda menyetujui <a href="#">Syarat & Ketentuan</a>.
                </p>
                <button type="submit" class="continue-button">LANJUTKAN</button>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

</body>

</html>
