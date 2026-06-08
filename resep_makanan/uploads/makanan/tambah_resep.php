<?php

include '../config/session_user.php';
include '../config/koneksi.php';

if(isset($_POST['tambah'])) {

    $user_id       = $_SESSION['id'];

    $judul         = $_POST['judul'];
    $deskripsi     = $_POST['deskripsi'];
    $bahan         = $_POST['bahan'];
    $langkah       = $_POST['langkah'];

    /* =========================
       UPLOAD GAMBAR
    ========================== */

    $gambar        = $_FILES['gambar']['name'];
    $tmp           = $_FILES['gambar']['tmp_name'];

    $folder        = "../uploads/makanan/";

    /* BUAT NAMA GAMBAR UNIK */

    $nama_gambar = time() . '-' . $gambar;

    move_uploaded_file(
        $tmp,
        $folder . $nama_gambar
    );

    /* =========================
       INSERT DATABASE
    ========================== */

    mysqli_query($conn, "
    INSERT INTO resep
    (
        user_id,
        judul,
        deskripsi,
        bahan,
        langkah,
        gambar
    )

    VALUES

    (
        '$user_id',
        '$judul',
        '$deskripsi',
        '$bahan',
        '$langkah',
        '$nama_gambar'
    )
    ");

    header("Location: resep_saya.php");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Resep</title>
</head>
<body>

<h1>Tambah Resep</h1>

<form method="POST" enctype="multipart/form-data">

    <input
    type="text"
    name="judul"
    placeholder="Judul Resep"
    required>

    <br><br>

    <textarea
    name="deskripsi"
    placeholder="Deskripsi"></textarea>

    <br><br>

    <textarea
    name="bahan"
    placeholder="Bahan"
    required></textarea>

    <br><br>

    <textarea
    name="langkah"
    placeholder="Langkah"
    required></textarea>

    <br><br>

    <input
    type="file"
    name="gambar"
    accept="image/*"
    required>

    <br><br>

    <button type="submit" name="tambah">
        Simpan Resep
    </button>

</form>

</body>
</html