-- Nexabie — contact-form messages table.
--
-- Optional: the site creates this table automatically on first run when
-- DB_NAME is set in .envnexabie. Use this file if you prefer to set the
-- structure up by hand:
--   cPanel → phpMyAdmin → select your database → Import →
--   Choose file (this file) → Go

DROP TABLE IF EXISTS `inquiries`;

CREATE TABLE `inquiries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(190) NOT NULL DEFAULT '',
  `email` varchar(190) NOT NULL DEFAULT '',
  `service` varchar(120) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
