-- Changes the amount and price of a product in the specified transaction.
UPDATE transaction_products
   SET transaction_product_quantity= ?,
       transaction_product_price = ?
 WHERE transaction_id = ? AND product_id = ?
