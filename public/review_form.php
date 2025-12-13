<?php
session_start();
require_once "../config/db.php";

// Cek login
if(!isset($_SESSION['user_id'])) die("Akses ditolak 😅");

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id'] ?? 0);

// Ambil produk
$res = $conn->query("SELECT * FROM products WHERE id=$product_id");
if($res->num_rows==0) die("Produk tidak ditemukan 😭");
$product = $res->fetch_assoc();

// Ambil review user
$review_res = $conn->query("SELECT * FROM reviews WHERE user_id='$user_id' AND product_id='$product_id'");
$review = $review_res->fetch_assoc() ?? null;

// Ambil opsi review
$option_res = $conn->query("SELECT * FROM review_options ORDER BY id ASC");
$options = [];
while($row = $option_res->fetch_assoc()) $options[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Review Produk</title>
<style>
#word_count { font-weight:bold; }
</style>
</head>
<body>
<h2><?php echo htmlspecialchars($product['nama_produk']); ?></h2>

<!-- Form review sederhana -->
<form method="POST" action="../submit_review/submit_review.php" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

    <label>Review (maks 300 kata):</label><br>
    <textarea id="review_text" name="review_text" rows="5" required><?php echo htmlspecialchars($review['review_text'] ?? ''); ?></textarea>
    <div>Jumlah kata: <span id="word_count">0</span> / 300</div>

    <label>Upload Media:</label>
    <input type="file" name="media_review" accept="image/*,video/*">

    <button type="submit">Kirim Review</button>
</form>

<!-- <script>
// Hitung jumlah kata realtime
const textarea = document.getElementById('review_text');
const counter = document.getElementById('word_count');

function updateCount() {
    let words = textarea.value.trim().split(/\s+/).filter(Boolean).length;
    counter.textContent = words;

    if(words > 300){
        alert("Maksimal 300 kata ya 😅");
        let limited = textarea.value.trim().split(/\s+/).slice(0,300).join(" ");
        textarea.value = limited;
        counter.textContent = 300;
    }
}

textarea.addEventListener('input', updateCount);
window.addEventListener('load', updateCount);
</script> -->
</body>
</html>
