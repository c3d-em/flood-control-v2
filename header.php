<?php
session_start();

if (!function_exists('getImagePath')) {
    function getImagePath($baseName) {
        $extensions = ['.png', '.jpg', '.jpeg', '.PNG', '.JPG', '.JPEG'];
        $basePath = "assets/images/";
        foreach ($extensions as $ext) {
            $fullPath = $basePath . $baseName . $ext;
            if (file_exists($fullPath)) {
                return $baseName . $ext;
            }
        }
        return 'default.jpg';
    }
}

if (!function_exists('getLogoImage')) {
    function getLogoImage($logoName) {
        return getImagePath($logoName);
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ToyRex Corner</title>
<link rel="icon" type="image/x-icon" href="assets/images/<?php echo getLogoImage('logo1'); ?>">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav>
    <div class="nav-container">
        <div class="logo">
            <?php $logo = getLogoImage('logo'); ?>
            <?php if($logo != 'default.jpg'): ?>
                <img src="assets/images/<?php echo $logo; ?>" alt="ToyRex Corner Logo">
            <?php endif; ?>
            <div class="logo-text">
                <span class="logo-main">TOYREX</span>
                <span class="logo-sub">CORNER</span>
            </div>
        </div>
        
        <div class="nav-links" id="navLinks">
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a>
            <a href="services.php" class="<?php echo $current_page == 'services.php' ? 'active' : ''; ?>">Services</a>
            <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">Products</a>
            <a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact Us</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['user_type'] == 'admin'): ?>
                    <a href="admin.php" class="<?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">Admin Dashboard</a>
                <?php else: ?>
                    <a href="client.php" class="<?php echo $current_page == 'client.php' ? 'active' : ''; ?>">Client Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="includes/login_register_page.php" class="login-register-btn">Login / Register</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
