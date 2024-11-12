DROP DATABASE IF EXISTS upward_outfitters;
CREATE DATABASE upward_outfitters;

USE upward_outfitters;


-- States Table
CREATE TABLE states (
    state_abbreviation CHAR(2) PRIMARY KEY,
    state_name VARCHAR(100) NOT NULL
);


-- Addresses Table
CREATE TABLE addresses (
    address_id INT AUTO_INCREMENT  PRIMARY KEY,
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255),
    address_city VARCHAR(100) NOT NULL,
    state_abbreviation CHAR(2) NOT NULL,
    address_zip_code VARCHAR(10) NOT NULL,
    FOREIGN KEY (state_abbreviation) REFERENCES states(state_abbreviation)
);

-- Partners Table
CREATE TABLE partners (
    partner_id INT AUTO_INCREMENT PRIMARY KEY,
    partner_name VARCHAR(255) NOT NULL,
    address_id INT,
    partner_phone_number VARCHAR(15),
    partner_email VARCHAR(255),
    FOREIGN KEY (address_id) REFERENCES addresses(address_id)
);

-- Suppliers Table
CREATE TABLE suppliers (
    partner_id INT PRIMARY KEY,
    FOREIGN KEY (partner_id) REFERENCES partners(partner_id)
);

-- Customers Table
CREATE TABLE customers (
    partner_id INT PRIMARY KEY,
    FOREIGN KEY (partner_id) REFERENCES partners(partner_id)
);

-- Product Categories Table
CREATE TABLE product_categories (
    product_category_id INT AUTO_INCREMENT PRIMARY KEY,
    product_category_name VARCHAR(255) NOT NULL
);

-- Product Brands Table
CREATE TABLE product_brands (
    product_brand_id INT AUTO_INCREMENT PRIMARY KEY,
    product_brand_name VARCHAR(255) NOT NULL
);

-- Products Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    product_sale_price DECIMAL(10, 2) NOT NULL,
    product_description TEXT,
    product_warranty_length INT NOT NULL DEFAULT 24,
    product_brand_id INT,
    product_category_id INT,
    product_discontinued BOOLEAN NOT NULL DEFAULT FALSE,
    product_discount_pct DECIMAL(5, 2) NOT NULL DEFAULT 0.0,
    partner_id INT,
    FOREIGN KEY (product_brand_id) REFERENCES product_brands(product_brand_id),
    FOREIGN KEY (product_category_id) REFERENCES product_categories(product_category_id),
    FOREIGN KEY (partner_id) REFERENCES partners(partner_id)
);


-- Product Lengths Table
CREATE TABLE products_length (
    product_id INT,
    product_length VARCHAR(50),
    PRIMARY KEY (product_id, product_length),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Product Shoe Sizes Table
CREATE TABLE products_shoe_size (
    product_id INT,
    product_shoe_size VARCHAR(50),
    PRIMARY KEY (product_id, product_shoe_size),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Product Capacity Table
CREATE TABLE products_capacity (
    product_id INT,
    product_capacity VARCHAR(50),
    PRIMARY KEY (product_id, product_capacity),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Product Sizes Table to list possible sizes
CREATE TABLE product_sizes (
    product_size VARCHAR(50) PRIMARY KEY
);

-- Product Sizes Table
CREATE TABLE products_size (
    product_id INT,
    product_size VARCHAR(50),
    PRIMARY KEY (product_id, product_size),
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (product_size) REFERENCES product_sizes(product_size)
);

-- Locations Table
CREATE TABLE locations (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    address_id INT,
    location_active BOOLEAN NOT NULL,
    FOREIGN KEY (address_id) REFERENCES addresses(address_id)
);

-- Stores Table
CREATE TABLE stores (
    location_id INT PRIMARY KEY,
    store_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
);

-- Employees Table
CREATE TABLE employees (
    employee_id INT PRIMARY KEY,
    employee_name VARCHAR(255) NOT NULL,
    location_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
);

-- Shipments Table
CREATE TABLE shipments (
    shipment_id INT PRIMARY KEY,
    shipment_dispatch_timestamp TIMESTAMP NOT NULL,
    shipment_tracking_number VARCHAR(50)
);


