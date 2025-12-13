<?php
session_start();
require_once "../config/db.php";

// Pastikan user login
if(!isset($_SESSION['user_id'])) die("Akses ditolak 😅");

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id'] ?? 0);

// Ambil produk berdasarkan id
$res = $conn->query("SELECT * FROM products WHERE id=$product_id");
if($res->num_rows==0) die("Produk tidak ditemukan 😭");
$product = $res->fetch_assoc();

// Ambil review user sebelumnya (kalau ada)
$review_res = $conn->query("SELECT * FROM reviews WHERE user_id='$user_id' AND product_id='$product_id'");
$review = $review_res->fetch_assoc() ?? null;

// Ambil daftar opsi review
$option_res = $conn->query("SELECT * FROM review_options ORDER BY id ASC");
$options = [];
while($row=$option_res->fetch_assoc()) $options[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Review Produk</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h2><?php echo htmlspecialchars($product['nama_produk']); ?></h2>
<p><?php echo htmlspecialchars($product['deskripsi']); ?></p>
<p>Harga: Rp <?php echo number_format($product['harga']); ?></p>

<!-- Form review -->
<form method="POST" action="../actions/submit_review.php" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

    <!-- Rating bintang -->
    <label>Rating:</label>
    <div class="star-rating">
        <?php
        $current_rating = $review['rating'] ?? 0;
        for($i=5;$i>=1;$i--):
        ?>
        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php if($current_rating==$i) echo 'checked'; ?>>
        <label for="star<?php echo $i; ?>">★</label>
        <?php endfor; ?>
    </div>

    <!-- Textarea review -->
    <label>Review (maks 300 kata / 1500 karakter):</label>
    <textarea id="review_text" name="review_text" rows="5" placeholder="Tulis review kamu..." required
              data-counter="counter_words" data-char="counter_chars"><?php echo htmlspecialchars($review['review_text'] ?? ''); ?></textarea>
    <div class="char-counter">
        Kata: <span id="counter_words">0</span>/300 |
        Karakter: <span id="counter_chars">0</span>/1500
    </div>

    <!-- Checklist opsi ulasan -->
    <label>Opsi ulasan:</label>
    <div class="review-options">
        <?php foreach($options as $opt): 
            $checked = $review && $review['review_option'] && strpos($review['review_option'],$opt['option_text'])!==false ? 'checked' : '';
        ?>
        <input type="checkbox" id="opt<?php echo $opt['id']; ?>" name="review_options[]" value="<?php echo $opt['id']; ?>" <?php echo $checked; ?>>
        <label for="opt<?php echo $opt['id']; ?>"><?php echo htmlspecialchars($opt['option_text']); ?></label>
        <?php endforeach; ?>
    </div>

    <!-- Upload media -->
    <label>Upload Media (≤5MB):</label>
    <input type="file" name="media_review" accept="image/*,video/*" data-preview="preview_media">
    <div id="preview_media"></div>

    <button type="submit">Kirim Review</button>
</form>

<script src="assets/js/app.js"></script>
</body>
</html>
