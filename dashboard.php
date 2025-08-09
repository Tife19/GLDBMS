<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "Boluwatife019$"; // Replace with your actual root password
$dbname = "GreenLeafDB";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch farms for dropdown
    $stmt = $conn->prepare("SELECT Farm_ID, Farm_Name FROM Farms ORDER BY Farm_Name");
    $stmt->execute();
    $farms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch crops for dropdown
    $stmt = $conn->prepare("SELECT Crop_ID, Crop_Name FROM Crops ORDER BY Crop_Name");
    $stmt->execute();
    $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch products for dropdown
    $stmt = $conn->prepare("SELECT Product_ID, Product_Name FROM Products ORDER BY Product_Name");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch customers for dropdown
    $stmt = $conn->prepare("SELECT Customer_ID, Customer_Name FROM Customers ORDER BY Customer_Name");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch suppliers for dropdown
    $stmt = $conn->prepare("SELECT Supplier_ID, Supplier_Name FROM Suppliers ORDER BY Supplier_Name");
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch harvest batches for dropdown
    $stmt = $conn->prepare("SELECT Batch_ID, Harvest_Date FROM Harvest_Batch ORDER BY Harvest_Date DESC");
    $stmt->execute();
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch crop fields for dropdown
    $stmt = $conn->prepare("SELECT Field_ID, Farm_ID FROM Crop_Fields ORDER BY Field_ID");
    $stmt->execute();
    $crop_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch employees for dropdown
    $stmt = $conn->prepare("SELECT Employee_ID, Employee_Name FROM Employees ORDER BY Employee_Name");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch orders for dropdown
    $stmt = $conn->prepare("SELECT Order_ID, Order_Date FROM Orders ORDER BY Order_Date DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GreenLeaf Organic Farms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e0f2e9, #ffffff);
            font-family: Arial, sans-serif;
        }
        .sidebar {
            background-color: #2c6e49;
            height: 100vh;
            padding: 20px;
            color: #ffffff;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin-bottom: 5px;
        }
        .sidebar a:hover {
            background-color: #1e4e34;
        }
        .content {
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #2c6e49;
            border-color: #2c6e49;
        }
        .btn-primary:hover {
            background-color: #1e4e34;
            border-color: #1e4e34;
        }
        h2, h5 {
            color: #2c6e49;
        }
        .alert {
            color: #2c6e49;
            background-color: #e0f2e9;
            border-color: #2c6e49;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <h4>GreenLeaf Organic Farms</h4>
                <a href="dashboard.php">Dashboard</a>
                <a href="search.php">Search</a>
                <a href="logout.php">Logout</a>
            </div>
            <div class="col-md-9 content">
                <h2>Dashboard</h2>

                <!-- Farms Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Farm</h5>
                        <form action="insert.php" method="POST">
                            <input type="hidden" name="table" value="farms">
                            <div class="mb-3">
                                <label for="farm_id" class="form-label">Farm ID</label>
                                <input type="number" class="form-control" id="farm_id" name="farm_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="farm_name" class="form-label">Farm Name</label>
                                <input type="text" class="form-control" id="farm_name" name="farm_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                            <div class="mb-3">
                                <label for="size" class="form-label">Size (acres)</label>
                                <input type="number" step="0.01" class="form-control" id="size" name="size">
                            </div>
                            <div class="mb-3">
                                <label for="farm_manager" class="form-label">Farm Manager</label>
                                <input type="text" class="form-control" id="farm_manager" name="farm_manager">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Farm</button>
                        </form>
                    </div>
                </div>

                <!-- Crop Fields Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Crop Field</h5>
                        <?php if (empty($farms)): ?>
                            <div class="alert alert-warning">No farms available. Please add a farm first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="crop_fields">
                                <div class="mb-3">
                                    <label for="field_id" class="form-label">Field ID</label>
                                    <input type="number" class="form-control" id="field_id" name="field_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="farm_id" class="form-label">Farm</label>
                                    <select class="form-control" id="farm_id" name="farm_id" required>
                                        <option value="">Select a Farm</option>
                                        <?php foreach ($farms as $farm): ?>
                                            <option value="<?php echo htmlspecialchars($farm['Farm_ID']); ?>">
                                                <?php echo htmlspecialchars($farm['Farm_Name']); ?> (ID: <?php echo htmlspecialchars($farm['Farm_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Crop Field</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Crops Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Crop</h5>
                        <form action="insert.php" method="POST">
                            <input type="hidden" name="table" value="crops">
                            <div class="mb-3">
                                <label for="crop_id" class="form-label">Crop ID</label>
                                <input type="number" class="form-control" id="crop_id" name="crop_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="crop_name" class="form-label">Crop Name</label>
                                <input type="text" class="form-control" id="crop_name" name="crop_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="organic_certification_status" class="form-label">Organic Certified</label>
                                <select class="form-control" id="organic_certification_status" name="organic_certification_status">
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="growth_period" class="form-label">Growth Period (days)</label>
                                <input type="number" class="form-control" id="growth_period" name="growth_period">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Crop</button>
                        </form>
                    </div>
                </div>

                <!-- Crop Field Crop Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Crop Field Crop</h5>
                        <?php if (empty($crop_fields) || empty($crops)): ?>
                            <div class="alert alert-warning">No crop fields or crops available. Please add a crop field and crop first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="crop_field_crop">
                                <div class="mb-3">
                                    <label for="field_id" class="form-label">Crop Field</label>
                                    <select class="form-control" id="field_id" name="field_id" required>
                                        <option value="">Select a Crop Field</option>
                                        <?php foreach ($crop_fields as $field): ?>
                                            <option value="<?php echo htmlspecialchars($field['Field_ID']); ?>">
                                                Field ID <?php echo htmlspecialchars($field['Field_ID']); ?> (Farm ID <?php echo htmlspecialchars($field['Farm_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="crop_id" class="form-label">Crop</label>
                                    <select class="form-control" id="crop_id" name="crop_id" required>
                                        <option value="">Select a Crop</option>
                                        <?php foreach ($crops as $crop): ?>
                                            <option value="<?php echo htmlspecialchars($crop['Crop_ID']); ?>">
                                                <?php echo htmlspecialchars($crop['Crop_Name']); ?> (ID: <?php echo htmlspecialchars($crop['Crop_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="planting_date" class="form-label">Planting Date</label>
                                    <input type="date" class="form-control" id="planting_date" name="planting_date">
                                </div>
                                <div class="mb-3">
                                    <label for="harvest_date" class="form-label">Harvest Date</label>
                                    <input type="date" class="form-control" id="harvest_date" name="harvest_date">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Crop Field Crop</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Harvest Batch Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Harvest Batch</h5>
                        <?php if (empty($crops) || empty($crop_fields)): ?>
                            <div class="alert alert-warning">No crops or crop fields available. Please add a crop and crop field first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="harvest_batch">
                                <div class="mb-3">
                                    <label for="batch_id" class="form-label">Batch ID</label>
                                    <input type="number" class="form-control" id="batch_id" name="batch_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="crop_id" class="form-label">Crop</label>
                                    <select class="form-control" id="crop_id" name="crop_id" required>
                                        <option value="">Select a Crop</option>
                                        <?php foreach ($crops as $crop): ?>
                                            <option value="<?php echo htmlspecialchars($crop['Crop_ID']); ?>">
                                                <?php echo htmlspecialchars($crop['Crop_Name']); ?> (ID: <?php echo htmlspecialchars($crop['Crop_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="field_id" class="form-label">Crop Field</label>
                                    <select class="form-control" id="field_id" name="field_id" required>
                                        <option value="">Select a Crop Field</option>
                                        <?php foreach ($crop_fields as $field): ?>
                                            <option value="<?php echo htmlspecialchars($field['Field_ID']); ?>">
                                                Field ID <?php echo htmlspecialchars($field['Field_ID']); ?> (Farm ID <?php echo htmlspecialchars($field['Farm_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="harvest_date" class="form-label">Harvest Date</label>
                                    <input type="date" class="form-control" id="harvest_date" name="harvest_date">
                                </div>
                                <div class="mb-3">
                                    <label for="quantity_harvested" class="form-label">Quantity Harvested</label>
                                    <input type="number" step="0.01" class="form-control" id="quantity_harvested" name="quantity_harvested">
                                </div>
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created At</label>
                                    <input type="datetime-local" class="form-control" id="created_at" name="created_at">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Harvest Batch</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Products Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Product</h5>
                        <form action="insert.php" method="POST">
                            <input type="hidden" name="table" value="products">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product ID</label>
                                <input type="number" class="form-control" id="product_id" name="product_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="unit_price" class="form-label">Unit Price</label>
                                <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price">
                            </div>
                            <div class="mb-3">
                                <label for="packaging_type" class="form-label">Packaging Type</label>
                                <input type="text" class="form-control" id="packaging_type" name="packaging_type">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                </div>

                <!-- Product Batch Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Product Batch</h5>
                        <?php if (empty($products) || empty($batches)): ?>
                            <div class="alert alert-warning">No products or harvest batches available. Please add a product and harvest batch first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="product_batch">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product</label>
                                    <select class="form-control" id="product_id" name="product_id" required>
                                        <option value="">Select a Product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                                                <?php echo htmlspecialchars($product['Product_Name']); ?> (ID: <?php echo htmlspecialchars($product['Product_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="batch_id" class="form-label">Harvest Batch</label>
                                    <select class="form-control" id="batch_id" name="batch_id" required>
                                        <option value="">Select a Batch</option>
                                        <?php foreach ($batches as $batch): ?>
                                            <option value="<?php echo htmlspecialchars($batch['Batch_ID']); ?>">
                                                Batch ID <?php echo htmlspecialchars($batch['Batch_ID']); ?> (Harvest Date: <?php echo htmlspecialchars($batch['Harvest_Date'] ?? 'N/A'); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Product Batch</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Inventory Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Inventory</h5>
                        <?php if (empty($products)): ?>
                            <div class="alert alert-warning">No products available. Please add a product first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="inventory">
                                <div class="mb-3">
                                    <label for="inventory_id" class="form-label">Inventory ID</label>
                                    <input type="number" class="form-control" id="inventory_id" name="inventory_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product</label>
                                    <select class="form-control" id="product_id" name="product_id" required>
                                        <option value="">Select a Product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                                                <?php echo htmlspecialchars($product['Product_Name']); ?> (ID: <?php echo htmlspecialchars($product['Product_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity_in_stock" class="form-label">Quantity in Stock</label>
                                    <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock">
                                </div>
                                <div class="mb-3">
                                    <label for="storage_location" class="form-label">Storage Location</label>
                                    <input type="text" class="form-control" id="storage_location" name="storage_location">
                                </div>
                                <div class="mb-3">
                                    <label for="last_updated" class="form-label">Last Updated</label>
                                    <input type="datetime-local" class="form-control" id="last_updated" name="last_updated">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Inventory</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Employees Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Employee</h5>
                        <?php if (empty($farms)): ?>
                            <div class="alert alert-warning">No farms available. Please add a farm first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="employees">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee ID</label>
                                    <input type="number" class="form-control" id="employee_id" name="employee_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="employee_name" class="form-label">Employee Name</label>
                                    <input type="text" class="form-control" id="employee_name" name="employee_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control" id="role" name="role">
                                </div>
                                <div class="mb-3">
                                    <label for="hire_date" class="form-label">Hire Date</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date">
                                </div>
                                <div class="mb-3">
                                    <label for="salary" class="form-label">Salary</label>
                                    <input type="number" step="0.01" class="form-control" id="salary" name="salary">
                                </div>
                                <div class="mb-3">
                                    <label for="farm_id" class="form-label">Farm</label>
                                    <select class="form-control" id="farm_id" name="farm_id" required>
                                        <option value="">Select a Farm</option>
                                        <?php foreach ($farms as $farm): ?>
                                            <option value="<?php echo htmlspecialchars($farm['Farm_ID']); ?>">
                                                <?php echo htmlspecialchars($farm['Farm_Name']); ?> (ID: <?php echo htmlspecialchars($farm['Farm_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Employee</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Employee Tasks Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Employee Task</h5>
                        <?php if (empty($employees)): ?>
                            <div class="alert alert-warning">No employees available. Please add an employee first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="employee_tasks">
                                <div class="mb-3">
                                    <label for="task_id" class="form-label">Task ID</label>
                                    <input type="number" class="form-control" id="task_id" name="task_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select class="form-control" id="employee_id" name="employee_id" required>
                                        <option value="">Select an Employee</option>
                                        <?php foreach ($employees as $employee): ?>
                                            <option value="<?php echo htmlspecialchars($employee['Employee_ID']); ?>">
                                                <?php echo htmlspecialchars($employee['Employee_Name']); ?> (ID: <?php echo htmlspecialchars($employee['Employee_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="task_type" class="form-label">Task Type</label>
                                    <input type="text" class="form-control" id="task_type" name="task_type">
                                </div>
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date">
                                </div>
                                <div class="mb-3">
                                    <label for="assigned_unit_id" class="form-label">Assigned Unit ID</label>
                                    <input type="number" class="form-control" id="assigned_unit_id" name="assigned_unit_id">
                                </div>
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created At</label>
                                    <input type="datetime-local" class="form-control" id="created_at" name="created_at">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Task</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Suppliers Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Supplier</h5>
                        <form action="insert.php" method="POST">
                            <input type="hidden" name="table" value="suppliers">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier ID</label>
                                <input type="number" class="form-control" id="supplier_id" name="supplier_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="supplier_name" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Contact Info</label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Supplier</button>
                        </form>
                    </div>
                </div>

                <!-- Supplier Crop Materials Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Supplier Crop Material</h5>
                        <?php if (empty($suppliers) || empty($crops)): ?>
                            <div class="alert alert-warning">No suppliers or crops available. Please add a supplier and crop first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="supplier_crop_materials">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Supplier</label>
                                    <select class="form-control" id="supplier_id" name="supplier_id" required>
                                        <option value="">Select a Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?php echo htmlspecialchars($supplier['Supplier_ID']); ?>">
                                                <?php echo htmlspecialchars($supplier['Supplier_Name']); ?> (ID: <?php echo htmlspecialchars($supplier['Supplier_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="crop_id" class="form-label">Crop</label>
                                    <select class="form-control" id="crop_id" name="crop_id" required>
                                        <option value="">Select a Crop</option>
                                        <?php foreach ($crops as $crop): ?>
                                            <option value="<?php echo htmlspecialchars($crop['Crop_ID']); ?>">
                                                <?php echo htmlspecialchars($crop['Crop_Name']); ?> (ID: <?php echo htmlspecialchars($crop['Crop_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="material_type" class="form-label">Material Type</label>
                                    <input type="text" class="form-control" id="material_type" name="material_type" required>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" step="0.01" class="form-control" id="quantity" name="quantity">
                                </div>
                                <div class="mb-3">
                                    <label for="supply_date" class="form-label">Supply Date</label>
                                    <input type="date" class="form-control" id="supply_date" name="supply_date">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Material</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customers Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Customer</h5>
                        <form action="insert.php" method="POST">
                            <input type="hidden" name="table" value="customers">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer ID</label>
                                <input type="number" class="form-control" id="customer_id" name="customer_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Contact Info</label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info">
                            </div>
                            <div class="mb-3">
                                <label for="region" class="form-label">Region</label>
                                <input type="text" class="form-control" id="region" name="region">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Customer</button>
                        </form>
                    </div>
                </div>

                <!-- Orders Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Order</h5>
                        <?php if (empty($customers)): ?>
                            <div class="alert alert-warning">No customers available. Please add a customer first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="orders">
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">Order ID</label>
                                    <input type="number" class="form-control" id="order_id" name="order_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select class="form-control" id="customer_id" name="customer_id" required>
                                        <option value="">Select a Customer</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?php echo htmlspecialchars($customer['Customer_ID']); ?>">
                                                <?php echo htmlspecialchars($customer['Customer_Name']); ?> (ID: <?php echo htmlspecialchars($customer['Customer_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="order_date" class="form-label">Order Date</label>
                                    <input type="date" class="form-control" id="order_date" name="order_date">
                                </div>
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount</label>
                                    <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount">
                                </div>
                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created At</label>
                                    <input type="datetime-local" class="form-control" id="created_at" name="created_at">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Order</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Products Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Order Product</h5>
                        <?php if (empty($orders) || empty($products)): ?>
                            <div class="alert alert-warning">No orders or products available. Please add an order and product first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="order_products">
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">Order</label>
                                    <select class="form-control" id="order_id" name="order_id" required>
                                        <option value="">Select an Order</option>
                                        <?php foreach ($orders as $order): ?>
                                            <option value="<?php echo htmlspecialchars($order['Order_ID']); ?>">
                                                Order ID <?php echo htmlspecialchars($order['Order_ID']); ?> (Date: <?php echo htmlspecialchars($order['Order_Date'] ?? 'N/A'); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product</label>
                                    <select class="form-control" id="product_id" name="product_id" required>
                                        <option value="">Select a Product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                                                <?php echo htmlspecialchars($product['Product_Name']); ?> (ID: <?php echo htmlspecialchars($product['Product_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity">
                                </div>
                                <div class="mb-3">
                                    <label for="unit_price" class="form-label">Unit Price</label>
                                    <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Order Product</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Certifications Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Certification</h5>
                        <?php if (empty($farms)): ?>
                            <div class="alert alert-warning">No farms available. Please add a farm first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="certifications">
                                <div class="mb-3">
                                    <label for="certification_id" class="form-label">Certification ID</label>
                                    <input type="number" class="form-control" id="certification_id" name="certification_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="farm_id" class="form-label">Farm</label>
                                    <select class="form-control" id="farm_id" name="farm_id" required>
                                        <option value="">Select a Farm</option>
                                        <?php foreach ($farms as $farm): ?>
                                            <option value="<?php echo htmlspecialchars($farm['Farm_ID']); ?>">
                                                <?php echo htmlspecialchars($farm['Farm_Name']); ?> (ID: <?php echo htmlspecialchars($farm['Farm_ID']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="certification_agency" class="form-label">Certification Agency</label>
                                    <input type="text" class="form-control" id="certification_agency" name="certification_agency">
                                </div>
                                <div class="mb-3">
                                    <label for="certification_date" class="form-label">Certification Date</label>
                                    <input type="date" class="form-control" id="certification_date" name="certification_date">
                                </div>
                                <div class="mb-3">
                                    <label for="last_verified" class="form-label">Last Verified</label>
                                    <input type="datetime-local" class="form-control" id="last_verified" name="last_verified">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Certification</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Distributions Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Distribution</h5>
                        <?php if (empty($orders)): ?>
                            <div class="alert alert-warning">No orders available. Please add an order first.</div>
                        <?php else: ?>
                            <form action="insert.php" method="POST">
                                <input type="hidden" name="table" value="distributions">
                                <div class="mb-3">
                                    <label for="distribution_id" class="form-label">Distribution ID</label>
                                    <input type="number" class="form-control" id="distribution_id" name="distribution_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">Order</label>
                                    <select class="form-control" id="order_id" name="order_id" required>
                                        <option value="">Select an Order</option>
                                        <?php foreach ($orders as $order): ?>
                                            <option value="<?php echo htmlspecialchars($order['Order_ID']); ?>">
                                                Order ID <?php echo htmlspecialchars($order['Order_ID']); ?> (Date: <?php echo htmlspecialchars($order['Order_Date'] ?? 'N/A'); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="vehicle_id" class="form-label">Vehicle ID</label>
                                    <input type="text" class="form-control" id="vehicle_id" name="vehicle_id">
                                </div>
                                <div class="mb-3">
                                    <label for="delivery_date" class="form-label">Delivery Date</label>
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                                </div>
                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" class="form-control" id="destination" name="destination">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Distribution</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>