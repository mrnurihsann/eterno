<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete user data
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Terjadi kesalahan saat menghapus data.";
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>
