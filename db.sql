CREATE DATABASE IF NOT EXISTS `customercontrol` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `customercontrol`;

CREATE TABLE `customercontrol`.`operators`
( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
, `name` VARCHAR(255) NOT NULL
, `username` VARCHAR(255) NOT NULL
, `password` VARCHAR(64) NOT NULL
, PRIMARY KEY (`id`)
, UNIQUE `idx_operators_username` (`username`)
) ENGINE = InnoDB;

CREATE TABLE `customercontrol`.`customers`
( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
, `name` VARCHAR(255) NOT NULL
, `address` VARCHAR(512) NOT NULL
, `datecreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
, `username` VARCHAR(255) NOT NULL
, `password` VARCHAR(64) NOT NULL
, `balance` DECIMAL(8,2) NOT NULL
, `deleted` TINYINT(1) NOT NULL DEFAULT 0
, PRIMARY KEY (`id`)
, UNIQUE `idx_customers_username` (`username`)
) ENGINE = InnoDB;

CREATE TABLE `customercontrol`.`invoices`
( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
, `customerid` BIGINT UNSIGNED NOT NULL
, `description` VARCHAR(512) NULL
, `datecreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
, `amount` DECIMAL(8,2) NOT NULL DEFAULT 0
, `paid` TINYINT NOT NULL DEFAULT 0
, PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE `customercontrol`.`invoices`
ADD CONSTRAINT `fk_invoices_customerid`
FOREIGN KEY (`customerid`)
REFERENCES `customercontrol`.`customers`(`id`)
ON DELETE NO ACTION
ON UPDATE CASCADE;

CREATE TABLE `customercontrol`.`invoicelines`
( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
, `invoiceid` BIGINT UNSIGNED NOT NULL
, `description` VARCHAR(512) NULL
, `datecreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
, `amount` DECIMAL(8,2) NOT NULL
, PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE `customercontrol`.`invoicelines`
ADD FOREIGN KEY fk_invoicelines_invoiceid(`invoiceid`)
REFERENCES `customercontrol`.`invoices`(`id`)
ON DELETE NO ACTION
ON UPDATE CASCADE;

CREATE TABLE `customercontrol`.`payments`
( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
, `customerid` BIGINT UNSIGNED NOT NULL
, `datecreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
, `amount` DECIMAL(8,2) NOT NULL
, PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE `customercontrol`.`payments`
ADD FOREIGN KEY fk_payments_customerid(`customerid`)
REFERENCES `customercontrol`.`customers`(`id`)
ON DELETE NO ACTION
ON UPDATE CASCADE;

CREATE TABLE `customercontrol`.`invoicepayments`
( `invoiceid` BIGINT UNSIGNED NOT NULL
, `paymentid` BIGINT UNSIGNED NOT NULL
, PRIMARY KEY (`invoiceid`, `paymentid`)
) ENGINE = InnoDB;

ALTER TABLE `customercontrol`.`invoicepayments`
ADD FOREIGN KEY fk_invoicepayments_invoiceid(`invoiceid`)
REFERENCES `customercontrol`.`invoices`(`id`)
ON DELETE NO ACTION
ON UPDATE CASCADE;

ALTER TABLE `customercontrol`.`invoicepayments`
ADD FOREIGN KEY fk_invoicepayments_invoiceid(`paymentid`)
REFERENCES `customercontrol`.`payments`(`id`)
ON DELETE NO ACTION
ON UPDATE CASCADE;

CREATE TABLE `customercontrol`.`audit`
( `id` BIGINT UNSIGNED AUTO_INCREMENT
, `recordid` BIGINT UNSIGNED
, `customerid` BIGINT UNSIGNED
, `event` VARCHAR(50)
, `table`  VARCHAR(50)
, `description` VARCHAR(512)
, `amount` DECIMAL(8,2)
, `auditdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
, PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DELIMITER $$
/*
 * Update the customer's balance whenever the invoice changes
 */
CREATE TRIGGER after_invoices_update
 AFTER UPDATE ON invoices
 FOR EACH ROW
BEGIN
 DECLARE `difference` DECIMAL(8,2);
 SET `difference` = NEW.amount - OLD.amount;

 if (`difference` <> 0) THEN
  UPDATE `customercontrol`.`customers`
   SET `balance` = `balance` - `difference`
   WHERE `id` = NEW.customerid;
 END IF;
END$$

/*
 * Increment the invoice's amount when a new invoice line is inserted
 */
CREATE TRIGGER after_invoicelines_insert
 AFTER INSERT ON invoicelines
 FOR EACH ROW
BEGIN
 UPDATE `customercontrol`.`invoices`
  SET `amount` = `amount` + NEW.amount
  WHERE `id` = NEW.invoiceid;

 INSERT INTO `customercontrol`.`audit`
  (`recordid`, `event`, `table`, `description`, `amount`)
 VALUES
  (NEW.id, "insert", "invoicelines", NEW.description, NEW.amount);
END$$

/*
 * Decrement the invoice's amount when a new invoice line is deleted
 */
CREATE TRIGGER after_invoicelines_delete
 AFTER DELETE ON invoicelines
 FOR EACH ROW
BEGIN
 UPDATE `customercontrol`.`invoices`
  SET `amount` = `amount` - OLD.amount
  WHERE `id` = OLD.invoiceid;

 INSERT INTO `customercontrol`.`audit`
  (`recordid`, `event`, `table`, `description`, `amount`)
 VALUES
  (OLD.id, "delete", "invoicelines", OLD.description, OLD.amount);
END$$

/*
 * Update the invoice's amount when a new invoice line is modified
 */
CREATE TRIGGER after_invoicelines_update
 AFTER UPDATE ON invoicelines
 FOR EACH ROW
BEGIN
 DECLARE `difference` DECIMAL(8,2);
 SET `difference` = NEW.amount - OLD.amount;

 if (`difference` <> 0) THEN
  UPDATE `customercontrol`.`invoices`
   SET `amount` = `amount` + `difference`
   WHERE `id` = NEW.invoiceid;
 END IF;

 if (`difference` <> 0 OR NEW.description <> OLD.description) THEN
  INSERT INTO `customercontrol`.`audit`
   (`recordid`, `event`, `table`, `description`, `amount`)
  VALUES
   (NEW.id, "update", "invoicelines", NEW.description, NEW.amount);
 END IF;
END$$

/*
 * Increment the customer's balance when a new payment is inserted
 */
CREATE TRIGGER after_payments_insert
 AFTER INSERT ON payments
 FOR EACH ROW
BEGIN
 UPDATE `customercontrol`.`customers`
  SET `balance` = `balance` + NEW.amount
  WHERE `id` = NEW.customerid;

 INSERT INTO `customercontrol`.`audit`
  (`recordid`, `event`, `table`, `customerid`, `amount`)
 VALUES
  (NEW.id, "insert", "payments", NEW.customerid, NEW.amount);
END$$

/*
 * Decrement the customer's balance when a new payment is deleted
 */
CREATE TRIGGER after_payments_delete
 AFTER DELETE ON payments
 FOR EACH ROW
BEGIN
 UPDATE `customercontrol`.`customers`
  SET `balance` = `balance` - OLD.amount
  WHERE `id` = OLD.customerid;

 INSERT INTO `customercontrol`.`audit`
  (`recordid`, `event`, `table`, `customerid`, `amount`)
 VALUES
  (OLD.id, "delete", "payments", OLD.customerid, OLD.amount);
END$$

/*
 * Update the customers's balance when a new payment is modified
 */
CREATE TRIGGER after_payments_update
 AFTER UPDATE ON payments
 FOR EACH ROW
BEGIN
 DECLARE `difference` DECIMAL(8,2);
 SET `difference` = NEW.amount - OLD.amount;

 if (`difference` <> 0) THEN
  UPDATE `customercontrol`.`customers`
   SET `balance` = `balance` + `difference`
   WHERE `id` = OLD.customerid;

  INSERT INTO `customercontrol`.`audit`
   (`recordid`, `event`, `table`, `customerid`, `amount`)
  VALUES
   (NEW.id, "update", "payments", NEW.customerid, NEW.amount);
 END IF;
END$$
DELIMITER ;
