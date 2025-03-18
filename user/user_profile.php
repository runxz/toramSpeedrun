<?php
session_start();
require '../config/db.php';

// Check if userID is provided
if (!isset($_GET["userID"])) {
    die("User not specified.");
}

$userID = $_GET["userID"];

// Fetch user details
$stmt = $pdo->prepare("SELECT username, email, role, profilePicture, registrationDate FROM users WHERE userID = :userID");
$stmt->execute(["userID" => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$registrationDate = $user["registrationDate"] ?? "Unknown"; 

if (!$user) {
    die("User not found.");
}

// Fetch user's speedruns
$stmt = $pdo->prepare("
    SELECT s.runID, b.name AS boss_name, s.runTime, s.submissionDate, s.verificationStatus, s.videoURL
    FROM speedruns s
    JOIN bosses b ON s.bossID = b.bossID
    WHERE s.userID = :userID
    ORDER BY s.submissionDate DESC
");
$stmt->execute(["userID" => $userID]);
$runs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to format time elapsed
function time_elapsed($datetime) {
    $timestamp = strtotime($datetime);
    $seconds = time() - $timestamp;

    $units = [
        "year" => 31536000, "month" => 2592000, "week" => 604800, "day" => 86400,
        "hour" => 3600, "minute" => 60, "second" => 1
    ];

    foreach ($units as $unit => $value) {
        if ($seconds >= $value) {
            $count = floor($seconds / $value);
            return "$count $unit" . ($count > 1 ? "s" : "") . " ago";
        }
    }
    return "Just now";
}

$profilePicture = $user["profilePicture"] ? htmlspecialchars($user["profilePicture"]) : "../uploads/default-user.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user["username"]); ?>'s Profile</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="card bg-dark text-light shadow-lg p-4">
            <div class="text-center">
                <img src="<?php echo $profilePicture; ?>" class="rounded-circle border border-light mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h2><?php echo htmlspecialchars($user["username"]); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($user["role"]); ?></p>
                <p>Joined: <strong><?php echo htmlspecialchars($registrationDate); ?></strong></p>
            
            </div>
        </div>

        <div class="card bg-secondary text-light shadow-lg mt-4 p-4">
            <h3 class="text-center">Speedruns by <?php echo htmlspecialchars($user["username"]); ?></h3>

            <?php if (empty($runs)): ?>
                <p class="text-center">No speedruns submitted yet.</p>
            <?php else: ?>
                <table class="table table-dark table-hover mt-3">
                    <thead>
                        <tr>
                            <th>Boss</th>
                            <th>Run Time</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Video</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($runs as $run): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($run["boss_name"]); ?></td>
                                <td><?php echo htmlspecialchars($run["runTime"]); ?></td>
                                <td><?php echo time_elapsed($run["submissionDate"]); ?></td>
                                <td>
                                    <?php if ($run["verificationStatus"] === "Verified"): ?>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($run["verificationStatus"]); ?></span>
                                    <?php elseif ($run["verificationStatus"] === "Rejected"): ?>
                                        <span class="badge bg-danger"><?php echo htmlspecialchars($run["verificationStatus"]); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?php echo htmlspecialchars($run["verificationStatus"]); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($run["videoURL"]); ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-video"></i> Watch
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="text-center mt-3">
            <a href="view_bosses.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Boss List</a>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub.</p>
    </footer>
</body>
</html>
