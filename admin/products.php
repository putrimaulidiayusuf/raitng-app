<?php
session_start();
require_once "../config/db.php";

// Pastikan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../public/index.php");
    exit;
}

// Ambil semua produk
$res = $conn->query("SELECT p.*, u.username AS creator FROM products p LEFT JOIN users u ON p.created_by=u.id ORDER BY p.id DESC");

// Ambil semua user
$user_result = $conn->query("SELECT id, username FROM users WHERE role='user'");

// Handle Assign ke User
if (isset($_POST['action'])) {
    $product_id = intval($_POST['product_id']);
    $user_id = intval($_POST['user_id']);

    if ($_POST['action'] == 'assign') {
        $stmt = $conn->prepare("UPDATE products SET assigned_to=? WHERE id=?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    } elseif ($_POST['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    }
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin - Produk</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>

<body>
    <h2>CRUD Produk & Assign ke User</h2>
    <p><a href="dashboard.php">Dashboard</a> | <a href="../public/logout.php">Logout</a></p>

    <div class="add-product">
        <h3>Tambah Produk Baru</h3>
        <form method="POST" action="../actions/product_action.php" enctype="multipart/form-data">
            <label>Nama Produk:</label>
            <input type="text" name="nama_produk" required>
            <label>Deskripsi:</label>
            <textarea name="deskripsi" required></textarea>
            <label>Harga:</label>
            <input type="number" name="harga" required>
            <label>Upload Media (Gambar/Video ≤5MB):</label>
            <input type="file" name="media" accept="image/*,video/*">
            <button type="submit" name="action" value="add">Tambah Produk</button>
        </form>
    </div>

    <div class="product-list">
        <h3>Daftar Produk</h3>
        <?php if ($res->num_rows > 0): ?>
            <table border="1" cellpadding="5">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Media</th>
                    <th>Assign ke User</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td>Rp <?= number_format($row['harga']); ?></td>
                        <td>
                            <?php if ($row['media']): ?>
                                <?php if (preg_match("/\.(mp4|mov|avi)$/i", $row['media'])): ?>
                                    <video src="../uploads/<?= $row['media']; ?>" controls width="120"></video>
                                <?php else: ?>
                                    <img src="../uploads/<?= $row['media']; ?>" width="120">
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <select name="user_id" required>
                                    <option value="">Pilih User</option>
                                    <?php
                                    $user_result->data_seek(0);
                                    while ($user = $user_result->fetch_assoc()): ?>
                                        <option value="<?= $user['id']; ?>" <?php if ($row['assigned_to'] == $user['id']) echo "selected"; ?>><?= $user['username']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                                <button type="submit" name="action" value="assign">Assign</button>
                            </form>
                        </td>
                        <td>
                            <a class="btn edit" href="edit_product.php?id=<?= $row['id']; ?>">Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin hapus produk?');">
                                <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                                <button type="submit" name="action" value="delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Belum ada produk 😭</p>
        <?php endif; ?>
    </div>
</body>

</html>