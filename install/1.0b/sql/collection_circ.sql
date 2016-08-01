CREATE TABLE IF NOT EXISTS %prfx%.collection_circ (
  code smallint NOT NULL AUTO_INCREMENT,
  days_due_back smallint unsigned NOT NULL,
  minutes_due_back int unsigned NOT NULL,
  due_date_calculator varchar(30) NOT NULL DEFAULT 'simple',
  important_date timestamp NULL DEFAULT NULL,
  important_date_purpose varchar(30) NOT NULL DEFAULT 'not enabled',
  regular_late_fee decimal(4,2) NOT NULL,
  number_of_minutes_between_fee_applications int NOT NULL DEFAULT 1440,
  number_of_minutes_in_grace_period int DEFAULT 0,
  pre_closing_padding int DEFAULT 0,
  PRIMARY KEY (code),
  CONSTRAINT chk_time_between_fee_applications CHECK (number_of_minutes_between_fee_applications>0)
);
INSERT INTO %prfx%.collection_circ (code, days_due_back, regular_late_fee) VALUES
(1, 30, 0.01),
(2, 14, 0.10),
(3, 7, 0.25),
(4, 7, 0.10),
(5, 7, 0.10),
(11, 7, 0.10),
(12, 7, 0.10),
(13, 7, 0.10),
(14, 7, 0.10),
(15, 7, 0.10),
(6, 7, 0.10),
(18, 7, 0.00);
