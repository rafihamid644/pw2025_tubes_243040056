<?php
session_start();

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';

// Ambil semua data pengguna (menghapus created_at karena tidak ada di DB)
$sql = "SELECT id, username, role FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$title = 'Kelola Pengguna';
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
                    <a href="manage_posts.php" class="list-group-item list-group-item-action"><i class="bi bi-images me-2"></i> Kelola Postingan</a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action active"><i class="bi bi-people me-2"></i> Kelola Pengguna</a>
                    <a href="../index.php" class="list-group-item list-group-item-action"><i class="bi bi-box-arrow-left me-2"></i> Kembali ke Situs</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <!-- Konten Utama -->
            <div class="card glass-card">
                <div class="card-header">
                    <h4 class="mb-0">Daftar Semua Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless text-white">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Peran (Role)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="align-middle"><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td class="align-middle">
                                                <?php
                                                if ($row['role'] === 'admin') {
                                                    echo '<span class="badge bg-success">admin</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">user</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="align-middle">
                                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-person-gear"></i> Ubah Peran</a>
                                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus pengguna ini? Ini tidak bisa dibatalkan.')"><i class="bi bi-trash"></i> Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada pengguna terdaftar.</td>
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