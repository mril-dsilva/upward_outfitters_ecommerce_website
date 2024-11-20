UPDATE products
   SET product_discontinued = TRUE,
       product_discount_pct = 0.50
 WHERE product_id = (?);