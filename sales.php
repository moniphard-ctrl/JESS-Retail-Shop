<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['record_sale'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = (int)$_POST['user_id'];
    $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare(
        "SELECT selling_price, quantity_in_stock
         FROM products WHERE product_id = ?"
    );
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        $message = "Product not found.";
    } elseif ($quantity < 1 || $quantity > $product['quantity_in_stock']) {
        $message = "Invalid quantity or insufficient stock.";
    } else {
        $price = $product['selling_price'];
        $total = $price * $quantity;

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare(
                "INSERT INTO sales (customer_id, user_id, total_amount, payment_method)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iids", $customer_id, $user_id, $total, $payment_method);
            $stmt->execute();

            $sale_id = $conn->insert_id;

            $stmt = $conn->prepare(
                "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iiidd", $sale_id, $product_id, $quantity, $price, $total);
            $stmt->execute();

            $stmt = $conn->prepare(
                "UPDATE products SET quantity_in_stock = quantity_in_stock - ?
                 WHERE product_id = ?"
            );
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();

            $conn->commit();
            $message = "Sale recorded successfully! Receipt number: " . $sale_id;
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Sale failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS - Sales</title>
</head>
<body>

<h1>JESS Retail Shop</h1>
<h2>Sales and Receipts</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    <label>Select Product:</label><br>
    <select name="product_id" required>
        <option value="">Choose Product</option>
        <?php
        $products = $conn->query("SELECT product_id, product_name, selling_price, quantity_in_stock FROM products");
        while ($p = $products->fetch_assoc()) {
            echo "<option value='" . $p['product_id'] . "'>" .
            htmlspecialchars($p['product_name']) . " - UGX " .
            number_format($p['selling_price']) . " (Stock: " .
            $p['quantity_in_stock'] . ")</option>";
        }
        ?>
    </select><br><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" min="1" required><br><br>

    <label>Customer:</label><br>
    <select name="customer_id">
        <option value="">Walk-in Customer</option>
        <?php
        $customers = $conn->query("SELECT customer_id, customer_name FROM customers");
        while ($c = $customers->fetch_assoc()) {
            echo "<option value='" . $c['customer_id'] . "'>" .
            htmlspecialchars($c['customer_name']) . "</option>";
        }
        ?>
    </select><br><br>

    <label>Cashier:</label><br>
    <select name="user_id" required>
        <option value="">Choose Cashier</option>
        <?php
        $users = $conn->query("SELECT user_id, full_name FROM users");
        while ($u = $users->fetch_assoc()) {
            echo "<option value='" . $u['user_id'] . "'>" .
            htmlspecialchars($u['full_name']) . "</option>";
        }
        ?>
    </select><br><br>

    <label>Payment Method:</label><br>
    <select name="payment_method" required>
        <option value="Cash">Cash</option>
        <option value="Mobile Money">Mobile Money</option>
        <option value="Card">Card</option>
    </select><br><br>

    <button type="submit" name="record_sale">Record Sale</button>
</form>

<h2>Recent Sales</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Sale ID</th>
    <th>Date</th>
    <th>Total Amount</th>
    <th>Payment Method</th>
</tr>

<?php
$result = $conn->query("SELECT sale_id, sale_date, total_amount, payment_method FROM sales ORDER BY sale_id DESC");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['sale_id'] . "</td>";
    echo "<td>" . $row['sale_date'] . "</td>";
    echo "<td>UGX " . number_format($row['total_amount']) . "</td>";
    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
    echo "</tr>";
}
?>

</table>

</body>
</html>