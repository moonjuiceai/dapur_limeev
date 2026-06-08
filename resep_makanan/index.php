<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limeev - Resep Makanan</title>
    
    <link rel="stylesheet" href="assets/css/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        body, .hero, nav {
            background: linear-gradient(180deg, #FFFFFF 0%, #FFFFC5 100%) !important; 
        }
        
        .hero-text p {
            color: #333333 !important; 
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .hero-logo {
            width: 320px; 
            max-width: 80%; 
            height: auto;
            filter: drop-shadow(0px 10px 15px rgba(255, 159, 67, 0.3)); 
            margin-bottom: 10px;
        }

        nav ul {
            display: flex;
            gap: 15px; 
            align-items: center;
        }

        nav ul li a {
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 25px; 
            transition: all 0.3s ease; 
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px; 
        }

        .nav-btn-special {
            background-color: #ffc800 !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(255, 200, 0, 0.3);
        }

        .nav-btn-special:hover {
            background-color: #e6b400 !important; 
            transform: translateY(-2px); 
            color: white !important;
            box-shadow: 0 6px 15px rgba(255, 200, 0, 0.4);
        }
        
    </style>
</head>
<body>

<nav>
    <div class="logo">
      
    </div>

    <ul>
        <?php if(isset($_SESSION['user_id'])){ ?>
            <li>
                <a href="logout.php" class="nav-btn-special"><i class="fa fa-right-from-bracket"></i> Logout</a>
            </li>
            <li>
                <a href="user/index.php" class="nav-btn-special"><i class="fa fa-chart-line"></i> Dashboard</a>
            </li>
        <?php } else { ?>
            <li>
                <a href="login.php" class="nav-btn-special"><i class="fa fa-right-to-bracket"></i> Login</a>
            </li>
            <li>
                <a href="register.php" class="nav-btn-special"><i class="fa fa-user-plus"></i> Register</a>
            </li>
        <?php } ?>
    </ul>
</nav>

<section class="hero">
    <div class="hero-text" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%;">
        
        <img src="assets/logo_limeev.png" alt="Logo Besar Limeev" class="hero-logo">
        
        <p>
            Limeev adalah tempat berbagi resep makanan modern, mudah, dan menarik.
        </p>

        <?php if(isset($_SESSION['user_id'])){ ?>
            <a href="user/index.php" class="btn" style="background: #ffc800; color: white !important; font-weight: 600; padding: 12px 35px; border-radius: 30px; box-shadow: 0 5px 15px rgba(255,200,0,0.3);">
                Masuk Dashboard
            </a>
        <?php } else { ?>
            <a href="home.php" class="btn" style="background: #ffc800; color: white !important; font-weight: 600; padding: 12px 35px; border-radius: 30px; box-shadow: 0 5px 15px rgba(255,200,0,0.3);">
                Mulai Sekarang
            </a>
        <?php } ?>

    </div>
</section>

</body>
</html>