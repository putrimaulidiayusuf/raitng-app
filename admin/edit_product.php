<?php
session_start();
require_once "../config/db.php";

// Pastikan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: ../public/index.php");
  exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $res->fetch_assoc();
if (!$product) die("Produk tidak ditemukan 😭");

// Handle update produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = $conn->real_escape_string($_POST['nama_produk']);
  $harga = floatval($_POST['harga']);
  $deskripsi = $conn->real_escape_string($_POST['deskripsi']);

  // Upload media baru opsional
  $media = $product['media'];
  if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
    $file = $_FILES['media'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'];
    if (!in_array($ext, $allowed)) die("Format file tidak diijinkan 😅");
    if ($file['size'] > 5 * 1024 * 1024) die("File terlalu besar 😭");
    $media = uniqid() . '.' . $ext;
    move_uploaded_file($file['tmp_name'], "../uploads/" . $media);
  }

  $stmt = $conn->prepare("UPDATE products SET nama_produk=?, harga=?, deskripsi=?, media=? WHERE id=?");
  $stmt->bind_param("sdssi", $nama, $harga, $deskripsi, $media, $id);
  $stmt->execute();
  header("Location: products.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Produk</title>
  <link rel="stylesheet" href="../public/assets/css/style.css">
</head>

<body>
  <h2>Edit Produk</h2>
  <p><a href="products.php">Kembali ke Daftar Produk</a></p>

  <form method="POST" enctype="multipart/form-data">
    <label>Nama Produk:</label>
    <input type="text" name="nama_produk" value="<?= htmlspecialchars($product['nama_produk']); ?>" required>

    <label>Harga:</label>
    <input type="number" name="harga" value="<?= $product['harga']; ?>" required>

    <label>Deskripsi:</label>
    <textarea name="deskripsi" required><?= htmlspecialchars($product['deskripsi']); ?></textarea>

    <label>Media Lama:</label>
    <?php if ($product['media']): ?>
      <?php if (preg_match("/\.(mp4|mov|avi)$/i", $product['media'])): ?>
        <video src="../uploads/<?= $product['media']; ?>" controls width="120"></video>
      <?php else: ?>
        <img src="../uploads/<?= $product['media']; ?>" width="120">
      <?php endif; ?>
    <?php endif; ?>

    <label>Ganti Media (Opsional):</label>
    <input type="file" name="media" accept="image/*,video/*">

    <button type="submit">Update Produk</button>
  </form>
</body>

</html>