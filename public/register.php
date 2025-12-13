<?php
session_start();

// Kalau sudah login, user gak bisa buka halaman register lagi
if (isset($_SESSION['role'])) {
    header("Location: product.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Rating App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-menu">
        <h2>Register</h2>
        <!-- Form register kirim ke actions/auth.php -->
        <form method="POST" action="../actions/auth.php">
            <input type="hidden" name="action" value="register">

            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>
