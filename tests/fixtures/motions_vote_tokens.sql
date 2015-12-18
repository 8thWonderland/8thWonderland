DROP TABLE IF EXISTS `motions_vote_tokens`;

CREATE TABLE IF NOT EXISTS `motions_vote_tokens` (
  `motion_id` integer NOT NULL,
  `citizen_id` integer NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `browser` varchar(150) NOT NULL
);

INSERT INTO `motions_vote_tokens` (`motion_id`, `citizen_id`, `date`, `ip`, `browser`) VALUES(1, 1, '2015-10-01 02:10:34', '127.0.0.1', '');