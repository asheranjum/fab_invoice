<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/database.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize error variable
$error = '';

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            header("Location: index.php"); // go to dashboard
            exit();
        } else {
            $_SESSION['error'] = 'Invalid username or password.';
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'Invalid username or password.';
        header("Location: login.php");
        exit();
    }
}

// Get error from session if exists
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
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
                            <?= $error ?>
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