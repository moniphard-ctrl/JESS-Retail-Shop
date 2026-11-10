<?php
$conn = new mysqli("localhost", "root", "", "jess retail shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['add_user'])) {
    $name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare(
        "INSERT INTO users (full_name, username, password, role)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $name, $username, $password, $role);

    if ($stmt->execute()) {
        $message = "User registered successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>JESS - User Management</title>
</head>
<body>

<h1>JESS Retail Shop</h1>
<h2>User Management</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <label>Full Name:</label><br>
    <input type="text" name="full_name" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Role:</label><br>
    <select name="role">
        <option value="Manager">Manager</option>
        <option value="Cashier">Cashier</option>
        <option value="Storekeeper">Storekeeper</option>
    </select><br><br>

    <button type="submit" name="add_user">Register User</button>
</form>

</body>
</html>