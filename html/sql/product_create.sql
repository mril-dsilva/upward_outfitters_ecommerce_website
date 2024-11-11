DELIMITER $$

DROP PROCEDURE IF EXISTS create_product;
CREATE PROCEDURE create_product (p_id INT, p_name VARCHAR(100), p_price INT, p_desc VARCHAR(1000), p_warranty_length INT,
    p_brand INT, p_category INT, p_discontinued BOOLEAN, p_partner_id INT, p_discount_pct DECIMAL,
    p_length INT, p_size VARCHAR(10), p_shoe_size INT, p_capacity VARCHAR(50))

BEGIN
    -- Create base product entry
    INSERT INTO products (product_id, product_name, product_sale_price, product_description, product_warranty_length,
        product_brand_id, product_category_id, product_discontinued, partner_id, product_discount_pct)
         VALUES (p_id, p_name, p_price, p_desc, p_warranty_length, p_brand, p_category, p_discontinued, p_partner_id,
                p_discount_pct);

    -- If a product has a variant value, add it to that subset table
    IF p_length IS NOT NULL THEN
        INSERT INTO products_length (product_id, product_length)
             VALUES (p_id, p_length);
    END IF;

    IF p_size IS NOT NULL THEN
        INSERT INTO products_size (product_id, product_size)
             VALUES (p_id, p_size);
    END IF;

    IF p_shoe_size IS NOT NULL THEN
        INSERT INTO products_shoe_size (product_id, product_shoe_size)
             VALUES (p_id, p_shoe_size);
    END IF;

    IF p_capacity IS NOT NULL THEN
        INSERT INTO products_capacity (product_id, product_capacity)
             VALUES (p_id, p_capacity);
    END IF;

END$$

DELIMITER ;

CALL create_product(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);