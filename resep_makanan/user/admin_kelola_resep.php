<?php
session_start();
include '../config/koneksi.php';

$id_user_login = $_SESSION['user_id'];

$query_user = mysqli_query($conn, "
    SELECT foto_profile, nama 
    FROM users 
    WHERE id = '$id_user_login'
");

$data_user = mysqli_fetch_assoc($query_user);

$foto_db = $data_user['foto_profile'];
$nama_user = $data_user['nama'];

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

$query = mysqli_query($conn, "
    SELECT resep.*, users.nama AS nama_penulis 
    FROM resep 
    JOIN users ON resep.user_id = users.id 
    ORDER BY resep.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Resep - Admin Limeev</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #fff;
        }

        /* KONTEN UTAMA */
        .main {
            margin-left: 290px;
            padding: 30px;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .user-box {
            background: #FFFFC5;
            padding: 8px 15px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-box img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* TABEL */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
        }

        th {
            background: #ffc800;
            color: white;
            padding: 15px;
            text-align: left;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:hover {
            background: #fafafa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px 10px;
            vertical-align: middle;
        }

        td:first-child,
        th:first-child {
            width: 60px;
            text-align: center;
        }

        td:nth-child(2),
        th:nth-child(2) {
            width: 120px;
            text-align: center;
        }

        .gambar-resep {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            display: block;
            margin: auto;
        }

        /* BUTTON */
        .btn {
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            text-align: center;
        }

        .btn-lihat {
            background: #ffffc5;
            color: #333;
        }

        .btn-hapus {
            background: #ff4d4d;
            color: white;
        }

        .btn-lihat:hover {
            background: #d0d0d0;
        }

        .btn-hapus:hover {
            background: #ff2e2e;
        }

        .gambar-resep {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }

        h2 {
            margin-top: 15px;
            margin-bottom: 10px;
        }

        p {
            color: #777;
            margin-bottom: 20px;
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

        .search-box {
            margin: 20px 0;
        }

        .search-box input {
            width: 350px;
            padding: 12px 18px;
            border: 2px solid #ffc800;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: .3s;
        }

        .search-box input:focus {
            box-shadow: 0 0 10px rgba(255, 200, 0, .3);
        }
    </style>

</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main" style="padding: 20px;">
        <div class="topbar">
            <div class="user-box">
                <img src="<?= $url_foto; ?>"
                    width="40"
                    height="40"
                    style="border-radius:50%;object-fit:cover;">

                <span><?= $_SESSION['nama']; ?> <strong style="color:#ff9f43;">(Admin)</strong></span>
            </div>
        </div>

        <div style="clear: both;"></div>
        <div>
            <h2 style="margin-top: 15px;">Kelola Semua Resep 🍲</h2>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Cari resep berdasarkan judul...">
            </div>
            <p style="color: #777;">Daftar seluruh resep yang diunggah oleh pengguna Dapur Limeev.</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Judul Resep</th>
                        <th>Diunggah Oleh</th>
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

                            <td>
                                <img src="../uploads/makanan/<?= $data['gambar']; ?>" class="gambar-resep">
                            </td>

                            <td>
                                <strong><?= $data['judul']; ?></strong>
                            </td>

                            <td>
                                <i class="fa fa-user"></i>
                                <?= $data['nama_penulis']; ?>
                            </td>

                            <td>
                                <div style="display:flex;gap:8px;justify-content:center;">
                                    <a href="detail_resep.php?id=<?= $data['id']; ?>&ref=admin" class="btn btn-lihat">Lihat</a>
                                    <a href="hapus_resep.php?id=<?= $data['id']; ?>&ref=admin" class="btn btn-hapus">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {

            let filter = this.value.toLowerCase();

            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(function(row) {

                let judul = row.cells[2].textContent.toLowerCase();

                if (judul.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });

        });
    </script>
</body>

</html>