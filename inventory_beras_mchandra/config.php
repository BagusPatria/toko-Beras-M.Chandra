<?php
// config.php
ob_start(); // Pastikan ob_start() dan session_start() ada di config.php jika dipakai secara global
session_start();

// Koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'inventory_beras';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi dasar
function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

function isPimpinan() {
    return isLoggedIn() && $_SESSION['role'] === 'pimpinan';
}

// Inisialisasi data awal jika diperlukan
function initializeData($conn) {
    // Cek apakah tabel users kosong
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        // Buat user admin default
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, password, nama_lengkap, role) 
                     VALUES ('admin', '$password', 'Administrator', 'admin')");
        
        // Buat kategori default
        $kategori = [
            ['Premium', 'Beras dengan kualitas terbaik'],
            ['Medium', 'Beras dengan kualitas menengah'],
            ['Lokal', 'Beras produksi dalam negeri'],
            ['Impor', 'Beras dari luar negeri']
        ];
        
        foreach ($kategori as $kat) {
            $stmt = $conn->prepare("INSERT INTO kategori_beras (nama_kategori, deskripsi) VALUES (?, ?)");
            $stmt->bind_param("ss", $kat[0], $kat[1]);
            $stmt->execute();
        }
    }
}

// Panggil initializeData setelah koneksi dibuat
initializeData($conn);
?>