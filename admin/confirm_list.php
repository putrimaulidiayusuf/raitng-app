<?php
session_start();

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../public/index.php");
    exit;
}

require_once "../config/db.php";

// Ambil semua assignment produk ke user yang statusnya pending
$sql = "SELECT pa.id AS assignment_id, u.username, p.nama_produk, pa.status 
        FROM product_assignments pa
        JOIN users u ON pa.user_id = u.id
        JOIN products p ON pa.product_id = p.id
        WHERE pa.status='pending' 
        ORDER BY pa.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Produk User</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>

<body>
    <h2>Daftar Produk Menunggu Konfirmasi User</h2>
    <p><a href="dashboard.php">Kembali ke Dashboard</a> | <a href="../public/logout.php">Logout</a></p>

    <div class="confirm-list">
        <?php if ($result->num_rows > 0): ?>
            <table border="1" cellpadding="5">
                <tr>
                    <th>ID Assignment</th>
                    <th>User</th>
                    <th>Produk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['assignment_id']; ?></td>
                        <td><?= $row['username']; ?></td>
                        <td><?= $row['nama_produk']; ?></td>
                        <td><?= $row['status']; ?></td>
                        <td>
                            <!-- Form untuk approve atau reject -->
                            <form method="POST" action="../actions/confirm_action.php">
                                <input type="hidden" name="assignment_id" value="<?= $row['assignment_id']; ?>">
                                <button type="submit" name="action" value="approve">Setuju</button>
                                <button type="submit" name="action" value="reject">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Belum ada produk menunggu konfirmasi 😅</p>
        <?php endif; ?>
    </div>
</body>

</html>