<?php
include 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

        if (mysqli_query($conn, $sql)) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Gagal mendaftar. Username mungkin sudah ada.";
        }
    }
}

$title = 'Registrasi';
$header_path = 'assets/partials/header.php';
$footer_path = 'assets/partials/footer.php';

include $header_path;
?>

<div class="form-auth card text-white">
    <div class="card-body p-4 p-md-5">
        <h2 class="card-title text-center mb-4">Buat Akun Baru</h2>

        <?php if ($error) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($success) : ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> <?= $success ?>
                <hr>
                <a href="login.php" class="btn btn-primary">Login Sekarang</a>
            </div>
        <?php else : ?>
            <form method="post">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Konfirmasi Password" required>
                    <label for="password_confirm">Konfirmasi Password</label>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Registrasi</button>
                </div>
                <div class="text-center">
                    <small class="text-white-50">Sudah punya akun? <a href="login.php">Login di sini</a></small>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include $footer_path; ?>