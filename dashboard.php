<?php
// Memulai session untuk melacak status login pengguna
session_start();

// Mengecek apakah pengguna yang sedang login memiliki role 'admin', jika tidak maka akan diarahkan ke halaman index.php
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); // Mengarahkan pengguna ke halaman login
    exit; // Menghentikan eksekusi skrip lebih lanjut
}

// Memanggil file db.php untuk koneksi ke database
require 'db.php';

// Menentukan menu yang aktif berdasarkan parameter 'menu' di URL, default-nya adalah 'dashboard'
$menu = isset($_GET['menu']) ? $_GET['menu'] : 'dashboard';

// Mengambil data semua pengguna yang memiliki role 'user' dari database
$query_users = "SELECT * FROM users WHERE role = 'user'";
$result_users = $conn->query($query_users);

// Mengambil data semua permintaan top-up yang masih dalam status 'pending'
$query_topup = "
    SELECT t.id, u1.nama AS pengirim, u2.nama AS penerima, t.jumlah, t.catatan, t.tanggal
    FROM topup t
    JOIN users u1 ON t.pengirim_id = u1.id
    JOIN users u2 ON t.penerima_id = u2.id
    WHERE t.status = 'pending'";
$result_topup = $conn->query($query_topup);

// Memproses aksi approval atau rejection untuk permintaan top-up
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil id top-up dan aksi (approved/rejected) yang dipilih
    $topup_id = intval($_POST['topup_id']);
    $action = $_POST['action'];

    // Jika aksi adalah 'approved' atau 'rejected', maka akan memperbarui status top-up
    if (in_array($action, ['approved', 'rejected'])) {
        // Memperbarui status permintaan top-up berdasarkan aksi yang dipilih
        $stmt = $conn->prepare("UPDATE topup SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $topup_id);
        $stmt->execute();
        $stmt->close();

        // Jika top-up disetujui, menambahkan jumlah top-up ke saldo penerima
        if ($action === 'approved') {
            // Mengambil id penerima dan jumlah top-up dari database
            $stmt = $conn->prepare("SELECT penerima_id, jumlah FROM topup WHERE id = ?");
            $stmt->bind_param("i", $topup_id);
            $stmt->execute();
            $stmt->bind_result($penerima_id, $jumlah);
            $stmt->fetch();
            $stmt->close();

            // Memperbarui saldo pengguna penerima top-up
            $stmt = $conn->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
            $stmt->bind_param("di", $jumlah, $penerima_id);
            $stmt->execute();
            $stmt->close();
        }

        // Setelah aksi selesai, mengarahkan kembali ke halaman dashboard
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
    <title>Admin Panel</title>
    <!-- Mengimpor CSS dari Bootstrap untuk desain responsif dan elemen UI yang cepat -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mengimpor FontAwesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Mengimpor Material Icons untuk ikon tambahan -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <!-- Mengimpor stylesheet khusus untuk dashboard -->
    <link rel="stylesheet" href="./style/dashboard.css">
    <style>
        /* Style untuk body halaman */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar dengan lebar tetap dan posisi tetap di kiri */
        .sidebar {
            width: 250px;
            background: #343a40;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 10px;
            color: #fff;
            transition: transform 0.3s ease;
        }

        /* Styling untuk judul sidebar */
        .sidebar h3 {
            text-align: center;
            font-weight: bold;
            color: #ffc107;
            margin-bottom: 30px;
        }

        /* Styling untuk link dalam sidebar */
        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        /* Menambahkan efek hover dan aktif pada link sidebar */
        .sidebar a:hover, .sidebar a.active {
            background: #ffc107;
            color: #343a40;
        }

        /* Konten utama di sebelah kanan sidebar */
        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease;
        }

        /* Styling untuk tabel responsif */
        .table-responsive {
            overflow-x: auto;
        }

        /* Tombol untuk toggle menu sidebar di perangkat kecil */
        .menu-toggle {
            display: none;
            background: transparent;
            color: black;
            border: none;
            font-size: 24px;
            padding: 10px;
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            cursor: pointer;
        }

        /* Styling untuk tampilan sidebar pada layar kecil */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                transform: translateX(-100%);
                z-index: 100;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
                width: 100%;
            }
            .menu-toggle {
                display: block;
            }
        }

        /* Menyesuaikan ukuran font pada tabel di layar sangat kecil */
        @media (max-width: 576px) {
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol untuk menampilkan sidebar pada layar kecil -->
    <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h3>Admin Panel</h3>
        <!-- Navigasi menu sidebar dengan link aktif sesuai menu yang dipilih -->
        <a href="dashboard.php?menu=dashboard" class="<?= $menu == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-fw fa-tachometer-alt"></i> Dashboard</a>
        <a href="dashboard.php?menu=users" class="<?= $menu == 'users' ? 'active' : '' ?>"><i class="fas fa-users"></i> Daftar Pengguna</a>
        <a href="dashboard.php?menu=topup" class="<?= $menu == 'topup' ? 'active' : '' ?>"><i class="fas fa-wallet"></i> Approval Top-Up</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Konten utama -->
    <div class="content">
        <!-- Kondisional menu berdasarkan pilihan yang dipilih oleh admin -->
        <?php if ($menu == 'dashboard'): ?>
            <h1>Selamat Datang, Admin!</h1>
            <p>Ini adalah profil Anda:</p>
        <?php elseif ($menu == 'users'): ?>
            <h2>Daftar Pengguna</h2>
            <div class="table-responsive">
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
                        <!-- Menampilkan data pengguna dari hasil query -->
                        <?php while ($row = $result_users->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['role'] ?></td>
                                <td>
                                    <!-- Tombol untuk mengedit dan menghapus data pengguna -->
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($menu == 'topup'): ?>
            <h2>Approval Top-Up</h2>
            <div class="table-responsive">
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
                        <!-- Menampilkan data top-up yang belum disetujui -->
                        <?php while ($row = $result_topup->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['pengirim'] ?></td>
                                <td><?= $row['penerima'] ?></td>
                                <td><?= number_format($row['jumlah'], 2) ?></td>
                                <td><?= $row['catatan'] ?></td>
                                <td><?= $row['tanggal'] ?></td>
                                <td>
                                    <!-- Form untuk menyetujui atau menolak permintaan top-up -->
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
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Mengatur agar sidebar dapat ditampilkan/dihilangkan dengan tombol menu-toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        menuToggle.addEventListener('click', () => { 
            sidebar.classList.toggle('show');
        });
    </script>
</body>
</html>
