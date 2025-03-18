<?php
require '../config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT userID FROM users WHERE username = :username OR email = :email");
    $stmt->execute(["username" => $username, "email" => $email]);
    
    if ($stmt->rowCount() > 0) {
        $message = '<div class="alert alert-danger text-center">Username or email already taken.</div>';
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute(["username" => $username, "email" => $email, "password" => $hashedPassword]);

        $message = '<div class="alert alert-success text-center">Registration successful! <a href="../login">Login here</a></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
   

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card bg-dark text-light shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center">Register</h2>

            <?php echo $message; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-user-plus"></i> Register</button>
            </form>

            <p class="mt-3 text-center">Already have an account? <a href="../login" class="text-warning">Login here</a></p>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
