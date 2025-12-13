<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../public/index.php");
    exit;
}

// PhpSpreadsheet
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Reviews');

// Header
$sheet->setCellValue('A1', 'ID Review');
$sheet->setCellValue('B1', 'Produk');
$sheet->setCellValue('C1', 'User');
$sheet->setCellValue('D1', 'Rating');
$sheet->setCellValue('E1', 'Review Text');
$sheet->setCellValue('F1', 'Edit Count');
$sheet->setCellValue('G1', 'Deleted');

// Ambil data
$sql = "SELECT r.id, p.nama_produk, u.username, r.rating, r.review_text, r.edit_count, r.deleted
        FROM reviews r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.id ASC";
$result = $conn->query($sql);

$rowNum = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $row['id']);
    $sheet->setCellValue('B' . $rowNum, $row['nama_produk']);
    $sheet->setCellValue('C' . $rowNum, $row['username']);
    $sheet->setCellValue('D' . $rowNum, $row['rating']);
    $sheet->setCellValue('E' . $rowNum, $row['review_text']);
    $sheet->setCellValue('F' . $rowNum, $row['edit_count']);
    $sheet->setCellValue('G' . $rowNum, $row['deleted'] ? 'Ya' : 'Tidak');
    $rowNum++;
}

// Download
$filename = 'laporan_reviews.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
