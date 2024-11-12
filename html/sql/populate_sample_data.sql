-- Using insert statements to populate all tables for some SAMPLE DATA

--states
INSERT INTO states (state_abbreviation, state_name) VALUES ('CA', 'California');
INSERT INTO states (state_abbreviation, state_name) VALUES ('TX', 'Texas');

--addresses
INSERT INTO addresses (address_line_1, address_line_2, address_city, state_abbreviation, address_zip_code) VALUES ('123 Climbing St', NULL, 'Los Angeles', 'CA', '90001');
INSERT INTO addresses (address_line_1, address_line_2, address_city, state_abbreviation, address_zip_code) VALUES ('456 Hiking Rd', NULL, 'Austin', 'TX', '73301');

--partners
INSERT INTO partners (partner_name, address_id, partner_phone_number, partner_email) VALUES ('PeakTools Supplier', 1, '123-456-7890', 'contact@peaktools.com');
INSERT INTO partners (partner_name, address_id, partner_phone_number, partner_email) VALUES ('John Doe', 2, '987-654-3210', 'johndoe@fmail.com');

    --suppliers
    INSERT INTO suppliers (partner_id) VALUES (1);

    --customers
    INSERT INTO customers (partner_id) VALUES (2);

--product categories
INSERT INTO product_categories (product_category_name) VALUES ('Climbing Equipment');
INSERT INTO product_categories (product_category_name) VALUES ('Outdoor Clothing');
INSERT INTO product_categories (product_category_name) VALUES ('Camping Equipment');
INSERT INTO product_categories (product_category_name) VALUES ('Camping Accessories');

--product brands
INSERT INTO product_brands (product_brand_name) VALUES ('PeakTools');
INSERT INTO product_brands (product_brand_name) VALUES ('GripMaster');
INSERT INTO product_brands (product_brand_name) VALUES ('SafeClimb');
INSERT INTO product_brands (product_brand_name) VALUES ('RockTech');
INSERT INTO product_brands (product_brand_name) VALUES ('AlpineWear');
INSERT INTO product_brands (product_brand_name) VALUES ('TrailGear');
INSERT INTO product_brands (product_brand_name) VALUES ('CampComfort');
INSERT INTO product_brands (product_brand_name) VALUES ('LightTrail');
INSERT INTO product_brands (product_brand_name) VALUES ('PeakTech');

--products
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Climbing Rope 9.8mm (60m)', 150.0, 'Durable dynamic rope for sport climbing', 1, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Climbing Rope 9.8mm (70m)', 175.0, 'Durable dynamic rope for sport climbing', 1, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Climbing Rope 9.8mm (80m)', 200.0, 'Durable dynamic rope for sport climbing', 1, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Harness SecureMax', 80.0, 'Comfortable harness with adjustable straps', 2, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Climbing Helmet AeroShield', 100.0, 'Lightweight climbing helmet with ventilation', 3, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Carabiner LockSafe', 12.0, 'Twist-lock carabiner for secure connections', 3, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Belay Device SmoothDescent', 35.0, 'Auto-locking belay device', 4, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Quickdraw Set (12cm)', 45.0, 'Lightweight quickdraw set for sport climbing', 1, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Quickdraw Set (18cm)', 50.0, 'Lightweight quickdraw set for sport climbing', 1, 1);
INSERT INTO products (product_name, product_sale_price, product_description, product_brand_id, product_category_id) VALUES ('Quickdraw Set (24cm)', 55.0, 'Lightweight quickdraw set for sport climbing', 1, 1);


        -- Adding product subtable info (lengths, sizes, shoe sizes, capacity)
    INSERT INTO products_length (product_id, product_length) VALUES (1, '60m');
    INSERT INTO products_length (product_id, product_length) VALUES (2, '70m');
    INSERT INTO products_length (product_id, product_length) VALUES (3, '80m');

    INSERT INTO product_sizes(product_size) VALUES ('M');
    INSERT INTO product_sizes(product_size) VALUES ('S');
    INSERT INTO product_sizes(product_size) VALUES ('L');

    INSERT INTO products_size (product_id, product_size) VALUES (4, 'M');
    INSERT INTO products_size (product_id, product_size) VALUES (4, 'L');
    INSERT INTO products_size (product_id, product_size) VALUES (5, 'M');
    INSERT INTO products_size (product_id, product_size) VALUES (5, 'L');

    INSERT INTO products_length (product_id, product_length) VALUES (8, '12cm');
    INSERT INTO products_length (product_id, product_length) VALUES (9, '18cm');
    INSERT INTO products_length (product_id, product_length) VALUES (10, '24cm');