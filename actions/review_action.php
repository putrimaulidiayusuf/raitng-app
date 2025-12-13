<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) die("Akses ditolak 😅");
if (!isset($_POST['product_id'], $_POST['rating'], $_POST['review_text'])) die("Data tidak lengkap 😭");

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$rating = (int)$_POST['rating'];
$review_text = trim($_POST['review_text']);

// Ambil opsi review jika ada
$review_option = null;
if (isset($_POST['review_options']) && is_array($_POST['review_options'])) {
    $options_text = [];
    foreach ($_POST['review_options'] as $opt_id) {
        $res = $conn->query("SELECT option_text FROM review_options WHERE id='$opt_id'");
        if ($res && $row = $res->fetch_assoc()) $options_text[] = $row['option_text'];
    }
    if ($options_text) $review_option = implode(", ", $options_text);
}

// Batasi kata 300
$words = preg_split('/\s+/', $review_text);
if (count($words) > 300) {
    $review_text = implode(' ', array_slice($words, 0, 300));
}

// Upload media review
$media = null;
if (isset($_FILES['media_review']) && $_FILES['media_review']['error'] == 0) {
    $file = $_FILES['media_review'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'];
    if (!in_array($ext, $allowed)) die("Format file tidak diijinkan 😅");
    if ($file['size'] > 5 * 1024 * 1024) die("File terlalu besar 😭");
    $media = uniqid() . '.' . $ext;
    move_uploaded_file($file['tmp_name'], "../uploads/" . $media);
}

// Cek review lama untuk edit count
$res_check = $conn->query("SELECT * FROM reviews WHERE user_id='$user_id' AND product_id='$product_id'");
if ($res_check->num_rows > 0) {
    $row = $res_check->fetch_assoc();
    if ($row['edit_count'] >= 3) die("Ulasan sudah di edit maksimal 3x 😭");
    $edit_count = $row['edit_count'] + 1;
    $rev_id = $row['id'];
    $stmt = $conn->prepare("UPDATE reviews SET review_text=?, rating=?, media=?, review_option=?, edit_count=? WHERE id=?");
    $stmt->bind_param("sisisi", $review_text, $rating, $media, $review_option, $edit_count, $rev_id);
    $stmt->execute();
} else {
    $edit_count = 0;
    $stmt = $conn->prepare("INSERT INTO reviews (user_id,product_id,rating,review_text,media,review_option,edit_count) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("iiisssi", $user_id, $product_id, $rating, $review_text, $media, $review_option, $edit_count);
    $stmt->execute();
}

header("Location: ../public/product.php");
exit;
