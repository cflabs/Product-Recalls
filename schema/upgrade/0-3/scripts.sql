ALTER TABLE `users` ADD COLUMN `category` INT  NOT NULL DEFAULT 0 AFTER `live`;
ALTER TABLE `recalls` ADD COLUMN `category_id` INT  NOT NULL AFTER `category`;
ALTER TABLE `recalls` ADD COLUMN `status` ENUM('active','removed','updated')  NOT NULL DEFAULT 'active' AFTER `source`;
ALTER TABLE `recalls` ADD COLUMN `status_text` VARCHAR(255)  NOT NULL AFTER `status`;
ALTER TABLE `recalls` ADD COLUMN `status_updated` TIMESTAMP  NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `status_text`;

CREATE TABLE  `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
