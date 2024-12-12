<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
require 'db.php';

// Fetch all user data
$query_users = "SELECT * FROM users WHERE role = 'user'";
$result_users = $conn->query($query_users);

// Fetch all pending top-up requests
$query_topup = "
    SELECT t.id, u1.nama AS pengirim, u2.nama AS penerima, t.jumlah, t.catatan, t.tanggal
    FROM topup t
    JOIN users u1 ON t.pengirim_id = u1.id
    JOIN users u2 ON t.penerima_id = u2.id
    WHERE t.status = 'pending'";
$result_topup = $conn->query($query_topup);

// Process approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topup_id = intval($_POST['topup_id']);
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        // Update topup status
        $stmt = $conn->prepare("UPDATE topup SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $topup_id);
        $stmt->execute();
        $stmt->close();

        // Add balance if approved
        if ($action === 'approved') {
            $stmt = $conn->prepare("SELECT penerima_id, jumlah FROM topup WHERE id = ?");
            $stmt->bind_param("i", $topup_id);
            $stmt->execute();
            $stmt->bind_result($penerima_id, $jumlah);
            $stmt->fetch();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
            $stmt->bind_param("di", $jumlah, $penerima_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="./style/dashboard.css">
</head>
<body class="light-theme">
    <header>
        <div class="theme-toggler p-2">
            <span class="material-icons-sharp active">light_mode</span>
            <span class="material-icons-sharp">dark_mode</span>
        </div>
    </header>
    <div class="container mt-5">
        <h1 class="text-center">Dashboard Admin</h1>
        <p class="text-center">Selamat datang, Admin!</p>

        <!-- User Table -->
        <h2 class="mt-5">Daftar Pengguna</h2>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Topup Approval Table -->
        <h2 class="mt-5">Approval Top-Up</h2>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Pengirim</th>
                    <th>Penerima</th>
                    <th>Jumlah (Rp)</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_topup->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['pengirim']) ?></td>
                        <td><?= htmlspecialchars($row['penerima']) ?></td>
                        <td><?= number_format($row['jumlah'], 2) ?></td>
                        <td><?= htmlspecialchars($row['catatan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="topup_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approved" class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="topup_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/script.js"></script>
</body>
</html>
