<?php
session_start();
require 'config/db.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION["userID"]);

// Fetch total players
$stmt = $pdo->query("SELECT COUNT(*) AS totalPlayers FROM users");
$totalPlayers = $stmt->fetch(PDO::FETCH_ASSOC)["totalPlayers"];

// Fetch total runs submitted
$stmt = $pdo->query("SELECT COUNT(*) AS totalRuns FROM speedruns");
$totalRuns = $stmt->fetch(PDO::FETCH_ASSOC)["totalRuns"];
?>


<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toram Online Speedrun Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../responsive-navbar.css">
 
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Speedrun Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item"><a class="nav-link" href="./user/dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="./login">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="./register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="text-center">
            <div class="hero-content">
                <h1 class="hero-title">Toram Online Speedrun Hub</h1>
                <p class="hero-subtitle">Push your limits. Join the race. Rewrite history.</p>
            </div>
            <br><br>
            <div class="counters">
                <div class="counter-box">
                    <h2><?php echo htmlspecialchars($totalPlayers); ?></h2>
                    <p>Runners</p>
                </div>
                <div class="counter-box">
                    <h2><?php echo htmlspecialchars($totalRuns); ?></h2>
                    <p>Runs Submitted</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Form Section -->
    <?php if (!$isLoggedIn): ?>
        <div class="container my-5">
            <h2 class="text-center mb-4">Login to Your Account</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php if (!empty($message)): ?>
                        <p class="alert alert-danger text-center"><?php echo htmlspecialchars($message); ?></p>
                    <?php endif; ?>
                    <form method="POST" action="./login/" class="bg-dark text-light p-4 rounded">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="mt-3 text-center">Don't have an account? <a href="../register" class="text-warning">Register here</a></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Facebook News & Updates -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Latest News & Updates</h2>
        <div class="justify-content-center">
            <!-- Elfsight Facebook Feed | Untitled Facebook Feed -->
<script src="https://static.elfsight.com/platform/platform.js" async></script>
<div class="elfsight-app-07ee6f32-fc0b-4acf-b82c-39cc3f3e2a8b" data-elfsight-app-lazy></div>
        </div>
    </div>

    <!-- Socials -->
    <div class="help-us-grow py-5">
        <div class="container text-center">
            <h2 class="mb-4">Help Us Grow</h2>
            <p class="mb-4">Follow us on social media and help us build a bigger community of Toram Online speedrunners!</p>
            <div class="social-icons d-flex justify-content-center gap-4">
                <a href="https://www.facebook.com/profile.php?id=61574324578183" class="social-link" target="_blank"><i class="fab fa-facebook fa-3x"></i></a>
                <a href="https://www.twitter.com/yourhandle" class="social-link" target="_blank"><i class="fab fa-twitter fa-3x"></i></a>
                <a href="https://www.instagram.com/yourprofile" class="social-link" target="_blank"><i class="fab fa-instagram fa-3x"></i></a>
            </div>
            <!-- Ko-fi Button -->
            <div class="mt-4">
                <script type='text/javascript' src='https://storage.ko-fi.com/cdn/widget/Widget_2.js'></script>
                <script type='text/javascript'>
                    kofiwidget2.init('Support us on Ko-fi', '#79879e', 'A0A718CFLA');
                    kofiwidget2.draw();
                </script>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
