<?php
require_once "config/db.php";
/** @var mysqli $conn */
session_start();

// Get listing ID from URL
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: /makazi254/index.php");
    exit;
}

$id = (int)$_GET['id'];

// Fetch the listing
$query = "SELECT properties.*, users.name as seller_name, users.email as seller_email, users.phone as seller_phone
          FROM properties 
          JOIN users ON properties.seller_id = users.id 
          WHERE properties.id = $id AND properties.status = 'approved'";

$result = mysqli_query($conn, $query);
$listing = mysqli_fetch_assoc($result);

// If listing not found redirect home
if(!$listing){
    header("Location: /makazi254/index.php");
    exit;
}

$error = '';
$success = '';

// Handle inquiry form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);

    // Validation
    if(empty($name) || empty($email) || empty($phone) || empty($message)){
        $error = 'All fields are required.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Enter a valid email address.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO inquiries (property_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issss", $id, $name, $email, $phone, $message);
        mysqli_stmt_execute($stmt);
        $success = 'Your inquiry has been sent. The seller will contact you shortly.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($listing['title']); ?> — Makazi254</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/listing-single.css">
</head>

<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/makazi254/includes/navbar.php"; ?>

<div class="listing-single-container">

    <!-- Image and Details -->
    <div class="listing-single-left">

        <!-- image -->
        <div class="listing-single-image">
            <?php if($listing['image']): ?>
                <img src="/makazi254/assets/uploads/<?php echo htmlspecialchars($listing['image']); ?>"
                     alt="<?php echo htmlspecialchars($listing['title']); ?>">
            <?php else: ?>
                <div class="no-image-large">No Image Available</div>
            <?php endif; ?>
            <span class="type-tag type-<?php echo $listing['type']; ?>">
                <?php echo ucfirst($listing['type']); ?>
            </span>
        </div>

        <!-- Details below image -->
        <div class="listing-single-details">
            <h1><?php echo htmlspecialchars($listing['title']); ?></h1>
            <p class="listing-single-location"><?php echo htmlspecialchars($listing['location']); ?></p>
            <p class="listing-single-price">KES <?php echo number_format($listing['price']); ?></p>

            <div class="listing-single-description">
                <h3>About this property</h3>
                <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
            </div>

            <div class="listing-single-seller">
             <h3>Listed by</h3>
             <p class="seller-name"><?php echo htmlspecialchars($listing['seller_name']); ?>
    
    <div class="seller-contacts">
    <?php if($listing['seller_phone']): ?>
        <a href="tel:<?php echo htmlspecialchars($listing['seller_phone']); ?>" class="contact-btn contact-call">
            <i class="fas fa-phone"></i> Call
        </a>
        <a href="https://wa.me/254<?php echo ltrim(htmlspecialchars($listing['seller_phone']), '0'); ?>" 
           target="_blank" class="contact-btn contact-whatsapp">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
    <?php endif; ?>
    <a href="mailto:<?php echo htmlspecialchars($listing['seller_email']); ?>" class="contact-btn contact-email">
        <i class="fas fa-envelope"></i> Email
    </a>
</div>
</div>
        </div>
    </div>

    <!-- Right Column: Inquiry Form -->
    <div class="inquiry-card">
        <h3>Interested in this property?</h3>
        <p class="auth-sub">Fill in your details and the seller will contact you.</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if(empty($success)): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                       placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="john@email.com">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                       placeholder="07XXXXXXXX">
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5"
                          placeholder="I am interested in this property. Please contact me..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn-primary">Submit Inquiry</button>
        </form>
        <?php endif; ?>
    </div>

</div>

</body>
</html>