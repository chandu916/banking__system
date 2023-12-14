<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details and account type name from the database
$user_query = "SELECT users.id, username, address, phonenumber, account_type_id, account_types.name as account_type_name 
               FROM users 
               JOIN account_types ON users.account_type_id = account_types.id 
               WHERE users.id = $user_id";

$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Fetch accounts associated with the user
$accounts_query = "SELECT account_type_id, balance FROM accounts WHERE user_id = $user_id";
$accounts_result = $conn->query($accounts_query);
$accounts = $accounts_result->fetch_assoc(); // Fetch the accounts data

// Fetch transactions associated with the user
$transactions_query = "SELECT amount, description, timestamp FROM transactions WHERE user_id = $user_id";
$transactions_result = $conn->query($transactions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head section with metadata and styles -->
    <link rel="stylesheet" href="styles.css">
    <title>User Home</title>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $user['username']; ?>!</h1>

        <h2>Your User Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Account Type</th>
                    <th>Bank Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['address']; ?></td>
                    <td><?php echo $user['phonenumber']; ?></td>
                    <td><?php echo $user['account_type_name']; ?></td>
                    <td><?php echo $accounts['balance']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2>Your Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = $transactions_result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $transaction['amount']; ?></td>
                        <td><?php echo $transaction['description']; ?></td>
                        <td><?php echo $transaction['timestamp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class='container'>
        <p><a class='mnybtn' href="transfer.php">Send Money</a></p>
        <p><a class='mnybtn' href="logout.php">Logout</a></p>

    </div>
</body>
</html>
