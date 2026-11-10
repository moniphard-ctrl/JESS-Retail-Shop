<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $category = (int)$_POST['category_id'];
    $buying = (float)$_POST['buying_price'];
    $selling = (float)$_POST['selling_price'];
    $quantity = (int)$_POST['quantity'];
    $reorder = (int)$_POST['reorder_level'];

    $stmt = $conn->prepare(
        "INSERT INTO products
        (product_name, category_id, buying_price, selling_price,
        quantity_in_stock, reorder_level)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "siddii",
        $name, $category, $buying, $selling, $quantity, $reorder
    );

    if ($stmt->execute()) {
        $message = "Product added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");

$products = $conn->query(
    "SELECT p.*, c.category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     ORDER BY p.product_id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS - Products</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f5f8;
            padding: 25px;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 { color: #172b3a; }

        input, select {
            width: 100%;
            padding: 11px;
            margin: 8px 0 15px;
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
            padding: 10px;
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
    <h2>Product Management</h2>

    <p><?php echo htmlspecialchars($message); ?></p>

    <form method="POST">

        <label>Product Name</label>
        <input type="text" name="product_name" required>

        <label>Category</label>
        <select name="category_id" required>
            <option value="">Select Category</option>

            <?php while ($cat = $categories->fetch_assoc()) { ?>
                <option value="<?php echo $cat['category_id']; ?>">
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
            <?php } ?>

        </select>

        <label>Buying Price</label>
        <input type="number" step="0.01" min="0"
               name="buying_price" required>

        <label>Selling Price</label>
        <input type="number" step="0.01" min="0"
               name="selling_price" required>

        <label>Quantity in Stock</label>
        <input type="number" name="quantity" min="0" required>

        <label>Reorder Level</label>
        <input type="number" name="reorder_level"
               min="0" value="5" required>

        <button type="submit" name="add_product">
            Add Product
        </button>

    </form>

    <h2>Available Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Category</th>
            <th>Buying Price</th>
            <th>Selling Price</th>
            <th>Stock</th>
        </tr>

        <?php while ($row = $products->fetch_assoc()) { ?>

        <tr>
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></td>
            <td><?php echo number_format($row['buying_price'], 2); ?></td>
            <td><?php echo number_format($row['selling_price'], 2); ?></td>
            <td><?php echo $row['quantity_in_stock']; ?></td>
        </tr>

        <?php } ?>

    </table>

    <p><a href="index.php">Back to Dashboard</a></p>

</div>

</body>
</html>