<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

// Block anyone who is not a logged in seller
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "seller"){
    header("Location: /makazi254/login.php");
    exit;
}

// Total listings
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM properties WHERE seller_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total = mysqli_fetch_assoc($result)['total'];

// Approved listings
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as approved FROM properties WHERE seller_id = ? AND status = 'approved'");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$approved = mysqli_fetch_assoc($result)['approved'];

// Pending listings
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as pending FROM properties WHERE seller_id = ? AND status = 'pending'");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pending = mysqli_fetch_assoc($result)['pending'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css"
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION["name"]); ?></h2>
    <p class="auth-sub">Manage your property listings from here</p>

    <div class="dashboard-stats">
        <div class="stat-card">
            <span class="stat-label">Total Listings</span>
            <span class="stat-number"><?php echo $total; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Approved</span>
            <span class="stat-number approved"><?php echo $approved; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Pending</span>
            <span class="stat-number pending"><?php echo $pending; ?></span>
        </div>
    </div>

    <div class="dashboard-actions">
    <a href="/makazi254/seller/add-listing.php" class="btn-primary">+ Add New Listing</a>
    <a href="/makazi254/seller/my-listings.php" class="btn-secondary">View My Listings</a>
    <a href="/makazi254/seller/inquiries.php" class="btn-secondary">View Inquiries</a>
</div>
</div>

</body>
</html>