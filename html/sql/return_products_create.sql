-- Associate a products that are returned to the specified transaction.
INSERT INTO return_products (transaction_id, product_id, replacement_product_id, return_timestamp)
VALUES (?, ?, ?, ?);
