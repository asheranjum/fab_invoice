<?php
// Include database connection
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password

    // Insert user into the database
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
    if (mysqli_query($conn, $sql)) {
      "Registration successful! <a href='index.php'>Login</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="register-form">
                    <h2>Sign-Up</h2>
                    <form action="register.php" method="post">
                        <div class="form-group">
                            <label class="label" for="username">User Name</label>
                            <input type="text" class="form-control" id="username" name="username" required placeholder="Insert User Name">
                        </div>
                        <div class="form-group mt-2">
                            <label class="label" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Insert Password">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>