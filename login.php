<?php
require_once "config/db.php";
session_start();

//if already logged in redirects 
if (isset($_SESSION["user_id"])){
    if ($_SESSION["role"] ==="admin"){
        header("location: /makazi254/admin/dashboard.php");
    }else{
        header("location: /makazi254/seller/dashboard.php");
    }
    exit;

}
$error = "";
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email)|| empty($password)){
        $error = "Both fields are required.";
    }else{
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s",$email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["name"] = $user["name"];

            if($user["role"]==="admin"){
                header("location:/makazi254/admin/dashboard.php");
            }else{
                header("location: /makazi254/seller/dashboard.php");
            }
            exit;

        }else{
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Makazi_254</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
   <?php include $_SERVER['DOCUMENT_ROOT'] . "/makazi254/includes/navbar.php"; ?>
    <div class = "auth-container">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p class="auth-sub">Log in to manage your Listings.</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                   <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                  placeholder="Enter Email Address">
                   
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password">
                </div>

                <button type="SUBMIT" class="btn-primary">LOG IN </button>
            </form>

            <p class="auth-footer">No account yet?<br><br><a href="register.php">Register as a seller/Landloard</a></p>
         </div>

    </div>
</body>
</html>