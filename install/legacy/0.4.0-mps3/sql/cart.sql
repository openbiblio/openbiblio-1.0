CREATE TABLE `cart` (
  `sess_id` text NOT NULL,
  `name` char(16) NOT NULL,
  `id` integer NOT NULL,
  PRIMARY KEY (`sess_id`(64), `name`, `id`)
);
