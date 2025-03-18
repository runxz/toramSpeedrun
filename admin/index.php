<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== "Admin") {
    header("Location: ../login");
    exit;
}

require '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="card bg-dark text-light shadow-lg p-4">
            <h2 class="text-center mb-4">Admin Dashboard</h2>

            <!-- Accordion -->
            <div class="accordion" id="adminDashboardAccordion">

                <!-- Manage Users -->
                <div class="accordion-item bg-secondary text-light">
                    <h2 class="accordion-header" id="headingUsers">
                        <button class="accordion-button bg-dark text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUsers">
                            <i class="fas fa-users me-2"></i> Manage Users
                        </button>
                    </h2>
                    <div id="collapseUsers" class="accordion-collapse collapse" data-bs-parent="#adminDashboardAccordion">
                        <div class="accordion-body">
                            <?php include 'manage_users.php'; ?>
                        </div>
                    </div>
                </div>

                <!-- Manage Bosses -->
                <div class="accordion-item bg-secondary text-light">
                    <h2 class="accordion-header" id="headingBosses">
                        <button class="accordion-button bg-dark text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBosses">
                            <i class="fas fa-dragon me-2"></i> Manage Bosses
                        </button>
                    </h2>
                    <div id="collapseBosses" class="accordion-collapse collapse" data-bs-parent="#adminDashboardAccordion">
                        <div class="accordion-body">
                            <?php include 'manage_bosses.php'; ?>
                        </div>
                    </div>
                </div>

                <!-- Audit Logs -->
                <div class="accordion-item bg-secondary text-light">
                    <h2 class="accordion-header" id="headingAuditLog">
                        <button class="accordion-button bg-dark text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAuditLog">
                            <i class="fas fa-history me-2"></i> Audit Log
                        </button>
                    </h2>
                    <div id="collapseAuditLog" class="accordion-collapse collapse" data-bs-parent="#adminDashboardAccordion">
                        <div class="accordion-body">
                            <?php include 'audit_log.php'; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 Toram Online Speedrun Hub. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
