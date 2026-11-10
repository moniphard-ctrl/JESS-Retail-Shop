<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function countRecords($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
    return $result ? $result->fetch_assoc()['total'] : 0;
}

$products = countRecords($conn, "products");
$customers = countRecords($conn, "customers");
$sales = countRecords($conn, "sales");
$suppliers = countRecords($conn, "suppliers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS Retail Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f2f5f8;
        }

        header {
            background: #172b3a;
            color: white;
            padding: 25px;
            text-align: center;
        }

        .container {
            padding: 30px;
            </body>
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
        }

        .card h2 {
            color: #172b3a;
        }

        .number {
            font-size: 30px;
            font-weight: bold;
            color: #16845b;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>

<body>

<header>
    <h1>JESS RETAIL SHOP</h1>
    <p>Management Information System</p>
</header>

<div class="container">
    <h2>Welcome to JESS Dashboard</h2>
    <p>Manage your retail shop operations from one place.</p>

    <div class="cards">

        <div class="card">
            <h2>Products</h2>
            <p class="number"><?php echo $products; ?></p>
            <p>Registered products</p>
        </div>

        <div class="card">
            <h2>Customers</h2>
            <p class="number"><?php echo $customers; ?></p>
            <p>Registered customers</p>
        </div>

        <div class="card">
            <h2>Sales</h2>
            <p class="number"><?php echo $sales; ?></p>
            <p>Recorded sales</p>
        </div>

        <div class="card">
            <h2>Suppliers</h2>
            <p class="number"><?php echo $suppliers; ?></p>
            <p>Registered suppliers</p>
        </div>

    </div>
</div>

<footer>
    <p>JESS Retail Shop MIS &copy; 2026</p>
</footer>

</body>
</html>