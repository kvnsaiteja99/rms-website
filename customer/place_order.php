<?php
session_start();
$conn = new mysqli('sql306.infinityfree.com', 'if0_37704135', 'Hasini143', 'if0_37704135_restaurant_billing_system');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$table_number = $_SESSION['table_number']; // Get table number from session
$total_amount = 0;
$item_names = [];

// Fetch cart items for the customer
$cart_items = $conn->query("SELECT * FROM cart WHERE customer_id = $customer_id");
while ($cart_item = $cart_items->fetch_assoc()) {
    $item = $conn->query("SELECT * FROM menu_items WHERE id = " . $cart_item['item_id'])->fetch_assoc();
    $total_amount += $item['price'] * $cart_item['quantity'];
    $item_names[] = $item['item_name'] . ' (Qty: ' . $cart_item['quantity'] . ')';
}

// Concatenate item names into a single string
$item_names_str = implode(', ', $item_names);

// Insert order into the orders table
$order_query = "INSERT INTO orders (customer_id, total_amount, status, payment_status, item_name, table_number) VALUES (?, ?, 'Pending', 'Pending', ?, ?)";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("idss", $customer_id, $total_amount, $item_names_str, $table_number);
$stmt->execute();
$stmt->close();

// Clear the cart
$conn->query("DELETE FROM cart WHERE customer_id = $customer_id");

$conn->close();

// Redirect or display a success message
echo "<script>alert('Order placed successfully!'); window.location.href = 'menu.php';</script>";
?>
