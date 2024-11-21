DELIMITER $$

DROP PROCEDURE IF EXISTS create_supplier_partner;
CREATE PROCEDURE create_supplier_partner (p_id INT, p_name VARCHAR(100), p_address_id INT, p_phone VARCHAR(15), p_email VARCHAR(100))
BEGIN

    INSERT INTO partners (partner_id, partner_name, address_id, partner_phone_number, partner_email)
    VALUES (p_id, p_name, p_address_id, p_phone, p_email);
    
    -- adding the id of this partner to the suppliers (subset of partners) table as well.
    INSERT INTO suppliers (partner_id)
    VALUES (p_id);

END$$

DELIMITER ;

CALL create_supplier_partner(?, ?, ?, ?, ?);
