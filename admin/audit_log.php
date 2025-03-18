<?php


// Fetch audit logs
$stmt = $pdo->query("SELECT a.logID, u.username AS admin_name, a.action, a.targetType, a.targetID, a.timestamp 
                     FROM audit_log a 
                     JOIN users u ON a.adminID = u.userID 
                     ORDER BY a.timestamp DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


    <h2>Audit Log</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Admin</th>
                <th>Action</th>
                <th>Target Type</th>
                <th>Target ID</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log["admin_name"]); ?></td>
                    <td><?php echo htmlspecialchars($log["action"]); ?></td>
                    <td><?php echo htmlspecialchars($log["targetType"]); ?></td>
                    <td><?php echo htmlspecialchars($log["targetID"]); ?></td>
                    <td><?php echo htmlspecialchars($log["timestamp"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

  
</body>
</html>
