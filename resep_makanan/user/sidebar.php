<div class="sidebar" style="
background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important;
border-right: 4px solid #ffc800 !important;
position: fixed !important;
top: 0 !important;
left: 0 !important;
height: 100vh !important;
overflow-y: auto !important;
overflow-x: hidden !important;
z-index: 9999;
">
    <div class="logo" style="text-align: center; padding-top: 30px; margin-bottom: 5px;">
        <img src="../assets/logo_limeev.png" style="width: 180px;">
    </div>
    <ul class="menu" style="list-style: none; padding: 0 15px; margin-top: 20px;">
        <li><a href="../home.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>"><i class="fa fa-house"></i> Home</a></li>
        <li><a href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>"><i class="fa fa-chart-line"></i> Dashboard</a></li>
        <li><a href="tambah_resep.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'tambah_resep.php') ? 'active' : ''; ?>"><i class="fa fa-plus"></i> Tambah Resep</a></li>
        <li><a href="favorit.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'favorit.php') ? 'active' : ''; ?>"><i class="fa fa-heart"></i> Favorit</a></li>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <li style="margin-top: 15px; padding: 10px 15px; color: #ff9f43; font-weight: bold; font-size: 0.9em;">
                MENU ADMIN
            </li>

            <li>
                <a href="admin_kelola_resep.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admin_kelola_resep.php') ? 'active' : ''; ?>">
                    <i class="fa fa-book"></i> Kelola Resep
                </a>
            </li>

            <li>
                <a href="admin_kelola_user.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admin_kelola_user.php') ? 'active' : ''; ?>">
                    <i class="fa fa-users"></i> Kelola User
                </a>
            </li>
        <?php endif; ?>

        <li><a href="profil.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'active' : ''; ?>"><i class="fa fa-user-edit"></i> Edit Profil</a></li>

        <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
            <a href="../logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
        </li>
    </ul>
</div>