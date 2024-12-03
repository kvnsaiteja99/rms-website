<?php 
$conn = new mysqli('sql306.infinityfree.com', 'if0_37704135', 'Hasini143', 'if0_37704135_restaurant_billing_system');

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all orders that are either paid or ready for payment
$orders = $conn->query("SELECT * FROM orders WHERE payment_status IN ('Paid', 'Ready for Payment')");

// Update payment status and mode if 'Mark as Paid' button is clicked
if (isset($_POST['mark_paid'])) {
    $order_id = $_POST['order_id'];
    $payment_mode = $_POST['payment_mode'];

    $conn->query("UPDATE orders SET payment_status = 'Paid', payment_mode = '$payment_mode' WHERE id = $order_id");
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page
    exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_start();
    session_destroy();
    header("Location: http://localhost/rms/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Dashboard - Orders</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .paid { color: green; font-weight: bold; }
        .unpaid { color: red; font-weight: bold; }
        h2 { text-align: center; }
        .logout-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            #print-section, #print-section * {
                visibility: visible;
            }
            #print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 10px;
            }
        }

        #print-section {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #333;
            border-radius: 10px;
            text-align: center;
            font-family: 'Courier New', Courier, monospace;
        }
        
        #print-section h2 {
            font-family: 'Arial Black', Arial, sans-serif;
            margin-bottom: 10px;
        }
        
        #print-section img {
            width: 100px;
            margin-bottom: 10px;
        }

        #print-section p, #print-section ul {
            margin: 5px 0;
        }

        .amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #f44336; /* Use a distinct color for amounts */
        }
        
        .border-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>Orders</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Total Amount (₹)</th>
        <th>Mode of Payment</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($order = $orders->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['id']); ?></td>
            <td><?php echo htmlspecialchars($order['item_name']); ?></td>
            <td><?php echo htmlspecialchars($order['table']); ?></td>
            <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
            <td>
                <?php if ($order['payment_status'] == 'Paid'): ?>
                    <?php echo htmlspecialchars($order['payment_mode']); ?>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                        <select name="payment_mode">
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Card</option>
                        </select>
                        <button type="submit" name="mark_paid">Mark as Paid</button>
                    </form>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($order['payment_status'] == 'Paid'): ?>
                    <span class="paid">Paid</span>
                <?php else: ?>
                    <span class="unpaid">Ready for Payment</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($order['payment_status'] == 'Paid'): ?>
                    <button onclick="printOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)">Print Bill</button>
                <?php else: ?>
                    <span class="unpaid">Make payment first</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<form method="POST">
    <button type="submit" name="logout" class="logout-button">Logout</button>
</form>

<!-- Hidden print section -->
<div id="print-section" style="display: none;">
    <h2>Chef Restaurant</h2>
    <img src="image.png" alt="Logo">
    <p><strong>Multi Restaurant</strong></p>
    <p>Vignan University, Vadlamudi, Guntur</p>
    <p>Phone: 9493504801</p>
    <hr>
    <div class="border-box">
        <p>Order ID: <span id="print-order-id"></span></p>
        <p>Date & Time: <span id="print-date-time"></span></p>
        <ul id="print-item-list"></ul>
        <p>Total Items: <span id="print-total-items"></span></p>
        <p>Total Quantity: <span id="print-total-quantity"></span></p>
        <p>Subtotal: <span class="amount">₹<span id="print-subtotal"></span></span></p>
        <p>Total Amount: <span class="amount">₹<span id="print-total-amount"></span></span></p>
        <p>Received Amount: <span class="amount">₹<span id="print-received-amount"></span></span></p>
        <p>Payment Mode: <span id="print-payment-mode"></span></p>
    </div>
    <p>Thank you, visit again!</p>
</div>

<script>
function printOrder(order) {
    document.getElementById('print-order-id').textContent = order.id;
    document.getElementById('print-date-time').textContent = new Date().toLocaleString();

    const itemList = document.getElementById('print-item-list');
    itemList.innerHTML = ''; // Clear previous items
    const items = order.item_name.split(',');
    items.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item.trim();
        itemList.appendChild(li);
    });

    document.getElementById('print-total-items').textContent = items.length;
    document.getElementById('print-total-quantity').textContent = order.table; // Assuming 'table' holds the quantity
    document.getElementById('print-subtotal').textContent = order.total_amount;
    
    // Set total amount as the subtotal
    document.getElementById('print-total-amount').textContent = order.total_amount;
    document.getElementById('print-received-amount').textContent = order.total_amount;
    document.getElementById('print-payment-mode').textContent = order.payment_mode;

    const printSection = document.getElementById('print-section');
    printSection.style.display = 'block'; // Show the print section
    window.print(); // Print the bill
    printSection.style.display = 'none'; // Hide the print section again
}
</script>

</body>
</html> 
