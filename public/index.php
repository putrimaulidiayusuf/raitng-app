<?php
// Mulai session, biar bisa simpan info login user
session_start();

// Kalau user sudah login, langsung lempar ke halaman sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
        exit;
    } else {
        header("Location: product.php");
        exit;
    }
}

// Sambungkan ke database
require_once "../config/db.php";

// Variabel untuk simpan pesan error login
$login_error = "";

// Kalau form login dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Ambil email & password dari form
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // sekarang masih plain text, nanti bisa pakai hash biar aman

    // Cari user di database berdasarkan email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Cocokin password (sementara langsung ==, belum hash)
        if ($password == $user['password']) {
            // Simpan data user ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            // Arahkan ke dashboard sesuai role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                header("Location: product.php");
                exit;
            }
        } else {
            $login_error = "Password salah 😅";
        }
    } else {
        $login_error = "Email tidak ditemukan 😭";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Rating App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="login-container">
        <h2>Login Rating App</h2>
        <!-- Kalau ada error, tampilkan -->
        <?php if ($login_error != ""): ?>
            <p style="color:red;"><?php echo $login_error; ?></p>
        <?php endif; ?>

        <!-- Form login -->
        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" required>
            <br>
            <label>Password:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Register di sini</a></p>
    </div>
</body>

</html>