<?php
require_once 'config.php';

// Pastikan pengguna sudah login
if (!isLoggedIn()) {
    redirect('login.php');
}

// --- Kontrol Akses: Hanya admin yang boleh mengakses halaman ini ---
if ($_SESSION['role'] !== 'admin') {
    redirect('akses_ditolak.php');
    exit();
}
// --- Akhir Kontrol Akses ---

$pageTitle = "Data Supplier";
include 'header.php';

// --- Ambil semua data beras untuk dropdown di form ---
$query_all_beras = "SELECT id, nama_beras FROM beras ORDER BY nama_beras ASC";
$all_beras = $conn->query($query_all_beras)->fetch_all(MYSQLI_ASSOC);

// --- Logika Penanganan POST (Tambah, Update) ---

// Fungsi helper untuk mengelola asosiasi beras dengan supplier (DIUBAH UNTUK SINGLE SELECT)
function updateSupplierBeras($conn, $supplier_id, $selected_beras_id) {
    // 1. Set id_supplier = NULL untuk beras yang sebelumnya terkait dengan supplier ini.
    //    Ini memastikan supplier hanya terkait dengan satu beras yang dipilih di form.
    $stmt_reset_old = $conn->prepare("UPDATE beras SET id_supplier = NULL WHERE id_supplier = ?");
    $stmt_reset_old->bind_param("i", $supplier_id);
    $stmt_reset_old->execute();
    $stmt_reset_old->close();

    // 2. Set id_supplier ke supplier_id untuk beras yang baru dipilih (jika ada pilihan)
    if (!empty($selected_beras_id)) {
        $stmt_update_new = $conn->prepare("UPDATE beras SET id_supplier = ? WHERE id = ?");
        $stmt_update_new->bind_param("ii", $supplier_id, $selected_beras_id);
        $stmt_update_new->execute();
        $stmt_update_new->close();
    }
}

// Tambah data supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama_supplier = $conn->real_escape_string($_POST['nama_supplier']);
    $alamat_supplier = $conn->real_escape_string($_POST['alamat_supplier']);
    $telepon = $conn->real_escape_string($_POST['telepon']);
    $email = $conn->real_escape_string($_POST['email']);
    $selected_beras_id = isset($_POST['merek_beras']) && $_POST['merek_beras'] !== '' ? (int)$_POST['merek_beras'] : null; // Ambil satu ID

    $stmt = $conn->prepare("INSERT INTO supplier (nama_supplier, alamat_supplier, telepon, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_supplier, $alamat_supplier, $telepon, $email);

    if ($stmt->execute()) {
        $new_supplier_id = $stmt->insert_id; // Dapatkan ID supplier yang baru ditambahkan
        updateSupplierBeras($conn, $new_supplier_id, $selected_beras_id);
        $_SESSION['success'] = "Data supplier berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan data supplier! Error: " . $stmt->error;
    }
    $stmt->close();

    redirect('supplier.php');
}

// Update data supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_supplier = $conn->real_escape_string($_POST['nama_supplier']);
    $alamat_supplier = $conn->real_escape_string($_POST['alamat_supplier']);
    $telepon = $conn->real_escape_string($_POST['telepon']);
    $email = $conn->real_escape_string($_POST['email']);
    $selected_beras_id = isset($_POST['merek_beras']) && $_POST['merek_beras'] !== '' ? (int)$_POST['merek_beras'] : null; // Ambil satu ID

    $stmt = $conn->prepare("UPDATE supplier SET nama_supplier=?, alamat_supplier=?, telepon=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $nama_supplier, $alamat_supplier, $telepon, $email, $id);

    if ($stmt->execute()) {
        updateSupplierBeras($conn, $id, $selected_beras_id); // Panggil fungsi update asosiasi
        $_SESSION['success'] = "Data supplier berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data supplier! Error: " . $stmt->error;
    }
    $stmt->close();

    redirect('supplier.php');
}

// --- Logika Penanganan GET (Hapus) ---

// Hapus data supplier
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // Sebelum menghapus supplier, set id_supplier di tabel beras menjadi NULL
    // Ini penting agar tidak ada data beras yang kehilangan referensi supplier secara tiba-tiba
    $stmt_update_beras = $conn->prepare("UPDATE beras SET id_supplier = NULL WHERE id_supplier = ?");
    $stmt_update_beras->bind_param("i", $id);
    $stmt_update_beras->execute();
    $stmt_update_beras->close();

    $stmt = $conn->prepare("DELETE FROM supplier WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data supplier berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus data supplier! Error: " . $stmt->error;
    }
    $stmt->close();

    redirect('supplier.php');
}

// --- Logika Pengambilan Data ---

// Ambil semua data supplier dan gabungkan dengan nama beras yang mereka sediakan
$query_suppliers = "
    SELECT
        s.*,
        GROUP_CONCAT(b.nama_beras ORDER BY b.nama_beras ASC SEPARATOR ', ') AS merek_beras_disediakan,
        GROUP_CONCAT(b.id ORDER BY b.nama_beras ASC SEPARATOR ',') AS ids_beras_disediakan
    FROM
        supplier s
    LEFT JOIN
        beras b ON s.id = b.id_supplier
    GROUP BY
        s.id
    ORDER BY
        s.nama_supplier ASC";
$suppliers = $conn->query($query_suppliers)->fetch_all(MYSQLI_ASSOC);


// Ambil data untuk form edit (jika ada parameter edit di URL)
$edit_data = null;
$beras_selected_for_edit = ''; // Inisialisasi sebagai string kosong untuk single select
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt_edit = $conn->prepare("SELECT * FROM supplier WHERE id=?");
    $stmt_edit->bind_param("i", $id_edit);
    $stmt_edit->execute();
    $edit_data = $stmt_edit->get_result()->fetch_assoc();
    $stmt_edit->close();

    // Jika data tidak ditemukan, redirect kembali ke daftar supplier
    if (!$edit_data) {
        $_SESSION['error'] = "Data supplier tidak ditemukan.";
        redirect('supplier.php');
    }

    // Ambil MEREK BERAS PERTAMA yang terkait dengan supplier ini untuk pre-selection
    // Jika supplier memasok lebih dari satu jenis beras, hanya yang pertama akan ditampilkan di dropdown ini.
    $stmt_beras_terkait = $conn->prepare("SELECT id FROM beras WHERE id_supplier = ? LIMIT 1");
    $stmt_beras_terkait->bind_param("i", $id_edit);
    $stmt_beras_terkait->execute();
    $result_beras_terkait = $stmt_beras_terkait->get_result();
    $row = $result_beras_terkait->fetch_assoc();
    if ($row) {
        $beras_selected_for_edit = $row['id']; // Simpan sebagai scalar ID
    }
    $stmt_beras_terkait->close();
}
?>

<div class="container">
    <h1><?php echo $pageTitle; ?></h1>

    <?php
    // Fungsi untuk menampilkan pesan (jika belum ada di config.php atau header.php)
    if (function_exists('display_session_messages')) {
        display_session_messages();
    } else {
        // Fallback jika fungsi belum didefinisikan di tempat lain
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
    }
    ?>

    <div class="card form-card">
        <h3><?php echo $edit_data ? 'Edit Data Supplier' : 'Tambah Data Supplier Baru'; ?></h3>
        <form method="POST" action="supplier.php">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_supplier">Nama Supplier</label>
                <input type="text" class="form-control" id="nama_supplier" name="nama_supplier"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_supplier']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="alamat_supplier">Alamat</label>
                <textarea class="form-control" id="alamat_supplier" name="alamat_supplier"><?php echo $edit_data ? htmlspecialchars($edit_data['alamat_supplier']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="telepon">Telepon</label>
                <input type="text" class="form-control" id="telepon" name="telepon"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['telepon']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="merek_beras">Merek Beras Disediakan (Pilihan Utama)</label>
                <select class="form-control" id="merek_beras" name="merek_beras"> <!-- MULTIPLE dan SIZE dihapus -->
                    <option value="">Pilih Beras</option> <!-- Opsi default baru -->
                    <?php if (empty($all_beras)): ?>
                        <option value="" disabled>Tidak ada merek beras tersedia. Tambahkan beras terlebih dahulu.</option>
                    <?php else: ?>
                        <?php foreach ($all_beras as $beras): ?>
                            <option value="<?php echo htmlspecialchars($beras['id']); ?>"
                                <?php 
                                // Logika pemilihan untuk single select
                                if ($edit_data && $beras_selected_for_edit == $beras['id']) {
                                    echo 'selected'; 
                                }
                                ?>>
                                <?php echo htmlspecialchars($beras['nama_beras']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small class="form-text text-muted">Pilih merek beras utama yang dipasok oleh supplier ini.</small>
            </div>
            <button type="submit" name="<?php echo $edit_data ? 'update' : 'tambah'; ?>" class="btn btn-primary">
                <?php echo $edit_data ? 'Update Data' : 'Tambah Supplier'; ?>
            </button>

            <?php if ($edit_data): ?>
                <a href="supplier.php" class="btn btn-secondary">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    ---

    <div class="card mt-4">
        <h3>Daftar Supplier</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Merek Beras Disediakan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data supplier.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $key => $item): ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo htmlspecialchars($item['nama_supplier']); ?></td>
                                <td><?php echo htmlspecialchars($item['alamat_supplier'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['telepon'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['email'] ?? '-'); ?></td>
                                <td>
                                    <?php
                                    // Tampilkan semua merek beras yang dipasok (sesuai GROUP_CONCAT)
                                    if (!empty($item['merek_beras_disediakan'])) {
                                        echo htmlspecialchars($item['merek_beras_disediakan']);
                                    } else {
                                        echo '-'; // Atau 'Belum ada beras terkait'
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="supplier.php?edit=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="supplier.php?hapus=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data supplier ini? Semua data beras yang terkait dengan supplier ini akan kehilangan referensi supplier.')">Hapus</a>
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
