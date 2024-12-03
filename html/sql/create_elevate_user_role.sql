USE upward_outfitters;

DELIMITER $$

DROP PROCEDURE IF EXISTS elevate_user_role;
CREATE PROCEDURE elevate_user_role (u_id INT)

BEGIN
    UPDATE users
       SET user_role = 1
     WHERE user_id = u_id;
END$$


DELIMITER ;