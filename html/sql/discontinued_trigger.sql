USE upward_outfitters;

DELIMITER $$

-- Update trigger
DROP TRIGGER IF EXISTS check_for_discontinue$$
CREATE TRIGGER check_for_discontinue
BEFORE UPDATE
    ON products FOR EACH ROW

BEGIN
    SET @new_discontinued_status = NEW.product_discontinued;
    SET @old_discontinued_status = OLD.product_discontinued;

    IF @old_discontinued_status = FALSE AND @new_discontinued_status = TRUE THEN
        SET NEW.product_discount_pct = 0.75;
    END IF;
END$$

DELIMITER ;