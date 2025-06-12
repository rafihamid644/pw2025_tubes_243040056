<?php
session_start();

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: manage_posts.php');
    exit;
}

$id = intval($_GET['id']);

// 1. Ambil nama file gambar sebelum menghapus record dari database
$sql_select = "SELECT image FROM posts WHERE id = ?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result_select = mysqli_stmt_get_result($stmt_select);

if ($row = mysqli_fetch_assoc($result_select)) {
    $image_filename = $row['image'];
    $image_path = '../uploads/' . $image_filename;

    // 2. Hapus record dari database
    $sql_delete = "DELETE FROM posts WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id);

    if (mysqli_stmt_execute($stmt_delete)) {
        // 3. Jika record berhasil dihapus, hapus juga file gambarnya dari server
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
}

// 4. Redirect kembali ke halaman kelola postingan
header('Location: manage_posts.php');
exit;
