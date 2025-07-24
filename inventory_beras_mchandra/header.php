<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Toko Beras M.Chandra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Definisi variabel warna */
        :root {
            --primary: #4CAF50; /* Hijau utama */
            --primary-dark: #388E3C; /* Hijau gelap */
            --secondary: #FF9800; /* Oranye */
            --danger: #F44336; /* Merah bahaya */
            --warning: #FFC107; /* Kuning peringatan */
            --light: #F5F5F5; /* Abu-abu terang */
            --dark: #212121; /* Abu-abu sangat gelap */
            --gray: #757575; /* Abu-abu sedang */
        }

        /* Reset dan pengaturan dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #F5F5F5;
            color: var(--dark);
        }

        /* Kontainer umum untuk konten */
        .container {
            width: 90%;
            max-width: 1600px; /* Further increased for an even larger dashboard */
            margin: 0 auto;
            padding: 30px; /* Increased padding */
        }

        /* Header halaman */
        header {
            background-color: var(--primary);
            color: white;
            padding: 25px 0; /* Increased padding */
            box-shadow: 0 3px 8px rgba(0,0,0,0.15); /* Stronger shadow */
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            max-width: 1600px; /* Further increased */
            margin: 0 auto;
        }

        .logo {
            font-size: 32px; /* Larger font */
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 25px; /* Increased gap */
        }

        .user-info span {
            font-size: 1.2em; /* Larger font for user info */
        }

        .user-info img {
            width: 50px; /* Larger image */
            height: 50px; /* Larger image */
            border-radius: 50%;
            object-fit: cover;
        }

        /* Navigasi */
        nav {
            background-color: var(--primary-dark);
        }

        .nav-container {
            display: flex;
            width: 90%;
            max-width: 1600px; /* Further increased */
            margin: 0 auto;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 20px 30px; /* Significantly increased padding */
            display: block;
            transition: background-color 0.3s;
            font-size: 1.15em; /* Larger font for nav links */
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.15); /* Slightly more prominent hover */
        }

        .nav-link i {
            margin-right: 12px; /* Increased margin */
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); /* Adjusted minmax */
            gap: 30px; /* Increased gap */
            margin: 30px 0; /* Increased margin */
            justify-content: center;
        }

        .card {
            background-color: white;
            border-radius: 12px; /* Larger border-radius */
            padding: 30px; /* Increased padding */
            box-shadow: 0 6px 15px rgba(0,0,0,0.1); /* Stronger shadow */
        }

        .card h3 {
            color: var(--gray);
            margin-bottom: 15px; /* Increased margin */
            font-size: 20px; /* Larger font */
        }

        .card p {
            font-size: 32px; /* Larger font */
            font-weight: bold;
            color: var(--dark);
        }

        /* Grid Container */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(650px, 1fr)); /* Adjusted minmax for larger cards */
            gap: 30px; /* Increased gap */
            margin-top: 30px; /* Increased margin */
            justify-content: center;
        }

        /* --- PERBAIKAN UTAMA: STYLES UNTUK TABEL YANG LEBIH PRESISI DAN LEBIH BESAR --- */
        .table-wrapper {
            max-height: 650px; /* Significantly increased max-height for larger tables */
            overflow-y: auto;
            overflow-x: auto; 
            border: 1px solid #b0b0b0; /* Darker border */
            border-radius: 12px; /* Larger border-radius */
            box-shadow: 0 6px 15px rgba(0,0,0,0.08); /* Stronger shadow */
        }

        .table-wrapper-small {
            max-height: 380px; /* Significantly increased max-height for smaller tables */
            overflow-y: auto;
            overflow-x: auto; 
            border: 1px solid #b0b0b0; /* Darker border */
            border-radius: 12px; /* Larger border-radius */
            box-shadow: 0 6px 15px rgba(0,0,0,0.08); /* Stronger shadow */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 18px 22px; /* Even more increased padding for more space */
            text-align: left;
            border-bottom: 3px solid #ddd; /* Thicker border */
            background-color: var(--primary);
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
            white-space: nowrap; 
            font-size: 17px; /* Larger font for headers */
        }

        td {
            padding: 16px 22px; /* Even more increased padding for more space */
            text-align: left;
            border-bottom: 1px solid #eee; /* Lighter border */
            vertical-align: top; 
            white-space: normal; 
            word-wrap: break-word; 
            word-break: break-word; 
            font-size: 16px; /* Larger font for content */
            line-height: 1.6; /* Increased line height for readability */
        }

        /* Untuk sel yang berisi teks panjang (seperti deskripsi) */
        td.wrap-content {
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Untuk sel kosong */
        td:empty::after {
            content: "-";
            color: var(--gray);
        }

        /* Penyesuaian lebar kolom untuk tabel "Semua Stok Beras" */
        .card:has(h3:contains("Semua Stok Beras")) table {
            table-layout: fixed; /* Use fixed layout for better control */
            min-width: 1050px; /* Significantly increased min-width for the rice stock table */
        }

        .card:has(h3:contains("Semua Stok Beras")) th:nth-child(1),
        .card:has(h3:contains("Semua Stok Beras")) td:nth-child(1) {
            width: 5%;
            min-width: 60px; 
        }

        .card:has(h3:contains("Semua Stok Beras")) th:nth-child(2),
        .card:has(h3:contains("Semua Stok Beras")) td:nth-child(2) {
            width: 18%; /* Adjusted width */
            min-width: 200px; /* Increased min-width for name */
        }

        .card:has(h3:contains("Semua Stok Beras")) th:nth-child(3),
        .card:has(h3:contains("Semua Stok Beras")) td:nth-child(3) {
            width: 15%;
            min-width: 140px; /* Increased min-width for type */
        }

        .card:has(h3:contains("Semua Stok Beras")) th:nth-child(4),
        .card:has(h3:contains("Semua Stok Beras")) td:nth-child(4) {
            width: 12%; /* Adjusted width */
            min-width: 100px;
            text-align: right; 
            white-space: nowrap; 
        }

        .card:has(h3:contains("Semua Stok Beras")) th:nth-child(5),
        .card:has(h3:contains("Semua Stok Beras")) td:nth-child(5) {
            width: 50%; /* Adjusted width for description */
            min-width: 450px; /* Significantly increased min-width for description */
            white-space: normal; 
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Penyesuaian lebar kolom untuk tabel "Daftar Pemasok" */
        .card:has(h3:contains("Daftar Pemasok")) table {
            table-layout: fixed; /* Use fixed layout for better control */
            min-width: 1200px; /* Significantly increased min-width for the supplier table */
        }

        .card:has(h3:contains("Daftar Pemasok")) th:nth-child(1),
        .card:has(h3:contains("Daftar Pemasok")) td:nth-child(1) {
            width: 5%;
            min-width: 60px;
        }

        .card:has(h3:contains("Daftar Pemasok")) th:nth-child(2),
        .card:has(h3:contains("Daftar Pemasok")) td:nth-child(2) {
            width: 15%; 
            min-width: 180px; /* Increased min-width for name */
        }

        .card:has(h3:contains("Daftar Pemasok")) th:nth-child(3),
        .card:has(h3:contains("Daftar Pemasok")) td:nth-child(3) {
            width: 25%;
            min-width: 250px; /* Increased min-width for address */
            white-space: normal; 
            word-wrap: break-word;
            word-break: break-word;
        }

        .card:has(h3:contains("Daftar Pemasok")) th:nth-child(4),
        .card:has(h3:contains("Daftar Pemasok")) td:nth-child(4) {
            width: 15%;
            min-width: 140px; /* Increased min-width for phone */
            white-space: nowrap; 
        }

        .card:has(h3:contains("Daftar Pemasok")) th:nth-child(5),
        .card:has(h3:contains("Daftar Pemasok")) td:nth-child(5) {
            width: 20%;
            min-width: 180px; /* Increased min-width for email */
            white-space: nowrap; 
        }

        .card:has(h3:contains("Daftar Pemasok")) th:nth-child(6),
        .card:has(h3:contains("Daftar Pemasok")) td:nth-child(6) {
            width: 20%;
            min-width: 200px; /* Increased min-width for notes */
            white-space: normal; 
            word-wrap: break-word;
            word-break: break-word;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .danger {
            color: var(--danger);
            font-weight: bold;
        }

        .warning {
            color: var(--warning);
            font-weight: bold;
        }

        /* Tombol */
        .btn {
            display: inline-block;
            padding: 12px 24px; /* Increased padding */
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 6px; /* Larger border-radius */
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 1.05em; /* Slightly larger font */
        }

        .btn:hover {
            background-color: var(--primary-dark);
        }

        .btn-danger {
            background-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #D32F2F;
        }

        .btn-warning {
            background-color: var(--warning);
            color: var(--dark);
        }

        .btn-warning:hover {
            background-color: #FFA000;
        }

        /* Grup formulir */
        .form-group {
            margin-bottom: 25px; /* Increased margin */
        }

        .form-group label {
            display: block;
            margin-bottom: 10px; /* Increased margin */
            font-weight: bold;
            font-size: 1.1em; /* Larger font */
        }

        .form-control {
            width: 100%;
            padding: 14px; /* Increased padding */
            border: 1px solid #bbb; /* Darker border */
            border-radius: 6px; /* Larger border-radius */
            font-size: 18px; /* Larger font */
        }

        textarea.form-control {
            min-height: 140px; /* Increased min-height */
        }

        /* Footer halaman */
        footer {
            background-color: var(--dark);
            color: white;
            text-align: center;
            padding: 30px 0; /* Increased padding */
            margin-top: 50px; /* Increased margin */
            font-size: 1.05em; /* Slightly larger font */
        }

        /* Media queries untuk responsivitas */
        @media (max-width: 1200px) { /* Adjusted breakpoint */
            .grid-container {
                grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); /* Adjust for smaller screens */
            }
            .summary-cards {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
            .table-wrapper, .table-wrapper-small {
                max-height: 500px; /* Reduce max height on smaller screens */
            }
            /* Adjust min-width for tables on smaller large screens */
            .card:has(h3:contains("Semua Stok Beras")) table {
                min-width: 900px;
            }
            .card:has(h3:contains("Daftar Pemasok")) table {
                min-width: 1000px;
            }
        }

        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .user-info {
                flex-direction: column;
                gap: 10px;
            }

            .nav-container {
                flex-direction: column;
                align-items: stretch; /* Stretch items to full width */
            }
            .nav-link {
                padding: 15px 20px;
                text-align: center;
                font-size: 1em;
            }

            /* Penyesuaian tabel untuk mobile */
            .table-wrapper, .table-wrapper-small {
                max-height: none;
                overflow-x: auto; 
            }

            /* Ensure fixed layout tables still scroll horizontally on small screens */
            .card:has(h3:contains("Semua Stok Beras")) table,
            .card:has(h3:contains("Daftar Pemasok")) table {
                min-width: 750px; /* Ensure horizontal scroll on mobile */
            }
            th, td {
                padding: 12px 15px; /* Reduce padding on smaller screens */
                font-size: 14px; /* Smaller font on mobile */
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 24px;
            }
            .user-info span {
                font-size: 0.9em;
            }
            .btn {
                padding: 8px 16px;
                font-size: 0.9em;
            }
            .card h3 {
                font-size: 16px;
            }
            .card p {
                font-size: 24px;
            }
            th, td {
                padding: 10px 12px;
                font-size: 13px;
            }
            /* Even smaller min-width for tables on very small screens */
            .card:has(h3:contains("Semua Stok Beras")) table,
            .card:has(h3:contains("Daftar Pemasok")) table {
                min-width: 600px;
            }
        }

        /* --- Gaya Khusus untuk Cetak --- */
        @media print {
            /* Penting: Memastikan warna latar belakang dan warna teks tercetak */
            body {
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Pastikan font konsisten */
                font-size: 10pt; /* Ukuran font lebih kecil untuk cetak */
                color: #333; /* Warna teks gelap */
                margin: 0; /* Hapus margin halaman */
                padding: 0;
            }

            /* Sembunyikan elemen yang tidak perlu dicetak */
            header, nav, footer, .alert, .btn, .user-info, .nav-link, .mt-3 {
                display: none !important;
            }

            /* Tampilkan kembali judul utama dan sub-judul */
            h2, h3 {
                display: block !important;
                text-align: center;
                color: var(--primary-dark); /* Warna hijau gelap untuk judul cetak */
                margin-bottom: 15px;
            }

            h2 {
                font-size: 24pt; /* Ukuran judul utama yang lebih besar */
                margin-top: 30px;
            }

            h3 {
                font-size: 18pt; /* Ukuran sub-judul yang lebih besar */
            }

            /* Atur ulang kontainer utama agar tidak ada padding berlebihan */
            .container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 10mm !important; /* Gunakan satuan fisik untuk margin cetak */
            }

            /* Hapus bayangan dan border card saat dicetak */
            .card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Gaya tabel untuk cetak */
            .table-responsive {
                overflow: visible !important; /* Pastikan tabel tidak terpotong */
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 20px !important;
                page-break-inside: auto; /* Memungkinkan tabel terpotong jika terlalu panjang */
            }

            table th, table td {
                border: 1px solid #ccc !important; /* Border sel yang lebih jelas */
                padding: 8px 12px !important; /* Padding yang seragam */
                text-align: left !important;
                font-size: 9pt !important; /* Ukuran font sel yang lebih kecil */
                vertical-align: top !important; /* Pastikan konten sel rapi */
                line-height: 1.3 !important;
            }

            table th {
                background-color: var(--primary) !important; /* Warna latar belakang header */
                color: white !important; /* Warna teks header */
                font-weight: bold !important;
            }

            /* Warna latar belakang baris ganjil/genap */
            table tbody tr:nth-child(odd) {
                background-color: #ffffff !important;
            }
            table tbody tr:nth-child(even) {
                background-color: #f2f2f2 !important; /* Warna abu-abu terang */
            }

            /* Styling untuk stok menipis (pastikan tetap merah) */
            table tbody td span[style*="color: red"] {
                color: #dc3545 !important; /* Merah yang lebih standar */
                font-weight: bold !important;
            }

            /* Pastikan gambar profil atau elemen lain yang tidak perlu tercetak tidak muncul */
            .user-info img {
                display: none !important;
            }

            /* Header dan footer cetak kustom (opsional) */
            @page {
                size: A4 portrait; /* Ukuran kertas dan orientasi */
                margin: 20mm; /* Atur margin halaman cetak */
            }
            
            /* Untuk memastikan tabel tetap pada satu halaman jika memungkinkan */
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; page-break-after: auto; }

            /* Penyesuaian lebar kolom untuk laporan stok beras agar tidak berantakan */
            /* Ini akan menimpa pengaturan di atas untuk tabel laporan beras */
            .card:has(h2:contains("Daftar Detail Beras")) table,
            .card:has(h3:contains("Informasi Detail Setiap Jenis Beras")) table {
                table-layout: fixed !important;
                width: 100% !important; /* Pastikan tabel mengambil lebar penuh halaman cetak */
            }

            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(1),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(1) { width: 4% !important; } /* No */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(2),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(2) { width: 14% !important; } /* Nama Beras */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(3),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(3) { width: 10% !important; } /* Kategori */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(4),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(4) { width: 8% !important; } /* Kualitas */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(5),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(5) { width: 10% !important; } /* Harga Beli */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(6),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(6) { width: 10% !important; } /* Harga Jual */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(7),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(7) { width: 8% !important; } /* Stok */
            .card:has(h2:contains("Daftar Detail Beras")) th:nth-child(8),
            .card:has(h2:contains("Daftar Detail Beras")) td:nth-child(8) { 
                width: 36% !important; /* Deskripsi */
                white-space: normal !important; /* Pastikan teks membungkus */
                word-wrap: break-word !important;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">Toko Beras M.Chandra</div>
            <div class="user-info">
                <span><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Tamu'); ?> (<?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? '')); ?>)</span>
                <a href="logout.php" class="btn btn-danger">Keluar</a>
            </div>
        </div>
    </header>

    <nav>
        <div class="nav-container">
            <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <?php if (isset($_SESSION['role'])): ?>
                <?php
                $isAdmin = ($_SESSION['role'] === 'admin');
                $isPimpinan = ($_SESSION['role'] === 'pimpinan');
                ?>

                <?php if ($isAdmin): ?>
                    <a href="beras.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'beras.php' ? 'active' : ''; ?>">
                        <i class="fas fa-wheat"></i> Data Beras
                    </a>
                    <a href="supplier.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'supplier.php' ? 'active' : ''; ?>">
                        <i class="fas fa-truck"></i> Supplier
                    </a>
                <?php endif; ?>

                <?php if ($isAdmin || $isPimpinan): ?>
                    <a href="laporan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </nav>

    <main class="container">