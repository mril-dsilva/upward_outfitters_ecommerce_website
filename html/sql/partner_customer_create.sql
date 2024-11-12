DELIMITER $$

DROP PROCEDURE IF EXISTS create_customer_partner;
CREATE PROCEDURE create_customer_partner (p_partner_id INT, p_partner_name VARCHAR(100), p_address_id INT, p_phone VARCHAR(15), p_email VARCHAR(100))
BEGIN

    INSERT INTO partners (partner_id, partner_name, address_id, partner_phone_number, partner_email)
    VALUES (p_partner_id, p_partner_name, p_address_id, p_phone, p_email);
    
    -- adding the id of this partner to the customers (subset of partners) table as well.
    INSERT INTO customers (partner_id)
    VALUES (p_partner_id);
    
END$$

DELIMITER ;

CALL create_customer_partner(?, ?, ?, ?, ?);
