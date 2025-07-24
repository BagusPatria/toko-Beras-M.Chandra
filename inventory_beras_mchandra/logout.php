<?php
// Pastikan sesi dimulai di awal setiap halaman yang membutuhkan sesi
session_start();

// Hapus semua variabel sesi
$_SESSION = array();

// Jika ingin menghapus cookie sesi, hapus juga cookie tersebut.
// Catatan: Ini akan menghancurkan sesi, dan bukan hanya data sesi!
// Harap dicatat bahwa ini akan menghapus sesi, dan bukan hanya data sesi.
// Menghapus cookie sesi juga akan menghapus cookie yang digunakan untuk mempertahankan sesi.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Terakhir, hancurkan sesi.
session_destroy();

// Arahkan pengguna ke halaman login atau halaman beranda
header("Location: login.php"); // Ganti 'login.php' jika halaman login Anda berbeda
exit(); // Penting: Selalu keluar setelah redirect untuk mencegah eksekusi kode lebih lanjut
?>