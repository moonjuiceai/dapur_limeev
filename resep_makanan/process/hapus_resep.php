<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_resep = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah resep ini benar-benar milik user yang sedang login
    $cek_kepemilikan = mysqli_query($conn, "SELECT * FROM resep WHERE id = '$id_resep' AND user_id = '$user_id'");
    
    if (mysqli_num_rows($cek_kepemilikan) > 0) {
        $data_resep = mysqli_fetch_assoc($cek_kepemilikan);
        $gambar_lama = $data_resep['gambar'];

        // Hapus file gambar dari folder uploads agar rapi
        if ($gambar_lama != "" && file_exists("../uploads/makanan/" . $gambar_lama)) {
            unlink("../uploads/makanan/" . $gambar_lama);
        }

        // Hapus data favorit & komentar yang nyangkut di resep ini
        mysqli_query($conn, "DELETE FROM favorit WHERE resep_id = '$id_resep'");
        mysqli_query($conn, "DELETE FROM komentar WHERE resep_id = '$id_resep'");
        
        // Terakhir, hapus data resepnya
        $hapus = mysqli_query($conn, "DELETE FROM resep WHERE id = '$id_resep'");

        if ($hapus) {
            echo "<script>alert('Resep berhasil dihapus!'); window.location='../user/index.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus resep!'); window.location='../user/index.php';</script>";
        }
    } else {
        echo "<script>alert('Akses Ditolak! Anda tidak bisa menghapus resep orang lain.'); window.location='../user/index.php';</script>";
    }
} else {
    header("Location: ../user/index.php");
}
?>