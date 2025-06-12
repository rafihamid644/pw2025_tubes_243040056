<?php
include 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$post_id = intval($_GET['id']);

// Ambil data karya seni
$sql = "SELECT posts.*, users.username, 
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS total_likes
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.id = $post_id
        LIMIT 1";
$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

// Jika post tidak ditemukan, tampilkan pesan error
if (!$post) {
    $title = 'Error 404';
    include 'assets/partials/header.php';
    echo '<div class="alert alert-danger text-center"><h2>Karya tidak ditemukan.</h2><p>Karya yang Anda cari mungkin telah dihapus atau URL-nya salah.</p><a href="index.php" class="btn btn-primary">Kembali ke Galeri</a></div>';
    include 'assets/partials/footer.php';
    exit;
}

$title = htmlspecialchars($post['title']) . ' oleh ' . htmlspecialchars($post['username']);
$header_path = 'assets/partials/header.php';
$footer_path = 'assets/partials/footer.php';

include $header_path;
?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'uploaded') : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <strong>Berhasil!</strong> Karya seni Anda telah berhasil diupload.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="mb-4">
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Galeri</a>
</div>

<div class="card text-white overflow-hidden">
    <div class="row g-0">
        <div class="col-md-7">
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded-start w-100" style="aspect-ratio: 16/10; object-fit: cover;" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>
        <div class="col-md-5 d-flex flex-column">
            <div class="card-body">
                <h1 class="card-title display-6"><?= htmlspecialchars($post['title']) ?></h1>
                <p class="card-text fs-5 mb-3">Oleh: <strong class="text-white"><?= htmlspecialchars($post['username']) ?></strong></p>

                <div class="d-flex align-items-center gap-3">
                    <form action="like.php" method="post" class="d-inline">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="redirect" value="detail.php?id=<?= $post['id'] ?>">
                        <button type="submit" class="btn btn-lg btn-outline-danger d-flex align-items-center gap-2">
                            <i class="bi bi-heart"></i>
                            <span>Like</span>
                        </button>
                    </form>
                    <span class="badge bg-danger fs-6 rounded-pill"><?= $post['total_likes'] ?> Suka</span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-secondary mt-auto">
                <small class="text-white-50">Diunggah pada: <?= date('d F Y', strtotime($post['created_at'])) ?></small>
            </div>
        </div>
    </div>
</div>

<?php include $footer_path; ?>