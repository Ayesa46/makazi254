<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

// Block anyone who is not a logged in seller
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller'){
    header("Location: /makazi254/login.php");
    exit;
}

// Fetch all inquiries for this seller's listings
$stmt = mysqli_prepare($conn, "SELECT inquiries.*, properties.title as property_title 
                                FROM inquiries 
                                JOIN properties ON inquiries.property_id = properties.id 
                                WHERE properties.seller_id = ? 
                                ORDER BY inquiries.created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$inquiries = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inquiries — Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/listing.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
    <h2>My Inquiries</h2>
    <p class="auth-sub">Messages from interested buyers and tenants.</p>

    <div class="dashboard-actions" style="margin-bottom: 2rem;">
        <a href="/makazi254/seller/dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(count($inquiries) === 0): ?>
        <div class="alert alert-error">No inquiries yet.</div>
    <?php else: ?>
        <div class="listings-table-wrapper">
            <table class="listings-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inquiries as $inquiry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inquiry['property_title']); ?></td>
                        <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>">
                                <?php echo htmlspecialchars($inquiry['email']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="tel:<?php echo htmlspecialchars($inquiry['phone']); ?>">
                                <?php echo htmlspecialchars($inquiry['phone']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($inquiry['message']); ?></td>
                        <td><?php echo date('d M Y', strtotime($inquiry['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>