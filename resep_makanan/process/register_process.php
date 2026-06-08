<?php
session_start();

include '../config/koneksi.php';

$nama = $_POST['nama'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

/* INSERT USER */

$query = mysqli_query($conn,
"INSERT INTO users
(nama,username,email,password)
VALUES
('$nama','$username','$email','$password')");

if($query){

    // Ambil data user
    $user = mysqli_query($conn,
    "SELECT * FROM users WHERE email='$email'");

    $data = mysqli_fetch_assoc($user);

    // SESSION LOGIN
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['username'] = $data['username'];

    // MASUK DASHBOARD
    header("Location: ../user/index.php");
    exit;

}else{

    echo "Register gagal";

}
?>