<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

// Block anyone who is not a logged in admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: /makazi254/login.php");
    exit;
}

// Handle delete action
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $seller_id = (int)$_POST['seller_id'];

    // to make sure admins don't accidentally delete themselves or other admins, we check the role before deleting
    if($seller_id !== (int)$_SESSION['user_id']){
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'seller'");
        mysqli_stmt_bind_param($stmt, "i", $seller_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: /makazi254/admin/users.php");
    exit;
}

// Fetch all sellers, counting all their listings
$stmt = mysqli_prepare($conn,"SELECT users.*, COUNT(properties.id) as total_listings 
                                FROM users 
                                LEFT JOIN properties ON users.id = properties.seller_id 
                                WHERE users.role = 'seller' 
                                GROUP BY users.id 
                                ORDER BY users.created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sellers = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sellers — Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/listing.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
    <h2>Manage Sellers</h2>
    <p class="auth-sub">View and manage all registered sellers on Makazi254.</p>

    <div class="dashboard-actions" style="margin-bottom: 2rem;">
        <a href="/makazi254/admin/dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(count($sellers) === 0): ?>
        <div class="alert alert-error">No sellers registered yet.</div>
    <?php else: ?>
        <div class="listings-table-wrapper">
            <table class="listings-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Total Listings</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sellers as $seller): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($seller['name']); ?></td>
                        <td><?php echo htmlspecialchars($seller['email']); ?></td>
                        <td><?php echo $seller['total_listings']; ?></td>
                        <td><?php echo date('d M Y', strtotime($seller['created_at'])); ?></td>
                        <td>
                            <form method="POST" action="" 
                                  onsubmit="return confirm('Are you sure you want to delete this seller? All their listings will also be deleted.')">
                                <input type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>">
                                <button type="submit" class="btn-reject">Delete</button>
                            </form>
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