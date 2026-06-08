<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['status_login'] = true;
        $_SESSION['nama']         = $data['nama'];
        $_SESSION['username']     = $data['username'];
        $_SESSION['email']        = $data['email'];
        $_SESSION['user_id']      = array_values($data)[0];
        $_SESSION['role']         = $data['role'];

        echo "<script>alert('Selamat datang kembali, " . $data['nama'] . "!'); window.location='home.php';</script>";
        exit;
    } else {
        echo "<script>alert('Email atau Password salah!'); window.location='login.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Limeev</title>
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
            <li><a href="login.php" class="active"><i class="fa fa-right-from-bracket"></i> Login</a></li>
            <li><a href="register.php"><i class="fa fa-user-plus"></i> Register</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar" style="display: flex; justify-content: flex-end;">
            <div class="user-box" style="display: flex; align-items: center; gap: 10px;">
                <img src="https://ui-avatars.com/api/?name=Guest&background=fff&color=333" style="width: 40px; height: 40px; border-radius: 50%;">
                <span>Guest</span>
            </div>
        </div>

        <div class="auth-container" style="display: flex; justify-content: center; margin-top: 50px;">
            <div class="auth-box" style="width: 400px; padding: 40px; border: 3px solid #fef3c7; border-radius: 25px; text-align: center;">
                <img src="assets/logo_login.png" style="width: 200px; margin-bottom: 10px;">
                <p style="color: #64748b; margin-bottom: 25px;">Login untuk mulai upload resep</p>

                <form action="" method="POST" style="text-align: left;">
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Masukkan email Anda" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px;">
                    </div>

                    <div class="input-group" style="margin-bottom: 20px;">

                    <label>Password</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" name="password" id="password" placeholder="Masukkan password"
                                style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px; padding-right: 45px;">

                            <i class="fa fa-eye" id="togglePassword"
                                style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; padding: 5px;">
                            </i>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn-auth">Login</button>
                </form>

                <div style="margin-top: 15px; font-size: 14px;">
                    Belum punya akun? <a href="register.php" style="color: #FFC800; font-weight: 600; text-decoration: none;">Daftar</a>
                </div>
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