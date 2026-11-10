<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['add_customer'])) {
    $name = trim($_POST['customer_name']);
    $phone = trim($_POST['phone_number']);

    $stmt = $conn->prepare(
        "INSERT INTO customers (customer_name, phone_number)
         VALUES (?, ?)"
    );

    $stmt->bind_param("ss", $name, $phone);

    if ($stmt->execute()) {
        $message = "Customer registered successfully!";
    } else {
        $message = "Unable to register customer.";
    }

    $stmt->close();
}

$result = $conn->query("SELECT * FROM customers ORDER BY customer_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>JESS - Customers</title>

<style>
body {
    font-family: Arial;
    background: #f2f5f8;
    padding: 30px;
}

.container {
    background: white;
    padding: 25px;
    border-radius: 10px;
    max-width: 900px;
    margin: auto;
}

h1 { color: #172b3a; }

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px;
    box-sizing: border-box;
}

button {
    background: #16845b;
    color: white;
    padding: 12px 25px;
    border: none;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

th {
    background: #172b3a;
    color: white;
}
</style>
</head>

<body>

<div class="container">

<h1>JESS RETAIL SHOP</h1>
<h2>Customer Management</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

<label>Customer Name</label>
<input type="text" name="customer_name" required>

<label>Phone Number</label>
<input type="text" name="phone_number">

<button type="submit" name="add_customer">
Register Customer
</button>

</form>

<h2>Registered Customers</h2>

<table>
<tr>
<th>Customer ID</th>
<th>Customer Name</th>
<th>Phone Number</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
<td><?php echo $row['customer_id']; ?></td>
<td><?php echo htmlspecialchars($row['customer_name']); ?></td>
<td><?php echo htmlspecialchars($row['phone_number']); ?></td>
</tr>
<?php } ?>

</table>

<p><a href="index.php">Back to Dashboard</a></p>

</div>
</body>
</html>