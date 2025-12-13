<?php
// ==========================
// CONFIG DATABASE
// ==========================

// Informasi koneksi database
$host = "localhost";     // biasanya localhost kalau pakai XAMPP/WAMP/MAMP
$user = "root";          // username database
$pass = "";              // password database, default XAMPP kosong
$db_name = "rating_app"; // nama database yang sudah dibuat

// Buat koneksi ke database pakai mysqli
$conn = new mysqli($host, $user, $pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    // Kalau koneksi gagal, berhenti dan tampilkan pesan error
    die("Koneksi gagal: " . $conn->connect_error);
}

// Kalau sampai sini, koneksi berhasil dan bisa dipakai di file lain
?>
