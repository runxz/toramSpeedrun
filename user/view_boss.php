<?php
session_start();
require '../config/db.php';

// Get filter and search parameters
$difficultyFilter = isset($_GET["difficulty"]) ? $_GET["difficulty"] : "";
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

// Fetch bosses with images
$sql = "SELECT bossID, name, description, difficulty, image FROM bosses WHERE 1";

if ($difficultyFilter) {
    $sql .= " AND difficulty = :difficulty";
}
if ($search) {
    $sql .= " AND name LIKE :search";
}

$sql .= " ORDER BY bossID DESC"; // Latest bosses first

$stmt = $pdo->prepare($sql);

$params = [];
if ($difficultyFilter) $params["difficulty"] = $difficultyFilter;
if ($search) $params["search"] = "%$search%";

$stmt->execute($params);
$bosses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bosses</title>
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
        <h2 class="text-center mb-4">Boss List</h2>

        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Filter by Difficulty:</label>
                <select name="difficulty" class="form-select">
                    <option value="">All Difficulties</option>
                    <option value="Easy" <?php if ($difficultyFilter == "Easy") echo "selected"; ?>>Easy</option>
                    <option value="Normal" <?php if ($difficultyFilter == "Normal") echo "selected"; ?>>Normal</option>
                    <option value="Hard" <?php if ($difficultyFilter == "Hard") echo "selected"; ?>>Hard</option>
                    <option value="Ultimate" <?php if ($difficultyFilter == "Ultimate") echo "selected"; ?>>Ultimate</option>
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label">Search Boss Name:</label>
                <input type="text" name="search" class="form-control" placeholder="Enter boss name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>

        <!-- Boss Cards -->
        <div class="row g-4">
            <?php foreach ($bosses as $boss): ?>
                <div class="col-md-4">
                    <div class="card bg-dark text-light h-100">
                        <!-- Boss Image -->
                        <?php $bossImage = $boss["image"] ? htmlspecialchars($boss["image"]) : "default-boss.png"; ?>
                        <img src="<?php echo $bossImage; ?>" class="card-img-top boss-img" alt="Boss Image">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($boss["name"]); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($boss["description"]); ?></p>
                            <span class="badge 
                                <?php echo $boss["difficulty"] == "Easy" ? 'bg-success' : 
                                           ($boss["difficulty"] == "Normal" ? 'bg-info' : 
                                           ($boss["difficulty"] == "Hard" ? 'bg-warning' : 'bg-danger')); ?>">
                                <?php echo htmlspecialchars($boss["difficulty"]); ?>
                            </span>
                        </div>

                        <div class="card-footer text-center">
                            <a href="leaderboard.php?bossID=<?php echo $boss['bossID']; ?>" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-trophy"></i> View Leaderboard
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub.</p>
    </footer>
</body>
</html>
