<?php
require_once "config/db.php";
session_start();

// Build query based on filters
$where = "WHERE status = 'approved'";
$params = [];
$types = "";

if(!empty($_GET['type']) && in_array($_GET['type'], ['sale', 'rental', 'bnb', 'hostel'])){
    $where .= " AND type = ?";
    $params[] = $_GET['type'];
    $types .= "s";
}

if(!empty($_GET['location'])){
    $where .= " AND location LIKE ?";
    $params[] = "%" . $_GET['location'] . "%";
    $types .= "s";
}

if(!empty($_GET['min_price']) && is_numeric($_GET['min_price'])){
    $where .= " AND price >= ?";
    $params[] = $_GET['min_price'];
    $types .= "d";
}

if(!empty($_GET['max_price']) && is_numeric($_GET['max_price'])){
    $where .= " AND price <= ?";
    $params[] = $_GET['max_price'];
    $types .= "d";
}

$sql = "SELECT properties.*, users.name as seller_name, users.email as seller_email 
        FROM properties 
        JOIN users ON properties.seller_id = users.id 
        $where 
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if(!empty($params)){
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$listings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makazi254 — Find Property in Kenya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/makazi254/includes/navbar.php"; ?>

<!-- Hero Section -->
<div class="hero">
    <div class="hero-content">
        <h1>Find Your Perfect Property in Kenya</h1>
        <p>Browse verified listings for sale, rental, BnB and hostels across Kenya.</p>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-container">
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <select name="type">
                <option value="">All Types</option>
                <option value="sale" <?php echo (($_GET['type'] ?? '') === 'sale') ? 'selected' : ''; ?>>For Sale</option>
                <option value="rental" <?php echo (($_GET['type'] ?? '') === 'rental') ? 'selected' : ''; ?>>Rental</option>
                <option value="bnb" <?php echo (($_GET['type'] ?? '') === 'bnb') ? 'selected' : ''; ?>>BnB</option>
                <option value="hostel" <?php echo (($_GET['type'] ?? '') === 'hostel') ? 'selected' : ''; ?>>Hostel</option>
            </select>
        </div>

        <div class="filter-group">
            <input type="text" name="location" 
                   value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>"
                   placeholder="Search by location...">
        </div>

        <div class="filter-group">
            <input type="number" name="min_price"
                   value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>"
                   placeholder="Min price (KES)">
        </div>

        <div class="filter-group">
            <input type="number" name="max_price"
                   value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>"
                   placeholder="Max price (KES)">
        </div>

        <button type="submit" class="btn-primary">Search</button>
        <a href="/makazi254/index.php" class="btn-secondary">Clear</a>
    </form>
</div>

<!-- Listings Section -->
<div class="listings-container">
    <?php if(count($listings) === 0): ?>
        <div class="no-listings">
            <p>No properties found. Try adjusting your filters.</p>
        </div>
    <?php else: ?>
        <p class="results-count"><?php echo count($listings); ?> propert<?php echo count($listings) === 1 ? 'y' : 'ies'; ?> found</p>
        <div class="listings-grid">
            <?php foreach($listings as $listing): ?>
            <div class="listing-card">
                <a href="/makazi254/listing.php?id=<?php echo $listing['id']; ?>">
                    <div class="listing-image">
                        <?php if($listing['image']): ?>
                            <img src="/makazi254/assets/uploads/<?php echo htmlspecialchars($listing['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($listing['title']); ?>">
                        <?php else: ?>
                            <div class="no-image-card">No Image</div>
                        <?php endif; ?>
                        <span class="type-tag type-<?php echo $listing['type']; ?>">
                            <?php echo ucfirst($listing['type']); ?>
                        </span>
                    </div>
                    <div class="listing-info">
                        <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                        <p class="listing-location"> <?php echo htmlspecialchars($listing['location']); ?></p>
                        <p class="listing-price">KES <?php echo number_format($listing['price']); ?></p>
                        <p class="listing-seller">Listed by <?php echo htmlspecialchars($listing['seller_name']); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
     <?php include $_SERVER['DOCUMENT_ROOT'] . "/makazi254/includes/footer.php"; ?>
</body>
</body>
</html>