USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS retrieve_product$$
CREATE PROCEDURE retrieve_product (category INT)
BEGIN
    -- If category is set, then filter based on it. If it isn't, then just show all
    IF category != -1 THEN
        SELECT product_id, product_name, product_category_name, partner_name, product_brand_name, 
               product_sale_price, product_description, 
               product_warranty_length, product_discontinued, product_discount_pct, 
               product_length, product_size, product_shoe_size, product_capacity
          FROM products
               LEFT OUTER JOIN products_length
               USING (product_id)
               LEFT OUTER JOIN products_size
               USING (product_id)
               LEFT OUTER JOIN products_shoe_size
               USING (product_id)
               LEFT OUTER JOIN products_capacity
               USING (product_id) 
               LEFT OUTER JOIN product_categories
               USING (product_category_id)
               LEFT OUTER JOIN product_brands
               USING (product_brand_id)
               LEFT OUTER JOIN partners
               USING (partner_id)
         WHERE product_category_id = category;
    ELSE
        SELECT product_id, product_name, product_category_name, partner_name, product_brand_name, 
               product_sale_price, product_description, 
               product_warranty_length, product_discontinued, product_discount_pct, 
               product_length, product_size, product_shoe_size, product_capacity
          FROM products
               LEFT OUTER JOIN products_length
               USING (product_id)
               LEFT OUTER JOIN products_size
               USING (product_id)
               LEFT OUTER JOIN products_shoe_size
               USING (product_id)
               LEFT OUTER JOIN products_capacity
               USING (product_id) 
               LEFT OUTER JOIN product_categories
               USING (product_category_id)
               LEFT OUTER JOIN product_brands
               USING (product_brand_id)
               LEFT OUTER JOIN partners
               USING (partner_id); 
    END IF;

END;
$$

DELIMITER ;