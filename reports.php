<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sales = $conn->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM sales")->fetch_assoc();
$product_count = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc();
$customer_count = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc();
$supplier_count = $conn->query("SELECT COUNT(*) AS total FROM suppliers")->fetch_assoc();

$low_stock = $conn->query(
    "SELECT product_name, quantity_in_stock, reorder_level
     FROM products
     WHERE quantity_in_stock <= reorder_level"
);

$recent_sales = $conn->query(
    "SELECT sale_id, sale_date, total_amount, payment_method
     FROM sales ORDER BY sale_id DESC LIMIT 10"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS - Reports</title>
</head>
<body>

<h1>JESS RETAIL SHOP</h1>
<h2>Management Reports Dashboard</h2>

<h3>Total Sales: UGX <?php echo number_format($sales['total']); ?></h3>
<h3>Total Products: <?php echo $product_count['total']; ?></h3>
<h3>Total Customers: <?php echo $customer_count['total']; ?></h3>
<h3>Total Suppliers: <?php echo $supplier_count['total']; ?></h3>

<hr>

<h2>Low Stock Alerts</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Product</th>
    <th>Available Stock</th>
    <th>Reorder Level</th>
</tr>

<?php while ($row = $low_stock->fetch_assoc()) { ?>
<tr>
    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
    <td><?php echo $row['quantity_in_stock']; ?></td>
    <td><?php echo $row['reorder_level']; ?></td>
</tr>
<?php } ?>
</table>

<hr>

<h2>Recent Sales Report</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Sale ID</th>
    <th>Date</th>
    <th>Amount</th>
    <th>Payment Method</th>
</tr>

<?php while ($row = $recent_sales->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['sale_id']; ?></td>
    <td><?php echo $row['sale_date']; ?></td>
    <td>UGX <?php echo number_format($row['total_amount']); ?></td>
    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
</tr>
<?php } ?>
</table>

</body>
</html>