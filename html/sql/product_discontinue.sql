DELIMITER $$

DROP PROCEDURE IF EXISTS discontinue_product;
CREATE PROCEDURE discontinue_product (p_id INT, discount DECIMAL)

BEGIN
    -- If there is not set discount, set it to a default set here
    IF discount IS NOT NULL THEN
        UPDATE products
           SET product_discontinued = TRUE,
               product_discount_pct = discount
         WHERE product_id = p_id

END$$

DELIMITER ;

CALL discontinue_product(?, ?);