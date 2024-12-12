<?php
session_start();
require '../db.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: index.php");
    exit;
}

// Mendapatkan ID pengguna yang sedang login
$current_user_id = $_SESSION['id'];

// Menampilkan daftar pengguna lain
$query = "SELECT id, nama, username FROM users WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$users = $stmt->get_result();

// Menangani pengiriman pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiver_id']) && isset($_POST['message'])) {
        $receiver_id = $_POST['receiver_id'];
        $message = $_POST['message'];

        // Simpan pesan ke database
        $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $current_user_id, $receiver_id, $message);
        $stmt->execute();

        header("Location: chat.php?receiver_id=$receiver_id");
        exit;
    }
}

// Mendapatkan nama penerima berdasarkan penerima
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id'];

    // Query untuk mengambil nama penerima
    $query = "SELECT nama FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $receiver_name = $result->fetch_assoc()['nama'];

    // Mendapatkan pesan antara pengguna saat ini dan penerima
    $query = "SELECT m.message, u.nama AS sender_name, m.created_at, m.sender_id 
              FROM messages m 
              JOIN users u ON m.sender_id = u.id 
              WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
              ORDER BY m.created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $current_user_id, $receiver_id, $receiver_id, $current_user_id);
    $stmt->execute();
    $messages = $stmt->get_result();
} else {
    $receiver_id = null;
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
    <link rel="stylesheet" href="../style/chat.css">
</head>
<body>
     <?php include '../partials/navbar.html'; ?>
<div class="container mt-5">
    <h2 class="text-center chat-header">Chat Room</h2>

    <!-- Daftar Kontak -->
    <div class="row">
        <div class="col-md-3">
            <h4>Pilih User</h4>
            <div class="contact-list">
                <ul class="list-group">
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <a href="chat.php?receiver_id=<?= $user['id'] ?>"><?= $user['nama'] ?> (<?= $user['username'] ?>)</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div>
             <a href="../profile.php" class="btn btn-dark bg-primary m-2 p-2">
                <i class="fas fa-user"></i> Profile
            </a>
            </div>
            <a href="../logout.php" class="btn btn-danger m-2 p-2">
                <i class="fas fa-sign-in-alt"></i> Logout
            </a>
        </div>

        <!-- Pesan Room -->
        <div class="col-md-9">
            <?php if ($receiver_id): ?>
                <h4>Chat dengan <span class="user-name"><?= $receiver_name ?></span></h4>

                <div class="chat-box">
                    <?php while ($message = $messages->fetch_assoc()): ?>
                        <div class="message <?= $message['sender_id'] == $current_user_id ? 'sent' : 'received' ?>">
                            <strong><?= $message['sender_name'] ?>:</strong>
                            <p><?= $message['message'] ?></p>
                            <span class="timestamp"><?= $message['created_at'] ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </form>
            <?php else: ?>
                <p>Pilih kontak untuk memulai percakapan.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
