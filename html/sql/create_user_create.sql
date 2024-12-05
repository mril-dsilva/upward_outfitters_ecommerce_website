USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS create_user;
CREATE PROCEDURE create_user (u_name VARCHAR(50), u_password VARCHAR(255))

BEGIN
    SET @hashed_password = PASSWORD(u_password);
    
    SET @username_exists = EXISTS (SELECT * FROM users WHERE user_username = u_name);
    
    -- IF this username already exists, end this procedure early by returning a 0
    IF @username_exists THEN
        SELECT 0;
    ELSE

        INSERT INTO partners (partner_name)
            VALUES (u_name);
        
        SET @partner_id = LAST_INSERT_ID();

        INSERT INTO customers (partner_id)
            VALUES (@partner_id);

        INSERT INTO users (user_username, user_password, partner_id)
            VALUES (u_name, @hashed_password, @partner_id);

        SELECT 1;
    END IF;

END$$

DELIMITER ;