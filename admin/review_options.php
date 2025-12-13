<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../public/index.php");
    exit;
}

// Tambah opsi baru
if (isset($_POST['option_text'])) {
    $text = $conn->real_escape_string($_POST['option_text']);
    $conn->query("INSERT INTO review_options(option_text) VALUES('$text')");
}

// Update opsi
if (isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $text = $conn->real_escape_string($_POST['option_text']);
    $conn->query("UPDATE review_options SET option_text='$text' WHERE id=$id");
}

// Hapus opsi
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM review_options WHERE id=$id");
}

// Ambil semua opsi
$res = $conn->query("SELECT * FROM review_options ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Opsi Ulasan</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>

<body>
    <h2>Opsi Ulasan</h2>
    <p><a href="dashboard.php">Dashboard</a></p>

    <h3>Tambah Opsi Baru</h3>
    <form method="POST">
        <input type="text" name="option_text" placeholder="Contoh: Cepat, Ramah" required>
        <button type="submit">Tambah</button>
    </form>

    <h3>Daftar Opsi</h3>
    <table>
        <tr>
            <th>Opsi</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['option_text']); ?></td>
                <td>
                    <form style="display:inline" method="POST">
                        <input type="hidden" name="edit_id" value="<?= $row['id']; ?>">
                        <input type="text" name="option_text" value="<?= htmlspecialchars($row['option_text']); ?>" required>
                        <button type="submit" class="btn edit">Edit</button>
                    </form>
                    <a href="review_options.php?delete=<?= $row['id']; ?>" class="btn delete" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>