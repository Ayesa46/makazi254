<?php 
require_once 'config/db.php';      //to connect to the database if this is not included PHP stops immediately
session_start();

$error= '';
$success= '';

if($_SERVER['REQUEST_METHOD']==='POST'){  //the registration only runs when the form is actually submitted not on every page  (when the page is blank,GET.)
    $name = trim($_POST['name']);   //trim removes any accidental space before and after the value
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone_number = trim($_POST['phone_number']);
    $confirm = $_POST['confirm_password'];

    //validation chain
    if(empty($name)|| empty($email)|| empty ($password)|| empty($confirm) || empty($phone_number)){
        $error = 'All fields are required.';
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Enter a valid email address.';
    }elseif($password!==$confirm){
        $error = 'password do not match.';
    }else{
        //check whether the email already exixts
        $stmt = mysqli_prepare($conn,"SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s" ,$email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt)>0){
            $error = 'An account with this email already exists.';
        }else{
       //hash password and insert
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'seller')");
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed, $phone_number);
            mysqli_stmt_execute($stmt);

            $success = "Account created successfully. Proceed to log in";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Makazi_254</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/makazi254/includes/navbar.php"; ?>
    <div class="auth-container">
    <div class="auth-card">
        <h2>Create Seller Account</h2>
        <p class="auth-sub">List with us and get matched to genuine buyers and tenants.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                       placeholder="Enter Full Name">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="Enter Email Address">
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" 
                       value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>"
                       placeholder="Enter Phone Number">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       placeholder="Min. 6 characters">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn-primary">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account?<br><br><a href="login.php">LOG IN</a></p>
    </div>
</div>

</body>
</html
    
