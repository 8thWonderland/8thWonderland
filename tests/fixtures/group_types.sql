DROP TABLE IF EXISTS `group_types`;

CREATE TABLE IF NOT EXISTS `group_types`(
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `label` varchar(45)
);

INSERT INTO `group_types`(id, label) VALUES(1, 'groups.regional');
INSERT INTO `group_types`(id, label) VALUES(2, 'groups.thematic');