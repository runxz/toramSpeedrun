<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

// Fetch user details
$stmt = $pdo->prepare("SELECT username, email, role FROM users WHERE userID = :userID");
$stmt->execute(["userID" => $_SESSION["userID"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch total submitted runs by user
$stmt = $pdo->prepare("SELECT COUNT(*) AS totalRuns FROM speedruns WHERE userID = :userID");
$stmt->execute(["userID" => $_SESSION["userID"]]);
$totalRuns = $stmt->fetch(PDO::FETCH_ASSOC)["totalRuns"];

// Fetch latest 5 bosses
$stmt = $pdo->query("SELECT bossID, name, difficulty, image FROM bosses ORDER BY bossID DESC LIMIT 5");
$latestBosses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../responsive-navbar.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
           <!-- Latest 5 Bosses -->
           <div class="card bg-dark text-light shadow-lg p-4">
            <h4 class="text-center">Latest Bosses</h4>
            <div class="row g-3">
                <?php foreach ($latestBosses as $boss): ?>
                    <div class="col-md-4">
                        <div class="card bg-secondary text-light">
                            <?php if ($boss['image']): ?>
                                <img src="<?php echo htmlspecialchars($boss['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($boss['name']); ?>">
                            <?php else: ?>
                                <div class="bg-dark text-center p-4">No Image</div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($boss['name']); ?></h5>
                                <p class="card-text">Difficulty: <strong><?php echo htmlspecialchars($boss['difficulty']); ?></strong></p>
                                <a href="leaderboard.php?bossID=<?php echo $boss['bossID']; ?>" class="btn btn-primary w-100"><i class="fas fa-trophy"></i> View Leaderboard</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- User Stats -->
        <div class="card bg-dark text-light text-center mb-4">
            <div class="card-body">
               
                <?php include 'view_user_runs.php'; ?>
            </div>
        </div>

     
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
