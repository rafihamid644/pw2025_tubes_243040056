<?php
session_start();
include 'config/db.php';

// Proses pencarian
$where = "";
if (isset($_GET['q']) && $_GET['q'] !== '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where = "WHERE posts.title LIKE '%$q%' OR users.username LIKE '%$q%'";
}

// Ambil semua post dan join dengan user (plus filter pencarian jika ada)
$sql = "SELECT posts.*, users.username, 
           (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS total_likes
        FROM posts
        JOIN users ON posts.user_id = users.id
        $where
        ORDER BY posts.created_at DESC";
$result = mysqli_query($conn, $sql);

$title = 'KanvasKita';
// Menggunakan path parsial yang sudah dibuat pengguna
$header_path = 'assets/partials/header.php';
$footer_path = 'assets/partials/footer.php';

include $header_path;
?>

<div class="container py-5">
    <div class="hero-section mb-5">
        <div class="hero-content">
            <h1 class="display-4 fw-bold">Satu Kanvas, Seribu Gaya!</h1>
            <p class="lead text-white-50 mt-3">Jelajahi galeri tanpa batas, tempat setiap goresan bercerita.</p>
        </div>
        <div class="hero-image">
            <img src="assets/img/artist.png" alt="3D Artist Character">
        </div>
    </div>

    <!-- Form Search -->
    <form method="get" class="form-search mb-5">
        <div class="input-group input-group-lg">
            <input type="text" class="form-control" name="q" placeholder="Cari berdasarkan judul atau nama seniman..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col">
                    <div class="card h-100 text-white card-post">
                        <a href="detail.php?id=<?= $row['id'] ?>" class="text-decoration-none">
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><a href="detail.php?id=<?= $row['id'] ?>" class="text-white text-decoration-none"><?= htmlspecialchars($row['title']) ?></a></h5>
                            <p class="card-text text-white-50 small">Oleh: <?= htmlspecialchars($row['username']) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                            <form action="like.php" method="post" class="d-inline">
                                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-heart-fill"></i> <?= $row['total_likes'] ?>
                                </button>
                            </form>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-light">Detail</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <h4 class="alert-heading">Oops! Tidak Ditemukan</h4>
                    <p>Kami tidak dapat menemukan karya seni yang cocok dengan pencarian Anda.</p>
                    <hr>
                    <a href="index.php" class="btn btn-warning">Kembali ke Galeri</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include $footer_path; ?>