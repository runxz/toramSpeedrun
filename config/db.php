<?php
$host = "localhost"; // Change if using a remote DB
$dbname = "toram_speedrun";
$username = "root"; // Change if needed
$password = ""; // Change if using a secured DB

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function logAction($pdo, $adminID, $action, $targetType, $targetID) {
    $stmt = $pdo->prepare("INSERT INTO audit_log (adminID, action, targetType, targetID) VALUES (:adminID, :action, :targetType, :targetID)");
    $stmt->execute([
        "adminID" => $adminID,
        "action" => $action,
        "targetType" => $targetType,
        "targetID" => $targetID
    ]);
}

?>
