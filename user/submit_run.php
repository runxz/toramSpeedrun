<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../login");
    exit;
}

require '../config/db.php';

// Check if bossID is provided
if (!isset($_GET["bossID"])) {
    die("No boss selected.");
}

$bossID = $_GET["bossID"];

// Fetch boss details
$stmt = $pdo->prepare("SELECT name FROM bosses WHERE bossID = :bossID");
$stmt->execute(["bossID" => $bossID]);
$boss = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$boss) {
    die("Boss not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION["userID"];
    $runTime = $_POST["runTime"];
    $videoURL = $_POST["videoURL"];

    // Insert speedrun into database
    $stmt = $pdo->prepare("INSERT INTO speedruns (userID, bossID, runTime, videoURL, submissionDate, verificationStatus) 
                           VALUES (:userID, :bossID, :runTime, :videoURL, NOW(), 'Pending')");
    $stmt->execute([
        "userID" => $userID, 
        "bossID" => $bossID, 
        "runTime" => $runTime, 
        "videoURL" => $videoURL
    ]);

    $message = "Speedrun submitted successfully! Pending verification.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Speedrun - <?php echo htmlspecialchars($boss["name"]); ?></title>
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
            <h2 class="mb-4">Submit a Speedrun for <span class="text-warning"><?php echo htmlspecialchars($boss["name"]); ?></span></h2>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Speedrun Submission Form -->
        <form method="POST" class="bg-dark text-light p-4 rounded shadow">
            <div class="mb-3">
                <label class="form-label">Boss:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($boss["name"]); ?>" disabled>
                <input type="hidden" name="bossID" value="<?php echo $bossID; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Run Time (HH:MM:SS):</label>
                <input type="time" name="runTime" step="1" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Video URL:</label>
                <input type="url" name="videoURL" class="form-control" placeholder="https://example.com" required>
            </div>

            <button type="submit" class="btn btn-success w-100"><i class="fas fa-upload"></i> Submit Speedrun</button>
        </form>

        <div class="text-center mt-4">
            <a href="leaderboard.php?bossID=<?php echo $bossID; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Leaderboard</a>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub.</p>
    </footer>
</body>
</html>
