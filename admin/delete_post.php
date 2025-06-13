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

// Mulai transaksi untuk memastikan semua query berhasil atau tidak sama sekali
mysqli_begin_transaction($conn);

try {
    // 1. Ambil nama file gambar sebelum menghapus data post
    $sql_select = "SELECT image FROM posts WHERE id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $post = mysqli_fetch_assoc($result_select);
    $image_to_delete = $post ? $post['image'] : null;

    // 2. Hapus semua likes yang terkait dengan post ini terlebih dahulu
    $sql_delete_likes = "DELETE FROM likes WHERE post_id = ?";
    $stmt_likes = mysqli_prepare($conn, $sql_delete_likes);
    mysqli_stmt_bind_param($stmt_likes, "i", $id);
    mysqli_stmt_execute($stmt_likes);

    // 3. Hapus data post dari database
    $sql_delete_post = "DELETE FROM posts WHERE id = ?";
    $stmt_post = mysqli_prepare($conn, $sql_delete_post);
    mysqli_stmt_bind_param($stmt_post, "i", $id);
    mysqli_stmt_execute($stmt_post);

    // 4. Jika query berhasil, hapus file gambar dari server
    if ($image_to_delete && file_exists('../uploads/' . $image_to_delete)) {
        unlink('../uploads/' . $image_to_delete);
    }

    // Jika semua berhasil, commit transaksi
    mysqli_commit($conn);

    $_SESSION['success_message'] = "Postingan berhasil dihapus.";
} catch (mysqli_sql_exception $exception) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($conn);
    $_SESSION['error_message'] = "Gagal menghapus postingan: " . $exception->getMessage();
} finally {
    // Selalu tutup statement jika sudah dibuat
    if (isset($stmt_select)) mysqli_stmt_close($stmt_select);
    if (isset($stmt_likes)) mysqli_stmt_close($stmt_likes);
    if (isset($stmt_post)) mysqli_stmt_close($stmt_post);
}

// Redirect kembali ke halaman manage_posts.php
header('Location: manage_posts.php');
exit;
