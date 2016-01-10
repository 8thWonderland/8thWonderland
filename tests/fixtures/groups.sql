DROP TABLE IF EXISTS `groups`;

CREATE TABLE IF NOT EXISTS `groups`(
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `type_id` int(11) NOT NULL,
    `name` varchar(65) NOT NULL,
    `description`varchar(250) NOT NULL,
    `contact_id` int(11) NOT NULL,
    `is_public` boolean NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    FOREIGN KEY(type_id) REFERENCES group_types(id),
    FOREIGN KEY(contact_id) REFERENCES users(id)
);

INSERT INTO `groups`(`id`, `type_id`, `name`, `description`, `contact_id`, `is_public`, `created_at`, `updated_at`) VALUES(1, 2, 'Développeurs', 'Equipe de développement du site', 1, 0, '2015-08-22 15:30:00', '2015-09-01 23:54:32');
INSERT INTO `groups`(`id`, `type_id`, `name`, `description`, `contact_id`, `is_public`, `created_at`, `updated_at`) VALUES(2, 1, 'Île-de-France', "Groupe régional d'Île-de-France", 1, 1, '2013-10-13 14:07:00', '2013-10-13 14:07:00');
INSERT INTO `groups`(`id`, `type_id`, `name`, `description`, `contact_id`, `is_public`, `created_at`, `updated_at`) VALUES(3, 1, 'Normandie', 'Groupe régional de Normandie', 2, 1, '2014-05-17 10:30:00', '2014-12-15 17:52:50');