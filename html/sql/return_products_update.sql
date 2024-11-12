-- Changes the replacement product and timestamp of a return associated with the specified transaction.
UPDATE return_products
   SET replacement_product_id = ?,
       return_timestamp = ?
 WHERE transaction_id = ? AND product_id = ?
