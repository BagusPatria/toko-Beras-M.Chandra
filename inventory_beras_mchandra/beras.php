<?php
require_once 'config.php';

// Pastikan pengguna sudah login
if (!isLoggedIn()) {
    redirect('login.php');
}

// --- Kontrol Akses: Hanya admin yang boleh mengakses halaman ini ---
// Jika peran pengguna bukan 'admin', arahkan mereka ke halaman akses ditolak atau dashboard
if ($_SESSION['role'] !== 'admin') {
    redirect('akses_ditolak.php'); // Ganti dengan halaman yang sesuai jika Anda punya
    // Atau bisa juga redirect ke dashboard:
    // redirect('dashboard.php'); 
}
// --- Akhir Kontrol Akses ---

$pageTitle = "Data Beras";
include 'header.php';

// --- Logika Penanganan POST (Tambah, Update, Hapus) ---

// Tambah data beras
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama_beras = $_POST['nama_beras'];
    $kualitas = $_POST['kualitas'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $berat_per_sak = $_POST['berat_per_sak'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    $stmt = $conn->prepare("INSERT INTO beras (id_kategori, nama_beras, kualitas, harga_beli, harga_jual, berat_per_sak, stok, deskripsi) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdddis", $id_kategori, $nama_beras, $kualitas, $harga_beli, $harga_jual, $berat_per_sak, $stok, $deskripsi);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data beras berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan data beras! Error: " . $stmt->error;
    }
    
    redirect('beras.php');
}

// Update data beras
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $id_kategori = $_POST['id_kategori'];
    $nama_beras = $_POST['nama_beras'];
    $kualitas = $_POST['kualitas'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $berat_per_sak = $_POST['berat_per_sak'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    $stmt = $conn->prepare("UPDATE beras SET id_kategori=?, nama_beras=?, kualitas=?, harga_beli=?, harga_jual=?, berat_per_sak=?, stok=?, deskripsi=? 
                             WHERE id=?");
    $stmt->bind_param("issdddisi", $id_kategori, $nama_beras, $kualitas, $harga_beli, $harga_jual, $berat_per_sak, $stok, $deskripsi, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data beras berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data beras! Error: " . $stmt->error;
    }
    
    redirect('beras.php');
}

// Hapus data beras
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $stmt = $conn->prepare("DELETE FROM beras WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data beras berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus data beras! Error: " . $stmt->error;
    }
    
    redirect('beras.php');
}

// --- Logika Pengambilan Data ---

// Ambil data beras (untuk tabel Daftar Beras)
$query = "SELECT b.*, k.nama_kategori 
           FROM beras b 
           LEFT JOIN kategori_beras k ON b.id_kategori = k.id 
           ORDER BY b.nama_beras";
$beras = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Ini adalah data yang perlu Anda tampilkan di dashboard, JANGAN HAPUS BARIS INI dari beras.php
$semua_stok_beras = $conn->query("SELECT nama_beras, stok FROM beras ORDER BY nama_beras ASC")->fetch_all(MYSQLI_ASSOC);

// Ambil data kategori untuk dropdown
$kategori = $conn->query("SELECT * FROM kategori_beras ORDER BY nama_kategori")->fetch_all(MYSQLI_ASSOC);

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM beras WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="card">
    <h2>Data Beras</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3><?php echo $edit_data ? 'Edit Data Beras' : 'Tambah Data Beras Baru'; ?></h3>
        <form method="POST" action="">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="id_kategori">Kategori Beras</label>
                <select class="form-control" id="id_kategori" name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategori as $kat): ?>
                        <option value="<?php echo $kat['id']; ?>" 
                            <?php if ($edit_data && $edit_data['id_kategori'] == $kat['id']) echo 'selected'; ?>>
                            <?php echo $kat['nama_kategori']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="nama_beras">Nama Beras</label>
                <input type="text" class="form-control" id="nama_beras" name="nama_beras" 
                        value="<?php echo $edit_data ? $edit_data['nama_beras'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="kualitas">Kualitas</label>
                <select class="form-control" id="kualitas" name="kualitas" required>
                    <option value="premium" <?php if ($edit_data && $edit_data['kualitas'] == 'premium') echo 'selected'; ?>>Premium</option>
                    <option value="medium" <?php if ($edit_data && $edit_data['kualitas'] == 'medium') echo 'selected'; ?>>Medium</option>
                    <option value="lokal" <?php if ($edit_data && $edit_data['kualitas'] == 'lokal') echo 'selected'; ?>>Lokal</option>
                    <option value="impor" <?php if ($edit_data && $edit_data['kualitas'] == 'impor') echo 'selected'; ?>>Impor</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="harga_beli">Harga Beli (per kg)</label>
                    <input type="number" class="form-control" id="harga_beli" name="harga_beli" step="0.01"
                            value="<?php echo $edit_data ? $edit_data['harga_beli'] : ''; ?>" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="harga_jual">Harga Jual (per kg)</label>
                    <input type="number" class="form-control" id="harga_jual" name="harga_jual" step="0.01"
                            value="<?php echo $edit_data ? $edit_data['harga_jual'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="berat_per_sak">Berat per Sak (kg)</label>
                <input type="number" step="0.01" class="form-control" id="berat_per_sak" name="berat_per_sak" 
                        value="<?php echo $edit_data ? $edit_data['berat_per_sak'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="stok">Stok (kg)</label>
                <input type="number" class="form-control" id="stok" name="stok" 
                        value="<?php echo $edit_data ? $edit_data['stok'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi"><?php echo $edit_data ? $edit_data['deskripsi'] : ''; ?></textarea>
            </div>
            
            <button type="submit" name="<?php echo $edit_data ? 'update' : 'tambah'; ?>" class="btn btn-primary">
                <?php echo $edit_data ? 'Update Data' : 'Tambah Beras'; ?>
            </button>
            
            <?php if ($edit_data): ?>
                <a href="beras.php" class="btn btn-secondary">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    ---
    
    <div class="card mt-4">
        <h3>Daftar Beras (Manajemen Data)</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Beras</th>
                        <th>Kategori</th>
                        <th>Kualitas</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok (kg)</th> 
                        <th>Deskripsi</th> 
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($beras)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data beras.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($beras as $key => $item): ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $item['nama_beras']; ?></td>
                            <td><?php echo $item['nama_kategori'] ?? '-'; ?></td>
                            <td><?php echo ucfirst($item['kualitas']); ?></td>
                            <td>Rp <?php echo number_format($item['harga_beli'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($item['stok'], 2, ',', '.'); ?></td> 
                            <td><?php echo $item['deskripsi'] ?? '-'; ?></td> 
                            <td>
                                <a href="beras.php?edit=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="beras.php?hapus=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>