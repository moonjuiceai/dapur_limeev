<?php
session_start();

include '../config/koneksi.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM users
WHERE email='$email'
AND password='$password'");

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
$data = mysqli_fetch_assoc($query);

if($data){
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['status_login'] = true;
    $_SESSION['nama'] = $data['nama']; 

    $_SESSION['role'] = $data['role'];
    
    $_SESSION['status_login'] = true;

    header("Location: ../home.php"); 
    exit;
}
 else {
    header("Location: ../login.php?error=invalid_credentials");
    exit;
}
?>