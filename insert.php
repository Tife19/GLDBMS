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
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'] ?? '';
    $allowed_tables = [
        'farms', 'crop_fields', 'crops', 'crop_field_crop', 'harvest_batch', 'products',
        'product_batch', 'inventory', 'employees', 'employee_tasks', 'suppliers',
        'supplier_crop_materials', 'customers', 'orders', 'order_products',
        'certifications', 'distributions'
    ];

    if (!in_array($table, $allowed_tables)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid table name']);
        exit;
    }

    try {
        switch ($table) {
            case 'farms':
                if (empty($_POST['farm_id']) || empty($_POST['farm_name'])) {
                    throw new PDOException("Farm ID and Farm Name are required");
                }
                $stmt = $conn->prepare("SELECT Farm_ID FROM Farms WHERE Farm_ID = :farm_id");
                $stmt->execute(['farm_id' => $_POST['farm_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Farm ID already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Farms (Farm_ID, Farm_Name, Location, Size, Farm_Manager) VALUES (:farm_id, :farm_name, :location, :size, :farm_manager)");
                $stmt->execute([
                    'farm_id' => $_POST['farm_id'],
                    'farm_name' => $_POST['farm_name'],
                    'location' => $_POST['location'] ?? null,
                    'size' => $_POST['size'] ?? null,
                    'farm_manager' => $_POST['farm_manager'] ?? null
                ]);
                break;

            case 'crop_fields':
                if (empty($_POST['field_id']) || empty($_POST['farm_id'])) {
                    throw new PDOException("Field ID and Farm ID are required");
                }
                $stmt = $conn->prepare("SELECT Field_ID FROM Crop_Fields WHERE Field_ID = :field_id");
                $stmt->execute(['field_id' => $_POST['field_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Field ID already exists");
                }
                $stmt = $conn->prepare("SELECT Farm_ID FROM Farms WHERE Farm_ID = :farm_id");
                $stmt->execute(['farm_id' => $_POST['farm_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Farm does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Crop_Fields (Field_ID, Farm_ID) VALUES (:field_id, :farm_id)");
                $stmt->execute([
                    'field_id' => $_POST['field_id'],
                    'farm_id' => $_POST['farm_id']
                ]);
                break;

            case 'crops':
                if (empty($_POST['crop_id']) || empty($_POST['crop_name'])) {
                    throw new PDOException("Crop ID and Crop Name are required");
                }
                $stmt = $conn->prepare("SELECT Crop_ID FROM Crops WHERE Crop_ID = :crop_id");
                $stmt->execute(['crop_id' => $_POST['crop_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Crop ID already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Crops (Crop_ID, Crop_Name, Organic_Certification_Status, Growth_Period) VALUES (:crop_id, :crop_name, :organic_certification_status, :growth_period)");
                $stmt->execute([
                    'crop_id' => $_POST['crop_id'],
                    'crop_name' => $_POST['crop_name'],
                    'organic_certification_status' => $_POST['organic_certification_status'] ?? null,
                    'growth_period' => $_POST['growth_period'] ?? null
                ]);
                break;

            case 'crop_field_crop':
                if (empty($_POST['field_id']) || empty($_POST['crop_id'])) {
                    throw new PDOException("Field ID and Crop ID are required");
                }
                $stmt = $conn->prepare("SELECT Field_ID FROM Crop_Fields WHERE Field_ID = :field_id");
                $stmt->execute(['field_id' => $_POST['field_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Field does not exist");
                }
                $stmt = $conn->prepare("SELECT Crop_ID FROM Crops WHERE Crop_ID = :crop_id");
                $stmt->execute(['crop_id' => $_POST['crop_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Crop does not exist");
                }
                $stmt = $conn->prepare("SELECT Field_ID, Crop_ID FROM Crop_Field_Crop WHERE Field_ID = :field_id AND Crop_ID = :crop_id");
                $stmt->execute(['field_id' => $_POST['field_id'], 'crop_id' => $_POST['crop_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("This Field-Crop combination already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Crop_Field_Crop (Field_ID, Crop_ID, Planting_Date, Harvest_Date) VALUES (:field_id, :crop_id, :planting_date, :harvest_date)");
                $stmt->execute([
                    'field_id' => $_POST['field_id'],
                    'crop_id' => $_POST['crop_id'],
                    'planting_date' => $_POST['planting_date'] ?? null,
                    'harvest_date' => $_POST['harvest_date'] ?? null
                ]);
                break;

            case 'harvest_batch':
                if (empty($_POST['batch_id']) || empty($_POST['crop_id']) || empty($_POST['field_id'])) {
                    throw new PDOException("Batch ID, Crop ID, and Field ID are required");
                }
                $stmt = $conn->prepare("SELECT Batch_ID FROM Harvest_Batch WHERE Batch_ID = :batch_id");
                $stmt->execute(['batch_id' => $_POST['batch_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Batch ID already exists");
                }
                $stmt = $conn->prepare("SELECT Crop_ID FROM Crops WHERE Crop_ID = :crop_id");
                $stmt->execute(['crop_id' => $_POST['crop_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Crop does not exist");
                }
                $stmt = $conn->prepare("SELECT Field_ID FROM Crop_Fields WHERE Field_ID = :field_id");
                $stmt->execute(['field_id' => $_POST['field_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Field does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Harvest_Batch (Batch_ID, Crop_ID, Field_ID, Harvest_Date, Quantity_Harvested, Created_At) VALUES (:batch_id, :crop_id, :field_id, :harvest_date, :quantity_harvested, :created_at)");
                $stmt->execute([
                    'batch_id' => $_POST['batch_id'],
                    'crop_id' => $_POST['crop_id'],
                    'field_id' => $_POST['field_id'],
                    'harvest_date' => $_POST['harvest_date'] ?? null,
                    'quantity_harvested' => $_POST['quantity_harvested'] ?? null,
                    'created_at' => $_POST['created_at'] ?? null
                ]);
                break;

            case 'products':
                if (empty($_POST['product_id']) || empty($_POST['product_name'])) {
                    throw new PDOException("Product ID and Product Name are required");
                }
                $stmt = $conn->prepare("SELECT Product_ID FROM Products WHERE Product_ID = :product_id");
                $stmt->execute(['product_id' => $_POST['product_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Product ID already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Products (Product_ID, Product_Name, Unit_Price, Packaging_Type) VALUES (:product_id, :product_name, :unit_price, :packaging_type)");
                $stmt->execute([
                    'product_id' => $_POST['product_id'],
                    'product_name' => $_POST['product_name'],
                    'unit_price' => $_POST['unit_price'] ?? null,
                    'packaging_type' => $_POST['packaging_type'] ?? null
                ]);
                break;

            case 'product_batch':
                if (empty($_POST['product_id']) || empty($_POST['batch_id'])) {
                    throw new PDOException("Product ID and Batch ID are required");
                }
                $stmt = $conn->prepare("SELECT Product_ID FROM Products WHERE Product_ID = :product_id");
                $stmt->execute(['product_id' => $_POST['product_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Product does not exist");
                }
                $stmt = $conn->prepare("SELECT Batch_ID FROM Harvest_Batch WHERE Batch_ID = :batch_id");
                $stmt->execute(['batch_id' => $_POST['batch_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Batch does not exist");
                }
                $stmt = $conn->prepare("SELECT Product_ID, Batch_ID FROM Product_Batch WHERE Product_ID = :product_id AND Batch_ID = :batch_id");
                $stmt->execute(['product_id' => $_POST['product_id'], 'batch_id' => $_POST['batch_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("This Product-Batch combination already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Product_Batch (Product_ID, Batch_ID) VALUES (:product_id, :batch_id)");
                $stmt->execute([
                    'product_id' => $_POST['product_id'],
                    'batch_id' => $_POST['batch_id']
                ]);
                break;

            case 'inventory':
                if (empty($_POST['inventory_id']) || empty($_POST['product_id'])) {
                    throw new PDOException("Inventory ID and Product ID are required");
                }
                $stmt = $conn->prepare("SELECT Inventory_ID FROM Inventory WHERE Inventory_ID = :inventory_id");
                $stmt->execute(['inventory_id' => $_POST['inventory_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Inventory ID already exists");
                }
                $stmt = $conn->prepare("SELECT Product_ID FROM Products WHERE Product_ID = :product_id");
                $stmt->execute(['product_id' => $_POST['product_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Product does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Inventory (Inventory_ID, Product_ID, Quantity_In_Stock, Storage_Location, Last_Updated) VALUES (:inventory_id, :product_id, :quantity_in_stock, :storage_location, :last_updated)");
                $stmt->execute([
                    'inventory_id' => $_POST['inventory_id'],
                    'product_id' => $_POST['product_id'],
                    'quantity_in_stock' => $_POST['quantity_in_stock'] ?? null,
                    'storage_location' => $_POST['storage_location'] ?? null,
                    'last_updated' => $_POST['last_updated'] ?? null
                ]);
                break;

            case 'employees':
                if (empty($_POST['employee_id']) || empty($_POST['employee_name']) || empty($_POST['farm_id'])) {
                    throw new PDOException("Employee ID, Employee Name, and Farm ID are required");
                }
                $stmt = $conn->prepare("SELECT Employee_ID FROM Employees WHERE Employee_ID = :employee_id");
                $stmt->execute(['employee_id' => $_POST['employee_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Employee ID already exists");
                }
                $stmt = $conn->prepare("SELECT Farm_ID FROM Farms WHERE Farm_ID = :farm_id");
                $stmt->execute(['farm_id' => $_POST['farm_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Farm does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Employees (Employee_ID, Employee_Name, Role, Hire_Date, Salary, Farm_ID) VALUES (:employee_id, :employee_name, :role, :hire_date, :salary, :farm_id)");
                $stmt->execute([
                    'employee_id' => $_POST['employee_id'],
                    'employee_name' => $_POST['employee_name'],
                    'role' => $_POST['role'] ?? null,
                    'hire_date' => $_POST['hire_date'] ?? null,
                    'salary' => $_POST['salary'] ?? null,
                    'farm_id' => $_POST['farm_id']
                ]);
                break;

            case 'employee_tasks':
                if (empty($_POST['task_id']) || empty($_POST['employee_id'])) {
                    throw new PDOException("Task ID and Employee ID are required");
                }
                $stmt = $conn->prepare("SELECT Task_ID FROM Employee_Tasks WHERE Task_ID = :task_id");
                $stmt->execute(['task_id' => $_POST['task_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Task ID already exists");
                }
                $stmt = $conn->prepare("SELECT Employee_ID FROM Employees WHERE Employee_ID = :employee_id");
                $stmt->execute(['employee_id' => $_POST['employee_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Employee does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Employee_Tasks (Task_ID, Employee_ID, Task_Type, Date, Assigned_Unit_ID, Created_At) VALUES (:task_id, :employee_id, :task_type, :date, :assigned_unit_id, :created_at)");
                $stmt->execute([
                    'task_id' => $_POST['task_id'],
                    'employee_id' => $_POST['employee_id'],
                    'task_type' => $_POST['task_type'] ?? null,
                    'date' => $_POST['date'] ?? null,
                    'assigned_unit_id' => $_POST['assigned_unit_id'] ?? null,
                    'created_at' => $_POST['created_at'] ?? null
                ]);
                break;

            case 'suppliers':
                if (empty($_POST['supplier_id']) || empty($_POST['supplier_name'])) {
                    throw new PDOException("Supplier ID and Supplier Name are required");
                }
                $stmt = $conn->prepare("SELECT Supplier_ID FROM Suppliers WHERE Supplier_ID = :supplier_id");
                $stmt->execute(['supplier_id' => $_POST['supplier_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Supplier ID already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Suppliers (Supplier_ID, Supplier_Name, Contact_Info, Location) VALUES (:supplier_id, :supplier_name, :contact_info, :location)");
                $stmt->execute([
                    'supplier_id' => $_POST['supplier_id'],
                    'supplier_name' => $_POST['supplier_name'],
                    'contact_info' => $_POST['contact_info'] ?? null,
                    'location' => $_POST['location'] ?? null
                ]);
                break;

            case 'supplier_crop_materials':
                if (empty($_POST['supplier_id']) || empty($_POST['crop_id']) || empty($_POST['material_type'])) {
                    throw new PDOException("Supplier ID, Crop ID, and Material Type are required");
                }
                $stmt = $conn->prepare("SELECT Supplier_ID FROM Suppliers WHERE Supplier_ID = :supplier_id");
                $stmt->execute(['supplier_id' => $_POST['supplier_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Supplier does not exist");
                }
                $stmt = $conn->prepare("SELECT Crop_ID FROM Crops WHERE Crop_ID = :crop_id");
                $stmt->execute(['crop_id' => $_POST['crop_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Crop does not exist");
                }
                $stmt = $conn->prepare("SELECT Supplier_ID, Crop_ID, Material_Type FROM Supplier_Crop_Materials WHERE Supplier_ID = :supplier_id AND Crop_ID = :crop_id AND Material_Type = :material_type");
                $stmt->execute([
                    'supplier_id' => $_POST['supplier_id'],
                    'crop_id' => $_POST['crop_id'],
                    'material_type' => $_POST['material_type']
                ]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("This Supplier-Crop-Material combination already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Supplier_Crop_Materials (Supplier_ID, Crop_ID, Material_Type, Quantity, Supply_Date) VALUES (:supplier_id, :crop_id, :material_type, :quantity, :supply_date)");
                $stmt->execute([
                    'supplier_id' => $_POST['supplier_id'],
                    'crop_id' => $_POST['crop_id'],
                    'material_type' => $_POST['material_type'],
                    'quantity' => $_POST['quantity'] ?? null,
                    'supply_date' => $_POST['supply_date'] ?? null
                ]);
                break;

            case 'customers':
                if (empty($_POST['customer_id']) || empty($_POST['customer_name'])) {
                    throw new PDOException("Customer ID and Customer Name are required");
                }
                $stmt = $conn->prepare("SELECT Customer_ID FROM Customers WHERE Customer_ID = :customer_id");
                $stmt->execute(['customer_id' => $_POST['customer_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Customer ID already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Customers (Customer_ID, Customer_Name, Contact_Info, Region) VALUES (:customer_id, :customer_name, :contact_info, :region)");
                $stmt->execute([
                    'customer_id' => $_POST['customer_id'],
                    'customer_name' => $_POST['customer_name'],
                    'contact_info' => $_POST['contact_info'] ?? null,
                    'region' => $_POST['region'] ?? null
                ]);
                break;

            case 'orders':
                if (empty($_POST['order_id']) || empty($_POST['customer_id'])) {
                    throw new PDOException("Order ID and Customer ID are required");
                }
                $stmt = $conn->prepare("SELECT Order_ID FROM Orders WHERE Order_ID = :order_id");
                $stmt->execute(['order_id' => $_POST['order_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Order ID already exists");
                }
                $stmt = $conn->prepare("SELECT Customer_ID FROM Customers WHERE Customer_ID = :customer_id");
                $stmt->execute(['customer_id' => $_POST['customer_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Customer does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Orders (Order_ID, Customer_ID, Order_Date, Total_Amount, Created_At) VALUES (:order_id, :customer_id, :order_date, :total_amount, :created_at)");
                $stmt->execute([
                    'order_id' => $_POST['order_id'],
                    'customer_id' => $_POST['customer_id'],
                    'order_date' => $_POST['order_date'] ?? null,
                    'total_amount' => $_POST['total_amount'] ?? null,
                    'created_at' => $_POST['created_at'] ?? null
                ]);
                break;

            case 'order_products':
                if (empty($_POST['order_id']) || empty($_POST['product_id'])) {
                    throw new PDOException("Order ID and Product ID are required");
                }
                $stmt = $conn->prepare("SELECT Order_ID FROM Orders WHERE Order_ID = :order_id");
                $stmt->execute(['order_id' => $_POST['order_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Order does not exist");
                }
                $stmt = $conn->prepare("SELECT Product_ID FROM Products WHERE Product_ID = :product_id");
                $stmt->execute(['product_id' => $_POST['product_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Product does not exist");
                }
                $stmt = $conn->prepare("SELECT Order_ID, Product_ID FROM Order_Products WHERE Order_ID = :order_id AND Product_ID = :product_id");
                $stmt->execute(['order_id' => $_POST['order_id'], 'product_id' => $_POST['product_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("This Order-Product combination already exists");
                }
                $stmt = $conn->prepare("INSERT INTO Order_Products (Order_ID, Product_ID, Quantity, Unit_Price) VALUES (:order_id, :product_id, :quantity, :unit_price)");
                $stmt->execute([
                    'order_id' => $_POST['order_id'],
                    'product_id' => $_POST['product_id'],
                    'quantity' => $_POST['quantity'] ?? null,
                    'unit_price' => $_POST['unit_price'] ?? null
                ]);
                break;

            case 'certifications':
                if (empty($_POST['certification_id']) || empty($_POST['farm_id'])) {
                    throw new PDOException("Certification ID and Farm ID are required");
                }
                $stmt = $conn->prepare("SELECT Certification_ID FROM Certifications WHERE Certification_ID = :certification_id");
                $stmt->execute(['certification_id' => $_POST['certification_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Certification ID already exists");
                }
                $stmt = $conn->prepare("SELECT Farm_ID FROM Farms WHERE Farm_ID = :farm_id");
                $stmt->execute(['farm_id' => $_POST['farm_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Farm does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Certifications (Certification_ID, Farm_ID, Certification_Agency, Certification_Date, Last_Verified) VALUES (:certification_id, :farm_id, :certification_agency, :certification_date, :last_verified)");
                $stmt->execute([
                    'certification_id' => $_POST['certification_id'],
                    'farm_id' => $_POST['farm_id'],
                    'certification_agency' => $_POST['certification_agency'] ?? null,
                    'certification_date' => $_POST['certification_date'] ?? null,
                    'last_verified' => $_POST['last_verified'] ?? null
                ]);
                break;

            case 'distributions':
                if (empty($_POST['distribution_id']) || empty($_POST['order_id'])) {
                    throw new PDOException("Distribution ID and Order ID are required");
                }
                $stmt = $conn->prepare("SELECT Distribution_ID FROM Distributions WHERE Distribution_ID = :distribution_id");
                $stmt->execute(['distribution_id' => $_POST['distribution_id']]);
                if ($stmt->rowCount() > 0) {
                    throw new PDOException("Distribution ID already exists");
                }
                $stmt = $conn->prepare("SELECT Order_ID FROM Orders WHERE Order_ID = :order_id");
                $stmt->execute(['order_id' => $_POST['order_id']]);
                if ($stmt->rowCount() == 0) {
                    throw new PDOException("Selected Order does not exist");
                }
                $stmt = $conn->prepare("INSERT INTO Distributions (Distribution_ID, Order_ID, Vehicle_ID, Delivery_Date, Destination) VALUES (:distribution_id, :order_id, :vehicle_id, :delivery_date, :destination)");
                $stmt->execute([
                    'distribution_id' => $_POST['distribution_id'],
                    'order_id' => $_POST['order_id'],
                    'vehicle_id' => $_POST['vehicle_id'] ?? null,
                    'delivery_date' => $_POST['delivery_date'] ?? null,
                    'destination' => $_POST['destination'] ?? null
                ]);
                break;
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Record inserted successfully']);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error inserting record: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>