<?php 
require_once 'config.php'; 

if (isLoggedIn()) { 
    redirect('dashboard.php'); 
} 

$error = ''; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $username = $_POST['username']; // real_escape_string tidak diperlukan jika pakai prepared statements
    $password = $_POST['password']; 
    
    // --- START: KODE LOGIN PIMPINAN (TIDAK DIREKOMENDASIKAN UNTUK PRODUKSI) ---
    $pimpinan_username_hardcoded = 'pimpinan'; // Username hardcoded untuk pimpinan
    $pimpinan_password_hardcoded = 'pimpinan123'; // Password hardcoded untuk pimpinan
    $pimpinan_role = 'pimpinan'; // Role yang akan diberikan pada sesi
    $pimpinan_nama_lengkap = 'Pimpinan Toko'; // Nama lengkap untuk sesi

    if ($username === $pimpinan_username_hardcoded && $password === $pimpinan_password_hardcoded) {
        // Jika username dan password cocok dengan hardcoded pimpinan
        $_SESSION['user_id'] = 9999; // Gunakan ID dummy atau ID khusus untuk hardcoded user
        $_SESSION['username'] = $pimpinan_username_hardcoded;
        $_SESSION['nama_lengkap'] = $pimpinan_nama_lengkap;
        $_SESSION['role'] = $pimpinan_role;
        redirect('dashboard.php');
        exit(); // Penting: hentikan eksekusi setelah redirect
    }
    // --- END: KODE LOGIN PIMPINAN (TIDAK DIREKOMENDASIKAN UNTUK PRODUKSI) ---

    // Lanjutkan dengan logika login dari database untuk pengguna lain
    $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ?"); 
    $stmt->bind_param("s", $username); 
    $stmt->execute(); 
    $result = $stmt->get_result(); 
    
    if ($result->num_rows == 1) { 
        $user = $result->fetch_assoc(); 
        if (password_verify($password, $user['password'])) { 
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 
            $_SESSION['nama_lengkap'] = $user['nama_lengkap']; 
            $_SESSION['role'] = $user['role']; 
            redirect('dashboard.php'); 
        } else { 
            $error = 'Password salah!'; 
        } 
    } else { 
        $error = 'Username tidak ditemukan!'; 
    } 
    $stmt->close(); // Tutup statement setelah digunakan
} 
?> 

<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Login - Toko Beras M.Chandra</title> 
    <style> 
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f5f5f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        } 
        .login-container { 
            background-color: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            width: 350px; 
        } 
        .login-container h2 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 20px; 
        } 
        .form-group { 
            margin-bottom: 15px; 
        } 
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
        } 
        .form-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box; 
        } 
        .btn { 
            width: 100%; 
            padding: 10px; 
            background-color: #4CAF50; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
        } 
        .btn:hover { 
            background-color: #45a049; 
        } 
        .error { 
            color: red; 
            margin-bottom: 15px; 
            text-align: center; 
        } 
        .logo { 
            text-align: center; 
            margin-bottom: 20px; 
            font-size: 24px; 
            font-weight: bold; 
            color: #4CAF50; 
        } 
    </style> 
</head> 
<body> 
    <div class="login-container"> 
        <div class="logo">Toko Beras M.Chandra</div> 
        <h2>Login Sistem Inventaris</h2> 
        
        <?php if ($error): ?> 
            <div class="error"><?php echo $error; ?></div> 
        <?php endif; ?> 
        
        <form method="POST" action=""> 
            <div class="form-group"> 
                <label for="username">Username</label> 
                <input type="text" id="username" name="username" required> 
            </div> 
            <div class="form-group"> 
                <label for="password">Password</label> 
                <input type="password" id="password" name="password" required> 
            </div> 
            <button type="submit" class="btn">Login</button> 
        </form> 
    </div> 
</body> 
</html>