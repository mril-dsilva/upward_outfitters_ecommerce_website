-- Associate a products that are warranty claimed to the specified transaction,
-- with its timestamp being when this query is called.
INSERT INTO warranty_products (transaction_id, product_id, warranty_claim_description, warranty_claim_timestamp)
VALUES (?, ?, ?, CURRENT_TIMESTAMP());
