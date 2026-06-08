<?php

include '../config/session_admin.php';

?>

<h1>Dashboard Admin</h1>

<p>
    Selamat datang,
    <?= $_SESSION['nama']; ?>
</p>

<a href="../logout.php">
    Logout
</a>