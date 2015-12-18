DROP TABLE IF EXISTS `motions_votes`;

CREATE TABLE IF NOT EXISTS `motions_votes` (
  `vote_token_id` integer PRIMARY KEY AUTOINCREMENT,
  `motion_id` int(11) NOT NULL,
  `choice` int(11) NOT NULL,
  `hash` varchar(129) NOT NULL
);

--
-- Contenu de la table `motions_votes`
--

INSERT INTO `motions_votes` (`vote_token_id`, `motion_id`, `choice`, `hash`) VALUES
(1, 47, 1, '59abb58b10f307558977907259a470211dbee6899f9708f4132a4179a1cf05ad49d058b181c3701b6018444f5b86e7e9c12b4772547af6e62a0803bc481651f8');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
