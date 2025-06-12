<?php
include 'config/db.php';

// Pastikan user sudah login untuk bisa upload
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $user_id = $_SESSION['user_id']; // Ambil user_id dari session

    // Validasi file
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filesize = $_FILES['image']['size'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Hanya file JPG, JPEG, PNG, atau GIF yang diperbolehkan.";
        } elseif ($filesize > 5 * 1024 * 1024) { // Batas 5MB
            $error = "Ukuran file maksimal adalah 5MB.";
        } else {
            $newname = uniqid('img_', true) . '.' . $ext;
            $upload_path = 'uploads/' . $newname;
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $sql = "INSERT INTO posts (user_id, title, image) VALUES ('$user_id', '$title', '$newname')";
                if (mysqli_query($conn, $sql)) {
                    $last_id = mysqli_insert_id($conn);
                    header('Location: detail.php?id=' . $last_id . '&status=uploaded');
                    exit;
                } else {
                    $error = "Gagal menyimpan ke database: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal mengupload gambar.";
            }
        }
    } else {
        $error = "Anda harus memilih sebuah gambar untuk diupload.";
    }
}

$title = 'Upload Karya';
$header_path = 'assets/partials/header.php';
$footer_path = 'assets/partials/footer.php';

include $header_path;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card text-white">
                <div class="card-body p-4 p-md-5">
                    <h2 class="card-title text-center mb-4">Upload Karya Seni Baru</h2>

                    <?php if ($error) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Judul Karya" required>
                            <label for="title">Judul Karya</label>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Pilih Gambar (Maks 5MB)</label>
                            <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required onchange="previewImage(event)">
                        </div>

                        <div class="mb-3 text-center">
                            <img id="image-preview" src="" alt="Pratinjau Gambar" class="img-fluid rounded" style="display:none; max-height: 300px;">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-cloud-upload"></i> Upload Sekarang</button>
                            <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('image-preview');
        reader.onload = function() {
            if (reader.readyState === 2) {
                preview.src = reader.result;
                preview.style.display = 'block';
            }
        }
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

<?php include $footer_path; ?>