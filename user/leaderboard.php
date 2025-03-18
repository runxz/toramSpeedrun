<?php
session_start();
require '../config/db.php';

// Check if bossID is provided
if (!isset($_GET["bossID"])) {
    die("No boss selected.");
}

$bossID = $_GET["bossID"];

// Fetch boss details
$stmt = $pdo->prepare("SELECT name, image FROM bosses WHERE bossID = :bossID");
$stmt->execute(["bossID" => $bossID]);
$boss = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$boss) {
    die("Boss not found.");
}

// Fetch leaderboard runs
$stmt = $pdo->prepare("
    SELECT s.runID, u.userID, u.username, s.runTime, s.submissionDate, s.videoURL
    FROM speedruns s
    JOIN users u ON s.userID = u.userID
    WHERE s.bossID = :bossID AND s.verificationStatus = 'Verified'
    ORDER BY s.runTime ASC
");
$stmt->execute(["bossID" => $bossID]);
$runs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to display time elapsed
function time_elapsed($datetime) {
    $timestamp = strtotime($datetime);
    $seconds = time() - $timestamp;

    $units = [
        "year" => 31536000,
        "month" => 2592000,
        "week" => 604800,
        "day" => 86400,
        "hour" => 3600,
        "minute" => 60,
        "second" => 1
    ];

    foreach ($units as $unit => $value) {
        if ($seconds >= $value) {
            $count = floor($seconds / $value);
            return "$count $unit" . ($count > 1 ? "s" : "") . " ago";
        }
    }
    return "Just now";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - <?php echo htmlspecialchars($boss["name"]); ?></title>
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
        <div class="text-center">
            <h2 class="mb-4">Leaderboard for <?php echo htmlspecialchars($boss["name"]); ?></h2>
            
            <!-- Boss Image -->
            <img src="<?php echo $boss['image'] ? htmlspecialchars($boss['image']) : 'default-boss.png'; ?>" 
                 class="img-fluid rounded shadow-lg" style="max-height: 250px;" alt="Boss Image">

            <!-- Submit Run Button -->
            <div class="mt-4">
                <a href="submit_run.php?bossID=<?php echo $bossID; ?>" class="btn btn-success">
                    <i class="fas fa-upload"></i> Submit Your Run
                </a>
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="table-responsive mt-4">
            <table class="table table-dark table-hover text-center">
                <thead>
                    <tr>
                        <th>Runner</th>
                        <th>Run Time</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($runs)): ?>
                        <?php foreach ($runs as $run): ?>
                            <tr>
                                <td>
                                    <a href="user_profile.php?userID=<?php echo $run['userID']; ?>" class="text-decoration-none text-light">
                                        <?php echo htmlspecialchars($run["username"]); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($run["runTime"]); ?></td>
                                <td><?php echo time_elapsed($run["submissionDate"]); ?></td>
                                <td>
                                    <a href="view_run.php?runID=<?php echo $run['runID']; ?>" target="_blank" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-play"></i> View Run
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-muted">No verified runs yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="view_bosses.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Boss List</a>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub.</p>
    </footer>
</body>
</html>
