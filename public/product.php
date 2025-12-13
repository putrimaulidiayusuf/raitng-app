<?php
// Mulai session & connect database
session_start();
require_once "../config/db.php";

// Kalau belum login -> tolak
if (!isset($_SESSION['user_id'])) die("Akses ditolak 😅");

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil semua produk + rata-rata rating + jumlah rating
$sql = "
    SELECT p.*, ROUND(AVG(r.rating),1) AS avg_rating, COUNT(r.id) AS rating_count
    FROM products p
    LEFT JOIN reviews r ON p.id = r.product_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
";
$result = $conn->query($sql);

// Ambil status assignment produk untuk user ini
$assignments = [];
$res = $conn->query("SELECT * FROM product_assignments WHERE user_id='$user_id'");
while ($row = $res->fetch_assoc()) $assignments[$row['product_id']] = $row['status'];

// Ambil opsi review (checklist)
$review_options = [];
$res_opts = $conn->query("SELECT * FROM review_options");
while ($row = $res_opts->fetch_assoc()) $review_options[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Produk</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Hai, <?php echo htmlspecialchars($username); ?>! Ini daftar produk:</h2>
    <p><a href="logout.php">Logout</a></p>

    <div class="product-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($prod = $result->fetch_assoc()): ?>
                <?php
                $prod_id = $prod['id'];
                $status = $assignments[$prod_id] ?? null;

                // Ambil review user lain
                $reviews = [];
                $res_rev = $conn->query("
                    SELECT r.*, u.username 
                    FROM reviews r 
                    JOIN users u ON r.user_id=u.id 
                    WHERE r.product_id='$prod_id'
                    ORDER BY r.created_at DESC
                ");
                while ($rev = $res_rev->fetch_assoc()) $reviews[] = $rev;
                ?>
                <div class="product-card">
                    <h3><?php echo htmlspecialchars($prod['nama_produk']); ?></h3>
                    <p><?php echo htmlspecialchars($prod['deskripsi']); ?></p>
                    <p>Harga: Rp <?php echo number_format($prod['harga']); ?></p>
                    <p>Rata-rata: <?php echo $prod['avg_rating'] ?? 0; ?> ⭐ (<?php echo $prod['rating_count']; ?> penilaian)</p>

                    <!-- Kalau ada media produk -->
                    <?php if ($prod['media']): ?>
                        <div class="product-media">
                            <?php if (preg_match("/\.(mp4|mov|avi)$/i", $prod['media'])): ?>
                                <video src="../uploads/<?php echo $prod['media']; ?>" controls width="300"></video>
                            <?php else: ?>
                                <img src="../uploads/<?php echo $prod['media']; ?>" width="300">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Status assignment -->
                    <?php if ($status == 'pending'): ?>
                        <form method="POST" action="../actions/confirm_action.php">
                            <input type="hidden" name="product_id" value="<?php echo $prod_id; ?>">
                            <button type="submit">Konfirmasi Penerimaan</button>
                        </form>
                    <?php elseif ($status == 'approved'): ?>
                        <!-- Form review -->
                        <form method="POST" action="../actions/review_action.php" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="<?php echo $prod_id; ?>">

                            <label>Rating:</label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i . '_' . $prod_id; ?>">
                                    <label for="star<?php echo $i . '_' . $prod_id; ?>">&#9733;</label>
                                <?php endfor; ?>
                            </div>

                            <label>Review (maks 300 kata):</label>
                            <textarea name="review_text" data-counter="counter_<?php echo $prod_id; ?>"></textarea>
                            <div id="counter_<?php echo $prod_id; ?>">0 / 300 kata</div>

                            <label>Opsi ulasan:</label>
                            <div class="review-options">
                                <?php foreach ($review_options as $opt): ?>
                                    <input type="checkbox" name="review_options[]" value="<?php echo $opt['id']; ?>" id="opt_<?php echo $prod_id . '_' . $opt['id']; ?>">
                                    <label for="opt_<?php echo $prod_id . '_' . $opt['id']; ?>"><?php echo htmlspecialchars($opt['option_text']); ?></label>
                                <?php endforeach; ?>
                            </div>

                            <label>Upload Media (≤5MB):</label>
                            <input type="file" name="media_review" accept="image/*,video/*">

                            <button type="submit">Kirim Review</button>
                        </form>
                    <?php else: ?>
                        <p>Status: <?php echo $status ?? 'Tidak ditugaskan'; ?></p>
                    <?php endif; ?>

                    <!-- Review terbaru -->
                    <div class="reviews">
                        <h4>Review terbaru:</h4>
                        <?php if ($reviews): $c = 0;
                            foreach ($reviews as $rev): if ($c >= 2) break; $c++; ?>
                                <p><strong><?php echo '@' . substr($rev['username'], 0, 1) . '*****'; ?></strong>: <?php echo htmlspecialchars($rev['review_text']); ?> (⭐<?php echo $rev['rating']; ?>)</p>
                                <?php if ($rev['media']): ?>
                                    <?php if (preg_match("/\.(mp4|mov|avi)$/i", $rev['media'])): ?>
                                        <video src="../uploads/<?php echo $rev['media']; ?>" controls width="250"></video>
                                    <?php else: ?>
                                        <img src="../uploads/<?php echo $rev['media']; ?>" width="250">
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach;
                            if (count($reviews) > 2): ?>
                                <p><a href="ringkasan.php?product_id=<?php echo $prod_id; ?>">> Lihat semua</a></p>
                            <?php endif;
                        else: ?>
                            <p>Belum ada review 😅</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada produk 😭</p>
        <?php endif; ?>
    </div>
</body>
</html>
