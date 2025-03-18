<?php
session_start();
require '../config/db.php';

$query = isset($_GET["query"]) ? trim($_GET["query"]) : "";

$users = $bosses = $runs = [];

if (!empty($query)) {
    // Search for users
    $stmt = $pdo->prepare("SELECT userID, username, profilePicture FROM users WHERE username LIKE :query LIMIT 5");
    $stmt->execute(["query" => "%$query%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search for bosses
    $stmt = $pdo->prepare("SELECT bossID, name, image FROM bosses WHERE name LIKE :query LIMIT 5");
    $stmt->execute(["query" => "%$query%"]);
    $bosses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search for speedruns
    $stmt = $pdo->prepare("
        SELECT s.runID, b.name AS boss_name, u.username, s.runTime 
        FROM speedruns s 
        JOIN bosses b ON s.bossID = b.bossID 
        JOIN users u ON s.userID = u.userID 
        WHERE b.name LIKE :query OR u.username LIKE :query 
        LIMIT 5
    ");
    $stmt->execute(["query" => "%$query%"]);
    $runs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../responsive-navbar.css">

    <link rel="stylesheet" href="../styles.css"> <!-- External CSS -->
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <h2 class="text-center">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>

        <?php if (empty($users) && empty($bosses) && empty($runs)): ?>
            <p class="text-center text-muted">No results found.</p>
        <?php else: ?>
            
            <!-- Users Section -->
            <?php if (!empty($users)): ?>
                <h3 class="mt-4"><i class="fas fa-users"></i> Users</h3>
                <div class="list-group">
                    <?php foreach ($users as $user): ?>
                        <a href="user_profile.php?userID=<?php echo $user['userID']; ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <img src="<?php echo $user['profilePicture'] ? htmlspecialchars($user['profilePicture']) : 'default-user.png'; ?>" 
                                 alt="Profile" class="rounded-circle me-3" width="40" height="40">
                            <?php echo htmlspecialchars($user["username"]); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Bosses Section -->
            <?php if (!empty($bosses)): ?>
                <h3 class="mt-4"><i class="fas fa-dragon"></i> Bosses</h3>
                <div class="row g-3">
                    <?php foreach ($bosses as $boss): ?>
                        <div class="col-md-4">
                            <div class="card bg-dark text-light h-100">
                                <img src="<?php echo $boss['image'] ? htmlspecialchars($boss['image']) : 'default-boss.png'; ?>" 
                                     class="card-img-top boss-img" alt="Boss Image">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($boss["name"]); ?></h5>
                                    <a href="leaderboard.php?bossID=<?php echo $boss['bossID']; ?>" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-trophy"></i> View Leaderboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Speedruns Section -->
            <?php if (!empty($runs)): ?>
                <h3 class="mt-4"><i class="fas fa-stopwatch"></i> Speedruns</h3>
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Runner</th>
                            <th>Boss</th>
                            <th>Run Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($runs as $run): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($run["username"]); ?></td>
                                <td><?php echo htmlspecialchars($run["boss_name"]); ?></td>
                                <td><?php echo htmlspecialchars($run["runTime"]); ?></td>
                                <td>
                                    <a href="view_run.php?runID=<?php echo $run['runID']; ?>" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-play"></i> View Run
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub.</p>
    </footer>
</body>
</html>
