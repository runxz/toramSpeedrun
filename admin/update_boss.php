<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== "Admin") {
    header("Location: ../login");
    exit;
}

require '../config/db.php';

function uploadImage($file, $existingImage = null) {
    $targetDir = "../uploads/";
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($file['error'] === 0 && in_array($file['type'], $allowedTypes)) {
        // Generate a unique file name
        $fileName = time() . "_" . basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;

        // Move the file to the uploads directory
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            // Delete old image if it exists
            if ($existingImage && file_exists($targetDir . $existingImage)) {
                unlink($targetDir . $existingImage);
            }
            return $fileName;
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $adminID = $_SESSION["userID"];

    if ($_POST["action"] === "add") {
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $difficulty = $_POST["difficulty"];

        $imageFileName = isset($_FILES["image"]) ? uploadImage($_FILES["image"]) : null;

        $stmt = $pdo->prepare("INSERT INTO bosses (name, description, difficulty, image) VALUES (:name, :description, :difficulty, :image)");
        $stmt->execute([
            "name" => $name,
            "description" => $description,
            "difficulty" => $difficulty,
            "image" => $imageFileName
        ]);

        $bossID = $pdo->lastInsertId();
        logAction($pdo, $adminID, "Added new boss", "Boss", $bossID);

    } elseif ($_POST["action"] === "edit" && isset($_POST["bossID"])) {
        $bossID = $_POST["bossID"];
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $difficulty = $_POST["difficulty"];

        // Get current image
        $stmt = $pdo->prepare("SELECT image FROM bosses WHERE bossID = :bossID");
        $stmt->execute(["bossID" => $bossID]);
        $boss = $stmt->fetch(PDO::FETCH_ASSOC);
        $existingImage = $boss["image"];

        $newImage = isset($_FILES["image"]) ? uploadImage($_FILES["image"], $existingImage) : $existingImage;

        $stmt = $pdo->prepare("UPDATE bosses SET name = :name, description = :description, difficulty = :difficulty, image = :image WHERE bossID = :bossID");
        $stmt->execute([
            "name" => $name,
            "description" => $description,
            "difficulty" => $difficulty,
            "image" => $newImage,
            "bossID" => $bossID
        ]);

        logAction($pdo, $adminID, "Edited boss", "Boss", $bossID);

    }
} elseif (isset($_GET["bossID"]) && $_GET["action"] === "delete") {
    $bossID = $_GET["bossID"];
    $adminID = $_SESSION["userID"];

    // Get current image
    $stmt = $pdo->prepare("SELECT image FROM bosses WHERE bossID = :bossID");
    $stmt->execute(["bossID" => $bossID]);
    $boss = $stmt->fetch(PDO::FETCH_ASSOC);
    $existingImage = $boss["image"];

    // Delete image file
    if ($existingImage && file_exists("../uploads/" . $existingImage)) {
        unlink("../uploads/" . $existingImage);
    }

    $stmt = $pdo->prepare("DELETE FROM bosses WHERE bossID = :bossID");
    $stmt->execute(["bossID" => $bossID]);

    logAction($pdo, $adminID, "Deleted boss", "Boss", $bossID);
}

header("Location: index.php");
exit;
?>
