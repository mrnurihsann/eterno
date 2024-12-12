<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit;
}

// Proses form
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pengirim_id = 1; // Contoh ID pengirim (dari sesi login pengguna)
    $username_penerima = $conn->real_escape_string($_POST['username']);
    $jumlah = (float) $_POST['jumlah'];
    $catatan = $conn->real_escape_string($_POST['catatan']);

    // Validasi penerima
    $result = $conn->query("SELECT id FROM users WHERE username = '$username_penerima'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $penerima_id = $row['id'];

        // Simpan transaksi
        $stmt = $conn->prepare("INSERT INTO topup (pengirim_id, penerima_id, jumlah, catatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $pengirim_id, $penerima_id, $jumlah, $catatan);

        if ($stmt->execute()) {
            $message = "Top-up berhasil dikirim ke $username_penerima sebesar Rp " . number_format($jumlah, 2);
        } else {
            $message = "Gagal mengirim top-up.";
        }
        $stmt->close();
    } else {
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
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        <?php include '../style/topup.css'; ?>
    </style>
</head>

<body>
    <?php include '../partials/navbar.html'; ?>
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
            <h2>Transfer Dana</h2>
            <p>Masukkan informasi penerima dan jumlah dana yang akan dikirim</p>

            <form method="POST">
                <?php if ($message): ?>
                    <div class="alert"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <div class="input-group">
                    <label for="username">Username Penerima: </label>
                    <input type="text" name="username" id="username" placeholder="Masukkan username penerima" required>
                </div>
                <div class="input-group">
                    <label for="jumlah">Jumlah Top-Up (Rp): </label>
                    <input type="number" name="jumlah" id="jumlah" placeholder="Masukkan jumlah dana" required>
                </div>
                <div class="input-group">
                    <label for="catatan">Catatan (Opsional): </label>
                    <textarea name="catatan" id="catatan" placeholder="Tambahkan catatan..."></textarea>
                </div>

                <p class="protection">Dijamin aman oleh <strong>DANA Protection</strong></p>
                <p class="footer-text">
                    Dengan melanjutkan, Anda menyetujui <a href="#">Syarat & Ketentuan</a>.
                </p>
                <button type="submit" class="continue-button">KIRIM</button>
            </form>
             <a href="../profile.php" class="btn btn-danger m-2 p-2 back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

</body>

</html>
