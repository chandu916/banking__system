<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'db.php'; // Make sure to include your database connection file

// If the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Authenticate the user
    $query = "SELECT id, username, password FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Store user information in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Redirect to home page
            header("Location: home.php");
            exit();
        } else {
            echo '<script>alert("Invalid password");</script>';
        }
    } else {
        echo '<script>alert("Invalid username");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head section with metadata and styles -->
    <link rel="stylesheet" href="styles.css">
    <title>User Login</title>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>
