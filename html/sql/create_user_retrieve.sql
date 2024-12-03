USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS retrieve_user;
CREATE PROCEDURE retrieve_user (u_name VARCHAR(50), u_password VARCHAR(255))

BEGIN
    SET @hashed_password = PASSWORD(u_password);

    SELECT user_username, user_role, partner_id
      FROM users
     WHERE user_username = u_name AND user_password = @hashed_password;
END$$

DELIMITER ;