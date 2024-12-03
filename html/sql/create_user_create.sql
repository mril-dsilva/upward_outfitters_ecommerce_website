USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS create_user;
CREATE PROCEDURE create_user (u_name VARCHAR(50), u_password VARCHAR(255))

BEGIN
    SET @hashed_password = PASSWORD(u_password);

    INSERT INTO partners (partner_name)
         VALUES (u_name);
    
    SET @partner_id = LAST_INSERT_ID();

    INSERT INTO users (user_username, user_password, partner_id)
         VALUES (u_name, @hashed_password, @partner_id);

END$$

DELIMITER ;