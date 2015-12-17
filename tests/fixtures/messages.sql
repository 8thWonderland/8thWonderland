DROP TABLE IF EXISTS `messages`;

CREATE TABLE IF NOT EXISTS `messages` (
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `title` varchar(255) NOT NULL,
    `content` text NOT NULL,
    `author_id` integer NOT NULL,
    `recipient_id` integer NOT NULL,
    `created_at` datetime NOT NULL,
    `opened_at` datetime,
    `deleted_by_author` tinyint(1) NOT NULL,
    `deleted_by_recipient` tinyint(1) NOT NULL,
    FOREIGN KEY(author_id) REFERENCES users(id),
    FOREIGN KEY(recipient_id) REFERENCES users(id)
);

--
-- Contenu de la table `messages`
--

INSERT INTO `messages` (`id`, `title`, `content`, `author_id`, `recipient_id`, `created_at`, `deleted_by_author`, `deleted_by_recipient`) VALUES(1, 'Test', 'test', 1, 2, '2015-10-03 08:39:36', 0, 1);
INSERT INTO `messages` (`id`, `title`, `content`, `author_id`, `recipient_id`, `created_at`, `deleted_by_author`, `deleted_by_recipient`) VALUES(2, 'Test again', 'again', 1, 2, '2015-10-03 09:03:21', 1, 0);
INSERT INTO `messages` (`id`, `title`, `content`, `author_id`, `recipient_id`, `created_at`, `deleted_by_author`, `deleted_by_recipient`) VALUES(3, 'Test again 2', 'again and again', 2, 1, '2015-10-03 09:03:28', 0, 1);
