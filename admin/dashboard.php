<?php
session_start();

// Pastikan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../public/index.php");
    exit;
}

require_once "../config/db.php";

// Ringkasan produk & review
$sql_produk = "SELECT COUNT(*) AS total_produk FROM products";
$total_produk = $conn->query($sql_produk)->fetch_assoc()['total_produk'];

$sql_review = "SELECT COUNT(*) AS total_review FROM reviews";
$total_review = $conn->query($sql_review)->fetch_assoc()['total_review'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        /* ==============================================
           RESET & GLOBAL
        ============================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8f0ff, #e0f7ff, #f0faff);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ==============================================
           LINKS
        ============================================== */
        a {
            text-decoration: none;
            color: #9b59b6;
            font-weight: bold;
            transition: 0.3s;
        }

        a:hover {
            color: #3498db;
        }

        /* ==============================================
           HEADER & GREETING
        ============================================== */
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        p a {
            display: inline-block;
            margin-top: 10px;
            background: linear-gradient(135deg, #9b59b6, #3498db);
            padding: 8px 18px;
            border-radius: 50px;
            color: #fff;
            font-weight: 600;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(155, 89, 182, 0.4);
        }

        p a:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        /* ==============================================
           DASHBOARD SUMMARY
        ============================================== */
        .dashboard-summary {
            background: #fff;
            padding: 25px;
            margin: 25px auto;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .dashboard-summary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(155, 89, 182, 0.3);
        }

        .dashboard-summary h3 {
            text-align: center;
            color: #9b59b6;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .dashboard-summary ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .dashboard-summary ul li {
            background: linear-gradient(135deg, #9b59b6, #3498db);
            color: #fff;
            padding: 15px 20px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .dashboard-summary ul li:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        /* ==============================================
           DASHBOARD MENU
        ============================================== */
        .dashboard-menu {
            background: #fff;
            padding: 25px;
            margin: 25px auto;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .dashboard-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(52, 152, 219, 0.3);
        }

        .dashboard-menu h3 {
            text-align: center;
            color: #3498db;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .dashboard-menu ul {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .dashboard-menu ul li a {
            display: block;
            background: linear-gradient(135deg, #9b59b6, #3498db);
            color: #fff;
            padding: 15px;
            border-radius: 15px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .dashboard-menu ul li a:hover {
            filter: brightness(1.15);
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        /* ==============================================
           TABLES (kalau nanti dipakai di dashboard)
        ============================================== */
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border-bottom: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
            font-size: 0.95rem;
        }

        th {
            background: linear-gradient(135deg, #9b59b6, #3498db);
            color: #fff;
        }

        tr:hover {
            background-color: #f8f0ff;
        }

        /* ==============================================
           FOOTER
        ============================================== */
        footer {
            margin-top: auto;
            text-align: center;
            padding: 15px 0;
            background: linear-gradient(135deg, #9b59b6, #3498db);
            color: #fff;
            border-radius: 15px;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* ==============================================
           RESPONSIVE
        ============================================== */
        @media (max-width:768px) {
            body {
                padding: 15px;
            }

            h2 {
                font-size: 1.6rem;
            }

            .dashboard-summary,
            .dashboard-menu {
                padding: 20px;
            }

            .dashboard-menu ul {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <h2>Hai Admin, <?= $_SESSION['username']; ?>!</h2>
    <p><a href="../public/logout.php">Logout</a></p>

    <div class="dashboard-summary">
        <h3>Ringkasan:</h3>
        <ul>
            <li>Total Produk: <?= $total_produk; ?></li>
            <li>Total Review: <?= $total_review; ?></li>
        </ul>
    </div>

    <div class="dashboard-menu">
        <h3>Menu Cepat:</h3>
        <ul>
            <li><a href="products.php">CRUD Produk</a></li>
            <li><a href="confirm_list.php">Konfirmasi Produk User</a></li>
            <li><a href="review_options.php">Opsi Ulasan</a></li>
            <li><a href="export.php">Export Laporan Excel</a></li>
        </ul>
    </div>

    <footer>
        <p>© <?= date("Y"); ?> Dashboard Admin | Fresh Design</p>
    </footer>
</body>

</html>
