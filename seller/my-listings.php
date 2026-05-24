<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

// Block anyone who is not a logged in seller
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller'){
    header("Location: /makazi254/login.php");
    exit;
}

// Fetch all listings for this seller
$stmt = mysqli_prepare($conn, "SELECT * FROM properties WHERE seller_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$listings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings — Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/listing.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
    <h2>My Listings</h2>
    <p class="auth-sub">All properties you have submitted to Makazi254.</p>

    <div class="dashboard-actions" style="margin-bottom: 2rem;">
        <a href="/makazi254/seller/add-listing.php" class="btn-primary">Add New Listing</a>
        <a href="/makazi254/seller/dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(count($listings) === 0): ?>
        <div class="alert alert-error">You have not submitted any listings yet.</div>
    <?php else: ?>
        <div class="listings-table-wrapper">
            <table class="listings-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Price (KES)</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listings as $listing): ?>
                    <tr>
                        <td>
                            <?php if($listing['image']): ?>
                                <img src="/makazi254/assets/uploads/<?php echo htmlspecialchars($listing['image']); ?>" 
                                     alt="Property" class="listing-thumb">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($listing['title']); ?></td>
                        <td class="type-badge type-<?php echo $listing['type']; ?>">
                            <?php echo ucfirst($listing['type']); ?>
                        </td>
                        <td><?php echo number_format($listing['price']); ?></td>
                        <td><?php echo htmlspecialchars($listing['location']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $listing['status']; ?>">
                                <?php echo ucfirst($listing['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($listing['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>