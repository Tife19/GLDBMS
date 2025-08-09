<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "Boluwatife019$";
$dbname = "GreenLeafDB";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$results = [];
$search_term = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_term'])) {
    $search_term = trim($_POST['search_term']);
    if (!empty($search_term)) {
        $search_term = "%" . $search_term . "%";
        
        // Search Farms
        $stmt = $conn->prepare("
            SELECT f.Farm_Name, f.Location, f.Size, f.Farm_Manager, 
                   GROUP_CONCAT(cf.Field_ID) as Crop_Fields,
                   GROUP_CONCAT(c.Certification_Agency) as Certifications
            FROM Farms f
            LEFT JOIN Crop_Fields cf ON f.Farm_ID = cf.Farm_ID
            LEFT JOIN Certifications c ON f.Farm_ID = c.Farm_ID
            WHERE f.Farm_Name LIKE :search_term
            GROUP BY f.Farm_ID
        ");
        $stmt->execute(['search_term' => $search_term]);
        $farms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($farms) $results['Farms'] = $farms;

        // Search Crop_Fields (by Farm_Name)
        $stmt = $conn->prepare("
            SELECT cf.Field_ID, f.Farm_Name
            FROM Crop_Fields cf
            JOIN Farms f ON cf.Farm_ID = f.Farm_ID
            WHERE f.Farm_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $crop_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($crop_fields) $results['Crop_Fields'] = $crop_fields;

        // Search Crops
        $stmt = $conn->prepare("
            SELECT c.Crop_Name, c.Organic_Certification_Status, c.Growth_Period,
                   GROUP_CONCAT(cfc.Field_ID) as Field_IDs
            FROM Crops c
            LEFT JOIN Crop_Field_Crop cfc ON c.Crop_ID = cfc.Crop_ID
            WHERE c.Crop_Name LIKE :search_term
            GROUP BY c.Crop_ID
        ");
        $stmt->execute(['search_term' => $search_term]);
        $crops = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($crops) $results['Crops'] = $crops;

        // Search Crop_Field_Crop (by Crop_Name)
        $stmt = $conn->prepare("
            SELECT cfc.Field_ID, cfc.Crop_ID, c.Crop_Name, cfc.Planting_Date, cfc.Harvest_Date
            FROM Crop_Field_Crop cfc
            JOIN Crops c ON cfc.Crop_ID = c.Crop_ID
            WHERE c.Crop_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $crop_field_crop = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($crop_field_crop) $results['Crop_Field_Crop'] = $crop_field_crop;

        // Search Harvest_Batch (by Crop_Name)
        $stmt = $conn->prepare("
            SELECT hb.Batch_ID, c.Crop_Name, cf.Field_ID, hb.Harvest_Date, hb.Quantity_Harvested
            FROM Harvest_Batch hb
            JOIN Crops c ON hb.Crop_ID = c.Crop_ID
            JOIN Crop_Fields cf ON hb.Field_ID = cf.Field_ID
            WHERE c.Crop_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $harvest_batch = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($harvest_batch) $results['Harvest_Batch'] = $harvest_batch;

        // Search Products
        $stmt = $conn->prepare("
            SELECT p.Product_Name, p.Unit_Price, p.Packaging_Type,
                   GROUP_CONCAT(hb.Batch_ID) as Harvest_Batches,
                   GROUP_CONCAT(hb.Harvest_Date) as Harvest_Dates
            FROM Products p
            LEFT JOIN Product_Batch pb ON p.Product_ID = pb.Product_ID
            LEFT JOIN Harvest_Batch hb ON pb.Batch_ID = hb.Batch_ID
            WHERE p.Product_Name LIKE :search_term
            GROUP BY p.Product_ID
        ");
        $stmt->execute(['search_term' => $search_term]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($products) $results['Products'] = $products;

        // Search Product_Batch (by Product_Name)
        $stmt = $conn->prepare("
            SELECT pb.Product_ID, p.Product_Name, pb.Batch_ID
            FROM Product_Batch pb
            JOIN Products p ON pb.Product_ID = p.Product_ID
            WHERE p.Product_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $product_batch = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($product_batch) $results['Product_Batch'] = $product_batch;

        // Search Inventory (by Product_Name)
        $stmt = $conn->prepare("
            SELECT i.Product_ID, p.Product_Name, i.Quantity_In_Stock, i.Storage_Location, i.Last_Updated
            FROM Inventory i
            JOIN Products p ON i.Product_ID = p.Product_ID
            WHERE p.Product_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($inventory) $results['Inventory'] = $inventory;

        // Search Employees
        $stmt = $conn->prepare("
            SELECT e.Employee_Name, e.Role, e.Hire_Date, e.Salary, f.Farm_Name
            FROM Employees e
            LEFT JOIN Farms f ON e.Farm_ID = f.Farm_ID
            WHERE e.Employee_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($employees) $results['Employees'] = $employees;

        // Search Employee_Tasks (by Employee_Name)
        $stmt = $conn->prepare("
            SELECT et.Task_ID, e.Employee_Name, et.Task_Type, et.Date, et.Assigned_Unit_ID
            FROM Employee_Tasks et
            JOIN Employees e ON et.Employee_ID = e.Employee_ID
            WHERE e.Employee_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $employee_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($employee_tasks) $results['Employee_Tasks'] = $employee_tasks;

        // Search Suppliers
        $stmt = $conn->prepare("
            SELECT s.Supplier_Name, s.Contact_Info, s.Location,
                   GROUP_CONCAT(scm.Material_Type) as Materials
            FROM Suppliers s
            LEFT JOIN Supplier_Crop_Materials scm ON s.Supplier_ID = scm.Supplier_ID
            WHERE s.Supplier_Name LIKE :search_term
            GROUP BY s.Supplier_ID
        ");
        $stmt->execute(['search_term' => $search_term]);
        $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($suppliers) $results['Suppliers'] = $suppliers;

        // Search Supplier_Crop_Materials (by Supplier_Name or Material_Type)
        $stmt = $conn->prepare("
            SELECT scm.Supplier_ID, s.Supplier_Name, c.Crop_Name, scm.Material_Type, scm.Quantity, scm.Supply_Date
            FROM Supplier_Crop_Materials scm
            JOIN Suppliers s ON scm.Supplier_ID = s.Supplier_ID
            JOIN Crops c ON scm.Crop_ID = c.Crop_ID
            WHERE s.Supplier_Name LIKE :search_term OR scm.Material_Type LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $supplier_crop_materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($supplier_crop_materials) $results['Supplier_Crop_Materials'] = $supplier_crop_materials;

        // Search Customers
        $stmt = $conn->prepare("
            SELECT c.Customer_Name, c.Contact_Info, c.Region,
                   GROUP_CONCAT(o.Order_ID) as Orders,
                   GROUP_CONCAT(o.Order_Date) as Order_Dates
            FROM Customers c
            LEFT JOIN Orders o ON c.Customer_ID = o.Customer_ID
            WHERE c.Customer_Name LIKE :search_term
            GROUP BY c.Customer_ID
        ");
        $stmt->execute(['search_term' => $search_term]);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($customers) $results['Customers'] = $customers;

        // Search Orders (by Customer_Name)
        $stmt = $conn->prepare("
            SELECT o.Order_ID, c.Customer_Name, o.Order_Date, o.Total_Amount
            FROM Orders o
            JOIN Customers c ON o.Customer_ID = c.Customer_ID
            WHERE c.Customer_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($orders) $results['Orders'] = $orders;

        // Search Order_Products (by Product_Name)
        $stmt = $conn->prepare("
            SELECT op.Order_ID, p.Product_Name, op.Quantity, op.Unit_Price
            FROM Order_Products op
            JOIN Products p ON op.Product_ID = p.Product_ID
            WHERE p.Product_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $order_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($order_products) $results['Order_Products'] = $order_products;

        // Search Certifications (by Certification_Agency or Farm_Name)
        $stmt = $conn->prepare("
            SELECT c.Farm_ID, f.Farm_Name, c.Certification_Agency, c.Certification_Date, c.Last_Verified
            FROM Certifications c
            JOIN Farms f ON c.Farm_ID = f.Farm_ID
            WHERE c.Certification_Agency LIKE :search_term OR f.Farm_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $certifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($certifications) $results['Certifications'] = $certifications;

        // Search Distributions (by Destination or Customer_Name via Orders)
        $stmt = $conn->prepare("
            SELECT d.Order_ID, d.Vehicle_ID, d.Delivery_Date, d.Destination, c.Customer_Name
            FROM Distributions d
            JOIN Orders o ON d.Order_ID = o.Order_ID
            JOIN Customers c ON o.Customer_ID = c.Customer_ID
            WHERE d.Destination LIKE :search_term OR c.Customer_Name LIKE :search_term
        ");
        $stmt->execute(['search_term' => $search_term]);
        $distributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($distributions) $results['Distributions'] = $distributions;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - GreenLeaf Organic Farms</title>
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
        .table thead {
            background-color: #2c6e49;
            color: #ffffff;
        }
        .table tbody tr:hover {
            background-color: #e0f2e9;
        }
        h2, h3 {
            color: #2c6e49;
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
                <h2>Search Records</h2>
                <div class="card">
                    <div class="card-body">
                        <form action="search.php" method="POST">
                            <div class="mb-3">
                                <label for="search_term" class="form-label">Search by Name</label>
                                <input type="text" class="form-control" id="search_term" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $table_name => $records): ?>
                        <h3><?php echo htmlspecialchars($table_name); ?></h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php foreach (array_keys($records[0]) as $column): ?>
                                        <th><?php echo htmlspecialchars($column); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <?php foreach ($record as $value): ?>
                                            <td><?php echo htmlspecialchars($value ?? 'N/A'); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                    <div class="alert alert-warning">No records found for "<?php echo htmlspecialchars($search_term); ?>"</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>