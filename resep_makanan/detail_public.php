<?php
session_start(); // INI WAJIB ADA DI BARIS PALING ATAS
include 'config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: home.php");
    exit;
}

// 1. Ambil data resep
$query = mysqli_query($conn, "SELECT resep.*, users.nama AS nama_penulis FROM resep JOIN users ON resep.user_id = users.id WHERE resep.id = '$id'");
$data = mysqli_fetch_assoc($query);

// 2. Ambil data komentar
$query_komentar = mysqli_query($conn, "SELECT komentar.*, users.nama AS nama_user FROM komentar JOIN users ON komentar.user_id = users.id WHERE resep_id = '$id' ORDER BY id DESC");

if (!$data) {
    echo "<script>alert('Resep tidak ditemukan!'); window.location='home.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep - <?php echo $data['judul']; ?></title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
    </style>

</head>

<body>

    <div class="sidebar">
        <div class="logo" style="text-align: center; padding-top: 30px; margin-bottom: 5px;">
            <img src="assets/logo_limeev.png" alt="Logo" style="width: 180px; filter: drop-shadow(0px 4px 6px rgba(255,159,67,0.2));">
        </div>

        <ul class="menu" style="list-style: none; padding: 0 15px; margin-top: 10px;">
            <li><a href="home.php"><i class="fa fa-house"></i> Home</a></li>

            <?php if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === true): ?>
                <li><a href="user/index.php"><i class="fa fa-chart-line"></i> Dashboard</a></li>
                <li><a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php"><i class="fa fa-right-to-bracket"></i> Login</a></li>
                <li><a href="register.php"><i class="fa fa-user-plus"></i> Register</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="main">
        <a href="home.php" style="
            display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px;
            background-color: #FFFFC5; color: #FFC800; border: 1px solid #FFC800;
            border-radius: 50px; text-decoration: none; font-weight: 600; margin-bottom: 20px;"
            onmouseover="this.style.backgroundColor='#FFC800'; this.style.color='#FFFFFF';"
            onmouseout="this.style.backgroundColor='#FFFFC5'; this.style.color='#FFC800';">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>

        <div class="recipe-detail-card" style="background: #FFFFFF; padding: 20px; border-radius: 15px; border: 3px solid #fef3c7;">
            <img src="uploads/makanan/<?php echo $data['gambar']; ?>" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 15px;">

            <div class="recipe-body-content" style="margin-top: 20px;">
                <h1 style="color: #1e293b;"><?php echo $data['judul']; ?></h1>
                <p style="color: #ffc800; font-weight: 600;"><i class="fa fa-user-pen"></i> Oleh: <?php echo $data['nama_penulis']; ?></p>
                <p style="color: #64748b; margin: 15px 0;"><?php echo htmlspecialchars($data['deskripsi']); ?></p>

                <h3 style="color: #1e293b;">Bahan</h3>
                <p style="color: #64748b;"><?php echo nl2br(htmlspecialchars($data['bahan'])); ?></p>

                <h3 style="color: #1e293b; margin-top: 15px;">Langkah Memasak</h3>
                <p style="color: #64748b;"><?php echo !empty($data['langkah']) ? nl2br(htmlspecialchars($data['langkah'])) : "Belum ada langkah."; ?></p>

                <hr style="margin: 30px 0; border: 0; border-top: 1px solid #fef3c7;">
                <h3 style="color: #1e293b;">Komentar</h3>
                <?php while ($kom = mysqli_fetch_assoc($query_komentar)) {
                    $text = $kom['komentar'] ?? $kom['isi_komentar'] ?? '-';
                ?>
                    <div style="background: #fffdf0; padding: 10px; border-radius: 10px; margin-bottom: 10px; border: 1px solid #fef3c7;">
                        <strong style="color: #ffc800;"><?php echo htmlspecialchars($kom['nama_user']); ?></strong>
                        <p style="color: #64748b; margin: 5px 0 0 0;"><?php echo htmlspecialchars($text); ?></p>
                    </div>
                <?php }
                if (mysqli_num_rows($query_komentar) == 0) {
                    echo "<p style='color:#94a3b8;'>Belum ada komentar.</p>";
                } ?>
            </div>
        </div>
    </div>
</body>

</html>