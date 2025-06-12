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
$user = null;

// Pastikan ID ada dan bukan ID admin yang sedang login
if (!isset($_GET['id']) || $_GET['id'] == $_SESSION['user_id']) {
    header('Location: manage_users.php');
    exit;
}

$id = intval($_GET['id']);

// Proses form jika ada data yang dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    // Validasi role untuk keamanan
    if ($role === 'admin' || $role === 'user') {
        $sql_update = "UPDATE users SET role = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $role, $id);
        if (mysqli_stmt_execute($stmt_update)) {
            $success = 'Peran pengguna berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui peran.';
        }
    } else {
        $error = 'Peran tidak valid.';
    }
}

// Ambil data pengguna untuk ditampilkan di form
$sql_select = "SELECT id, username, role FROM users WHERE id = ?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    header('Location: manage_users.php');
    exit;
}

$title = 'Ubah Peran Pengguna';
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
            <div class="card glass-card">
                <div class="card-header">
                    <h4>Ubah Peran untuk: <?= htmlspecialchars($user['username']) ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="role" class="form-label">Peran (Role)</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="manage_users.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $footer_path; ?>