<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

//block anyone that is not a logged in admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !=='admin'){
    header("Location: /makazi254/login.php");
    exit;
}

//handle approved or rejected action
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $listing_id = (int)$_POST['listing_id'];
    $action = $_POST['action'];

    if(in_array($action,['approved', 'rejected'])){
     $stmt = mysqli_prepare($conn, "UPDATE properties SET status = ? WHERE id = ?");
     mysqli_stmt_bind_param($stmt, "si" , $action, $listing_id);
     mysqli_stmt_execute($stmt);   
    }

    header("Location: /makazi254/admin/listings.php");
    exit;

}

// fetch all listings with seller name
$stmt = mysqli_prepare($conn, "SELECT properties.*, users.name as seller_name 
                                FROM properties 
                                JOIN users ON properties.seller_id = users.id 
                                ORDER BY 
                                CASE status 
                                    WHEN 'pending' THEN 1 
                                    WHEN 'approved' THEN 2 
                                    WHEN 'rejected' THEN 3 
                                END, 
                                created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$listings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width= device-width , initial-scale=1.0">
    <title>Manage Listings - Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/listing.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>
    <div class="dashboard-container">
        <h2>Manage Listings</h2>
        <p class="auth-sub">Approve or reject property listings submitted by sellers.</p>

        <div class="dashboard-actions" style="margin-bottom: 2rem;">
            <a href="/makazi254/admin/dashboard.php" class="btn-secondary">Back to Dashboard</a>

        </div>

        <?php if(count($listings)=== 0): ?>
            <div class="alert alert-error">No listing found</div>

            <?php else: ?>
                <div class="listings-table-wrapper">
                    <table class="listings-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Seller</th>
                                <th>Type</th>
                                <th>price (KES)</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($listings as $listing): ?>
                                <tr>
                                    <td>
                                     <?php if($listing['image']): ?>
                                        <img src="/makazi254/assets/uploads/<?php echo htmlspecialchars($listing['image']); ?>" alt="Property" class="listing-thumb">
                                     <?php else: ?>
                                        <div class="no-image">No Image</div>

                                    <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($listing['title']); ?></td>
                                    <td><?php echo htmlspecialchars($listing['seller_name']); ?></td>
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
                                    <td>
                                        <?php if($listing['status'] === 'pending'): ?>
                                            <form method="POST" action="" class="action-form">
                                                <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                                <button type="submit" name="action" value="approved" class="btn-success">Approve</button>
                                                <button type="submit" name="action" value="rejected" class="btn-danger">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size:13px; color:#888780;">No action needed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            <?php endif; ?>

    </div>
    
</body>
</html>