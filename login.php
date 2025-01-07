<?php
// Start the session only once, check if it's already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize error variable
$error = '';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to the admin page if already logged in
    exit();
}

// Include database configuration file
require 'config/database.php'; 

// Handle login request when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    // If the user exists, check password
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Redirect to the dashboard or admin page
            header("Location: login.php");
            exit();
        } else {
            // Incorrect password
            $error = 'Invalid username or password.';
        }
    } else {
        // User does not exist
        $error = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="signin-form">
                    <h2>Sign-In</h2>

                    <?php if ($error != ''): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label class="label" for="username">User Name</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter User Name" required>
                        </div>
                        <div class="form-group mt-2">
                            <label class="label" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Login</button>
                    </form>
                    <div class="mt-3">
                        <a href="register.php" class="text-warning">Create an Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
