<?php
// START SESSION: This allows us to access $_SESSION on every page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FindIt - Lost & Found Platform</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./CSS/style.css?v=2">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
  <nav class="navbar">
    <div class="nav-container">
      <a href="index.php" class="logo">FindIt.io</a>
      <ul class="nav-links">
        <li><a href="browse.php">Browse Items</a></li>
        
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant'): ?>
            <li><a href="report.php">Report a item</a></li>
        <?php endif; ?>

        <li><a href="index.php#how-it-works">How It Works</a></li>
        <li><a href="food_offers.php">Food offers</a></li>
      </ul>
      
      <div class="nav-buttons">
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" class="nav-btn nav-btn-profile">
            <span class="nav-icon">👤</span>
            <span class="nav-text"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </a>
        
        <a href="logout.php" class="nav-btn nav-btn-logout">
            Logout
        </a>
    <?php else: ?>
        <button class="nav-btn nav-btn-primary" id="loginBtn">Login / Register</button>
    <?php endif; ?>
</div>
      
      <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
    </div>
  </nav>