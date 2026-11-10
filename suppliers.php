<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['add_supplier'])) {
    $name = $_POST['supplier_name'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, phone_number, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $address);

    if ($stmt->execute()) {
        $message = "Supplier added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS - Suppliers</title>
</head>
<body>

<h1>JESS Retail Shop</h1>
<h2>Supplier Management</h2>

<p><?php echo $message; ?></p>

<form method="POST">
    <label>Supplier Name:</label><br>
    <input type="text" name="supplier_name" required><br><br>

    <label>Phone Number:</label><br>
    <input type="text" name="phone_number"><br><br>

    <label>Address:</label><br>
    <input type="text" name="address"><br><br>

    <button type="submit" name="add_supplier">Add Supplier</button>
</form>

<h2>Registered Suppliers</h2>

<table border="1" cellpadding="8">
<tr>
    <th>ID</th>
    <th>Supplier Name</th>
    <th>Phone</th>
    <th>Address</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM suppliers");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['supplier_id'] . "</td>";
    echo "<td>" . $row['supplier_name'] . "</td>";
    echo "<td>" . $row['phone_number'] . "</td>";
    echo "<td>" . $row['address'] . "</td>";
    echo "</tr>";
}
?>

</table>
</body>
</html>