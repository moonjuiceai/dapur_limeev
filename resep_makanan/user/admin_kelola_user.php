<?php
session_start();
include '../config/koneksi.php';

$id_user_login = $_SESSION['user_id'];

$query_user_login = mysqli_query($conn, "
    SELECT foto_profile, nama
    FROM users
    WHERE id = '$id_user_login'
");

$data_login = mysqli_fetch_assoc($query_user_login);

$foto_db = $data_login['foto_profile'];
$nama_user = $data_login['nama'];

if (
    !empty($foto_db) &&
    $foto_db != 'default.png' &&
    file_exists("../uploads/profil/" . $foto_db)
) {
    $url_foto = "../uploads/profil/" . $foto_db;
} else {
    $url_foto = "https://ui-avatars.com/api/?name=" .
        urlencode($nama_user) .
        "&background=ffc800&color=fff&bold=true";
}

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
    exit;
}

$id_saya = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id != '$id_saya' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin Limeev</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .admin-table th,
        .admin-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .admin-table th {
            background-color: #ff9f43;
            color: white;
            font-weight: 500;
        }

        .admin-table tr:hover {
            background-color: #f9f9f9;
        }

        .btn-delete {
            background: #ff4757;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-delete:hover {
            background: #ff6b81;
        }

        .badge-role {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-admin {
            background: #ffeaa7;
            color: #d35400;
        }

        .badge-user {
            background: #e2e8f0;
            color: #475569;
        }

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

        .user-box {
            background: #FFFFC5;
            padding: 5px 15px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <div class="user-box">
                <img src="<?= $url_foto; ?>"
                    width="40"
                    height="40"
                    style="border-radius:50%;object-fit:cover;">
                <span><?= $_SESSION['nama']; ?> <strong style="color:#ff9f43;">(Admin)</strong></span>
            </div>
        </div>

        <div>
            <h2 style="margin-top: 15px;">Kelola Akun Pengguna 👥</h2>
            <p style="color: #777;">Daftar seluruh pengguna terdaftar di sistem Dapur Limeev.</p>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Hak Akses</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td style="font-weight: 600; color: #333;"><?= $data['nama']; ?></td>
                        <td>@<?= $data['username']; ?></td>
                        <td><?= $data['email']; ?></td>
                        <td>
                            <?php if ($data['role'] == 'admin') { ?>
                                <span class="badge-role badge-admin">Admin</span>
                            <?php } else { ?>
                                <span class="badge-role badge-user">User</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="../process/admin_hapus_user.php?id=<?= $data['id']; ?>" class="btn-delete" onclick="return confirm('⚠️ PERINGATAN KERING: Menghapus user ini akan menghapus SELURUH resep yang pernah dia buat. Lanjutkan?');">
                                <i class="fa fa-trash"></i> Hapus Akun
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</body>

</html>