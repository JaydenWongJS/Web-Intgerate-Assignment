<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="app.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/image/logo.png">
</head>

<body>
<div class="nav_bar_container">
        <nav class="nav_bar">
            <div class="logo">
                <img src="image/logo.png" alt="Logo" width="70" height="70">
            </div>
            <ul class="nav_url">
                <li><a class="link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a class="link" href="aboutus.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li class="features">
                    <a class="link" href="#"><i class="fas fa-star"></i> Features <i class="fa fa-caret-down"></i></a>
                    <div class="dropdown-content">
                        <a href="#">Link 1</a>
                        <a href="#">Link 2</a>
                        <a href="#">Link 3</a>
                    </div>
                </li>
                <li><a class="link" href="shop.html"><i class="fas fa-dollar-sign"></i> Shop</a></li>
                <li><a class="link" href="contactus.php"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>

            <div class="user_settings">
                <a href="cart.html" class="cart"><i class="fas fa-shopping-cart"></i></a>
                <a href="login.php" class="login"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="profile.php" class="profile"><i class='fas fa-user-cog'></i> Profile</a>
            </div>
        </nav>
    </div>

