-- Associate a products that are warranty claimed to the specified transaction.
INSERT INTO warranty_products (transaction_id, product_id, warranty_claim_description, warranty_claim_timestamp)
VALUES (?, ?, ?, ?);
