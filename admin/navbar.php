<?php


// Check if the user is logged in
$isLoggedIn = isset($_SESSION["userID"]);

if ($isLoggedIn) {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT username, profilePicture FROM users WHERE userID = :userID");
    $stmt->execute(["userID" => $_SESSION["userID"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profilePicture = $user["profilePicture"] ? $user["profilePicture"] : "default-user.png";

    // Fetch latest notifications (Limit to 5)
    $stmt = $pdo->prepare("SELECT message, created_at FROM notifications WHERE userID = :userID ORDER BY created_at DESC LIMIT 5");
    $stmt->execute(["userID" => $_SESSION["userID"]]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Speedrun Hub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
         

   

            <?php if ($isLoggedIn): ?>
                <!-- Notifications Dropdown -->
                <div class="dropdown me-3">
                    <a class="nav-link position-relative" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        <?php if (count($notifications) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo count($notifications); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationsDropdown" style="min-width: 300px; max-height: 300px; overflow-y: auto;">
                     
                        <?php if (empty($notifications)): ?>
                            <li class="dropdown-item  text-center text-white">No new notifications</li>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): ?>
                                <li class="dropdown-item small">
                                    <p class="mb-0 text-white"><?php echo htmlspecialchars($notif["message"]); ?></p>
                                    <small class="text-muted text-white"><?php echo htmlspecialchars($notif["created_at"]); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown">
                    <h6 class="text-white"><?php echo htmlspecialchars($user["username"]); ?></h6>
                    
                    
                       
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        
        
                        <li><a class="dropdown-item" href="../user/profile_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary ms-3">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    document.getElementById('notificationsDropdown').addEventListener('click', function () {
        // Send AJAX request to mark notifications as read
        fetch('mark_notifications_read.php', { method: 'POST' })
        .then(response => response.text())
        .then(data => {
            document.querySelector('.badge.bg-danger').style.display = 'none'; // Hide notification count
        })
        .catch(error => console.error('Error:', error));
    });
</script>