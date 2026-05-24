<?php
if (session_status()=== PHP_SESSION_NONE){     //checks whether the session is already started, if not it starts the session. This prevents "headers already sent" errors when including this file in other scripts that also start a session.
    session_start();
}
?>

<nav class = "navbar">
    <div class="nav-brand">
        <a href="/makazi254/index.php">Makazi<span class="accent">254</span></a>
    </div>
    <ul class="nav-links">
        <li><a href="/makazi254/index.php">Browse</a></li>
        <?php if (isset($_SESSION['user_id'])): ?> 

            <!-- check whether the logged in user is a seller -->
            <?php if ($_SESSION['role'] === 'seller'): ?>
                <li><a href="/makazi254/seller/my-listings.php">My Listings</a></li>
                <li><a href="/makazi254/seller/add-listing.php" class="nav-cta">+ Add Listing</a></li>
                <li><a href="/makazi254/seller/inquiries.php">Inquiries</a></li>


                <!-- check whether the logged in user is an admin -->
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <li><a href="/makazi254/admin/listings.php">Pending Listings</a></li>
                <li><a href="/makazi254/admin/users.php">Manage Sellers</a></li>

            <?php endif; ?>

            <li><a href="/makazi254/logout.php" class="nav-logout">Log out</a></li>

        <?php else: ?>
            <!-- if no session exists, show login and register links -->
            <li><a href="/makazi254/login.php">Log in</a></li>
            <li><a href="/makazi254/register.php" class="nav-cta">Register</a></li>

        <?php endif; ?>
        
    </ul>

</nav>