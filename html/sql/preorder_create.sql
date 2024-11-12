START TRANSACTION;
    -- Create a transaction for the preorder.
    -- Assuming preorders are not handled by any employee, and no shipment info yet
    INSERT INTO transactions (transaction_timestamp, partner_id, location_id)
    VALUES (?, ?, ?);

    -- Create a new entry in the preorders table that references the inserted transaction
    INSERT INTO preorders(transaction_id) VALUES (LAST_INSERT_ID());
COMMIT;
