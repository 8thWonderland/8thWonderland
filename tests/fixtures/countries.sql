DROP TABLE IF EXISTS `countries`;

CREATE TABLE IF NOT EXISTS `countries` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `code` varchar(2) NOT NULL,
  `label` varchar(25) NOT NULL
);

INSERT INTO `countries` (`id`, `code`, `label`) VALUES(1, 'fr', 'countries.france');
INSERT INTO `countries` (`id`, `code`, `label`) VALUES(2, 'us', 'countries.united_states');
INSERT INTO `countries` (`id`, `code`, `label`) VALUES(3, 'en', 'countries.united_kingdom');