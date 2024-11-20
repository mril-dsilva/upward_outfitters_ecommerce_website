USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS delete_product$$
CREATE PROCEDURE delete_product (id INT)
BEGIN
    DELETE FROM products_length
      WHERE product_id = id;
    
    DELETE FROM products_size
      WHERE product_id = id;
    
    DELETE FROM products_shoe_size
      WHERE product_id = id;
    
    DELETE FROM products_capacity
      WHERE product_id = id;

    DELETE FROM products
      WHERE product_id = id;

END;
$$

DELIMITER ;