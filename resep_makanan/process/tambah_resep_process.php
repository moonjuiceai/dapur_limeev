<?php
session_start();

include '../config/koneksi.php';

/* CEK LOGIN */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

/* CEK SUBMIT */

if (isset($_POST['tambah'])) {

    /* USER LOGIN */

    $user_id = $_SESSION['user_id'];

    /* INPUT */

    $judul = $_POST['judul'];

    $deskripsi = $_POST['deskripsi'];

    $bahan = $_POST['bahan'];

    $langkah = $_POST['langkah'];

    $waktu_memasak = $_POST['waktu_memasak'];

    $porsi = $_POST['porsi'];

    $tingkat_kesulitan = $_POST['tingkat_kesulitan'];

    /* STATUS */

    $status = "published";

    /* SLUG */

    $slug = strtolower(
        str_replace(" ", "-", $judul)
    );

    /* =========================
       UPLOAD GAMBAR
    ========================= */

    $gambar = $_FILES['gambar']['name'];

    $tmp = $_FILES['gambar']['tmp_name'];

    /* NAMA GAMBAR UNIK */

    $nama_gambar =
        time() . '-' . $gambar;

    /* UPLOAD FILE */

    move_uploaded_file(
        $tmp,
        "../uploads/makanan/" . $nama_gambar
    );

    /* =========================
       INSERT DATABASE
    ========================= */

    $query = mysqli_query($conn, "

    INSERT INTO resep
    (
        user_id,
        judul,
        slug,
        deskripsi,
        bahan,
        langkah,
        gambar,
        waktu_memasak,
        porsi,
        tingkat_kesulitan,
        status
    )

    VALUES

    (
        '$user_id',
        '$judul',
        '$slug',
        '$deskripsi',
        '$bahan',
        '$langkah',
        '$nama_gambar',
        '$waktu_memasak',
        '$porsi',
        '$tingkat_kesulitan',
        '$status'
    )

    ");

    /* =========================
       HASIL
    ========================= */

    if ($query) {

        echo "

        <!DOCTYPE html>
        <html>
        <head>

        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

        </head>

        <body>

        <script>

        Swal.fire({
            icon: 'success',
            title: 'Berhasil 🎉',
            text: 'Resep berhasil diupload 🍜',
            confirmButtonColor: '#4CAF50'
        }).then(() => {

            window.location='../user/index.php';

        });

        </script>

        </body>
        </html>

        ";
    } else {

        echo "

        <!DOCTYPE html>
        <html>
        <head>

        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

        </head>

        <body>

        <script>

        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Resep gagal diupload',
            confirmButtonColor: '#d33'
        }).then(() => {

            window.history.back();

        });

        </script>

        </body>
        </html>

        ";
    }
}
