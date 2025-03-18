<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== "Admin") {
    header("Location: ../login");
    exit;
}

require '../config/db.php';

if (!isset($_GET["bossID"])) {
    header("Location: manage_bosses.php");
    exit;
}

$bossID = $_GET["bossID"];

// Fetch boss details
$stmt = $pdo->prepare("SELECT * FROM bosses WHERE bossID = :bossID");
$stmt->execute(["bossID" => $bossID]);
$boss = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$boss) {
    die("Boss not found.");
}

$currentImage = $boss['image'] ? "../uploads/" . htmlspecialchars($boss['image']) : "../uploads/default-boss.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Boss</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container my-5">
        <div class="card bg-dark text-light shadow-lg p-4">
            <h2 class="text-center mb-4">Edit Boss</h2>

            <form method="POST" action="update_boss.php" enctype="multipart/form-data">
                <input type="hidden" name="bossID" value="<?php echo $boss['bossID']; ?>">

                <div class="text-center mb-3">
                    <label class="form-label">Current Image</label><br>
                    <img src="<?php echo $currentImage; ?>" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                </div>

                <div class="mb-3">
                    <label class="form-label">Change Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label">Boss Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($boss['name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"><?php echo htmlspecialchars($boss['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Difficulty</label>
                    <select name="difficulty" class="form-control" required>
                        <option value="Easy" <?php if ($boss['difficulty'] == "Easy") echo "selected"; ?>>Easy</option>
                        <option value="Normal" <?php if ($boss['difficulty'] == "Normal") echo "selected"; ?>>Normal</option>
                        <option value="Hard" <?php if ($boss['difficulty'] == "Hard") echo "selected"; ?>>Hard</option>
                        <option value="Nightmare" <?php if ($boss['difficulty'] == "Nightmare") echo "selected"; ?>>Nightmare</option>
                        <option value="Ultimate" <?php if ($boss['difficulty'] == "Ultimate") echo "selected"; ?>>Ultimate</option>
                    </select>
                </div>

                <button type="submit" name="action" value="edit" class="btn btn-primary w-100">Update Boss</button>
            </form>

            <div class="text-center mt-3">
                <a href="manage_bosses.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Boss Management</a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
