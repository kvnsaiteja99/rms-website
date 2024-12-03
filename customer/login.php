<?php
session_start();
$conn = new mysqli('sql306.infinityfree.com', 'if0_37704135', 'Hasini143', 'if0_37704135_restaurant_billing_system');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $table_number = $conn->real_escape_string($_POST['table_number']);

    // Insert customer into the database
    $conn->query("INSERT INTO customers (name, table_number) VALUES ('$name', '$table_number')");
    $_SESSION['customer_id'] = $conn->insert_id; // Save customer ID in session

    // Store name and table number in session
    $_SESSION['customer_name'] = $name;
    $_SESSION['table_number'] = $table_number;

    header("Location: menu.php");
    exit();
}

// Display the login form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="table_number">Table Number:</label>
        <input type="number" name="table_number" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>