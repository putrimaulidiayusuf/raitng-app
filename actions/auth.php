<?php
// Mulai session supaya kita bisa simpan data user yang login
session_start();

// Sambungin ke database (file db.php harus bener konfigurasinya)
require_once "../config/db.php";

// Cek dulu, ada gak data "action" yang dikirim dari form (login / register / logout)
// Kalau gak ada, langsung dihentikan
if (!isset($_POST['action'])) {
    die("Aksi tidak ditemukan 😅");
}

// Simpan action biar gampang dicek
$action = $_POST['action'];

// ============================
// PROSES LOGIN
// ============================
if ($action == "login") {
    // Ambil email dan password dari form
    $email = $conn->real_escape_string($_POST['email']); // escape biar aman dari SQL Injection
    $password = $_POST['password']; // sementara masih plain text

    // Cari user berdasarkan email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Ambil data user dari database
        $user = $result->fetch_assoc();

        // Cek apakah password yang diketik sama dengan yang ada di database
        if ($password == $user['password']) {
            // Kalau cocok, simpan informasi penting ke session
            $_SESSION['user_id'] = $user['id'];       // id user
            $_SESSION['role'] = $user['role'];        // role user (admin / user)
            $_SESSION['username'] = $user['username']; // username

            // Arahkan ke halaman sesuai role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../public/product.php");
            }
            exit;
        } else {
            // Kalau password salah
            die("Password salah 😭");
        }
    } else {
        // Kalau email tidak ketemu
        die("Email tidak ditemukan 😅");
    }

// ============================
// PROSES REGISTER
// ============================
} elseif ($action == "register") {
    // Ambil data register dari form
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // sementara masih plain text

    // Cek apakah email sudah dipakai
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        die("Email sudah terdaftar 😭");
    }

    // Masukkan user baru ke tabel users
    // Role default = "user"
    $sql = "INSERT INTO users (username, email, password, role) 
            VALUES ('$username', '$email', '$password', 'user')";

    if ($conn->query($sql)) {
        // Kalau sukses, arahkan balik ke halaman login
        header("Location: ../public/index.php");
        exit;
    } else {
        // Kalau gagal simpan
        die("Gagal register: " . $conn->error);
    }

// ============================
// PROSES LOGOUT
// ============================
} elseif ($action == "logout") {
    // Hapus semua session biar user dianggap keluar
    session_unset();
    session_destroy();

    // Balikin ke halaman login
    header("Location: ../public/index.php");
    exit;

// ============================
// AKSI TIDAK DIKENALI
// ============================
} else {
    die("Aksi tidak dikenali 😅");
}
