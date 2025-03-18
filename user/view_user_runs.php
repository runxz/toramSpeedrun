<?php


$userID = $_SESSION["userID"];

$stmt = $pdo->prepare("SELECT s.runID, b.name AS boss_name, s.runTime, s.submissionDate, s.videoURL, s.verificationStatus 
                      FROM speedruns s
                      JOIN bosses b ON s.bossID = b.bossID
                      WHERE s.userID = :userID");
$stmt->execute(["userID" => $userID]);
$speedruns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


    <h2>My Speedruns</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Boss</th>
                <th>Run Time</th>
                <th>Submitted On</th>
                <th>Video</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($speedruns as $run): ?>
                <tr>
                    <td><?php echo htmlspecialchars($run["boss_name"]); ?></td>
                    <td><?php echo htmlspecialchars($run["runTime"]); ?></td>
                    <td><?php echo htmlspecialchars($run["submissionDate"]); ?></td>
                    <td><a href="<?php echo htmlspecialchars($run["videoURL"]); ?>" target="_blank">Watch</a></td>
                    <td><?php echo htmlspecialchars($run["verificationStatus"]); ?></td>
                    <td>
    <?php echo htmlspecialchars($run["verificationStatus"]); ?>
    <?php if ($run["verificationStatus"] == "Rejected" && !empty($run["verificationNotes"])): ?>
        <br><small>Reason: <?php echo htmlspecialchars($run["verificationNotes"]); ?></small>
    <?php endif; ?>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


