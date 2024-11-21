-- Using insert statements to populate all tables for some SAMPLE DATA

--states
INSERT INTO states (state_abbreviation, state_name) VALUES 
('CA', 'California'),
('TX', 'Texas');

--addresses
INSERT INTO addresses (address_line_1, address_line_2, address_city, state_abbreviation, address_zip_code) VALUES 
('123 Climbing St', NULL, 'Los Angeles', 'CA', '90001'),
('456 Hiking Rd', NULL, 'Austin', 'TX', '73301');

--partners
INSERT INTO partners (partner_name, address_id, partner_phone_number, partner_email) VALUES 
('PeakTools Supplier', 1, '123-456-7890', 'contact@peaktools.com'),
('John Doe', 2, '987-654-3210', 'johndoe@fmail.com');

--suppliers
INSERT INTO suppliers (partner_id) VALUES 
(1);

--customers
INSERT INTO customers (partner_id) VALUES 
(2);

--product categories
INSERT INTO product_categories (product_category_name) VALUES 
('Climbing Equipment'),
('Outdoor Clothing'),
('Camping Equipment'),
('Camping Accessories');

--product brands
INSERT INTO product_brands (product_brand_name) VALUES 
('PeakTools'),
('GripMaster'),
('SafeClimb'),
('RockTech'),
('AlpineWear'),
('TrailGear'),
('CampComfort'),
('LightTrail'),
('PeakTech');

--products
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES 
('Climbing Rope 9.8mm (60m)', 150.0, 'Durable dynamic rope for sport climbing', 1, 1),
('Climbing Rope 9.8mm (70m)', 175.0, 'Durable dynamic rope for sport climbing', 1, 1),
('Climbing Rope 9.8mm (80m)', 200.0, 'Durable dynamic rope for sport climbing', 1, 1),
('Harness SecureMax', 80.0, 'Comfortable harness with adjustable straps', 2, 1),
('Climbing Helmet AeroShield', 100.0, 'Lightweight climbing helmet with ventilation', 3, 1),
('Carabiner LockSafe', 12.0, 'Twist-lock carabiner for secure connections', 3, 1),
('Belay Device SmoothDescent', 35.0, 'Auto-locking belay device', 4, 1),
('Quickdraw Set (12cm)', 45.0, 'Lightweight quickdraw set for sport climbing', 1, 1),
('Quickdraw Set (18cm)', 50.0, 'Lightweight quickdraw set for sport climbing', 1, 1),
('Quickdraw Set (24cm)', 55.0, 'Lightweight quickdraw set for sport climbing', 1, 1);

    -- Adding product subtable info 
    INSERT INTO products_length (product_id, product_length) VALUES 
    (1, '60m'),
    (2, '70m'),
    (3, '80m'),
    (8, '12cm'),
    (9, '18cm'),
    (10, '24cm');
    
    INSERT INTO product_sizes(product_size) VALUES 
    ('M'),
    ('S'),
    ('L');
    
    INSERT INTO products_size (product_id, product_size) VALUES 
    (4, 'M'),
    (4, 'L'),
    (5, 'M'),
    (5, 'L');

INSERT INTO transactions (transaction_timestamp, partner_id, shipment_id, location_id) VALUES
(DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 7 DAY), 1, NULL, NULL),
(DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 6 DAY), 2, NULL, NULL);

INSERT INTO transaction_products (transaction_id, product_id, transaction_product_quantity, transaction_product_price) VALUES
(1, 1, 4, 150.0),
(1, 4, 2, 80.0),
(2, 5, 1, 100.0);
