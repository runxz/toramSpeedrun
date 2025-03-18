<?php


// Fetch all bosses
$stmt = $pdo->query("SELECT bossID, name, description, difficulty, image FROM bosses");
$bosses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


    <div class="container my-5">
        <div class="card bg-dark text-light shadow-lg p-4">
            <h2 class="text-center mb-4">Manage Bosses</h2>

            <!-- Boss Form -->
            <form method="POST" action="update_boss.php" enctype="multipart/form-data" class="mb-4">
                <div class="mb-3">
                    <label class="form-label">Boss Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter boss name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter description"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Difficulty</label>
                    <select name="difficulty" class="form-control" required>
                        <option value="Easy">Easy</option>
                        <option value="Normal">Normal</option>
                        <option value="Hard">Hard</option>
                        <option value="Ultimate">Ultimate</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Boss Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" name="action" value="add" class="btn btn-primary w-100">Add Boss</button>
            </form>

            <!-- Boss Table -->
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Difficulty</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bosses as $boss): ?>
                            <tr>
                                <td>
                                    <img src="../uploads/<?php echo htmlspecialchars($boss['image']); ?>" 
                                         class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($boss["name"]); ?></td>
                                <td><?php echo htmlspecialchars($boss["description"]); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php echo $boss['difficulty'] === 'Ultimate' ? 'bg-danger' : 
                                            ($boss['difficulty'] === 'Hard' ? 'bg-warning' : 'bg-success'); ?>">
                                        <?php echo htmlspecialchars($boss["difficulty"]); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="edit_boss.php?bossID=<?php echo $boss['bossID']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="update_boss.php?bossID=<?php echo $boss['bossID']; ?>&action=delete" 
                                       class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>

