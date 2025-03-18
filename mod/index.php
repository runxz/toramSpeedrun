<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== "Moderator") {
    header("Location: ../login");
    exit;
}

require '../config/db.php';

// Fetch pending, verified, and rejected speedruns
$stmt = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM speedruns WHERE verificationStatus = 'Pending') AS pendingRuns,
    (SELECT COUNT(*) FROM speedruns WHERE verificationStatus = 'Verified') AS verifiedRuns,
    (SELECT COUNT(*) FROM speedruns WHERE verificationStatus = 'Rejected') AS rejectedRuns
");
$counts = $stmt->fetch(PDO::FETCH_ASSOC);

$pendingRuns = $counts["pendingRuns"];
$verifiedRuns = $counts["verifiedRuns"];
$rejectedRuns = $counts["rejectedRuns"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard</title>

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
        <div class="row">
            <!-- Moderator Dashboard Card -->
            <div class="col-md-12">
                <div class="card bg-dark text-light shadow-lg p-4">
                    <h2 class="text-center mb-4">Moderator Dashboard</h2>

                    <div class="row g-3">
                        <!-- Pending Runs -->
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark text-center p-3">
                                <h4><i class="fas fa-hourglass-half"></i> Pending Runs</h4>
                                <p class="fs-4"><?php echo $pendingRuns; ?></p>
                            </div>
                        </div>

                        <!-- Verified Runs -->
                        <div class="col-md-4">
                            <div class="card bg-success text-light text-center p-3">
                                <h4><i class="fas fa-check-circle"></i> Verified Runs</h4>
                                <p class="fs-4"><?php echo $verifiedRuns; ?></p>
                            </div>
                        </div>

                        <!-- Rejected Runs -->
                        <div class="col-md-4">
                            <div class="card bg-danger text-light text-center p-3">
                                <h4><i class="fas fa-times-circle"></i> Rejected Runs</h4>
                                <p class="fs-4"><?php echo $rejectedRuns; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Moderation Runs -->
            <div class="col-md-12 mt-4">
                <div class="card bg-dark text-light shadow-lg p-4">
                    <h3 class="text-center">Pending Speedruns for Review</h3>
                    <?php include 'view_mod_runs.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
