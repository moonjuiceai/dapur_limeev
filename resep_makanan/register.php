<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "INSERT INTO users (nama, username, email, password, role) VALUES ('$nama', '$username', '$email', '$password', 'user')");

    if ($query) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register Limeev</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important;
            border-right: 4px solid #ffc800 !important;
        }

        body,
        .main {
            background-color: #FFFFFF !important;
        }

        .sidebar .menu li a.active {
            background-color: #ffc800 !important;
            color: white !important;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(255, 159, 67, 0.3);
        }

        .sidebar .menu li a:hover:not(.active) {
            background-color: rgba(255, 159, 67, 0.1) !important;
            color: #ffc800 !important;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .main {
            margin-left: 280px;
            padding: 30px;
            background: white !important;
        }

        .btn-auth {
            background-color: #FFC800 !important;
            color: white !important;
            padding: 12px;
            border-radius: 50px;
            border: none;
            width: 100%;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-auth:hover {
            background-color: #FFA500 !important;
        }

        .user-box {
            background: #FFFFC5;
            border: 1px solid #fef3c7;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            padding: 5px 15px 5px 5px !important;
            border-radius: 30px !important;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo" style="text-align: center; padding-top: 30px; margin-bottom: 5px;">
            <img src="assets/logo_limeev.png" alt="Logo" style="width: 180px; filter: drop-shadow(0px 4px 6px rgba(255,159,67,0.2));">
        </div>
        <ul class="menu" style="list-style: none; padding: 0 15px;">
            <li><a href="home.php"><i class="fa fa-house"></i> Home</a></li>
            <li><a href="login.php"><i class="fa fa-right-to-bracket"></i> Login</a></li>
            <li><a href="register.php" class="active"><i class="fa fa-user-plus"></i> Register</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar" style="display: flex; justify-content: flex-end;">
            <div class="user-box">
                <img src="https://ui-avatars.com/api/?name=Guest&background=fff&color=333" style="width: 40px; height: 40px; border-radius: 50%;">
                <span>Guest</span>
            </div>
        </div>

        <div class="auth-container" style="display: flex; justify-content: center; margin-top: 30px;">
            <div class="auth-box" style="width: 400px; padding: 40px; border: 3px solid #fef3c7; border-radius: 25px; text-align: center; background: #fff;">

                <img src="assets/logo_login.png" alt="Logo" style="width: 200px; margin-bottom: 10px;">

                <h2 style="color: #1e293b; margin-bottom: 5px;">Buat Akun</h2>
                <p style="color: #64748b; margin-bottom: 25px;">Bergabunglah dengan Limeev</p>

                <form action="" method="POST" style="text-align: left;">
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" placeholder="Masukkan nama lengkap" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px;">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Buat nama pengguna" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px;">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Masukkan email aktif" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px;">
                    </div>
                    <div class="input-group" style="margin-bottom: 20px;">
                        <label>Password</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" name="password" id="password" placeholder="Buat kata sandi baru" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px; padding-right: 45px;">
                            <i class="fa fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 60%; transform: translateY(-50%); cursor: pointer; color: #888;"></i>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn-auth">Daftar Sekarang</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>