-- MySQL Workbench Forward Engineering 

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

DROP SCHEMA IF EXISTS `GreenLeafDB`;
CREATE SCHEMA IF NOT EXISTS `GreenLeafDB` DEFAULT CHARACTER SET utf8mb4;
USE `GreenLeafDB`;

-- Farms Table
CREATE TABLE `Farms` (
  `Farm_ID` INT,
  `Farm_Name` VARCHAR(100) NOT NULL,
  `Location` VARCHAR(150),
  `Size` FLOAT,
  `Farm_Manager` VARCHAR(100),
  PRIMARY KEY (`Farm_ID`)
);

-- Crop_Fields Table
CREATE TABLE `Crop_Fields` (
  `Field_ID` INT,
  `Farm_ID` INT NOT NULL,
  PRIMARY KEY (`Field_ID`),
  INDEX (`Farm_ID`),
  CONSTRAINT `fk_CropFields_FarmID` FOREIGN KEY (`Farm_ID`) REFERENCES `Farms` (`Farm_ID`) ON DELETE CASCADE
);

-- Crops Table
CREATE TABLE `Crops` (
  `Crop_ID` INT,
  `Crop_Name` VARCHAR(100) NOT NULL,
  `Organic_Certification_Status` TINYINT,
  `Growth_Period` INT,
  PRIMARY KEY (`Crop_ID`)
);

-- Crop_Field_Crop Table
CREATE TABLE `Crop_Field_Crop` (
  `Field_ID` INT,
  `Crop_ID` INT,
  `Planting_Date` DATE,
  `Harvest_Date` DATE,
  PRIMARY KEY (`Field_ID`, `Crop_ID`),
  INDEX (`Crop_ID`),
  CONSTRAINT `fk_CFC_FieldID` FOREIGN KEY (`Field_ID`) REFERENCES `Crop_Fields` (`Field_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_CFC_CropID` FOREIGN KEY (`Crop_ID`) REFERENCES `Crops` (`Crop_ID`) ON DELETE CASCADE
);

-- Harvest_Batch Table
CREATE TABLE `Harvest_Batch` (
  `Batch_ID` INT,
  `Crop_ID` INT,
  `Field_ID` INT,
  `Harvest_Date` DATE,
  `Quantity_Harvested` FLOAT,
  `Created_At` DATETIME,
  PRIMARY KEY (`Batch_ID`),
  INDEX (`Crop_ID`),
  INDEX (`Field_ID`),
  CONSTRAINT `fk_HB_CropID` FOREIGN KEY (`Crop_ID`) REFERENCES `Crops` (`Crop_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_HB_FieldID` FOREIGN KEY (`Field_ID`) REFERENCES `Crop_Fields` (`Field_ID`) ON DELETE CASCADE
);

-- Products Table
CREATE TABLE `Products` (
  `Product_ID` INT,
  `Product_Name` VARCHAR(100) NOT NULL,
  `Unit_Price` DECIMAL(10,2),
  `Packaging_Type` VARCHAR(100),
  PRIMARY KEY (`Product_ID`)
);

-- Product_Batch Table
CREATE TABLE `Product_Batch` (
  `Product_ID` INT,
  `Batch_ID` INT,
  PRIMARY KEY (`Product_ID`, `Batch_ID`),
  INDEX (`Batch_ID`),
  CONSTRAINT `fk_PB_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `Products` (`Product_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_PB_BatchID` FOREIGN KEY (`Batch_ID`) REFERENCES `Harvest_Batch` (`Batch_ID`) ON DELETE CASCADE
);

-- Inventory Table
CREATE TABLE `Inventory` (
  `Inventory_ID` INT,
  `Product_ID` INT,
  `Quantity_In_Stock` INT,
  `Storage_Location` VARCHAR(100),
  `Last_Updated` DATETIME,
  PRIMARY KEY (`Inventory_ID`),
  INDEX (`Product_ID`),
  CONSTRAINT `fk_Inventory_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `Products` (`Product_ID`) ON DELETE CASCADE
);

-- Employees Table
CREATE TABLE `Employees` (
  `Employee_ID` INT,
  `Employee_Name` VARCHAR(100) NOT NULL,
  `Role` VARCHAR(100),
  `Hire_Date` DATE,
  `Salary` DECIMAL(10,2),
  `Farm_ID` INT,
  PRIMARY KEY (`Employee_ID`),
  INDEX (`Farm_ID`),
  CONSTRAINT `fk_Employees_FarmID` 
    FOREIGN KEY (`Farm_ID`) 
    REFERENCES `Farms` (`Farm_ID`) 
    ON DELETE RESTRICT
);

-- Employee_Tasks Table
CREATE TABLE `Employee_Tasks` (
  `Task_ID` INT,
  `Employee_ID` INT,
  `Task_Type` VARCHAR(100),
  `Date` DATE,
  `Assigned_Unit_ID` INT,
  `Created_At` DATETIME,
  PRIMARY KEY (`Task_ID`),
  INDEX (`Employee_ID`),
  CONSTRAINT `fk_Tasks_EmployeeID` FOREIGN KEY (`Employee_ID`) REFERENCES `Employees` (`Employee_ID`) ON DELETE CASCADE
);

-- Suppliers Table
CREATE TABLE `Suppliers` (
  `Supplier_ID` INT,
  `Supplier_Name` VARCHAR(100) NOT NULL,
  `Contact_Info` VARCHAR(150),
  `Location` VARCHAR(100),
  PRIMARY KEY (`Supplier_ID`)
);

-- Supplier_Crop_Materials Table
CREATE TABLE `Supplier_Crop_Materials` (
  `Supplier_ID` INT,
  `Crop_ID` INT,
  `Material_Type` VARCHAR(100),
  `Quantity` FLOAT,
  `Supply_Date` DATE,
  PRIMARY KEY (`Supplier_ID`, `Crop_ID`, `Material_Type`),
  INDEX (`Crop_ID`),
  CONSTRAINT `fk_SCM_SupplierID` FOREIGN KEY (`Supplier_ID`) REFERENCES `Suppliers` (`Supplier_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_SCM_CropID` FOREIGN KEY (`Crop_ID`) REFERENCES `Crops` (`Crop_ID`) ON DELETE CASCADE
);

-- Customers Table
CREATE TABLE `Customers` (
  `Customer_ID` INT,
  `Customer_Name` VARCHAR(100) NOT NULL,
  `Contact_Info` VARCHAR(150),
  `Region` VARCHAR(100),
  PRIMARY KEY (`Customer_ID`)
);

-- Orders Table
CREATE TABLE `Orders` (
  `Order_ID` INT,
  `Customer_ID` INT,
  `Order_Date` DATE,
  `Total_Amount` DECIMAL(10,2),
  `Created_At` DATETIME,
  PRIMARY KEY (`Order_ID`),
  INDEX (`Customer_ID`),
  CONSTRAINT `fk_Orders_CustomerID` FOREIGN KEY (`Customer_ID`) REFERENCES `Customers` (`Customer_ID`) ON DELETE CASCADE
);

-- Order_Products Table
CREATE TABLE `Order_Products` (
  `Order_ID` INT,
  `Product_ID` INT,
  `Quantity` INT,
  `Unit_Price` DECIMAL(10,2),
  PRIMARY KEY (`Order_ID`, `Product_ID`),
  INDEX (`Product_ID`),
  CONSTRAINT `fk_OP_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_OP_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `Products` (`Product_ID`) ON DELETE CASCADE
);

-- Certifications Table
CREATE TABLE `Certifications` (
  `Certification_ID` INT,
  `Farm_ID` INT,
  `Certification_Agency` VARCHAR(100),
  `Certification_Date` DATE,
  `Last_Verified` DATETIME,
  PRIMARY KEY (`Certification_ID`),
  INDEX (`Farm_ID`),
  CONSTRAINT `fk_Certifications_FarmID` FOREIGN KEY (`Farm_ID`) REFERENCES `Farms` (`Farm_ID`) ON DELETE CASCADE
);

-- Distributions Table
CREATE TABLE `Distributions` (
  `Distribution_ID` INT,
  `Order_ID` INT,
  `Vehicle_ID` VARCHAR(100),
  `Delivery_Date` DATE,
  `Destination` VARCHAR(100),
  PRIMARY KEY (`Distribution_ID`),
  INDEX (`Order_ID`),
  CONSTRAINT `fk_Distributions_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`) ON DELETE CASCADE
);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
