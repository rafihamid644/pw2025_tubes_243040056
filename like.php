<?php
include 'config/db.php';

// Sementara, user_id di-hardcode (misal: 1)
$user_id = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);

    // Cek apakah user sudah like post ini
    $cek = mysqli_query($conn, "SELECT * FROM likes WHERE user_id=$user_id AND post_id=$post_id");
    if (mysqli_num_rows($cek) == 0) {
        // Belum like, tambahkan
        mysqli_query($conn, "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
    }

    // Redirect ke halaman sesuai parameter redirect, jika ada
    if (isset($_POST['redirect'])) {
        header("Location: " . $_POST['redirect']);
    } else {
        header("Location: index.php");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
