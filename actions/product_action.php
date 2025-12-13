<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') die("Akses ditolak 😅");
if (!isset($_POST['action'])) die("Aksi tidak ditemukan 😭");

$action = $_POST['action'];

// ===================== Tambah produk =====================
if ($action == "add") {
  $nama_produk = $conn->real_escape_string($_POST['nama_produk']);
  $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
  $harga = (float)$_POST['harga'];

  // Upload media
  $media = null;
  if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
    $file = $_FILES['media'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'];
    if (!in_array($ext, $allowed)) die("Format file tidak diijinkan 😅");
    if ($file['size'] > 5 * 1024 * 1024) die("File terlalu besar 😭");
    $media = uniqid() . '.' . $ext;
    move_uploaded_file($file['tmp_name'], "../uploads/" . $media);
  }

  // Simpan ke DB
  $stmt = $conn->prepare("INSERT INTO products (nama_produk,deskripsi,harga,media,created_by) VALUES (?,?,?,?,?)");
  $stmt->bind_param("ssdsi", $nama_produk, $deskripsi, $harga, $media, $_SESSION['user_id']);
  $stmt->execute() or die("Gagal tambah produk: " . $conn->error);
  header("Location: ../admin/products.php");

  // ===================== Hapus produk =====================
} elseif ($action == "delete") {
  $product_id = $_POST['product_id'];
  $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
  $stmt->bind_param("i", $product_id);
  $stmt->execute() or die("Gagal hapus produk: " . $conn->error);
  header("Location: ../admin/products.php");

  // ===================== Assign produk ke user =====================
} elseif ($action == "assign") {
  $product_id = $_POST['product_id'];
  $user_id = $_POST['user_id'];
  $check = $conn->query("SELECT * FROM product_assignments WHERE product_id='$product_id' AND user_id='$user_id'");
  if ($check->num_rows > 0) die("Produk sudah diassign 😅");
  $stmt = $conn->prepare("INSERT INTO product_assignments (product_id,user_id,status) VALUES (?,?,?)");
  $status = "pending";
  $stmt->bind_param("iis", $product_id, $user_id, $status);
  $stmt->execute() or die("Gagal assign produk: " . $conn->error);
  header("Location: ../admin/products.php");
} else {
  die("Aksi tidak dikenali 😭");
}
