-- Changes the description and timestamp of a warranty claim associated with the specified transaction.
UPDATE warranty_products
   SET warranty_claim_description = ?,
       warranty_claim_timestamp = ?
 WHERE transaction_id = ? AND product_id = ?
