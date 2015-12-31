DROP TABLE IF EXISTS `motions`;

CREATE TABLE IF NOT EXISTS `motions` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `theme_id` integer NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `means` text NOT NULL,
  `created_at` datetime NOT NULL,
  `ended_at` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_approved` tinyint(1) NOT NULL,
  `score` varchar(6)
);

INSERT INTO `motions` (`id`, `theme_id`, `title`, `description`, `means`, `created_at`, `ended_at`, `author_id`, `is_active`, `is_approved`, `score`) VALUES(1, 1, 'Test des motions', 'test', 'test', '2015-09-23 13:57:10', '2015-10-01 13:57:10', 1, 0, 0, 0);