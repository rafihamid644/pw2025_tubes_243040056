<?php
session_start();

// 1. Cek jika user sudah login DAN rolenya adalah 'admin'
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, tendang ke halaman utama
    header('Location: ../index.php');
    exit;
}

// Jika lolos, lanjutkan memuat halaman admin
include '../config/db.php';

$title = 'Dashboard Admin';
// Menggunakan header dan footer dari direktori utama
$header_path = '../assets/partials/header.php';
$footer_path = '../assets/partials/footer.php';

include $header_path;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card glass-card">
                <div class="card-header fw-bold">
                    Menu Admin
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="manage_posts.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-images me-2"></i> Kelola Postingan
                    </a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Kelola Pengguna
                    </a>
                    <a href="../index.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-arrow-left me-2"></i> Kembali ke Situs
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card glass-card">
                <div class="card-body">
                    <h1 class="card-title">Selamat Datang, Administrator!</h1>
                    <p class="lead">Anda berada di pusat kendali KanvasKita.</p>
                    <hr>
                    <p>Dari sini, Anda dapat mengelola semua aspek situs, mulai dari postingan karya seni hingga akun pengguna. Gunakan menu di samping untuk memulai.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $footer_path; ?>