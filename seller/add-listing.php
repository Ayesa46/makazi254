<?php
require_once "../config/db.php";
/** @var mysqli $conn */
session_start();

// Block anyone who is not a logged in seller
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller'){
    header("Location: /makazi254/login.php");
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $price = trim($_POST['price']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    // Validation
    if(empty($title) || empty($type) || empty($price) || empty($location) || empty($description)){
        $error = 'All fields are required.';
    } elseif(!is_numeric($price) || $price <= 0){
        $error = 'Enter a valid price.';
    } elseif(!in_array($type, ['sale', 'rental', 'bnb', 'hostel'])){
        $error = 'Invalid property type.';
    } else {
        // Handle image upload
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            $maxsize = 2 * 1024 * 1024;

            if(!in_array($_FILES['image']['type'], $allowed)){
                $error = 'Only JPG, PNG and WEBP images are allowed.';
            } elseif($_FILES['image']['size'] > $maxsize){
                $error = 'Image must be smaller than 2MB.';
            } else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid('prop_', true) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], "../assets/uploads/" . $image);
            }
        }

        if(empty($error)){
            $stmt = mysqli_prepare($conn, "INSERT INTO properties (seller_id, title, type, price, location, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            mysqli_stmt_bind_param($stmt, "issdsss", $_SESSION['user_id'], $title, $type, $price, $location, $description, $image);
            mysqli_stmt_execute($stmt);
            $success = 'Listing submitted successfully. Waiting for admin approval.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Listing — Makazi254</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/listing.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
    <h2>Add New Listing</h2>
    <p class="auth-sub">Fill in the details below. Your listing will go live after admin approval.</p>

    <?php if($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="listing-form">
        <div class="form-group">
            <label for="title">Property Title</label>
            <input type="text" id="title" name="title"
                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                   placeholder="e.g. 3 Bedroom Apartment in Kilimani" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="type">Property Type</label>
                <select id="type" name="type" required>
                    <option value="">Select type</option>
                    <option value="sale" <?php echo (($_POST['type'] ?? '') === 'sale') ? 'selected' : ''; ?>>For Sale</option>
                    <option value="rental" <?php echo (($_POST['type'] ?? '') === 'rental') ? 'selected' : ''; ?>>Rental</option>
                    <option value="bnb" <?php echo (($_POST['type'] ?? '') === 'bnb') ? 'selected' : ''; ?>>BnB</option>
                    <option value="hostel" <?php echo (($_POST['type'] ?? '') === 'hostel') ? 'selected' : ''; ?>>Hostel</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price (KES)</label>
                <input type="number" id="price" name="price"
                       value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                       placeholder="e.g. 15000" required>
            </div>
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location"
                   value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                   placeholder="e.g. Westlands, Nairobi" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"
                      placeholder="Describe the property — size, features, nearby amenities..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Property Image</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
            <small>JPG, PNG or WEBP. Max 2MB.</small>
        </div>

        <button type="submit" class="btn-primary">Submit Listing</button>
    </form>
</div>

</body>
</html>