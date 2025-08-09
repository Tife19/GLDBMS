<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: dashboard.php");
    exit;
}

$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = "Boluwatife019$"; // Replace with your MySQL password
$dbname = "GreenLeafDB";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Hardcoded admin credentials
$admin_username = "admin";
$admin_password_hash = password_hash("1234", PASSWORD_DEFAULT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    if ($input_username === $admin_username && password_verify($input_password, $admin_password_hash)) {
        $_SESSION['loggedin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenLeaf Organic Farms - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e0f2e9, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            color: #2c6e49;
            font-weight: bold;
            text-align: center;
        }
        .btn-primary {
            background-color: #2c6e49;
            border-color: #2c6e49;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #1e4e34;
            border-color: #1e4e34;
        }
        .form-control:focus {
            border-color: #2c6e49;
            box-shadow: 0 0 5px rgba(44, 110, 73, 0.5);
        }
        .alert {
            background-color: #e0f2e9;
            color: #2c6e49;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <h2 class="mb-4">GreenLeaf Organic Farms Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>