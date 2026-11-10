<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    if ($name !== "") {
        $stmt = $conn->prepare(
            "INSERT INTO categories (category_name, description) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $description);

        if ($stmt->execute()) {
            $message = "Category added successfully!";
        } else {
            $message = "Error adding category.";
        }
        $stmt->close();
    } else {
        $message = "Please enter a category name.";
    }
}

$result = $conn->query("SELECT * FROM categories ORDER BY category_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS - Categories</title>
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
            max-width: 850px;
            margin: auto;
        }
        h1 { color: #172b3a; }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
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
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th { background: #172b3a; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h1>JESS RETAIL SHOP</h1>
    <h2>Product Categories</h2>

    <p><?php echo htmlspecialchars($message); ?></p>

    <form method="POST">
        <label>Category Name</label>
        <input type="text" name="category_name" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <button type="submit" name="add_category">
            Add Category
        </button>
    </form>

    <h2>Available Categories</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Description</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['category_id']; ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
        </tr>
        <?php } ?>
    </table>

    <p><a href="index.php">Back to Dashboard</a></p>
</div>

</body>
</html>