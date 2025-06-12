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

if (!$post) {
    echo "<h2>Karya tidak ditemukan.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Karya | KanvasKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">KanvasKita</a>
        </div>
    </nav>
    <div class="container mt-4">
        <a href="index.php" class="btn btn-secondary mb-3">&laquo; Kembali ke Galeri</a>
        <div class="row">
            <div class="col-md-6">
                <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($post['title']) ?>">
            </div>
            <div class="col-md-6">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p>Oleh: <strong><?= htmlspecialchars($post['username']) ?></strong></p>
                <p>Jumlah Like: <span class="badge bg-danger"><?= $post['total_likes'] ?></span></p>
                <!-- Form Like dengan redirect ke halaman detail -->
                <form action="like.php" method="post" style="display:inline;">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <input type="hidden" name="redirect" value="detail.php?id=<?= $post['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger">❤️ Like</button>
                </form>
                <p class="mt-4"><small>Diunggah pada: <?= $post['created_at'] ?></small></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>