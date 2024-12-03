<?php
session_start();
$conn = new mysqli('sql306.infinityfree.com', 'if0_37704135', 'Hasini143', 'if0_37704135_restaurant_billing_system');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$customer_name = $_SESSION['customer_name'] ?? 'Customer';
$table_number = $_SESSION['table_number'] ?? 'N/A';
$customer_id = $_SESSION['customer_id'] ?? null;

if (!$customer_id) {
    header("Location: login.php");
    exit();
}

$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$category_sql = $category_filter ? "WHERE category = '$category_filter'" : '';
$menu_items = $conn->query("SELECT * FROM menu_items $category_sql");

if (!$menu_items) {
    echo "Error: " . $conn->error;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $conn->query("INSERT INTO cart (customer_id, item_id, quantity) VALUES ($customer_id, $item_id, $quantity) ON DUPLICATE KEY UPDATE quantity = $quantity");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_item_id'])) {
    $update_item_id = $_POST['update_item_id'];
    $update_quantity = $_POST['update_quantity'];
    if ($update_quantity > 0) {
        $conn->query("UPDATE cart SET quantity = $update_quantity WHERE customer_id = $customer_id AND item_id = $update_item_id");
    } else {
        $conn->query("DELETE FROM cart WHERE customer_id = $customer_id AND item_id = $update_item_id");
    }
}

$cart_items = $conn->query("SELECT * FROM cart WHERE customer_id = $customer_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
        }
        .menu-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .menu-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
        .table-hover tbody tr:hover {
            background-color: #e8f7ff;
        }
        button {
            transition: background-color 0.3s, transform 0.3s;
        }
        button:hover {
            transform: scale(1.05);
            cursor: pointer;
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>
<body class="container mt-5">
    <h1 class="text-center text-primary">Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h1>
    <h4 class="text-center text-secondary">Your Table Number: <?php echo htmlspecialchars($table_number); ?></h4>

    <!-- Filter Selection -->
    <div class="d-flex justify-content-end mb-3">
        <label for="categoryFilter" class="me-2">Filter by Category:</label>
        <select id="categoryFilter" class="form-select w-auto" onchange="filterMenu()">
            <option value="">All</option>
            <option value="Veg" <?php if ($category_filter == 'Veg') echo 'selected'; ?>>Veg</option>
            <option value="Non-Veg" <?php if ($category_filter == 'Non-Veg') echo 'selected'; ?>>Non-Veg</option>
            <option value="Main Course" <?php if ($category_filter == 'Main Course') echo 'selected'; ?>>Main Course</option>
            <option value="Starters" <?php if ($category_filter == 'Starters') echo 'selected'; ?>>Starters</option>
        </select>
    </div>

    <!-- Menu Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Item Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Add to Cart</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $menu_items->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?></td>
                        <td>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(this.nextElementSibling, false)">-</button>
                                <input type="number" name="quantity" value="1" min="1" required class="form-control text-center" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(this.previousElementSibling, true)">+</button>
                            </div>
                        </td>
                        <td><button type="submit" class="btn btn-primary">Add to Cart</button></td>
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Your Cart</h2>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $total_amount = 0; ?>
            <?php while ($cart_item = $cart_items->fetch_assoc()):
                $item = $conn->query("SELECT * FROM menu_items WHERE id = " . $cart_item['item_id'])->fetch_assoc();
                if ($item) {
                    $item_total = $item['price'] * $cart_item['quantity'];
                    $total_amount += $item_total;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($cart_item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item_total); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="update_item_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="update_quantity" value="0"> <!-- Remove item -->
                            <button type="submit" class="btn btn-danger cursor-pointer">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php } endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Total Amount</strong></td>
                <td><strong><?php echo htmlspecialchars($total_amount); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <form method="post" action="place_order.php" class="text-center">
        <button type="submit" class="btn btn-success">Place Order</button>
    </form>

    <form action="logout.php" method="post" class="text-center mt-3">
        <button type="submit" class="btn btn-secondary">Logout</button>
    </form>

    <script>
        function updateQuantity(input, isIncrement) {
            let currentQuantity = parseInt(input.value);
            if (isIncrement) {
                input.value = currentQuantity + 1;
            } else if (currentQuantity > 1) {
                input.value = currentQuantity - 1;
            }
        }

        function filterMenu() {
            const category = document.getElementById('categoryFilter').value;
            window.location.href = `?category=${category}`;
        }
    </script>
</body>
</html>
