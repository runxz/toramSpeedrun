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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user["username"]); ?>'s Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../responsive-navbar.css">

    <link rel="stylesheet" href="../styles.css"> <!-- External CSS -->
</head>
<body>
    <h2><?php echo htmlspecialchars($user["username"]); ?>'s Profile</h2>

    <?php if ($user["profilePicture"]): ?>
        <img src="<?php echo htmlspecialchars($user["profilePicture"]); ?>" alt="Profile Picture" width="150">
    <?php else: ?>
        <p>No profile picture.</p>
    <?php endif; ?>

    <p><strong>Username:</strong> <?php echo htmlspecialchars($user["username"]); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user["role"]); ?></p>
    <p><strong>Joined:</strong> <?php echo htmlspecialchars($user["registrationDate"]); ?></p>

    <h3>Speedruns by <?php echo htmlspecialchars($user["username"]); ?></h3>
    <table border="1">
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
                    <td><?php echo htmlspecialchars($run["verificationStatus"]); ?></td>
                    <td><a href="<?php echo htmlspecialchars($run["videoURL"]); ?>" target="_blank">Watch</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="view_bosses.php">Back to Boss List</a>
</body>
</html>
