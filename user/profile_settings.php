<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../login");
    exit;
}

require '../config/db.php';

$userID = $_SESSION["userID"];

// Fetch user details
$stmt = $pdo->prepare("SELECT username, email, profilePicture FROM users WHERE userID = :userID");
$stmt->execute(["userID" => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure all fields exist
if (!$user) {
    die("User not found.");
}

$username = htmlspecialchars($user["username"] ?? ""); 
$email = htmlspecialchars($user["email"] ?? ""); 
$profilePicture = $user["profilePicture"] ?? "default-user.png";

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_profile"])) {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);

        // Update username and email
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE userID = :userID");
        $stmt->execute(["username" => $username, "email" => $email, "userID" => $userID]);

        $message = "Profile updated successfully.";
    }

    if (isset($_POST["update_password"])) {
        $currentPassword = $_POST["current_password"];
        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];

        // Fetch current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE userID = :userID");
        $stmt->execute(["userID" => $userID]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($currentPassword, $userData["password"])) {
            $message = "Incorrect current password.";
        } elseif ($newPassword !== $confirmPassword) {
            $message = "New passwords do not match.";
        } else {
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE userID = :userID");
            $stmt->execute(["password" => $hashedPassword, "userID" => $userID]);

            $message = "Password updated successfully.";
        }
    }

    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES["profile_picture"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
            $stmt = $pdo->prepare("UPDATE users SET profilePicture = :profilePicture WHERE userID = :userID");
            $stmt->execute(["profilePicture" => $targetFilePath, "userID" => $userID]);

            $user["profilePicture"] = $targetFilePath;
            $message = "Profile picture updated.";
        } else {
            $message = "Error uploading profile picture.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../responsive-navbar.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="card bg-dark text-light shadow-lg p-4">
            <h2 class="text-center mb-4">Profile Settings</h2>

            <?php if ($message): ?>
                <div class="alert alert-info text-center"><?php echo $message; ?></div>
            <?php endif; ?>

            <!-- Profile Picture Preview -->
            <div class="text-center mb-3">
                <img src="<?php echo $user["profilePicture"] ?: 'default-user.png'; ?>" alt="Profile Picture" class="rounded-circle shadow" style="width: 120px; height: 120px;">
            </div>

            <!-- Update Profile Form -->
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>

                </div>

                <button type="submit" name="update_profile" class="btn btn-primary w-100"><i class="fas fa-save"></i> Update Profile</button>
            </form>

            <hr class="text-light">

            <!-- Update Password Form -->
            <form method="POST" class="mb-4">
                <h4>Change Password</h4>
                <div class="mb-3">
                    <label class="form-label">Current Password:</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password:</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password:</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <button type="submit" name="update_password" class="btn btn-danger w-100"><i class="fas fa-lock"></i> Change Password</button>
            </form>

            <hr class="text-light">

            <!-- Upload Profile Picture Form -->
            <form method="POST" enctype="multipart/form-data" class="mb-4">
                <h4>Update Profile Picture</h4>
                <div class="mb-3">
                    <input type="file" name="profile_picture" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-warning w-100"><i class="fas fa-upload"></i> Upload Picture</button>
            </form>

            <div class="text-center">
                <a href="profile.php" class="btn btn-secondary"><i class="fas fa-user"></i> Back to Profile</a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub.</p>
    </footer>
</body>
</html>
