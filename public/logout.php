<?php
// Mulai session dulu
session_start();

// Hapus semua session (keluarin user dari login)
session_unset();
session_destroy();

// Setelah logout, lempar ke halaman login
header("Location: index.php");
exit;
