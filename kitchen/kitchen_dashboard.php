<?php 
session_start();
$conn = new mysqli('sql306.infinityfree.com', 'if0_37704135', 'Hasini143', 'if0_37704135_restaurant_billing_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['kitchen_user'])) {
    header("Location: kitchen_login.php");
    exit();
}

$pending_orders = $conn->query("SELECT * FROM orders WHERE status = 'Pending'");
$processing_orders = $conn->query("SELECT * FROM orders WHERE status = 'Processing'");
$delivered_orders = $conn->query("SELECT * FROM orders WHERE status = 'Delivered'");
$undelivered_orders = $conn->query("SELECT * FROM orders WHERE status = 'Undelivered'");
$rejected_orders = $conn->query("SELECT * FROM orders WHERE status = 'Rejected'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('/path/to/your/kitchen_image.png'); /* replace with the correct image path */
            background-size: cover;
            background-attachment: fixed;
            color: #fff;
        }
        .container { background: rgba(0, 0, 0, 0.6); padding: 20px; border-radius: 10px; margin-top: 20px; }
        table { width: 100%; margin-bottom: 20px; color: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #343a40; color: #fff; }
        .message { color: green; }
        button { transition: background-color 0.3s, transform 0.3s; }
        button:hover { background-color: #28a745; transform: scale(1.1); }
        .custom-cursor { cursor: url('https://cdn-icons-png.flaticon.com/512/2315/2315416.png'), auto; }
    </style>
</head>
<body class="custom-cursor">
    <div class="container">
        <h1 class="text-center">Kitchen Dashboard</h1>

        <?php if (isset($_GET['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <h2>Pending Orders</h2>
        <table class="table table-dark table-hover">
            <thead><tr><th>Order ID</th><th>Item Name</th><th>Quantity</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($order = $pending_orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['item_name']; ?></td>
                    <td><?php echo $order['table']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <form action="process_order.php" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm" name="accept">Accept</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Processing Orders</h2>
        <table class="table table-dark table-hover">
            <thead><tr><th>Order ID</th><th>Item Name</th><th>Quantity</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($order = $processing_orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['item_name']; ?></td>
                    <td><?php echo $order['table']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <form action="deliver_order.php" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm" name="delivered">Delivered</button>
                            <button type="submit" class="btn btn-warning btn-sm" name="undelivered">Undelivered</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Delivered Orders</h2>
        <table class="table table-dark table-hover">
            <thead><tr><th>Order ID</th><th>Item Name</th><th>Quantity</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($order = $delivered_orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['item_name']; ?></td>
                    <td><?php echo $order['table']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <form action="process_payment.php" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="btn btn-secondary btn-sm" name="process_payment">Process Payment</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Undelivered Orders</h2>
        <table class="table table-dark table-hover">
            <thead><tr><th>Order ID</th><th>Item Name</th><th>Quantity</th><th>Status</th></tr></thead>
            <tbody>
            <?php while ($order = $undelivered_orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['item_name']; ?></td>
                    <td><?php echo $order['table']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Rejected Orders</h2>
        <table class="table table-dark table-hover">
            <thead><tr><th>Order ID</th><th>Item Name</th><th>Quantity</th><th>Status</th></tr></thead>
            <tbody>
            <?php while ($order = $rejected_orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['item_name']; ?></td>
                    <td><?php echo $order['table']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <form action="logout.php" method="post" class="text-center mt-3">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('mousemove', function(e) {
            const cursor = document.createElement('div');
            cursor.className = 'cursor-trail';
            cursor.style.left = `${e.pageX}px`;
            cursor.style.top = `${e.pageY}px`;
            document.body.appendChild(cursor);
            setTimeout(() => cursor.remove(), 500);
        });
    </script>
</body>
</html>
