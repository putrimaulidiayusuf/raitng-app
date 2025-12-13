<?php
session_start();
require_once "../config/db.php";

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak 😅 Login dulu ya");
}

// Pastikan ada product_id dari form
if (!isset($_POST['product_id'])) {
    die("Data tidak lengkap 😭");
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Update status assignment dari pending ke approved
$sql = "UPDATE product_assignments 
        SET status='approved' 
        WHERE user_id='$user_id' AND product_id='$product_id' AND status='pending'";

if ($conn->query($sql)) {
    header("Location: ../public/product.php");
} else {
    die("Gagal konfirmasi: " . $conn->error);
}
