<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== "Admin") {
    header("Location: ../login");
    exit;
}

require '../config/db.php';

if (isset($_GET["userID"]) && isset($_GET["action"])) {
    $userID = $_GET["userID"];
    $action = $_GET["action"];
    $adminID = $_SESSION["userID"];

    // Prevent self-demotion
    if ($userID == $adminID) {
        die("You cannot change your own role.");
    }

    if ($action === "promote") {
        $stmt = $pdo->prepare("UPDATE users SET role = 'Moderator' WHERE userID = :userID");
        $stmt->execute(["userID" => $userID]);
        logAction($pdo, $adminID, "Promoted user to Moderator", "User", $userID);

    } elseif ($action === "unpromote") {
        $stmt = $pdo->prepare("UPDATE users SET role = 'User' WHERE userID = :userID AND role = 'Moderator'");
        $stmt->execute(["userID" => $userID]);
        logAction($pdo, $adminID, "Demoted user to User", "User", $userID);

    } elseif ($action === "delete") {
        $stmt = $pdo->prepare("DELETE FROM users WHERE userID = :userID");
        $stmt->execute(["userID" => $userID]);
        logAction($pdo, $adminID, "Deleted user", "User", $userID);
    }

    header("Location: manage_users.php");
    exit;
}
?>
