<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== "Moderator") {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["runID"]) && isset($_POST["action"])) {
    $runID = $_POST["runID"];
    $action = $_POST["action"];
    $moderatorID = $_SESSION["userID"];

    // Get userID of the run submitter
    $stmt = $pdo->prepare("SELECT userID FROM speedruns WHERE runID = :runID");
    $stmt->execute(["runID" => $runID]);
    $runData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$runData) {
        die("Speedrun not found.");
    }

    $userID = $runData["userID"];

    if ($action == "verify") {
        $status = "Verified";
        $notes = NULL;
        $message = "Your speedrun (Run ID: $runID) has been verified! 🎉";
    } elseif ($action == "reject") {
        if (!isset($_POST["rejectionNotes"]) || empty(trim($_POST["rejectionNotes"]))) {
            die("Rejection note is required.");
        }
        $status = "Rejected";
        $notes = trim($_POST["rejectionNotes"]);
        $message = "Your speedrun (Run ID: $runID) was rejected. Reason: $notes";
    } else {
        die("Invalid action.");
    }

    // Update speedrun status
    $stmt = $pdo->prepare("UPDATE speedruns SET verificationStatus = :status, verificationNotes = :notes WHERE runID = :runID");
    if ($stmt->execute(["status" => $status, "notes" => $notes, "runID" => $runID])) {
        echo "SQL executed successfully.<br>";

        // Send notification to the user
        $stmt = $pdo->prepare("INSERT INTO notifications (userID, message) VALUES (:userID, :message)");
        $stmt->execute(["userID" => $userID, "message" => $message]);

        echo "Notification sent to user ID: $userID<br>";
    } else {
        echo "SQL execution failed.<br>";
    }

    exit;
}
?>
