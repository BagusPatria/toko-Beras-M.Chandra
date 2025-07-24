<?php
// MULAI: Baris Debugging - Hapus atau jadikan komentar saat produksi!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// AKHIR: Baris Debugging

require_once 'config.php';

// Pastikan pengguna sudah login
if (!isLoggedIn()) {
    redirect('login.php');
}

// --- Kontrol Akses: Hanya peran 'admin' dan 'pimpinan' yang dapat mengakses dashboard ---
// Jika peran pengguna bukan 'admin' DAN bukan 'pimpinan', arahkan mereka
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'pimpinan') {
    redirect('akses_ditolak.php'); // Atau ke halaman yang lebih sesuai, misal 'login.php' atau 'index.php'
    exit(); // Hentikan eksekusi skrip setelah pengalihan
}
// --- Akhir Kontrol Akses ---

$pageTitle = "Dashboard";
include 'header.php'; // Pastikan header.php menangani visibilitas menu berdasarkan $_SESSION['role']

// Hitung total data
$total_beras = $conn->query("SELECT COUNT(*) as total FROM beras")->fetch_assoc()['total'];
$total_supplier = $conn->query("SELECT COUNT(*) as total FROM supplier")->fetch_assoc()['total'];

// Stok menipis (kurang dari 50) - Hanya nama dan stok
$stok_menipis_query = "SELECT nama_beras, stok FROM beras WHERE stok < 50 ORDER BY stok ASC LIMIT 5";
$stok_menipis = $conn->query($stok_menipis_query)->fetch_all(MYSQLI_ASSOC);

// Query untuk semua stok beras: nama beras, kualitas, stok, deskripsi
$semua_stok_dashboard_query = "
    SELECT
        nama_beras,
        kualitas,
        stok,
        deskripsi
    FROM
        beras
    ORDER BY
        nama_beras ASC";

$semua_stok_dashboard = $conn->query($semua_stok_dashboard_query)->fetch_all(MYSQLI_ASSOC);

// --- QUERY UNTUK DATA PEMASOK DENGAN MEREK BERAS YANG DISEDIAKAN ---
// Ambil data pemasok untuk tampilan dashboard (terlihat oleh admin dan pimpinan)
$query_suppliers = "
    SELECT
        s.id,
        s.nama_supplier,
        s.alamat_supplier,
        s.telepon,
        s.email,
        GROUP_CONCAT(b.nama_beras ORDER BY b.nama_beras SEPARATOR ', ') AS merek_beras_disediakan
    FROM
        supplier s
    LEFT JOIN
        beras b ON s.id = b.id_supplier
    GROUP BY
        s.id, s.nama_supplier, s.alamat_supplier, s.telepon, s.email
    ORDER BY
        s.nama_supplier ASC";
$suppliers_dashboard = $conn->query($query_suppliers)->fetch_all(MYSQLI_ASSOC);

?>

<div class="container">
    <h1>Dashboard</h1>
    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?> (Peran: <?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?>)!</p>

    <div class="summary-cards">
        <div class="card">
            <h3>Total Jenis Beras</h3>
            <p><?php echo $total_beras; ?></p>
        </div>

        <?php if ($_SESSION['role'] === 'admin'): // Kartu Total Pemasok hanya untuk admin ?>
        <div class="card">
            <h3>Total Pemasok</h3>
            <p><?php echo $total_supplier; ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div class="grid-container">
        <div class="card">
            <h3>Stok Menipis (Kurang dari 50 kg)</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Beras</th>
                            <th>Stok (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stok_menipis)): ?>
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada stok beras yang menipis.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stok_menipis as $key => $beras): ?>
                                <tr>
                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo htmlspecialchars($beras['nama_beras']); ?></td>
                                    <td class="<?php echo $beras['stok'] < 10 ? 'danger' : 'warning'; ?>">
                                        <?php echo number_format($beras['stok'], 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Semua Stok Beras</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Beras</th>
                            <th>Kualitas</th>
                            <th>Stok (kg)</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($semua_stok_dashboard)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data stok beras.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($semua_stok_dashboard as $key => $item): ?>
                                <tr>
                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['nama_beras']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($item['kualitas'] ?? '-')); ?></td>
                                    <td><?php echo number_format($item['stok'], 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($item['deskripsi'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <h3>Daftar Pemasok</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pemasok</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Merek Beras yang Disediakan</th> </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($suppliers_dashboard)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pemasok.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($suppliers_dashboard as $key => $supplier): ?>
                                <tr>
                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo htmlspecialchars($supplier['nama_supplier']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['alamat_supplier'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['telepon'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['merek_beras_disediakan'] ?? '-'); ?></td> </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>