-- Adds a transaction with its timestamp being when this query is called.
INSERT INTO transactions (transaction_timestamp, partner_id, shipment_id, employee_id, location_id)
VALUES (CURRENT_TIMESTAMP(), ?, ?, ?, ?);
