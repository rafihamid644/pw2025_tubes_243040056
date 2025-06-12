<?php
session_start();

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';

$error = '';
$success = '';
$post = null;

if (!isset($_GET['id'])) {
    header('Location: manage_posts.php');
    exit;
}

$id = intval($_GET['id']);

// Proses form jika ada data yang dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    // Logika pembaruan data (akan ditambahkan nanti)
    // ...

    $sql_update = "UPDATE posts SET title = ? WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $title, $id);

    if (mysqli_stmt_execute($stmt_update)) {
        $success = 'Postingan berhasil diperbarui!';
    } else {
        $error = 'Gagal memperbarui postingan.';
    }
}

// Ambil data postingan untuk ditampilkan di form
$sql_select = "SELECT * FROM posts WHERE id = ?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
if ($result && mysqli_num_rows($result) > 0) {
    $post = mysqli_fetch_assoc($result);
} else {
    // Jika post tidak ditemukan, redirect
    header('Location: manage_posts.php');
    exit;
}

$title = 'Edit Postingan';
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
            <div class="card glass-card">
                <div class="card-header">
                    <h4>Edit Postingan: <?= htmlspecialchars($post['title']) ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Karya</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label><br>
                            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="" width="200" class="rounded mb-2">
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Ganti Gambar (Opsional)</label>
                            <input class="form-control" type="file" id="image" name="image">
                            <small class="form-text text-white-50">Kosongkan jika tidak ingin mengganti gambar.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="manage_posts.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $footer_path; ?>