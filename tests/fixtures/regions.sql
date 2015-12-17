DROP TABLE IF EXISTS `regions`;

CREATE TABLE IF NOT EXISTS `regions`(
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `country_id` int(11) NOT NULL,
    `name` varchar(180) NOT NULL,
    `latitude` decimal(10,7),
    `longitude` decimal(10,7),
    `created_at` datetime NOT NULL,
    FOREIGN KEY(country_id) REFERENCES countries(id)
);

INSERT INTO `regions`(id, country_id, name, latitude, longitude, created_at) VALUES(1, 1, 'Île-de-France', 75.366, 125.84, '2010-04-17 15:20:36');
INSERT INTO `regions`(id, country_id, name, latitude, longitude, created_at) VALUES(2, 1, 'Normandie', 63.465, 113.252, '2011-01-03 09:50:10');