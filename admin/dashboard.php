<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

// Block anyone who is not a logged in admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: /makazi254/login.php");
    exit;
}

// Count pending listings
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM properties WHERE status = 'pending'");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pending = mysqli_fetch_assoc($result)['total'];

// Count approved listings
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM properties WHERE status = 'approved'");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$approved = mysqli_fetch_assoc($result)['total'];

// Count rejected listings
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM properties WHERE status = 'rejected'");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rejected = mysqli_fetch_assoc($result)['total'];

// Count total sellers
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'seller'");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sellers = mysqli_fetch_assoc($result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
    <h2>Admin Dashboard</h2>
    <p class="auth-sub">Manage listings and sellers on Makazi254.</p>

    <div class="dashboard-stats">
        <div class="stat-card">
            <span class="stat-label">Pending Listings</span>
            <span class="stat-number pending"><?php echo $pending; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Approved Listings</span>
            <span class="stat-number approved"><?php echo $approved; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Rejected Listings</span>
            <span class="stat-number" style="color:#A32D2D;"><?php echo $rejected; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Sellers</span>
            <span class="stat-number"><?php echo $sellers; ?></span>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="/makazi254/admin/listings.php" class="btn-primary">Manage Listings</a>
        <a href="/makazi254/admin/users.php" class="btn-secondary">Manage Sellers</a>
    </div>
</div>

</body>
</html>
