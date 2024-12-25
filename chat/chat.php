<?php
session_start(); // Memulai sesi untuk mengakses data pengguna yang terhubung
require '../db.php'; // Menghubungkan ke file db.php yang berisi konfigurasi database

// Mengecek apakah user sudah login dengan peran 'user'. Jika tidak, akan diarahkan ke halaman index.php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit;
}

$current_user_id = $_SESSION['id']; // Mengambil ID pengguna yang sedang login

// Query untuk mengambil daftar pengguna selain pengguna yang sedang login
$query = "SELECT id, nama, username FROM users WHERE role = 'user' AND id != $current_user_id";
$result = $conn->query($query); // Menjalankan query untuk mendapatkan daftar pengguna
$users = $result; // Menyimpan hasil query ke dalam variabel $users

// Mengecek apakah ada data yang dikirim melalui metode POST untuk mengirim pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiver_id']) && isset($_POST['message'])) {
        $receiver_id = $_POST['receiver_id']; // Mengambil ID penerima pesan
        $message = $_POST['message']; // Mengambil pesan yang dikirim

        // Query untuk menyimpan pesan ke dalam database
        $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($current_user_id, $receiver_id, '$message')";
        $conn->query($query); // Menjalankan query untuk menyimpan pesan ke database

        // Setelah pesan dikirim, akan diarahkan ke halaman chat dengan penerima yang dipilih
        header("Location: chat.php?receiver_id=$receiver_id");
        exit;
    }
}

// Mengecek apakah ada ID penerima yang diterima melalui URL untuk memulai percakapan
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id']; // Mengambil ID penerima dari parameter URL
    $query = "SELECT nama FROM users WHERE id = $receiver_id"; // Query untuk mendapatkan nama penerima
    $result = $conn->query($query);
    $receiver_name = $result->fetch_assoc()['nama']; // Menyimpan nama penerima

    // Query untuk mengambil semua pesan antara pengguna yang login dan penerima
    $query = "SELECT m.message, u.nama AS sender_name, m.created_at, m.sender_id 
              FROM messages m 
              JOIN users u ON m.sender_id = u.id 
              WHERE (m.sender_id = $current_user_id AND m.receiver_id = $receiver_id) 
              OR (m.sender_id = $receiver_id AND m.receiver_id = $current_user_id)
              ORDER BY m.created_at ASC"; 
    $messages = $conn->query($query); // Menjalankan query untuk mendapatkan pesan
} else {
    $receiver_id = null; // Jika tidak ada penerima yang dipilih, set receiver_id menjadi null
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    /* Styling untuk body */
    body {
        background-color: #212121; /* Warna latar belakang gelap */
    }

    /* Styling untuk Sidebar */
    .sidebar {
        background-color: #212121; /* Warna sidebar gelap */
        color: #fff; /* Warna teks di sidebar putih */
        height: 100vh; /* Sidebar sepanjang tinggi layar */
        padding-top: 20px; /* Padding di bagian atas sidebar */
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1); /* Bayangan untuk sidebar */
    }

    .sidebar h4, .sidebar a {
        color: #fff; /* Warna teks putih */
        font-weight: 600; /* Menebalkan teks */
    }

    .sidebar a:hover {
        color: #c5b82a; /* Warna teks saat hover */
    }

    /* Styling untuk chat box */
    .chat-box {
        background-image: url("../images/bg-chat.jpg"); /* Menambahkan gambar latar belakang pada chat box */
        color: #fff; /* Warna teks putih */
        max-height: 400px; /* Membatasi tinggi chat box */
        overflow-y: auto; /* Menambahkan scrollbar vertikal jika konten melebihi batas */
        padding: 20px; /* Padding di dalam chat box */
        border-radius: 10px; /* Membulatkan sudut chat box */
        margin-bottom: 20px; /* Memberikan jarak bawah pada chat box */
    }

    .message.sent {
        background-color: #9f8a0e; /* Warna pesan yang dikirim */
        color: white; /* Warna teks putih */
        align-self: flex-end; /* Menempatkan pesan yang dikirim di sebelah kanan */
        border-radius: 15px; /* Membulatkan sudut pesan */
        padding: 12px 20px; /* Padding di dalam pesan */
        max-width: 70%; /* Membatasi lebar pesan */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan pada pesan */
    }

    .message.received {
        background-color: #555555; /* Warna pesan yang diterima */
        color: white; /* Warna teks putih */
        align-self: flex-start; /* Menempatkan pesan yang diterima di sebelah kiri */
        border-radius: 15px; /* Membulatkan sudut pesan */
        padding: 12px 20px; /* Padding di dalam pesan */
        max-width: 70%; /* Membatasi lebar pesan */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan pada pesan */
    }

    .timestamp {
        font-size: 0.8em; /* Ukuran font untuk timestamp */
        color: #bdc3c7; /* Warna teks untuk timestamp */
        text-align: center; /* Menyusun timestamp di tengah */
        display: block; /* Membuat timestamp tampil sebagai blok */
        margin: 5px 0; /* Margin atas dan bawah untuk timestamp */
    }

    /* Layout Styling */
    .container {
        display: flex; /* Menggunakan flexbox untuk layout */
        margin-top: 20px; /* Margin atas untuk container */
        min-height: 100vh; /* Mengatur tinggi minimal container */
    }

    .sidebar {
        width: 250px; /* Lebar sidebar */
        padding-right: 20px; /* Padding kanan pada sidebar */
    }

    .chat-area {
        flex-grow: 1; /* Membuat chat area mengisi ruang yang tersisa */
        padding-left: 20px; /* Padding kiri pada chat area */
    }

    /* Styling untuk tombol sidebar */
    .btn-sidebar {
        width: 100%; /* Membuat tombol sidebar lebar penuh */
        margin-bottom: 15px; /* Memberikan jarak bawah pada tombol */
        color: #fff; /* Warna teks putih */
        background-color: #9f8a0e; /* Warna latar belakang tombol */
        border: none; /* Menghapus border tombol */
        padding: 12px; /* Padding pada tombol */
        font-weight: 600; /* Menebalkan teks tombol */
        transition: all 0.3s ease; /* Transisi untuk efek hover */
    }

    .btn-sidebar:hover {
        background-color: #2f3132; /* Warna latar belakang tombol saat hover */
    }

    li a {
        text-decoration: none; /* Menghapus garis bawah pada tautan */
    }

    /* Responsiveness untuk tampilan mobile */
    @media (max-width: 768px) {
        .container {
            flex-direction: column; /* Menyusun elemen secara vertikal pada layar kecil */
        }
        .sidebar {
            width: 100%; /* Sidebar menggunakan lebar penuh pada layar kecil */
            padding-bottom: 20px; /* Padding bawah pada sidebar */
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar untuk memilih user -->
        <div class="sidebar">
            <h4>Pilih User</h4>
            <ul class="list-group">
                <!-- Menampilkan daftar pengguna lain yang bisa dihubungi -->
                <?php while ($user = $users->fetch_assoc()): ?>
                    <li class="list-group-item bg-dark">
                        <a href="chat.php?receiver_id=<?= $user['id'] ?>" class="text-light"><?= $user['nama'] ?> (<?= $user['username'] ?>)</a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <!-- Tombol navigasi ke profil dan logout -->
            <div class="mt-4">
                <a href="../profile.php" class="btn btn-sidebar">
                    <i class="fas fa-user"></i> Profile
                </a>
            </div>
            <a href="../logout.php" class="btn btn-danger btn-sidebar">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <!-- Area chat untuk menampilkan pesan -->
        <div class="chat-area">
            <?php if ($receiver_id): ?>
                <h4 class="text-light">Chat dengan <span class="user-name text-primary"><?= $receiver_name ?></span></h4>

                <div class="chat-box d-flex flex-column">
                    <!-- Menampilkan pesan yang sudah ada dalam percakapan -->
                    <?php while ($message = $messages->fetch_assoc()): ?>
                        <span class="timestamp"><?= date('H:i', strtotime($message['created_at'])) ?></span>
                        <div class="message <?= $message['sender_id'] == $current_user_id ? 'sent' : 'received' ?>">
                            <p><?= $message['message'] ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Form untuk mengirim pesan -->
                <form method="POST" action="">
                    <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                    <div class="mb-3">
                        <textarea name="message" class="form-control" placeholder="masukkan pesan..." rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Kirim Pesan</button>
                </form>
            <?php else: ?>
                <p class="text-light">Pilih kontak untuk memulai percakapan.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mengimpor JavaScript untuk Bootstrap dan FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
