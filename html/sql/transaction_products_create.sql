-- Associate addition product(s) to the specified transaction
INSERT INTO transaction_products (transaction_id, product_id, transaction_product_quantity, transaction_product_price)
VALUES (?, ?, ?, ?);
