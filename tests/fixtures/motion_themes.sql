DROP TABLE IF EXISTS `motion_themes`;

CREATE TABLE IF NOT EXISTS `motion_themes` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `label` varchar(255) NOT NULL,
  `duration` integer
);

INSERT INTO `motion_themes` (`id`, `label`, `duration`) VALUES(1, 'motion_themes.constitutional', 8);
INSERT INTO `motion_themes` (`id`, `label`, `duration`) VALUES(2, 'motion_themes.action', 3);
INSERT INTO `motion_themes` (`id`, `label`, `duration`) VALUES(3, 'motion_themes.emergency', 1);