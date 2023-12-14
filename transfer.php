<?php
session_start();
require_once('db.php');

// Fetch all usernames for the dropdown, excluding the current user
$usernamesQuery = "SELECT id, username FROM users WHERE id != {$_SESSION['user_id']}";
$usernamesResult = $conn->query($usernamesQuery);
$usernames = $usernamesResult->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source_account_id = $_SESSION['user_id'];
    $destination_username = $_POST['transfer_to'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    // Check if the destination username exists
    $checkUserSql = "SELECT id FROM users WHERE username = '$destination_username'";
    $userResult = $conn->query($checkUserSql);

    if ($userResult->num_rows > 0) {
        $destination_account_id = $userResult->fetch_assoc()['id'];

        // Check if the source account has sufficient balance
        $checkBalanceSql = "SELECT balance FROM accounts WHERE user_id = '$source_account_id'";
        $balanceResult = $conn->query($checkBalanceSql);

        if ($balanceResult->num_rows > 0) {
            $balance = $balanceResult->fetch_assoc()['balance'];

            if ($balance >= $amount) {
                // Perform the transfer
                $updateSourceSql = "UPDATE accounts SET balance = balance - $amount WHERE user_id = '$source_account_id'";
                $updateDestinationSql = "UPDATE accounts SET balance = balance + $amount WHERE user_id = '$destination_account_id'";
                $insertTransactionSql = "INSERT INTO transactions (user_id, amount, description, source_account_id, destination_account_id) 
                                         VALUES ('$source_account_id', '$amount', '$description', '$source_account_id', '$destination_account_id')";

                $conn->query($updateSourceSql);
                $conn->query($updateDestinationSql);
                $conn->query($insertTransactionSql);

                header("Location: home.php");
                exit();
            } else {
                echo "Insufficient balance in the source account.";
            }
        } else {
            echo "Source account not found.";
        }
    } else {
        echo "Destination user not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head section with metadata and styles -->
    <link rel="stylesheet" href="styles.css">
    <title>Money Transfer</title>
</head>
<body>
    <div class="container">
        <h1>Money Transfer</h1>

        <form method="post" action="">
            <label for="transfer_to">Transfer To (Username):</label>
            <select id="transfer_to" name="transfer_to" required>
                <?php foreach ($usernames as $username) : ?>
                    <option value="<?php echo $username['username']; ?>"><?php echo $username['username']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <button type="submit">Transfer</button>
        </form>
    </div>
</body>
</html>
