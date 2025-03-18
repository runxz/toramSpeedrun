<?php
session_start();
require '../config/db.php';

// Check if runID is provided
if (!isset($_GET["runID"])) {
    die("No run selected.");
}

$runID = $_GET["runID"];

// Fetch run details
$stmt = $pdo->prepare("
    SELECT s.videoURL, u.username, b.name AS boss_name, s.runTime, s.submissionDate
    FROM speedruns s
    JOIN users u ON s.userID = u.userID
    JOIN bosses b ON s.bossID = b.bossID
    WHERE s.runID = :runID
");
$stmt->execute(["runID" => $runID]);
$run = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$run) {
    die("Run not found.");
}

// Extract YouTube Video ID
function getYoutubeID($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/[^\/]+|(?:v|e(?:mbed)?)|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches);
    return $matches[1] ?? null;
}

$videoID = getYoutubeID($run["videoURL"]);
if (!$videoID) {
    die("Invalid YouTube link.");
}

// Fetch comments and replies
$stmt = $pdo->prepare("
    SELECT c.commentID, c.parentID, c.commentText, c.upvotes, c.downvotes, c.created_at, u.username
    FROM comments c
    JOIN users u ON c.userID = u.userID
    WHERE c.runID = :runID
    ORDER BY c.created_at ASC
");
$stmt->execute(["runID" => $runID]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize comments into a threaded structure
$threadedComments = [];
foreach ($comments as $comment) {
    if ($comment["parentID"] === null) {
        $threadedComments[$comment["commentID"]] = $comment;
        $threadedComments[$comment["commentID"]]["replies"] = [];
    } else {
        $threadedComments[$comment["parentID"]]["replies"][] = $comment;
    }
}

// Handle comment/reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"])) {
    if (!isset($_SESSION["userID"])) {
        die("You must be logged in to comment.");
    }

    $userID = $_SESSION["userID"];
    $commentText = trim($_POST["comment"]);
    $parentID = isset($_POST["parentID"]) ? $_POST["parentID"] : null;

    if (!empty($commentText)) {
        $stmt = $pdo->prepare("INSERT INTO comments (runID, userID, commentText, parentID) VALUES (:runID, :userID, :commentText, :parentID)");
        $stmt->execute(["runID" => $runID, "userID" => $userID, "commentText" => $commentText, "parentID" => $parentID]);

        // Reload the page to show the new comment/reply
        header("Location: view_run.php?runID=$runID");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Run - <?php echo htmlspecialchars($run["username"]); ?></title>
    
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
        <div class="text-center">
            <h2 class="mb-3"><?php echo htmlspecialchars($run["username"]); ?>'s Run on <span class="text-warning"><?php echo htmlspecialchars($run["boss_name"]); ?></span></h2>
            <p class="text-light">Run Time: <strong><?php echo htmlspecialchars($run["runTime"]); ?></strong></p>
            <p class="text-light">Submitted: <strong><?php echo htmlspecialchars($run["submissionDate"]); ?></strong></p>
        </div>

        <!-- Video Embed -->
        <div class="ratio ratio-16x9 mb-4">
            <iframe src="https://www.youtube.com/embed/<?php echo $videoID; ?>" allowfullscreen></iframe>
        </div>

     

        <!-- Comment Section -->
        <div class="mt-5">
            <h3>Comments</h3>

            <?php if (isset($_SESSION["userID"])): ?>
                <!-- Comment Form -->
                <form method="POST" class="mb-3">
                    <input type="hidden" name="parentID" value="null">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
                    <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-comment"></i> Submit</button>
                </form>
            <?php else: ?>
                <p class="text-muted">You must <a href="login.php">log in</a> to comment.</p>
            <?php endif; ?>

            <!-- Display Comments and Replies -->
            <?php foreach ($threadedComments as $comment): ?>
                <div class="card bg-dark text-light mb-3">
                    <div class="card-body">
                        <strong><?php echo htmlspecialchars($comment["username"]); ?></strong>
                        <p><?php echo htmlspecialchars($comment["commentText"]); ?></p>
                        <button class="btn btn-outline-light btn-sm reply-btn" data-comment="<?php echo $comment['commentID']; ?>"><i class="fas fa-reply"></i> Reply</button>

                        <!-- Reply Form (Hidden by Default) -->
                        <form method="POST" class="reply-form mt-2 d-none">
                            <input type="hidden" name="parentID" value="<?php echo $comment['commentID']; ?>">
                            <textarea name="comment" class="form-control" rows="2" placeholder="Write a reply..." required></textarea>
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Submit</button>
                        </form>

                        <!-- Display Replies -->
                        <?php if (!empty($comment["replies"])): ?>
                            <div class="ms-4 mt-3">
                                <?php foreach ($comment["replies"] as $reply): ?>
                                    <div class="border-start border-secondary ps-3 mb-2">
                                        <strong><?php echo htmlspecialchars($reply["username"]); ?></strong>
                                        <p><?php echo htmlspecialchars($reply["commentText"]); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                this.nextElementSibling.classList.toggle('d-none');
            });
        });
    </script>
</body>
</html>
