<?php
session_start();

// Kalau user sudah login, langsung arahkan ke dashboard / product
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: product.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Rating App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="dashboard-menu">
        <h2>Login</h2>
        <!-- Form login kirim ke actions/auth.php -->
        <form method="POST" action="../actions/auth.php">
            <input type="hidden" name="action" value="login">

            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>

</html>