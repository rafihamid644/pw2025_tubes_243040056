 <?php
    session_start();
    include '../config/db.php';

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username='$username' AND role='admin' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            // Login sukses, simpan session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header("Location: posts.php");
            exit;
        } else {
            $error = "Username atau password salah, atau Anda bukan admin!";
        }
    }
    ?>

 <!DOCTYPE html>
 <html lang="id">

 <head>
     <meta charset="UTF-8">
     <title>Login Admin | KanvasKita</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 </head>

 <body>
     <div class="container mt-4">
         <h2>Login Admin</h2>
         <?php if ($error): ?>
             <div class="alert alert-danger"><?= $error ?></div>
         <?php endif; ?>
         <form method="post">
             <div class="mb-3">
                 <label for="username" class="form-label">Username</label>
                 <input type="text" class="form-control" id="username" name="username" required>
             </div>
             <div class="mb-3">
                 <label for="password" class="form-label">Password</label>
                 <input type="password" class="form-control" id="password" name="password" required>
             </div>
             <button type="submit" class="btn btn-primary">Login</button>
             <a href="../index.php" class="btn btn-secondary">Kembali</a>
         </form>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 </body>

 </html>