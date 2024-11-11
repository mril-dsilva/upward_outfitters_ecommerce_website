UPDATE products
   SET product_name = (?), 
       product_sale_price = (?), 
       product_description = (?), 
       product_warranty_length = (?),
       product_brand_id = (?), 
       product_category_id = (?), 
       product_discontinued = (?), 
       partner_id = (?), 
       product_discount_pct = (?)
 WHERE product_id = (?)