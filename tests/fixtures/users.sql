DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users`(
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `login` varchar(50) NOT NULL,
    `password` varchar(128) NOT NULL,
    `salt` varchar(85) NOT NULL,
    `identity` varchar(50) NOT NULL,
    `email` varchar(80) NOT NULL,
    `avatar` varchar(100),
    `language` varchar(3) NOT NULL,
    `country_id` int(11) NOT NULL,
    `region_id` int(11) NOT NULL,
    `created_at` datetime NOT NULL,
    `last_connected_at` datetime NOT NULL,
    `is_enabled` boolean NOT NULL,
    `is_banned` boolean NOT NULL,
    FOREIGN KEY(country_id) REFERENCES countries(id),
    FOREIGN KEY(region_id) REFERENCES regions(id)
);

INSERT INTO `users`(id, login, password, salt, identity, email, avatar, language, country_id, region_id, created_at, last_connected_at, is_enabled, is_banned) VALUES(1, 'john_doe', 'toronto789321', 'fq6b1yr5fq1q1v87h', 'John Doe', 'john_doe@gmail.com', 'avatar.png', 'fr', 1, 1, '2015-11-19 19:00:00', '2015-12-01 15:00:00', 1, 0);
INSERT INTO `users`(id, login, password, salt, identity, email, avatar, language, country_id, region_id, created_at, last_connected_at, is_enabled, is_banned) VALUES(2, 'alex_stone', 'toto896', 'fop44z3f1zyr5fq1q', 'Alexander', 'alexander_stone@gmail.com', 'avatar.jpg', 'fr', 1, 2, '2015-11-19 19:00:00', '2015-12-01 15:00:00', 1, 0);