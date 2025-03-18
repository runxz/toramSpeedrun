<?php





// Fetch all users
$stmt = $pdo->query("SELECT userID, username, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



    <div class="container my-5">
        <div class="card bg-dark text-light shadow-lg p-4">
            <h2 class="text-center mb-4">Manage Users</h2>

            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user["username"]); ?></td>
                                <td><?php echo htmlspecialchars($user["email"]); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php echo $user['role'] === 'Admin' ? 'bg-danger' : ($user['role'] === 'Moderator' ? 'bg-warning' : 'bg-primary'); ?>">
                                        <?php echo htmlspecialchars($user["role"]); ?>
                                    </span>
                                </td>
                                <td class="text-center">
    <?php if ($user["role"] === "User"): ?>
        <a href="update_user.php?userID=<?php echo $user['userID']; ?>&action=promote" 
           class="btn btn-success btn-sm"><i class="fas fa-user-shield"></i> Promote</a>
    <?php elseif ($user["role"] === "Moderator"): ?>
        <a href="update_user.php?userID=<?php echo $user['userID']; ?>&action=unpromote" 
           class="btn btn-warning btn-sm"><i class="fas fa-user-alt-slash"></i> Unpromote</a>
    <?php endif; ?>
    <a href="update_user.php?userID=<?php echo $user['userID']; ?>&action=delete" 
       class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
</td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

       
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
