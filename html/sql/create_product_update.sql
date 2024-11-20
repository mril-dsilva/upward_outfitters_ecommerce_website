USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS update_product;
CREATE PROCEDURE update_product (p_id INT, p_name VARCHAR(100), p_price DECIMAL, p_desc VARCHAR(1000), p_warranty_length INT,
    p_brand_id INT, p_category_id INT, p_discontinued BOOLEAN, p_partner_id INT, p_discount_pct DECIMAL,
    p_length VARCHAR(50), p_size VARCHAR(50), p_shoe_size VARCHAR(50), p_capacity VARCHAR(50))

BEGIN
    -- Update base product entry
    UPDATE products
       SET product_name = p_name, 
           product_sale_price = p_price, 
           product_description = p_desc, 
           product_warranty_length = p_warranty_length,
           product_brand_id = p_brand_id, 
           product_category_id = p_category_id, 
           product_discontinued = p_discontinued, 
           partner_id = p_partner_id, 
           product_discount_pct = p_discount_pct
     WHERE product_id = p_id;

    -- If a product has a variant value, updated its subset table
    IF p_length IS NOT NULL THEN
        UPDATE products_lengths
           SET product_length = p_length
         WHERE product_id = p_id;
    END IF;

    IF p_size IS NOT NULL THEN
        UPDATE products_size
           SET product_size = p_size
         WHERE product_id = p_id;
    END IF;

    IF p_shoe_size IS NOT NULL THEN
        UPDATE products_shoe_size
           SET product_shoe_size = p_shoe_size
         WHERE product_id = p_id;
    END IF;

    IF p_capacity IS NOT NULL THEN
        UPDATE products_capacity
           SET product_capacity = p_capacity
         WHERE product_id = p_id;
    END IF;

END$$

DELIMITER ;