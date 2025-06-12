<?php
session_start();

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';

// Pastikan ID ada dan bukan ID admin yang sedang login
if (!isset($_GET['id']) || $_GET['id'] == $_SESSION['user_id']) {
    // Redirect jika mencoba menghapus diri sendiri atau ID tidak valid
    header('Location: manage_users.php');
    exit;
}

$id = intval($_GET['id']);

// Hapus postingan terkait pengguna terlebih dahulu untuk menghindari error foreign key
$sql_delete_posts = "DELETE FROM posts WHERE user_id = ?";
$stmt_posts = mysqli_prepare($conn, $sql_delete_posts);
mysqli_stmt_bind_param($stmt_posts, "i", $id);
mysqli_stmt_execute($stmt_posts);

// Setelah postingan dihapus, baru hapus pengguna
$sql_delete_user = "DELETE FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $sql_delete_user);
mysqli_stmt_bind_param($stmt_user, "i", $id);
mysqli_stmt_execute($stmt_user);

// Redirect kembali ke halaman kelola pengguna
header('Location: manage_users.php');
exit;
