SET FOREIGN_KEY_CHECKS=0;


DROP TABLE IF EXISTS /*TABLE_PREFIX*/t_invoice;
CREATE TABLE /*TABLE_PREFIX*/t_invoice (
  pk_i_id INT NOT NULL AUTO_INCREMENT,
  fk_i_user_id INT(11),
  s_identifier VARCHAR(100),
  s_title VARCHAR(50),
  s_from VARCHAR(500),
  s_to VARCHAR(500),
  s_email VARCHAR(100),
  dt_date DATE,
  dt_due_date DATE,
  f_paid DECIMAL(20, 8),
  f_amount DECIMAL(20, 8),
  f_balance DECIMAL(20, 8),
  f_discount DECIMAL(20, 8),
  f_shipping DECIMAL(20, 8),
  f_fee DECIMAL(20, 8),
  f_tax DECIMAL(20, 8),
  s_currency VARCHAR(10),
  s_notes VARCHAR(500),
  s_terms VARCHAR(500),
  i_payment_id INT(20),
  s_source VARCHAR(50),
  s_cart VARCHAR(500), 
  s_comment VARCHAR(100),
  s_file VARCHAR(100) NULL,
  i_status INT(3),

  PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';


DROP TABLE IF EXISTS /*TABLE_PREFIX*/t_invoice_item;
CREATE TABLE /*TABLE_PREFIX*/t_invoice_item (
  pk_i_id INT NOT NULL AUTO_INCREMENT,
  fk_i_invoice_id INT,
  s_description VARCHAR(100),
  i_quantity INT(10),
  f_rate DECIMAL(20, 8),

  PRIMARY KEY (pk_i_id),
  FOREIGN KEY (fk_i_invoice_id) REFERENCES /*TABLE_PREFIX*/t_invoice (pk_i_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';


DROP TABLE IF EXISTS /*TABLE_PREFIX*/t_invoice_user;
CREATE TABLE /*TABLE_PREFIX*/t_invoice_user (
  fk_i_user_id INT(11) NOT NULL,
  s_vat_number VARCHAR(100) DEFAULT NULL,
  s_vat_number_local VARCHAR(100) DEFAULT NULL,
  i_vat_number_verified INT(3) DEFAULT NULL,
  s_header VARCHAR(300) DEFAULT NULL,
  s_ship_to VARCHAR(300) DEFAULT NULL,

  PRIMARY KEY (fk_i_user_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';


SET FOREIGN_KEY_CHECKS=1;