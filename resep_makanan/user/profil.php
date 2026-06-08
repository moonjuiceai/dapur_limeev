<?php
session_start();
include '../config/koneksi.php';

/* CEK LOGIN */
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

/* AMBIL DATA USER DARI DATABASE */
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$user = mysqli_fetch_assoc($query_user);

/* PROSES UPDATE PROFIL */
if (isset($_POST['update_profil'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama']);

    // Proses Gambar
    $gambar     = $_FILES['foto_profile']['name'];
    $tmp        = $_FILES['foto_profile']['tmp_name'];

    if ($gambar != "") {
        // Jika user upload foto baru
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $x = explode('.', $gambar);
        $ekstensi = strtolower(end($x));

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            $nama_gambar_baru = "profil_" . time() . '.' . $ekstensi;
            move_uploaded_file($tmp, "../uploads/profil/" . $nama_gambar_baru);

            // Hapus foto lama JIKA BUKAN default.png
            if ($user['foto_profile'] != 'default.png' && file_exists("../uploads/profil/" . $user['foto_profile'])) {
                unlink("../uploads/profil/" . $user['foto_profile']);
            }
        } else {
            echo "<script>alert('Ekstensi gambar hanya boleh JPG/PNG!'); window.history.back();</script>";
            exit;
        }
    } else {
        // Jika tidak upload foto, pakai foto lama
        $nama_gambar_baru = $user['foto_profile'];
    }

    /* UPDATE DATABASE */
    $update = mysqli_query($conn, "UPDATE users SET nama = '$nama_baru', foto_profile = '$nama_gambar_baru' WHERE id = '$id_user'");

    if ($update) {
        // Update Session Nama agar langsung berubah di Topbar
        $_SESSION['nama'] = $nama_baru;

        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>
        <script>
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Profil Anda telah diperbarui', confirmButtonColor: '#ffc800' }).then(() => { window.location='profil.php'; });
        </script></body></html>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Limeev</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #FFFFFF !important;
        }

        .sidebar {
            background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important;
            border-right: 4px solid #ffc800 !important;
        }

        .sidebar .menu li a.active {
            background-color: #ffc800 !important;
            color: white !important;
            border-radius: 12px;
        }

        .sidebar .menu li a:hover:not(.active) {
            background-color: rgba(255, 159, 67, 0.1) !important;
            color: #ffc800 !important;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        /* TOPBAR USER */
        .user-box {
            background: #FFFFC5 !important;
            border: 1px solid #fef3c7 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important;
            padding: 5px 15px 5px 5px !important;
            border-radius: 30px !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-box img {
            width: 45px !important;
            height: 45px !important;
            border-radius: 50%;
            object-fit: cover;
        }

        /* JUDUL HALAMAN */
        .hero {
            text-align: center;
            margin-bottom: 30px;
        }

        .hero h1 {
            font-size: 38px;
            font-weight: 700;
            color: #222;
            margin-bottom: 10px;
        }

        .hero p {
            color: #666;
        }

        /* FORM PROFIL */
        .form-profil {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        /* FOTO PROFIL */
        .profil-preview {
            width: 120px !important;
            height: 120px !important;
            min-width: 120px !important;
            min-height: 120px !important;
            max-width: 120px !important;
            max-height: 120px !important;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffc800;
            display: block;
            margin: 0 auto 20px auto;
        }

        /* INPUT */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .input-wrapper i {
            color: #888;
            margin-right: 10px;
        }

        .input-wrapper input[type="text"] {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px dashed #ffc800;
            border-radius: 10px;
            cursor: pointer;
        }

        /* BUTTON */
        .btn-simpan {
            background: #ffc800;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn-simpan:hover {
            background: #e6b500;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <div class="user-box">
                <img src="../uploads/profil/<?= $user['foto_profile'] != '' ? $user['foto_profile'] : 'default.png'; ?>">
                <span><?= $_SESSION['nama']; ?></span>
            </div>
        </div>

        <div class="hero" style="text-align: center;">
            <h1>Pengaturan Profil ⚙️</h1>
            <p>Sesuaikan nama dan foto profil akunmu.</p>
        </div>

        <div class="form-profil">
            <img src="../uploads/profil/<?= $user['foto_profile'] != '' ? $user['foto_profile'] : 'default.png'; ?>" class="profil-preview">

            <form action="" method="POST" enctype="multipart/form-data">

                <div class="input-group" style="text-align: left;">
                    <label>Nama Lengkap</label>
                    <div class="input-wrapper" style="display: flex; align-items: center; background: #f9f9f9; padding: 12px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 20px;">
                        <i class="fa fa-user" style="color: #888; margin-right: 10px;"></i>
                        <input type="text" name="nama" value="<?= $user['nama']; ?>" required style="border: none; background: transparent; outline: none; width: 100%;">
                    </div>
                </div>

                <div class="input-group" style="text-align: left;">
                    <label>Ganti Foto Profil *(Abaikan jika tidak ingin diganti)</label>
                    <div class="input-wrapper" style="margin-top: 5px; margin-bottom: 25px;">
                        <input type="file" name="foto_profile" accept="image/png, image/jpeg, image/jpg" style="width: 100%; padding: 10px; border: 1px dashed #ffc800; border-radius: 10px; cursor: pointer;">
                    </div>
                </div>

                <button type="submit" name="update_profil" style="background: #ffc800; color: white; border: none; padding: 12px 30px; border-radius: 10px; font-weight: 600; cursor: pointer; width: 100%; font-size: 16px;">
                    <i class="fa fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

</body>

</html>