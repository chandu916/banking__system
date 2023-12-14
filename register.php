<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// If the registration form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $account_type = $_POST['account_type'];
    $address = $_POST['address'];
    $phonenumber = $_POST['phonenumber'];

    // Check if username already exists
    $check_username_query = "SELECT id FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($check_username_query);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo '<script>alert("Username already exists. Please choose a different username.");</script>';
    } else {
        // Insert user data into the users table
        $insert_user_query = "INSERT INTO users (username, password, account_type_id, address, phonenumber) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_user = $conn->prepare($insert_user_query);
        $stmt_insert_user->bind_param("ssiss", $username, $hashed_password, $account_type, $address, $phonenumber);

        if ($stmt_insert_user->execute()) {
            echo '<script>alert("User successfully created!");</script>';
            
            // Insert a corresponding row into the accounts table
            $user_id = $stmt_insert_user->insert_id;
            $initial_balance = 0.00;
            $insert_account_query = "INSERT INTO accounts (user_id, account_type_id, balance) VALUES (?, ?, ?)";
            $stmt_insert_account = $conn->prepare($insert_account_query);
            $stmt_insert_account->bind_param("iid", $user_id, $account_type, $initial_balance);
            $stmt_insert_account->execute();
            $stmt_insert_account->close();
        } else {
            echo '<script>alert("Error creating user.");</script>';
        }

        $stmt_insert_user->close();
    }

    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head section with metadata and styles -->
    <link rel="stylesheet" href="styles.css">
    <title>User Registration</title>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="phonenumber">Phone Number:</label>
            <input type="text" id="phonenumber" name="phonenumber" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="account_type">Account Type:</label>
            <select name="account_type" id="account_type">
                <option value="1">Fixed Deposit</option>
                <option value="2">Savings</option>
                <option value="3">Current</option>
            </select>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
