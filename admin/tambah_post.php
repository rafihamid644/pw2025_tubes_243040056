<?php
include '../config/db.php';

$error = '';
$success = '';

// Ambil semua user (supaya admin bisa pilih pembuat karya)
$user_result = mysqli_query($conn, "SELECT id, username FROM users");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $user_id = intval($_POST['user_id']);

    // Validasi file
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filesize = $_FILES['image']['size'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Hanya file JPG, JPEG, PNG, GIF yang diperbolehkan!";
        } elseif ($filesize > 2 * 1024 * 1024) {
            $error = "Ukuran file maksimal 2MB!";
        } else {
            $newname = uniqid() . '.' . $ext;
            $upload_path = '../uploads/' . $newname;
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $sql = "INSERT INTO posts (user_id, title, image) VALUES ('$user_id', '$title', '$newname')";
                if (mysqli_query($conn, $sql)) {
                    $success = "Karya berhasil ditambahkan!";
                } else {
                    $error = "Gagal menyimpan ke database: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal upload gambar!";
            }
        }
    } else {
        $error = "Pilih gambar terlebih dahulu!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Karya | Admin KanvasKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Tambah Karya Seni</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Judul Karya</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Pembuat</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="">-- Pilih User --</option>
                    <?php while ($user = mysqli_fetch_assoc($user_result)): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Pilih Gambar (jpg, jpeg, png, gif)</label>
                <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
            <a href="posts.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>