<?php




// Fetch pending speedruns
$stmt = $pdo->query("SELECT s.runID, u.username, b.name AS boss_name, s.runTime, s.submissionDate, s.videoURL 
                     FROM speedruns s
                     JOIN users u ON s.userID = u.userID
                     JOIN bosses b ON s.bossID = b.bossID
                     WHERE s.verificationStatus = 'Pending'");
$speedruns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderation - Speedruns</title>
</head>
<body>
    <h2>Pending Speedruns for Review</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Runner</th>
                <th>Boss</th>
                <th>Run Time</th>
                <th>Submitted On</th>
                <th>Video</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($speedruns as $run): ?>
                <tr>
                    <td><?php echo htmlspecialchars($run["username"]); ?></td>
                    <td><?php echo htmlspecialchars($run["boss_name"]); ?></td>
                    <td><?php echo htmlspecialchars($run["runTime"]); ?></td>
                    <td><?php echo htmlspecialchars($run["submissionDate"]); ?></td>
                    <td><a href="<?php echo htmlspecialchars($run["videoURL"]); ?>" target="_blank">Watch</a></td>
                    <td>
                    <form method="POST" action="verify_run.php">
    <input type="hidden" name="runID" value="<?php echo $run['runID']; ?>">
    <input type="hidden" name="action" value="verify">
    <button type="submit">✔ Verify</button>
</form>

    <a href="#" onclick="showRejectForm(<?php echo $run['runID']; ?>)">❌ Reject</a>

    <form method="POST" action="verify_run.php" id="rejectForm_<?php echo $run['runID']; ?>" style="display:none;">
        <input type="hidden" name="runID" value="<?php echo $run['runID']; ?>">
        <input type="hidden" name="action" value="reject">
        <textarea name="rejectionNotes" placeholder="Enter rejection reason" required></textarea>
        <button type="submit">Reject</button>
    </form>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <script>
function showRejectForm(runID) {
    document.getElementById('rejectForm_' + runID).style.display = 'block';
}
</script>
</body>
</html>
