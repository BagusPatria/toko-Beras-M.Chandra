<?php
require_once 'config.php';

// Pastikan pengguna sudah login
if (!isLoggedIn()) {
    redirect('login.php');
}

// --- Kontrol Akses: Hanya admin dan pimpinan yang boleh mengakses halaman ini ---
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'pimpinan') {
    redirect('akses_ditolak.php');
}
// --- Akhir Kontrol Akses ---

$pageTitle = "Detail Beras (Produk)"; // Ubah judul halaman
include 'header.php';

// --- Logika Pengambilan Data Beras Lengkap ---
// Ambil semua data beras yang diperlukan
$query_beras_detail = "SELECT b.nama_beras, b.kualitas, b.harga_beli, b.harga_jual, b.stok, b.deskripsi, k.nama_kategori 
                       FROM beras b 
                       LEFT JOIN kategori_beras k ON b.id_kategori = k.id 
                       ORDER BY b.nama_beras";
$daftar_beras_lengkap = $conn->query($query_beras_detail)->fetch_all(MYSQLI_ASSOC);

// Batas stok menipis
$batas_stok_menipis = 50; 
?>

<div class="card">
    <h2>Daftar Detail Beras</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <h3>Informasi Detail Setiap Jenis Beras</h3>
        <div class="table-responsive">
            <table class="table" id="tabel-laporan-beras"> <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Beras</th>
                        <th>Kategori</th>
                        <th>Kualitas</th>
                        <th>Harga Beli (per kg)</th>
                        <th>Harga Jual (per kg)</th>
                        <th>Stok Saat Ini (kg)</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_beras_lengkap)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data beras yang ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftar_beras_lengkap as $key => $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($key + 1); ?></td>
                                <td><?php echo htmlspecialchars($item['nama_beras']); ?></td>
                                <td><?php echo htmlspecialchars($item['nama_kategori'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($item['kualitas'])); ?></td>
                                <td>Rp <?php echo htmlspecialchars(number_format($item['harga_beli'], 0, ',', '.')); ?></td>
                                <td>Rp <?php echo htmlspecialchars(number_format($item['harga_jual'], 0, ',', '.')); ?></td>
                                <td>
                                    <?php 
                                    $stok_display = htmlspecialchars(number_format($item['stok'], 2, ',', '.'));
                                    if ($item['stok'] < $batas_stok_menipis) {
                                        echo '<span style="color: red; font-weight: bold;">' . $stok_display . ' (Menipis!)</span>';
                                    } else {
                                        echo $stok_display;
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['deskripsi'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <button class="btn btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>