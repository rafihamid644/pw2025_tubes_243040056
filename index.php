<?php
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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>KanvasKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">KanvasKita</a>
            <!-- Tambahkan menu login/register jika ingin -->
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Galeri Karya Seni KanvasKita</h1>

        <!-- Form Search -->
        <form method="get" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="q" placeholder="Cari judul atau nama pembuat..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>

        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" style="height:250px;object-fit:cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="card-text">Oleh: <?= htmlspecialchars($row['username']) ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <!-- TOMBOL LIKE -->
                                <form action="like.php" method="post" style="display:inline;">
                                    <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        ❤️ Like (<?= $row['total_likes'] ?>)
                                    </button>
                                </form>
                                <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Detail</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">Tidak ada karya seni yang ditemukan.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>