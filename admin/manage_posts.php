<?php
session_start();

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';

// Ambil semua data postingan dan gabungkan dengan nama pengguna
$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
$result = mysqli_query($conn, $sql);

$title = 'Kelola Postingan';
$header_path = '../assets/partials/header.php';
$footer_path = '../assets/partials/footer.php';

include $header_path;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <!-- Menu Samping -->
            <div class="card glass-card">
                <div class="card-header fw-bold">Menu Admin</div>
                <div class="list-group list-group-flush">
                    <a href="index.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    <a href="manage_posts.php" class="list-group-item list-group-item-action active"><i class="bi bi-images me-2"></i> Kelola Postingan</a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action"><i class="bi bi-people me-2"></i> Kelola Pengguna</a>
                    <a href="../index.php" class="list-group-item list-group-item-action"><i class="bi bi-box-arrow-left me-2"></i> Kembali ke Situs</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <!-- Konten Utama -->
            <div class="card glass-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Semua Postingan</h4>
                    <a href="../upload.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Tambah Postingan Baru</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless text-white">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Seniman</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="" width="80" class="rounded"></td>
                                            <td class="align-middle"><?= htmlspecialchars($row['title']) ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($row['username']) ?></td>
                                            <td class="align-middle"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                            <td class="align-middle">
                                                <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                                                <a href="delete_post.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus postingan ini?')"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada postingan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $footer_path; ?>