-- Associate a products that are returned to the specified transaction,
-- with its timestamp being when this query is called.
INSERT INTO return_products (transaction_id, product_id, replacement_product_id, return_timestamp)
VALUES (?, ?, ?, CURRENT_TIMESTAMP());
