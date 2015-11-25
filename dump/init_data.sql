-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 02 Novembre 2015 à 21:15
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `thwonderbdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `abac_attributes`
--

CREATE TABLE IF NOT EXISTS `abac_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `column_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `criteria_column` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `abac_attributes`
--

INSERT INTO `abac_attributes` (`id`, `table_name`, `column_name`, `criteria_column`) VALUES
(1, 'groups', 'contact_id', 'id');

-- --------------------------------------------------------

--
-- Structure de la table `abac_attributes_data`
--

CREATE TABLE IF NOT EXISTS `abac_attributes_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `abac_attributes_data`
--

INSERT INTO `abac_attributes_data` (`id`, `created_at`, `updated_at`, `name`, `slug`) VALUES
(1, '2015-11-12 00:00:00', '2015-11-12 00:00:00', 'Group Owner', 'group-owner');

-- --------------------------------------------------------

--
-- Structure de la table `abac_environment_attributes`
--

CREATE TABLE IF NOT EXISTS `abac_environment_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable_name` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `abac_policy_rules`
--

CREATE TABLE IF NOT EXISTS `abac_policy_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `abac_policy_rules`
--

INSERT INTO `abac_policy_rules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'group-management', '2015-11-12 00:00:00', '2015-11-12 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `abac_policy_rules_attributes`
--

CREATE TABLE IF NOT EXISTS `abac_policy_rules_attributes` (
  `policy_rule_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `comparison_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `comparison` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY `policy_rule_id` (`policy_rule_id`,`attribute_id`),
  KEY `attributes` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `abac_policy_rules_attributes`
--

INSERT INTO `abac_policy_rules_attributes` (`policy_rule_id`, `attribute_id`, `type`, `comparison_type`, `comparison`, `value`) VALUES
(1, 1, 'object', 'Numeric', 'isEqual', 'dynamic');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `abac_attributes`
--
ALTER TABLE `abac_attributes`
  ADD CONSTRAINT `attributes_data` FOREIGN KEY (`id`) REFERENCES `abac_attributes_data` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `abac_environment_attributes`
--
ALTER TABLE `abac_environment_attributes`
  ADD CONSTRAINT `environment_attributes_data` FOREIGN KEY (`id`) REFERENCES `abac_attributes_data` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `abac_policy_rules_attributes`
--
ALTER TABLE `abac_policy_rules_attributes`
  ADD CONSTRAINT `attributes` FOREIGN KEY (`attribute_id`) REFERENCES `abac_attributes_data` (`id`),
  ADD CONSTRAINT `policy_rules` FOREIGN KEY (`policy_rule_id`) REFERENCES `abac_policy_rules` (`id`);

-- --------------------------------------------------------

--
-- Structure de la table `country`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `fr` varchar(255) NOT NULL,
  `en` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=239 ;

--
-- Contenu de la table `country`
--

INSERT INTO `country` (`rowid`, `code`, `fr`, `en`) VALUES
(1, 'AF', 'Afghanistan', 'Afghanistan'),
(2, 'ZA', 'Afrique du Sud', 'South Africa'),
(3, 'AL', 'Albanie', 'Albania'),
(4, 'DZ', 'AlgÃƒÂ©rie', 'Algeria'),
(5, 'DE', 'Allemagne', 'Germany'),
(6, 'AD', 'Andorre', 'Andorra'),
(7, 'AO', 'Angola', 'Angola'),
(8, 'AI', 'Anguilla', 'Anguilla'),
(9, 'AQ', 'Antarctique', 'Antarctica'),
(10, 'AG', 'Antigua-et-Barbuda', 'Antigua & Barbuda'),
(11, 'AN', 'Antilles nÃƒÂ©erlandaises', 'Netherlands Antilles'),
(12, 'SA', 'Arabie saoudite', 'Saudi Arabia'),
(13, 'AR', 'Argentine', 'Argentina'),
(14, 'AM', 'ArmÃƒÂ©nie', 'Armenia'),
(15, 'AW', 'Aruba', 'Aruba'),
(16, 'AU', 'Australie', 'Australia'),
(17, 'AT', 'Autriche', 'Austria'),
(18, 'AZ', 'AzerbaÃƒÂ¯djan', 'Azerbaijan'),
(19, 'BJ', 'BÃƒÂ©nin', 'Benin'),
(20, 'BS', 'Bahamas', 'Bahamas, The'),
(21, 'BH', 'BahreÃƒÂ¯n', 'Bahrain'),
(22, 'BD', 'Bangladesh', 'Bangladesh'),
(23, 'BB', 'Barbade', 'Barbados'),
(24, 'PW', 'Belau', 'Palau'),
(25, 'BE', 'Belgique', 'Belgium'),
(26, 'BZ', 'Belize', 'Belize'),
(27, 'BM', 'Bermudes', 'Bermuda'),
(28, 'BT', 'Bhoutan', 'Bhutan'),
(29, 'BY', 'BiÃƒÂ©lorussie', 'Belarus'),
(30, 'MM', 'Birmanie', 'Myanmar (ex-Burma)'),
(31, 'BO', 'Bolivie', 'Bolivia'),
(32, 'BA', 'Bosnie-HerzÃƒÂ©govine', 'Bosnia and Herzegovina'),
(33, 'BW', 'Botswana', 'Botswana'),
(34, 'BR', 'BrÃƒÂ©sil', 'Brazil'),
(35, 'BN', 'Brunei', 'Brunei Darussalam'),
(36, 'BG', 'Bulgarie', 'Bulgaria'),
(37, 'BF', 'Burkina Faso', 'Burkina Faso'),
(38, 'BI', 'Burundi', 'Burundi'),
(39, 'CI', 'CÃƒÂ´te d''Ivoire', 'Ivory Coast (see Cote d''Ivoire)'),
(40, 'KH', 'Cambodge', 'Cambodia'),
(41, 'CM', 'Cameroun', 'Cameroon'),
(42, 'CA', 'Canada', 'Canada'),
(43, 'CV', 'Cap-Vert', 'Cape Verde'),
(44, 'CL', 'Chili', 'Chile'),
(45, 'CN', 'Chine', 'China'),
(46, 'CY', 'Chypre', 'Cyprus'),
(47, 'CO', 'Colombie', 'Colombia'),
(48, 'KM', 'Comores', 'Comoros'),
(49, 'CG', 'Congo', 'Congo'),
(50, 'KP', 'CorÃƒÂ©e du Nord', 'Korea, Demo. People''s Rep. of'),
(51, 'KR', 'CorÃƒÂ©e du Sud', 'Korea, (South) Republic of'),
(52, 'CR', 'Costa Rica', 'Costa Rica'),
(53, 'HR', 'Croatie', 'Croatia'),
(54, 'CU', 'Cuba', 'Cuba'),
(55, 'DK', 'Danemark', 'Denmark'),
(56, 'DJ', 'Djibouti', 'Djibouti'),
(57, 'DM', 'Dominique', 'Dominica'),
(58, 'EG', 'Egypte', 'Egypt'),
(59, 'AE', 'Emirats arabes unis', 'United Arab Emirates'),
(60, 'EC', 'Equateur', 'Ecuador'),
(61, 'ER', 'ErythrÃƒÂ©e', 'Eritrea'),
(62, 'ES', 'Espagne', 'Spain'),
(63, 'EE', 'Estonie', 'Estonia'),
(64, 'US', 'Etats-Unis', 'United States'),
(65, 'ET', 'Ethiopie', 'Ethiopia'),
(66, 'FI', 'Finlande', 'Finland'),
(67, 'FR', 'France', 'France'),
(68, 'GE', 'GÃƒÂ©orgie', 'Georgia'),
(69, 'GA', 'Gabon', 'Gabon'),
(70, 'GM', 'Gambie', 'Gambia, the'),
(71, 'GH', 'Ghana', 'Ghana'),
(72, 'GI', 'Gibraltar', 'Gibraltar'),
(73, 'GR', 'GrÃƒÂ¨ce', 'Greece'),
(74, 'GD', 'Grenade', 'Grenada'),
(75, 'GL', 'Groenland', 'Greenland'),
(77, 'GU', 'Guam', 'Guam'),
(78, 'GT', 'Guatemala', 'Guatemala'),
(79, 'GN', 'GuinÃƒÂ©e', 'Guinea'),
(80, 'GQ', 'GuinÃƒÂ©e ÃƒÂ©quatoriale', 'Equatorial Guinea'),
(81, 'GW', 'GuinÃƒÂ©e-Bissao', 'Guinea-Bissau'),
(82, 'GY', 'Guyana', 'Guyana'),
(84, 'HT', 'HaÃƒÂ¯ti', 'Haiti'),
(85, 'HN', 'Honduras', 'Honduras'),
(86, 'HK', 'Hong Kong', 'Hong Kong, (China)'),
(87, 'HU', 'Hongrie', 'Hungary'),
(88, 'BV', 'Ile Bouvet', 'Bouvet Island'),
(89, 'CX', 'Ile Christmas', 'Christmas Island'),
(90, 'NF', 'Ile Norfolk', 'Norfolk Island'),
(91, 'KY', 'Iles Cayman', 'Cayman Islands'),
(92, 'CK', 'Iles Cook', 'Cook Islands'),
(93, 'FO', 'Iles FÃƒÂ©roÃƒÂ©', 'Faroe Islands'),
(94, 'FK', 'Iles Falkland', 'Falkland Islands (Malvinas)'),
(95, 'FJ', 'Iles Fidji', 'Fiji'),
(96, 'GS', 'Iles GÃƒÂ©orgie du Sud et Sandwich du Sud', 'S. Georgia and S. Sandwich Is.'),
(97, 'HM', 'Iles Heard et McDonald', 'Heard and McDonald Islands'),
(98, 'MH', 'Iles Marshall', 'Marshall Islands'),
(99, 'PN', 'Iles Pitcairn', 'Pitcairn Island'),
(100, 'SB', 'Iles Salomon', 'Solomon Islands'),
(101, 'SJ', 'Iles Svalbard et Jan Mayen', 'Svalbard and Jan Mayen Islands'),
(102, 'TC', 'Iles Turks-et-Caicos', 'Turks and Caicos Islands'),
(103, 'VI', 'Iles Vierges amÃƒÂ©ricaines', 'Virgin Islands, U.S.'),
(104, 'VG', 'Iles Vierges britanniques', 'Virgin Islands, British'),
(105, 'CC', 'Iles des Cocos (Keeling)', 'Cocos (Keeling) Islands'),
(106, 'UM', 'Iles mineures ÃƒÂ©loignÃƒÂ©es des Etats-Unis', 'US Minor Outlying Islands'),
(107, 'IN', 'Inde', 'India'),
(108, 'ID', 'IndonÃƒÂ©sie', 'Indonesia'),
(109, 'IR', 'Iran', 'Iran, Islamic Republic of'),
(110, 'IQ', 'Iraq', 'Iraq'),
(111, 'IE', 'Irlande', 'Ireland'),
(112, 'IS', 'Islande', 'Iceland'),
(113, 'IL', 'IsraÃƒÂ«l', 'Israel'),
(114, 'IT', 'Italie', 'Italy'),
(115, 'JM', 'JamaÃƒÂ¯que', 'Jamaica'),
(116, 'JP', 'Japon', 'Japan'),
(117, 'JO', 'Jordanie', 'Jordan'),
(118, 'KZ', 'Kazakhstan', 'Kazakhstan'),
(119, 'KE', 'Kenya', 'Kenya'),
(120, 'KG', 'Kirghizistan', 'Kyrgyzstan'),
(121, 'KI', 'Kiribati', 'Kiribati'),
(122, 'KW', 'KoweÃƒÂ¯t', 'Kuwait'),
(123, 'LA', 'Laos', 'Lao People''s Democratic Republic'),
(124, 'LS', 'Lesotho', 'Lesotho'),
(125, 'LV', 'Lettonie', 'Latvia'),
(126, 'LB', 'Liban', 'Lebanon'),
(127, 'LR', 'Liberia', 'Liberia'),
(128, 'LY', 'Libye', 'Libyan Arab Jamahiriya'),
(129, 'LI', 'Liechtenstein', 'Liechtenstein'),
(130, 'LT', 'Lituanie', 'Lithuania'),
(131, 'LU', 'Luxembourg', 'Luxembourg'),
(132, 'MO', 'Macao', 'Macao, (China)'),
(133, 'MG', 'Madagascar', 'Madagascar'),
(134, 'MY', 'Malaisie', 'Malaysia'),
(135, 'MW', 'Malawi', 'Malawi'),
(136, 'MV', 'Maldives', 'Maldives'),
(137, 'ML', 'Mali', 'Mali'),
(138, 'MT', 'Malte', 'Malta'),
(139, 'MP', 'Mariannes du Nord', 'Northern Mariana Islands'),
(140, 'MA', 'Maroc', 'Morocco'),
(141, 'MQ', 'Martinique', 'Martinique'),
(142, 'MU', 'Maurice', 'Mauritius'),
(143, 'MR', 'Mauritanie', 'Mauritania'),
(144, 'YT', 'Mayotte', 'Mayotte'),
(145, 'MX', 'Mexique', 'Mexico'),
(146, 'FM', 'MicronÃƒÂ©sie', 'Micronesia, Federated States of'),
(147, 'MD', 'Moldavie', 'Moldova, Republic of'),
(148, 'MC', 'Monaco', 'Monaco'),
(149, 'MN', 'Mongolie', 'Mongolia'),
(150, 'MS', 'Montserrat', 'Montserrat'),
(151, 'MZ', 'Mozambique', 'Mozambique'),
(152, 'NP', 'NÃƒÂ©pal', 'Nepal'),
(153, 'NA', 'Namibie', 'Namibia'),
(154, 'NR', 'Nauru', 'Nauru'),
(155, 'NI', 'Nicaragua', 'Nicaragua'),
(156, 'NE', 'Niger', 'Niger'),
(157, 'NG', 'Nigeria', 'Nigeria'),
(158, 'NU', 'NiouÃƒÂ©', 'Niue'),
(159, 'NO', 'NorvÃƒÂ¨ge', 'Norway'),
(160, 'NC', 'Nouvelle-CalÃƒÂ©donie', 'New Caledonia'),
(161, 'NZ', 'Nouvelle-ZÃƒÂ©lande', 'New Zealand'),
(162, 'OM', 'Oman', 'Oman'),
(163, 'UG', 'Ouganda', 'Uganda'),
(164, 'UZ', 'OuzbÃƒÂ©kistan', 'Uzbekistan'),
(165, 'PE', 'PÃƒÂ©rou', 'Peru'),
(166, 'PK', 'Pakistan', 'Pakistan'),
(167, 'PA', 'Panama', 'Panama'),
(168, 'PG', 'Papouasie-Nouvelle-GuinÃƒÂ©e', 'Papua New Guinea'),
(169, 'PY', 'Paraguay', 'Paraguay'),
(170, 'NL', 'Pays-Bas', 'Netherlands'),
(171, 'PH', 'Philippines', 'Philippines'),
(172, 'PL', 'Pologne', 'Poland'),
(173, 'PF', 'PolynÃƒÂ©sie franÃƒÂ§aise', 'French Polynesia'),
(174, 'PR', 'Porto Rico', 'Puerto Rico'),
(175, 'PT', 'Portugal', 'Portugal'),
(176, 'QA', 'Qatar', 'Qatar'),
(177, 'CF', 'RÃƒÂ©publique centrafricaine', 'Central African Republic'),
(178, 'CD', 'RÃƒÂ©publique dÃƒÂ©mocratique du Congo', 'Congo, Democratic Rep. of the'),
(179, 'DO', 'RÃƒÂ©publique dominicaine', 'Dominican Republic'),
(180, 'CZ', 'RÃƒÂ©publique tchÃƒÂ¨que', 'Czech Republic'),
(181, 'RE', 'RÃƒÂ©union', 'Reunion'),
(182, 'RO', 'Roumanie', 'Romania'),
(183, 'GB', 'Royaume-Uni', 'Saint Pierre and Miquelon'),
(184, 'RU', 'Russie', 'Russia (Russian Federation)'),
(185, 'RW', 'Rwanda', 'Rwanda'),
(186, 'SN', 'SÃƒÂ©nÃƒÂ©gal', 'Senegal'),
(187, 'EH', 'Sahara occidental', 'Western Sahara'),
(188, 'KN', 'Saint-Christophe-et-NiÃƒÂ©vÃƒÂ¨s', 'Saint Kitts and Nevis'),
(189, 'SM', 'Saint-Marin', 'San Marino'),
(190, 'PM', 'Saint-Pierre-et-Miquelon', 'Saint Pierre and Miquelon'),
(191, 'VA', 'Saint-SiÃƒÂ¨ge ', 'Vatican City State (Holy See)'),
(192, 'VC', 'Saint-Vincent-et-les-Grenadines', 'Saint Vincent and the Grenadines'),
(193, 'SH', 'Sainte-HÃƒÂ©lÃƒÂ¨ne', 'Saint Helena'),
(194, 'LC', 'Sainte-Lucie', 'Saint Lucia'),
(195, 'SV', 'Salvador', 'El Salvador'),
(196, 'WS', 'Samoa', 'Samoa'),
(197, 'AS', 'Samoa amÃƒÂ©ricaines', 'American Samoa'),
(198, 'ST', 'Sao TomÃƒÂ©-et-Principe', 'Sao Tome and Principe'),
(199, 'SC', 'Seychelles', 'Seychelles'),
(200, 'SL', 'Sierra Leone', 'Sierra Leone'),
(201, 'SG', 'Singapour', 'Singapore'),
(202, 'SI', 'SlovÃƒÂ©nie', 'Slovenia'),
(203, 'SK', 'Slovaquie', 'Slovakia'),
(204, 'SO', 'Somalie', 'Somalia'),
(205, 'SD', 'Soudan', 'Sudan'),
(206, 'LK', 'Sri Lanka', 'Sri Lanka (ex-Ceilan)'),
(207, 'SE', 'SuÃƒÂ¨de', 'Sweden'),
(208, 'CH', 'Suisse', 'Switzerland'),
(209, 'SR', 'Suriname', 'Suriname'),
(210, 'SZ', 'Swaziland', 'Swaziland'),
(211, 'SY', 'Syrie', 'Syrian Arab Republic'),
(212, 'TW', 'TaÃƒÂ¯wan', 'Taiwan'),
(213, 'TJ', 'Tadjikistan', 'Tajikistan'),
(214, 'TZ', 'Tanzanie', 'Tanzania, United Republic of'),
(215, 'TD', 'Tchad', 'Chad'),
(216, 'TF', 'Terres australes franÃƒÂ§aises', 'French Southern Territories - TF'),
(217, 'IO', 'Territoire britannique de l''OcÃƒÂ©an Indien', 'British Indian Ocean Territory'),
(218, 'TH', 'ThaÃƒÂ¯lande', 'Thailand'),
(219, 'TL', 'Timor Oriental', 'Timor-Leste (East Timor)'),
(220, 'TG', 'Togo', 'Togo'),
(221, 'TK', 'TokÃƒÂ©laou', 'Tokelau'),
(222, 'TO', 'Tonga', 'Tonga'),
(223, 'TT', 'TrinitÃƒÂ©-et-Tobago', 'Trinidad & Tobago'),
(224, 'TN', 'Tunisie', 'Tunisia'),
(225, 'TM', 'TurkmÃƒÂ©nistan', 'Turkmenistan'),
(226, 'TR', 'Turquie', 'Turkey'),
(227, 'TV', 'Tuvalu', 'Tuvalu'),
(228, 'UA', 'Ukraine', 'Ukraine'),
(229, 'UY', 'Uruguay', 'Uruguay'),
(230, 'VU', 'Vanuatu', 'Vanuatu'),
(231, 'VE', 'Venezuela', 'Venezuela'),
(232, 'VN', 'ViÃƒÂªt Nam', 'Viet Nam'),
(233, 'WF', 'Wallis-et-Futuna', 'Wallis and Futuna'),
(234, 'YE', 'YÃƒÂ©men', 'Yemen'),
(235, 'YU', 'Yougoslavie', 'Saint Pierre and Miquelon'),
(236, 'ZM', 'Zambie', 'Zambia'),
(237, 'ZW', 'Zimbabwe', 'Zimbabwe'),
(238, 'MK', 'ex-RÃƒÂ©publique yougoslave de MacÃƒÂ©doine', 'Macedonia, TFYR');

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `description` varchar(250) NOT NULL,
  `name` varchar(65) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `contact_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Creation` (`created_at`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=107 ;

--
-- Contenu de la table `groups`
--

INSERT INTO `groups` (`id`, `type_id`, `description`, `name`, `contact_id`, `created_at`, `updated_at`) VALUES
(1, 2, 'Groupe prive regroupant les developpeurs du site web', 'Développeurs', 1, '2012-01-27 11:14:42', '0000-00-00 00:00:00'),
(2, 1, 'Groupe régional', 'Île-de-France', 1, '2012-02-07 21:08:35', '2015-10-08 23:47:43'),
(15, 1, 'Groupe regional', 'Picardie', 9099, '2012-02-07 22:28:54', '0000-00-00 00:00:00'),
(10, 1, 'Groupe regional', 'Midi-PyrÃ©nÃ©es', 8867, '2012-02-07 20:37:13', '0000-00-00 00:00:00'),
(25, 1, 'Groupe regional', 'Bretagne', 12, '2012-02-07 23:44:06', '0000-00-00 00:00:00'),
(63, 1, 'Groupe regional', 'Pays de la Loire', 9346, '2012-02-08 22:07:08', '0000-00-00 00:00:00'),
(80, 1, 'Groupe regional', 'Province Sud', 5, '2012-02-11 10:43:39', '0000-00-00 00:00:00'),
(55, 1, 'Groupe regional', 'Languedoc-Roussillon', 9240, '2012-02-08 15:33:20', '0000-00-00 00:00:00'),
(56, 1, 'Groupe regional', 'Aquitaine', 9455, '2012-02-08 16:26:56', '0000-00-00 00:00:00'),
(57, 1, 'Groupe regional', 'Centre', 9210, '2012-02-08 17:22:27', '0000-00-00 00:00:00'),
(58, 1, 'Groupe regional', 'Nord-Pas-de-Calais', 5841, '2012-02-08 18:40:33', '0000-00-00 00:00:00'),
(59, 1, 'Groupe regional', 'RhÃ´ne-Alpes', 9456, '2012-02-08 18:41:02', '0000-00-00 00:00:00'),
(60, 1, 'Groupe regional', 'Lorraine', 5, '2012-02-08 19:20:35', '0000-00-00 00:00:00'),
(61, 1, 'Groupe regional', 'Basse-Normandie', 5, '2012-02-08 19:26:53', '0000-00-00 00:00:00'),
(62, 1, 'Groupe regional', 'Corse', 5, '2012-02-08 19:37:35', '0000-00-00 00:00:00'),
(64, 1, 'Groupe regional', 'Alsace', 9222, '2012-02-09 01:40:44', '0000-00-00 00:00:00'),
(65, 1, 'Groupe regional', 'La province de Luxembourg', 9633, '2012-02-09 02:44:11', '0000-00-00 00:00:00'),
(66, 1, 'Groupe regional', 'DÃ©partements d''Outre-Mer', 5, '2012-02-09 09:51:49', '0000-00-00 00:00:00'),
(67, 1, 'Groupe regional', 'Auvergne', 5, '2012-02-09 11:18:56', '0000-00-00 00:00:00'),
(68, 1, 'Groupe regional', 'Haute-Normandie', 5, '2012-02-09 14:14:03', '0000-00-00 00:00:00'),
(69, 1, 'Groupe regional', 'NeuchÃ¢tel', 9485, '2012-02-09 15:37:24', '0000-00-00 00:00:00'),
(70, 1, 'Groupe regional', 'Champagne-Ardenne', 9503, '2012-02-09 16:23:59', '0000-00-00 00:00:00'),
(71, 1, 'Groupe regional', 'Le Hainaut', 9633, '2012-02-09 16:42:12', '0000-00-00 00:00:00'),
(72, 1, 'Groupe regional', 'Bourgogne', 9316, '2012-02-09 21:18:48', '0000-00-00 00:00:00'),
(73, 1, 'Groupe regional', 'Vaud', 9485, '2012-02-09 22:18:10', '0000-00-00 00:00:00'),
(74, 1, 'Groupe regional', 'Poitou-Charentes', 5, '2012-02-09 22:33:13', '0000-00-00 00:00:00'),
(75, 1, 'Groupe regional', 'Grand Casablanca', 5, '2012-02-09 23:26:49', '0000-00-00 00:00:00'),
(76, 1, 'Groupe regional', 'Franche-ComtÃ©', 9503, '2012-02-10 00:14:00', '0000-00-00 00:00:00'),
(77, 1, 'Groupe regional', 'Provence-Alpes-CÃ´te-d''Azur', 9566, '2012-02-10 00:40:00', '0000-00-00 00:00:00'),
(78, 1, 'Groupe regional', 'Algeria', 5, '2012-02-10 12:51:02', '0000-00-00 00:00:00'),
(79, 1, 'Groupe regional', 'QuÃ©bec', 5, '2012-02-10 16:04:40', '0000-00-00 00:00:00'),
(81, 1, 'Groupe regional', 'La province de Namur', 9633, '2012-02-11 23:15:04', '0000-00-00 00:00:00'),
(82, 1, 'Groupe regional', 'La province de LiÃ¨ge', 9633, '2012-02-12 12:37:35', '0000-00-00 00:00:00'),
(83, 1, 'Groupe regional', 'Lisboa', 5, '2012-02-12 18:20:48', '0000-00-00 00:00:00'),
(84, 1, 'Groupe regional', 'Limousin', 5, '2012-02-12 20:16:27', '0000-00-00 00:00:00'),
(85, 1, 'Groupe regional', 'Le Brabant wallon', 9633, '2012-02-14 09:35:45', '0000-00-00 00:00:00'),
(86, 1, 'Groupe regional', 'Fribourg', 9485, '2012-02-14 23:44:27', '0000-00-00 00:00:00'),
(87, 1, 'Groupe regional', 'La Flandre orientale', 9633, '2012-02-15 13:24:00', '0000-00-00 00:00:00'),
(88, 2, 'Groupe d''informations', 'Groupe d''informations', 7117, '2012-02-16 17:35:55', '0000-00-00 00:00:00'),
(89, 1, 'Groupe regional', 'Tunis', 5, '2012-02-18 00:27:36', '0000-00-00 00:00:00'),
(90, 1, 'Groupe regional', 'Le Brabant flamand', 9633, '2012-02-21 00:22:07', '0000-00-00 00:00:00'),
(91, 1, 'Groupe regional', 'Guyane', 5, '2012-02-21 17:44:48', '0000-00-00 00:00:00'),
(92, 1, 'Groupe regional', 'Jura', 9485, '2012-02-22 18:37:38', '0000-00-00 00:00:00'),
(93, 1, 'Groupe regional', 'Haute-Savoie', 5, '2012-02-26 21:27:46', '0000-00-00 00:00:00'),
(94, 1, 'Groupe regional', 'Valais', 9485, '2012-02-26 23:55:48', '0000-00-00 00:00:00'),
(98, 1, 'Groupe regional', 'Martinique', 5, '2012-03-08 03:59:27', '0000-00-00 00:00:00'),
(96, 1, 'Groupe regional', 'Nordrhein-Westfalen', 5, '2012-03-03 00:47:24', '0000-00-00 00:00:00'),
(97, 1, 'Groupe regional', 'MeknÃ¨s-Tafilalet', 5, '2012-03-04 03:51:49', '0000-00-00 00:00:00'),
(99, 1, 'Groupe regional', 'Berlin', 5, '2012-03-09 01:40:13', '0000-00-00 00:00:00'),
(100, 1, 'Groupe regional', 'Guadeloupe', 5, '2012-03-15 03:50:34', '0000-00-00 00:00:00'),
(101, 1, 'Groupe regional', 'Territoires d''Outre-Mer', 5, '2012-03-17 05:37:48', '0000-00-00 00:00:00'),
(102, 1, 'Groupe regional', 'Baden-WÃ¼rttemberg', 5, '2012-03-19 22:21:47', '0000-00-00 00:00:00'),
(103, 1, 'Groupe regional', 'Reunion', 5, '2012-03-26 01:54:37', '0000-00-00 00:00:00'),
(104, 1, 'Groupe regional', 'Rabat-SalÃ©-Zemmour-ZaÃ«r', 5, '2012-03-26 02:59:31', '0000-00-00 00:00:00'),
(105, 1, 'Groupe regional', 'Tanger-TÃ©touan', 5, '2012-03-26 22:22:42', '0000-00-00 00:00:00'),
(106, 1, 'Groupe regional', 'GenÃ¨ve', 5, '2012-04-01 10:27:03', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `group_types`
--

CREATE TABLE IF NOT EXISTS `group_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `group_types`
--

INSERT INTO `group_types` (`id`, `label`) VALUES
(1, 'r&Atilde;&copy;gional'),
(2, 'Thematiques');

-- --------------------------------------------------------

--
-- Structure de la table `regions`
--

CREATE TABLE IF NOT EXISTS `regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` tinyint(4) NOT NULL,
  `name` varchar(180) CHARACTER SET utf8 NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=371 ;

--
-- Contenu de la table `regions`
--

INSERT INTO `regions` (`id`, `country_id`, `name`, `longitude`, `latitude`, `created_at`) VALUES
(1, 1, 'Alsace', 48.5000000, 7.5000000, '2012-01-25 10:37:05'),
(2, 1, 'Aquitaine', 44.5833330, 0.0166670, '2012-01-25 10:37:05'),
(3, 1, 'Auvergne', 45.3333330, 3.0000000, '2012-01-25 10:37:05'),
(4, 1, 'Basse-Normandie', 49.0000000, -1.0000000, '2012-01-25 10:37:05'),
(5, 1, 'Bourgogne', 47.2475000, 4.1513900, '2012-01-25 10:37:05'),
(6, 1, 'Bretagne', 48.0000000, -3.0000000, '2012-01-25 10:37:05'),
(7, 1, 'Centre', 47.5000000, 1.7500000, '2012-01-25 10:37:05'),
(8, 1, 'Champagne-Ardenne', 49.0000000, 4.5000000, '2012-01-25 10:37:05'),
(9, 1, 'Corse', 42.1500000, 9.0833330, '2012-01-25 10:37:05'),
(10, 1, 'DÃ©partements d''Outre-Mer', NULL, NULL, '2012-01-25 10:37:05'),
(11, 1, 'Franche-ComtÃ©', 47.0000000, 6.0000000, '2012-01-25 10:37:05'),
(12, 1, 'Haute-Normandie', 49.5000000, 1.0000000, '2012-01-25 10:37:05'),
(14, 1, 'Ile-de-France', 48.5000000, 2.5000000, '2012-01-25 10:37:05'),
(15, 1, 'Languedoc-Roussillon', 43.6666670, 3.1666670, '2012-01-25 10:37:05'),
(16, 1, 'Limousin', 45.6879500, 1.6204830, '2012-01-25 10:37:05'),
(17, 1, 'Lorraine', 49.0000000, 6.0000000, '2012-01-25 10:37:05'),
(18, 1, 'Midi-PyrÃ©nÃ©es', 43.5000000, 1.3333330, '2012-01-25 10:37:05'),
(19, 1, 'Nord-Pas-de-Calais', 50.4666670, 2.7166670, '2012-01-25 10:37:05'),
(20, 1, 'Pays de la Loire', 47.4280000, -1.1430000, '2012-01-25 10:37:05'),
(21, 1, 'Picardie', 49.5000000, 2.8333330, '2012-01-25 10:37:05'),
(22, 1, 'Poitou-Charentes', 46.0833330, 0.1666670, '2012-01-25 10:37:05'),
(23, 1, 'Provence-Alpes-CÃ´te-d''Azur', 44.0000000, 6.0000000, '2012-01-25 10:37:05'),
(24, 1, 'RhÃ´ne-Alpes', 45.5000000, 5.3333330, '2012-01-25 10:37:05'),
(25, 1, 'Territoires d''Outre-Mer', NULL, NULL, '2012-01-25 10:37:05'),
(13, 1, 'Haute-Savoie', 46.0000000, 6.3333330, '2012-02-07 10:32:36'),
(26, 0, 'Appenzell Rh.-Ext', 47.3664810, 9.3000916, '2012-02-07 11:09:57'),
(27, 0, 'Appenzell Rh.-Int', 47.3161925, 9.4316573, '2012-02-07 11:09:57'),
(28, 0, 'Argovie', 47.3907380, 8.0455830, '2012-02-07 11:09:57'),
(29, 0, 'BÃ¢le-Campagne', 47.4819450, 7.7403550, '2012-02-07 11:09:57'),
(30, 0, 'BÃ¢le-Ville', 47.5666670, 7.6000000, '2012-02-07 11:09:57'),
(31, 0, 'Bern', 46.9500000, 7.4500000, '2012-02-07 11:09:57'),
(32, 0, 'Fribourg', 46.8000000, 7.1500000, '2012-02-07 11:09:57'),
(33, 0, 'GenÃ¨ve', 46.2000130, 6.1499850, '2012-02-07 11:09:57'),
(34, 0, 'Glaris', 47.0333330, 9.0666670, '2012-02-07 11:09:57'),
(35, 0, 'Grisons', 46.7500000, 9.5000000, '2012-02-07 11:09:57'),
(36, 0, 'Jura', 47.3444474, 7.1430608, '2012-02-07 11:09:57'),
(37, 0, 'Lucerne', 47.0500000, 8.3000000, '2012-02-07 11:09:57'),
(38, 0, 'NeuchÃ¢tel', 46.9902810, 6.9305670, '2012-02-07 11:09:57'),
(39, 0, 'Nidwald', 46.9333330, 8.0666670, '2012-02-07 11:09:57'),
(40, 0, 'Obwald', 46.8666670, 8.0333330, '2012-02-07 11:09:57'),
(41, 0, 'Schaffhouse', 47.7000010, 8.6333330, '2012-02-07 11:09:57'),
(42, 0, 'Schywtz', 47.0198346, 8.6473977, '2012-02-07 11:09:57'),
(43, 0, 'Soleure', 47.2083310, 7.5375130, '2012-02-07 11:09:57'),
(44, 0, 'St-Gall', 47.4166670, 9.3666670, '2012-02-07 11:09:57'),
(45, 0, 'Tessin', 46.3317340, 8.8004529, '2012-02-07 11:09:57'),
(46, 0, 'Thurgovie', 47.5833330, 9.0666670, '2012-02-07 11:09:57'),
(47, 0, 'Uri', 46.7738629, 8.6025153, '2012-02-07 11:09:57'),
(48, 0, 'Valais', 46.0666670, 7.6000000, '2012-02-07 11:09:57'),
(49, 0, 'Vaud', 46.6166670, 6.5500000, '2012-02-07 11:09:57'),
(50, 0, 'Zoug', 47.1666670, 8.5166670, '2012-02-07 11:09:57'),
(51, 0, 'Zurich', 47.3778950, 8.5411830, '2012-02-07 11:09:57'),
(52, 0, 'Le Brabant flamand', 50.8815434, 4.5645970, '2012-02-07 11:28:21'),
(53, 0, 'Le Brabant wallon', 50.6332410, 4.5243150, '2012-02-07 11:28:21'),
(54, 0, 'La Flandre occidentale', 51.0536024, 3.1457942, '2012-02-07 11:28:21'),
(55, 0, 'La Flandre orientale', 51.0362101, 3.7373124, '2012-02-07 11:28:21'),
(56, 0, 'Le Hainaut', 50.5257076, 4.0621017, '2012-02-07 11:28:21'),
(57, 0, 'Le Limbourg', 50.9738973, 5.3419677, '2012-02-07 11:28:21'),
(58, 0, 'La province d''Anvers', 51.2194933, 4.4024500, '2012-02-07 11:28:21'),
(59, 0, 'La province de LiÃ¨ge', 50.6325574, 5.5796662, '2012-02-07 11:28:21'),
(60, 0, 'La province de Luxembourg', 49.6568287, 6.0333955, '2012-02-07 11:28:21'),
(61, 0, 'La province de Namur', 50.4653280, 4.8676650, '2012-02-07 11:28:21'),
(62, 0, 'Monaco', 43.7326220, 7.4182170, '2012-02-07 11:35:10'),
(63, 0, 'Liechtenstein', 47.1450000, 9.5538890, '2012-02-07 11:35:10'),
(64, 0, 'Andorra la Vella', 42.5075000, 1.5169400, '2012-02-07 11:35:10'),
(65, 0, 'Andalucia', 37.3833330, -5.9833330, '2012-02-07 16:20:18'),
(66, 0, 'Aragon', 41.0000000, -1.0000000, '2012-02-07 16:20:18'),
(67, 0, 'Principado de Asturias', 3.3333330, -6.0000000, '2012-02-07 16:20:18'),
(68, 0, 'Las Islas de Baleares', NULL, NULL, '2012-02-07 16:20:18'),
(69, 0, 'Canarias', 28.1000000, -15.4000000, '2012-02-07 16:20:18'),
(70, 0, 'Cantabria', 43.3333330, -4.0000000, '2012-02-07 16:20:18'),
(71, 0, 'CataluÃ±a', 41.8166670, 1.4666670, '2012-02-07 16:20:18'),
(72, 0, 'La Ciudad Autonoma de Ceuta', 35.8882475, -5.3162201, '2012-02-07 16:20:18'),
(73, 0, 'Extremadura', 39.0000000, -6.0000000, '2012-02-07 16:20:18'),
(74, 0, 'Galicia', 42.5000000, -8.1000000, '2012-02-07 16:20:18'),
(75, 0, 'Comunidad de Madrid', 40.5000000, -3.6666700, '2012-02-07 16:20:18'),
(76, 0, 'La Ciudad Autonoma de Melilla', 35.2888670, -2.9467832, '2012-02-07 16:20:18'),
(77, 0, 'Region de Murcia', 8.0000000, -1.0000000, '2012-02-07 16:20:18'),
(78, 0, 'Comunidad Foral de Navarra', 2.8166670, -1.6500000, '2012-02-07 16:20:18'),
(79, 0, 'Pais Vasco', 2.8333330, -2.6833330, '2012-02-07 16:20:18'),
(80, 0, 'La Rioja', 42.2500000, -2.5000000, '2012-02-07 16:20:18'),
(81, 0, 'Comunidad Valenciana', 39.5000000, -0.7500000, '2012-02-07 16:20:18'),
(82, 0, 'Alentejo', 40.3135915, -7.8015006, '2012-02-07 16:55:30'),
(83, 0, 'Algarve', 37.0144440, -7.9352780, '2012-02-07 16:55:30'),
(84, 0, 'Centro', 40.2069920, -8.4293810, '2012-02-07 16:55:30'),
(85, 0, 'Lisboa', 38.7000000, -9.1666670, '2012-02-07 16:55:30'),
(86, 0, 'Norte', 41.1499680, -8.6102426, '2012-02-07 16:55:30'),
(87, 0, 'RegiÃ£o autonoma de aÃ§ores', NULL, NULL, '2012-02-07 16:55:30'),
(88, 0, 'RegiÃ£o autonoma de Madeira', 32.6511111, -16.9097222, '2012-02-07 16:55:30'),
(89, 0, 'Baden-WÃ¼rttemberg', 48.5300000, 9.0500000, '2012-02-07 17:35:50'),
(90, 0, 'Bayern', 49.0000000, 11.5000000, '2012-02-07 17:35:50'),
(91, 0, 'Berlin', 52.5186000, 13.4081000, '2012-02-07 17:35:50'),
(92, 0, 'Brandenburg', 52.3619440, 13.0080560, '2012-02-07 17:35:50'),
(93, 0, 'Bremen', 7.5833330, 60.2000000, '2012-02-07 17:35:50'),
(94, 0, 'Hamburg', 53.5652780, 10.0013890, '2012-02-07 17:35:50'),
(95, 0, 'Hessen', 50.6661110, 8.5911110, '2012-02-07 17:35:50'),
(96, 0, 'CataluÃ±a', 41.8166670, 1.4666670, '2012-02-07 17:35:50'),
(97, 0, 'Mecklenburg-Vorpommern', 53.6121000, 12.7002000, '2012-02-07 17:35:50'),
(98, 0, 'Niedersachsen', 52.7562000, 9.3933100, '2012-02-07 17:35:50'),
(99, 0, 'Nordrhein-Westfalen', 51.4783000, 7.5550000, '2012-02-07 17:35:50'),
(100, 0, 'Rheinland-Pfalz', 49.9131000, 7.4497200, '2012-02-07 17:35:50'),
(101, 0, 'Saarland', 49.3833000, 6.8333300, '2012-02-07 17:35:50'),
(102, 0, 'Sachsen', 51.0269440, 13.3588890, '2012-02-07 17:35:50'),
(103, 0, 'Sachsen-Anhalt', 51.9713000, 11.4697000, '2012-02-07 17:35:50'),
(104, 0, 'Schleswig-Holstein', 54.4700000, 9.5141600, '2012-02-07 17:35:50'),
(105, 0, 'ThÃ¼ringen', 50.8611110, 11.0519440, '2012-02-07 17:35:50'),
(106, 0, 'Hovedstaden', 55.9398330, 12.3000000, '2012-02-07 19:57:27'),
(107, 0, 'Midtjylland', 56.1666670, 9.5000000, '2012-02-07 19:57:27'),
(108, 0, 'Nordjylland', 56.8307416, 9.4930528, '2012-02-07 19:57:27'),
(109, 0, 'SjÃ¦lland', 55.5000000, 11.7500000, '2012-02-07 19:57:27'),
(110, 0, 'Syddanmark', 55.3333330, 9.6666670, '2012-02-07 19:57:27'),
(111, 0, 'Burgenland', 47.5000000, 16.4166670, '2012-02-07 20:02:10'),
(112, 0, 'KÃ¤rnten', 46.7619000, 13.8189000, '2012-02-07 20:02:10'),
(113, 0, 'NiederÃ¶sterreich', 48.3333330, 15.7500000, '2012-02-07 20:02:10'),
(114, 0, 'OberÃ¶sterreich', 48.0258540, 13.9723665, '2012-02-07 20:02:10'),
(115, 0, 'Salzburg', 47.8025000, 13.0458330, '2012-02-07 20:02:10'),
(116, 0, 'Steiermark', 47.2500000, 15.1666670, '2012-02-07 20:02:10'),
(117, 0, 'Tirol', 47.2537414, 11.6014870, '2012-02-07 20:02:10'),
(118, 0, 'Vorarlberg', 47.2436000, 9.8938900, '2012-02-07 20:02:10'),
(119, 0, 'Wien', 48.2083330, 16.3730560, '2012-02-07 20:02:10'),
(120, 0, 'East Midlands', 2.9800000, -0.7500000, '2012-02-07 20:19:25'),
(121, 0, 'East of England', 2.2400000, 0.4100000, '2012-02-07 20:19:25'),
(122, 0, 'Greater London', 51.5084100, -0.1253600, '2012-02-07 20:19:25'),
(123, 0, 'North East England', 54.8892460, -1.3842770, '2012-02-07 20:19:25'),
(124, 0, 'North West England', 53.6316110, -2.6037600, '2012-02-07 20:19:25'),
(125, 0, 'South East England', 50.9238130, -0.3405760, '2012-02-07 20:19:25'),
(126, 0, 'South West England', 0.9600000, -3.2200000, '2012-02-07 20:19:25'),
(127, 0, 'West Midlands', 52.4700000, -2.2900000, '2012-02-07 20:19:25'),
(128, 0, 'Yorkshire and the Humber', 53.7006543, -0.4493882, '2012-02-07 20:19:25'),
(129, 0, 'Connacht', 53.5729167, -8.9900352, '2012-02-07 20:29:09'),
(130, 0, 'Leinster', 53.3477780, -6.2597220, '2012-02-07 20:29:09'),
(131, 0, 'Munster', 52.2500000, -9.0000000, '2012-02-07 20:29:09'),
(132, 0, 'Ulster', 54.5969440, -5.9300000, '2012-02-07 20:29:09'),
(133, 0, 'Friesland', 53.1641642, 5.7817542, '2012-02-07 20:45:30'),
(134, 0, 'Gelre', 52.0705520, 6.0276918, '2012-02-07 20:45:30'),
(135, 0, 'Holland', 52.2500000, 4.6670000, '2012-02-07 20:45:30'),
(136, 0, 'Overijssel', 52.4387814, 6.5016411, '2012-02-07 20:45:30'),
(137, 0, 'Stad en Lande', 52.2477712, 5.2389583, '2012-02-07 20:45:30'),
(138, 0, 'Utrecht', 52.0833330, 5.1000000, '2012-02-07 20:45:30'),
(139, 0, 'Zeeland', 51.6976800, 5.6738000, '2012-02-07 20:45:30'),
(140, 0, 'Diekirch', 49.8680000, 6.1566670, '2012-02-07 21:00:57'),
(141, 0, 'Grevenmacher', 49.6666670, 6.4500000, '2012-02-07 21:00:57'),
(142, 0, 'Luxembourg', 49.6000000, 6.1166670, '2012-02-07 21:00:57'),
(143, 0, 'Ontario', 50.7000000, -86.0500000, '2012-02-07 23:50:09'),
(144, 0, 'QuÃ©bec', 53.7500000, -71.9833330, '2012-02-07 23:50:09'),
(145, 0, 'Nouvelle-Ecosse', 44.6911120, -63.5668950, '2012-02-07 23:50:09'),
(146, 0, 'Nouveau-Brunswick', 45.9575940, -66.6444400, '2012-02-07 23:50:09'),
(147, 0, 'Manitoba', 55.0666670, -97.5166670, '2012-02-07 23:50:09'),
(148, 0, 'Colombie-Britannique', 54.0000000, -125.0000000, '2012-02-07 23:50:09'),
(149, 0, 'Ile-du-prince-Edouard', 46.3333330, -63.5000000, '2012-02-07 23:50:09'),
(150, 0, 'Saskatchewan', 54.5000000, -105.6813890, '2012-02-07 23:50:09'),
(151, 0, 'Alberta', 55.1666670, -114.4000000, '2012-02-07 23:50:09'),
(152, 0, 'Chaouia-Ouardigha', 33.0473251, -7.2652858, '2012-02-09 09:30:26'),
(153, 0, 'Doukkala-Abda', 32.5997754, -8.6600586, '2012-02-09 09:30:26'),
(154, 0, 'FÃ¨s-Boulemane', 33.1870471, -4.2333355, '2012-02-09 09:30:26'),
(155, 0, 'Gharb-Chrarda-Beni Hssen', 34.5434461, -5.8987139, '2012-02-09 09:30:26'),
(156, 0, 'Grand Casablanca', 33.5205933, -7.5680595, '2012-02-09 09:30:26'),
(157, 0, 'Guelmim-Es Smara', 28.7082053, -9.5450974, '2012-02-09 09:30:26'),
(158, 0, 'LaÃ¢youne-Boujdour-Sakia el Hamra', 26.1333330, -14.5000000, '2012-02-09 09:30:26'),
(159, 0, 'Marrakech-Tensift-Al Haouz', 31.5628076, -7.9592863, '2012-02-09 09:30:26'),
(160, 0, 'MeknÃ¨s-Tafilalet', 31.9051275, -4.7277528, '2012-02-09 09:30:26'),
(161, 0, 'Oriental', 34.6964610, -2.4499510, '2012-02-09 09:30:26'),
(162, 0, 'Oued Ed-Dahab-Lagouira', 22.7337892, -14.2861116, '2012-02-09 09:30:26'),
(163, 0, 'Rabat-SalÃ©-Zemmour-ZaÃ«r', 33.8175173, -6.2375947, '2012-02-09 09:30:26'),
(164, 0, 'Souss-Massa-DrÃ¢a', 31.1200185, -6.0679194, '2012-02-09 09:30:26'),
(165, 0, 'Tadla-Azilal', 32.0042620, -6.5783387, '2012-02-09 09:30:26'),
(166, 0, 'Tanger-TÃ©touan', 35.2629558, -5.5617279, '2012-02-09 09:30:26'),
(167, 0, 'Taza-Al Hoceima-Taounate', 34.2581709, -4.2333355, '2012-02-09 09:30:26'),
(168, 0, 'Algeria', 36.7000000, 3.2166700, '2012-02-10 09:25:34'),
(169, 0, 'Province Nord', -9.0000000, 148.0833330, '2012-02-10 13:30:59'),
(170, 0, 'Province Sud', 21.9166670, 166.3333330, '2012-02-10 13:30:59'),
(171, 0, 'Province des Ã®les LoyautÃ©', -21.0000000, 167.0000000, '2012-02-10 13:30:59'),
(172, 0, 'Adamaoua', 6.9181954, 12.8054753, '2012-02-11 09:29:45'),
(173, 0, 'Centre', 4.5764250, 12.0080570, '2012-02-11 09:29:45'),
(174, 0, 'Est', 3.4585910, 14.5678710, '2012-02-11 09:29:45'),
(175, 0, 'ExtrÃªme-Nord', 10.7577630, 14.5568850, '2012-02-11 09:29:45'),
(176, 0, 'Littoral', 4.1682138, 10.0807298, '2012-02-11 09:29:45'),
(177, 0, 'Nord', 8.5809013, 13.9143990, '2012-02-11 09:29:45'),
(178, 0, 'Nord-Ouest', 6.4703739, 10.4396560, '2012-02-11 09:29:45'),
(179, 0, 'Ouest', 5.4638158, 10.8000051, '2012-02-11 09:29:45'),
(180, 0, 'Sud', 2.7202832, 11.7068294, '2012-02-11 09:29:45'),
(181, 0, 'Sud-Ouest', 5.1573493, 9.3673084, '2012-02-11 09:29:45'),
(182, 0, 'Amazonas', -5.1151460, -78.1108279, '2012-02-11 09:43:20'),
(210, 0, 'Svealand', 61.2491020, 15.0732420, '2012-02-11 09:55:14'),
(184, 0, 'Ancash', -9.5500000, -77.6166670, '2012-02-11 09:43:44'),
(185, 0, 'Apurimac', -14.0504533, -73.0877490, '2012-02-11 09:43:44'),
(186, 0, 'Arequipa', -16.4308160, -71.5155030, '2012-02-11 09:43:44'),
(187, 0, 'Ayacucho', -13.1630560, -44.2244440, '2012-02-11 09:43:44'),
(188, 0, 'Cajamarca', -7.1644440, -78.5105560, '2012-02-11 09:43:44'),
(189, 0, 'Callao', -12.0584000, -77.1484000, '2012-02-11 09:43:44'),
(190, 0, 'Cuzco', -13.5250000, -71.9722220, '2012-02-11 09:43:44'),
(191, 0, 'Huancavelica', -12.7850000, -74.9713890, '2012-02-11 09:43:44'),
(192, 0, 'Huanuco', -9.9330000, -76.2330000, '2012-02-11 09:43:44'),
(193, 0, 'Ica', -14.0666670, -75.7333330, '2012-02-11 09:43:44'),
(194, 0, 'Junin', -11.3357980, -75.3412179, '2012-02-11 09:43:44'),
(195, 0, 'La Libertad', -8.1435933, -78.4751945, '2012-02-11 09:43:44'),
(196, 0, 'Lambayeque', -6.4776528, -79.9192702, '2012-02-11 09:43:44'),
(197, 0, 'Lima', -12.0452990, -77.0311370, '2012-02-11 09:43:44'),
(198, 0, 'Loreto', -4.2324729, -74.2179326, '2012-02-11 09:43:44'),
(199, 0, 'Madre de Dios', -11.7668705, -70.8119953, '2012-02-11 09:43:44'),
(200, 0, 'Moquegua', -17.2000000, -70.9333330, '2012-02-11 09:43:44'),
(201, 0, 'Pasco', -10.4475753, -75.1545381, '2012-02-11 09:43:44'),
(202, 0, 'Piura', -5.2000000, -80.6333330, '2012-02-11 09:43:44'),
(203, 0, 'Puno', -15.8375000, -70.0216000, '2012-02-11 09:43:44'),
(204, 0, 'San Martin', -7.2444881, -76.8259652, '2012-02-11 09:43:44'),
(205, 0, 'Tacna', -18.0555560, -70.2483330, '2012-02-11 09:43:44'),
(206, 0, 'Tumbes', -3.5666670, -80.4500000, '2012-02-11 09:43:44'),
(207, 0, 'Ucayali', -8.5918800, -74.3664600, '2012-02-11 09:43:44'),
(209, 0, 'GÃ¶taland', 57.4684121, 18.4867447, '2012-02-11 09:55:14'),
(211, 0, 'Norrland', 65.3301780, 18.1933590, '2012-02-11 09:55:14'),
(212, 0, 'Adrar', 19.8652176, -12.8054753, '2012-02-11 10:00:14'),
(213, 0, 'Assaba', 16.6000000, -11.9166670, '2012-02-11 10:00:14'),
(214, 0, 'BrÃ¢kna', 17.2317561, -13.1740348, '2012-02-11 10:00:14'),
(215, 0, 'Dakhlet Nouadhibou', 0.9500000, -16.2333330, '2012-02-11 10:00:14'),
(216, 0, 'Gorgol', 15.9717357, -12.6216211, '2012-02-11 10:27:22'),
(217, 0, 'Guidimaka', 15.3833330, -12.3500000, '2012-02-11 10:27:22'),
(218, 0, 'Hodh ech Chargui', 9.0000000, -7.2500000, '2012-02-11 10:27:22'),
(219, 0, 'Hodh el Gharbi', 6.5000000, -10.0000000, '2012-02-11 10:27:22'),
(220, 0, 'Inchiri', 20.0666670, -15.0666670, '2012-02-11 10:27:22'),
(221, 0, 'Nouakchott', 18.1000000, -15.9500000, '2012-02-11 10:27:22'),
(222, 0, 'Tagant', 18.7000000, -10.2000000, '2012-02-11 10:27:22'),
(223, 0, 'Tiris Zemmour', 4.0000000, -9.0000000, '2012-02-11 10:27:22'),
(224, 0, 'Trarza', 17.9833330, -14.7333330, '2012-02-11 10:27:22'),
(225, 0, 'Ariana', 36.8500000, 10.2000000, '2012-02-16 10:16:11'),
(226, 0, 'BÃ©ja', 36.7250000, 9.1820000, '2012-02-16 10:16:11'),
(227, 0, 'Ben Arous', 36.7400000, 10.2100000, '2012-02-16 10:16:11'),
(228, 0, 'Bizerte', 37.2700000, 9.8700000, '2012-02-16 10:16:11'),
(229, 0, 'GabÃ¨s', 33.8900000, 10.1100000, '2012-02-16 10:16:11'),
(230, 0, 'Gafsa', 34.4166670, 8.7833330, '2012-02-16 10:16:11'),
(231, 0, 'Jendouba', 36.4900000, 8.7800000, '2012-02-16 10:16:11'),
(232, 0, 'Kairouan', 35.6700000, 10.0900000, '2012-02-16 10:16:11'),
(233, 0, 'Kasserine', 35.1600000, 8.8300000, '2012-02-16 10:16:11'),
(234, 0, 'KÃ©bili', 33.7050000, 8.9650000, '2012-02-16 10:16:11'),
(235, 0, 'Kef', 36.1860000, 8.7000000, '2012-02-16 10:16:11'),
(236, 0, 'Mahdia', 35.5000000, 11.0600000, '2012-02-16 10:16:11'),
(237, 0, 'Manouba', 36.8071900, 10.1008100, '2012-02-16 10:16:11'),
(238, 0, 'MÃ©denine', 33.3450000, 10.4900000, '2012-02-16 10:16:11'),
(239, 0, 'Monastir', 35.7600000, 10.8100000, '2012-02-16 10:16:11'),
(240, 0, 'Nabeul', 36.4500000, 10.7400000, '2012-02-16 10:16:11'),
(241, 0, 'Sfax', 34.7400000, 10.7600000, '2012-02-16 10:16:11'),
(242, 0, 'Sidi Bouzid', 5.0333330, 9.5000000, '2012-02-16 10:16:11'),
(243, 0, 'Siliana', 36.0700000, 9.3600000, '2012-02-16 10:16:11'),
(244, 0, 'Sousse', 35.8260000, 10.6400000, '2012-02-16 10:16:11'),
(245, 0, 'Tataouine', 32.9278240, 10.4492430, '2012-02-16 10:16:11'),
(246, 0, 'Tozeur', 33.9200000, 8.1400000, '2012-02-16 10:16:11'),
(247, 0, 'Tunis', 36.7974500, 10.1657850, '2012-02-16 10:16:11'),
(248, 0, 'Zaghouan', 36.4000000, 10.1500000, '2012-02-16 10:16:11'),
(249, 0, 'Andorra', 40.9772220, -0.4447220, '2012-02-16 10:20:29'),
(250, 0, 'Reunion', -21.1151410, 55.5363840, '2012-02-16 10:29:31'),
(251, 0, 'Guadeloupe', 16.2650000, -61.5510000, '2012-02-19 19:00:40'),
(252, 0, 'Martinique', 14.6415280, -61.0241740, '2012-02-19 19:00:40'),
(253, 0, 'Guyane', 3.9338890, -53.1257820, '2012-02-19 19:01:13'),
(254, 0, 'Mayotte', -12.8275000, 45.1662440, '2012-02-19 19:01:13'),
(255, 0, 'Kayes', NULL, NULL, '2012-02-21 09:47:24'),
(256, 0, 'Koulikoro', NULL, NULL, '2012-02-21 09:47:24'),
(257, 0, 'Sikasso', NULL, NULL, '2012-02-21 09:47:24'),
(258, 0, 'SÃ©gou', NULL, NULL, '2012-02-21 09:47:24'),
(259, 0, 'Mopti', NULL, NULL, '2012-02-21 09:47:24'),
(260, 0, 'Gao', NULL, NULL, '2012-02-21 09:47:24'),
(261, 0, 'Tombouctou', NULL, NULL, '2012-02-21 09:47:24'),
(262, 0, 'Kidal', NULL, NULL, '2012-02-21 09:47:24'),
(263, 0, 'Hokkaido', NULL, NULL, '2012-02-21 10:11:07'),
(264, 0, 'Tohoku', NULL, NULL, '2012-02-21 10:11:07'),
(265, 0, 'Chubu', NULL, NULL, '2012-02-21 10:11:07'),
(266, 0, 'Kanto', NULL, NULL, '2012-02-21 10:11:07'),
(267, 0, 'Kansai', NULL, NULL, '2012-02-21 10:11:07'),
(268, 0, 'Chugoku', NULL, NULL, '2012-02-21 10:11:07'),
(269, 0, 'Shikoku', NULL, NULL, '2012-02-21 10:11:07'),
(270, 0, 'Kyushu', NULL, NULL, '2012-02-21 10:11:07'),
(271, 0, 'Alabama', NULL, NULL, '2012-02-24 12:34:42'),
(272, 0, 'Alaska', NULL, NULL, '2012-02-24 12:34:42'),
(273, 0, 'Arizona', NULL, NULL, '2012-02-24 12:34:42'),
(274, 0, 'Arkansas', NULL, NULL, '2012-02-24 12:34:42'),
(275, 0, 'Californie', NULL, NULL, '2012-02-24 12:34:42'),
(276, 0, 'Caroline du Nord', NULL, NULL, '2012-02-24 12:34:42'),
(277, 0, 'Caroline du Sud', NULL, NULL, '2012-02-24 12:34:42'),
(278, 0, 'Colorado', NULL, NULL, '2012-02-24 12:34:42'),
(279, 0, 'Connecticut', NULL, NULL, '2012-02-24 12:34:42'),
(280, 0, 'Dakota du Nord', NULL, NULL, '2012-02-24 12:34:42'),
(281, 0, 'Dakota du Sud', NULL, NULL, '2012-02-24 12:34:42'),
(282, 0, 'Delaware', NULL, NULL, '2012-02-24 12:34:42'),
(283, 0, 'Floride', NULL, NULL, '2012-02-24 12:34:42'),
(284, 0, 'Georgie', NULL, NULL, '2012-02-24 12:34:42'),
(285, 0, 'HawaÃ¯', NULL, NULL, '2012-02-24 12:34:42'),
(286, 0, 'Idaho', NULL, NULL, '2012-02-24 12:34:42'),
(287, 0, 'Illinois', NULL, NULL, '2012-02-24 12:34:42'),
(288, 0, 'Indiana', NULL, NULL, '2012-02-24 12:34:42'),
(289, 0, 'Iowa', NULL, NULL, '2012-02-24 12:34:42'),
(290, 0, 'Kansas', NULL, NULL, '2012-02-24 12:34:42'),
(291, 0, 'Kentucky', NULL, NULL, '2012-02-24 12:34:42'),
(292, 0, 'Louisiane', NULL, NULL, '2012-02-24 12:34:42'),
(293, 0, 'Maine', NULL, NULL, '2012-02-24 12:34:42'),
(294, 0, 'Massachusetts', NULL, NULL, '2012-02-24 12:34:42'),
(295, 0, 'Michigan', NULL, NULL, '2012-02-24 12:34:42'),
(296, 0, 'Minnesota', NULL, NULL, '2012-02-24 12:34:42'),
(297, 0, 'Mississippi', NULL, NULL, '2012-02-24 12:34:42'),
(298, 0, 'Missouri', NULL, NULL, '2012-02-24 12:34:42'),
(299, 0, 'Montana', NULL, NULL, '2012-02-24 12:34:42'),
(300, 0, 'Nebraska', NULL, NULL, '2012-02-24 12:34:42'),
(301, 0, 'Nevada', NULL, NULL, '2012-02-24 12:34:42'),
(302, 0, 'New Hampshire', NULL, NULL, '2012-02-24 12:34:42'),
(303, 0, 'New Jersey', NULL, NULL, '2012-02-24 12:34:42'),
(304, 0, 'New York', NULL, NULL, '2012-02-24 12:34:42'),
(305, 0, 'Nouveau Mexique', NULL, NULL, '2012-02-24 12:34:42'),
(306, 0, 'Ohio', NULL, NULL, '2012-02-24 12:34:42'),
(307, 0, 'Oklahoma', NULL, NULL, '2012-02-24 12:34:42'),
(308, 0, 'Oregon', NULL, NULL, '2012-02-24 12:34:42'),
(309, 0, 'Pennsylvanie', NULL, NULL, '2012-02-24 12:34:42'),
(310, 0, 'Rhode Island', NULL, NULL, '2012-02-24 12:34:42'),
(311, 0, 'Tenessee', NULL, NULL, '2012-02-24 12:34:42'),
(312, 0, 'Texas', NULL, NULL, '2012-02-24 12:34:42'),
(313, 0, 'Utah', NULL, NULL, '2012-02-24 12:34:42'),
(314, 0, 'Vermont', NULL, NULL, '2012-02-24 12:34:42'),
(315, 0, 'Virginie', NULL, NULL, '2012-02-24 12:34:42'),
(316, 0, 'Virginie occidentale', NULL, NULL, '2012-02-24 12:34:42'),
(317, 0, 'Washington', NULL, NULL, '2012-02-24 12:34:42'),
(318, 0, 'Wisconsin', NULL, NULL, '2012-02-24 12:34:42'),
(319, 0, 'Wyoming', NULL, NULL, '2012-02-24 12:34:42'),
(320, 0, 'Western Cape', NULL, NULL, '2012-02-24 12:40:15'),
(321, 0, 'Northern Cape', NULL, NULL, '2012-02-24 12:40:15'),
(322, 0, 'Eastern Cape', NULL, NULL, '2012-02-24 12:40:15'),
(323, 0, 'KwaZulu-Natal', NULL, NULL, '2012-02-24 12:40:15'),
(324, 0, 'Free State', NULL, NULL, '2012-02-24 12:40:15'),
(325, 0, 'North West', NULL, NULL, '2012-02-24 12:40:15'),
(326, 0, 'Gauteng', NULL, NULL, '2012-02-24 12:40:15'),
(327, 0, 'Mpumalanga', NULL, NULL, '2012-02-24 12:40:15'),
(328, 0, 'Limpopo', NULL, NULL, '2012-02-24 12:40:15'),
(329, 0, 'Dakar', NULL, NULL, '2012-02-27 11:24:13'),
(330, 0, 'Diourbel', NULL, NULL, '2012-02-27 11:24:13'),
(331, 0, 'Fatick', NULL, NULL, '2012-02-27 11:24:13'),
(332, 0, 'Kaolack', NULL, NULL, '2012-02-27 11:24:13'),
(333, 0, 'Kolda', NULL, NULL, '2012-02-27 11:24:13'),
(334, 0, 'Louga', NULL, NULL, '2012-02-27 11:24:13'),
(335, 0, 'Matam', NULL, NULL, '2012-02-27 11:24:13'),
(336, 0, 'Saint Louis', NULL, NULL, '2012-02-27 11:24:13'),
(337, 0, 'Tambacounda', NULL, NULL, '2012-02-27 11:24:13'),
(338, 0, 'ThiÃ¨s', NULL, NULL, '2012-02-27 11:24:13'),
(339, 0, 'Ziguinchor', NULL, NULL, '2012-02-27 11:24:13'),
(340, 0, 'Al Bahah', NULL, NULL, '2012-02-27 11:28:45'),
(341, 0, 'Al-Hudud ach-Chamaliya', NULL, NULL, '2012-02-27 11:28:45'),
(342, 0, 'Al Jawf', NULL, NULL, '2012-02-27 11:28:45'),
(343, 0, 'MÃ©dine', NULL, NULL, '2012-02-27 11:28:45'),
(344, 0, 'Al Qasim', NULL, NULL, '2012-02-27 11:28:45'),
(345, 0, 'HaÃ¯l', NULL, NULL, '2012-02-27 11:28:45'),
(346, 0, 'Asir', NULL, NULL, '2012-02-27 11:28:45'),
(347, 0, 'Ach-Charqiya', NULL, NULL, '2012-02-27 11:28:45'),
(348, 0, 'Riyad', NULL, NULL, '2012-02-27 11:28:45'),
(349, 0, 'Tabuk', NULL, NULL, '2012-02-27 11:28:45'),
(350, 0, 'Najran', NULL, NULL, '2012-02-27 11:28:45'),
(351, 0, 'La Mecque', NULL, NULL, '2012-02-27 11:28:45'),
(352, 0, 'Jizan', NULL, NULL, '2012-02-27 11:28:45'),
(354, 0, 'Grande plaine septentrionale', NULL, NULL, '2012-03-10 15:54:49'),
(355, 0, 'Grande plaine mÃ©ridionale', NULL, NULL, '2012-03-10 15:54:49'),
(356, 0, 'Hongrie centrale', NULL, NULL, '2012-03-10 15:54:49'),
(357, 0, 'Hongrie septentrionale', NULL, NULL, '2012-03-10 15:54:49'),
(358, 0, 'Transdanubie centrale', NULL, NULL, '2012-03-10 15:54:49'),
(359, 0, 'Transdanubie occidentale', NULL, NULL, '2012-03-10 15:54:49'),
(360, 0, 'Transdanubie mÃ©ridionale', NULL, NULL, '2012-03-10 15:54:49'),
(361, 0, 'Bouenza', NULL, NULL, '2012-03-14 08:23:09'),
(362, 0, 'Cuvette', NULL, NULL, '2012-03-14 08:23:09'),
(363, 0, 'Cuvette Ouest', NULL, NULL, '2012-03-14 08:23:09'),
(364, 0, 'Kouilou', NULL, NULL, '2012-03-14 08:23:09'),
(365, 0, 'Lekoumou', NULL, NULL, '2012-03-14 08:23:09'),
(366, 0, 'Likouala', NULL, NULL, '2012-03-14 08:23:09'),
(367, 0, 'Niari', NULL, NULL, '2012-03-14 08:23:09'),
(368, 0, 'Plateaux', NULL, NULL, '2012-03-14 08:23:09'),
(369, 0, 'Pool', NULL, NULL, '2012-03-14 08:23:09'),
(370, 0, 'Sangha', NULL, NULL, '2012-03-14 08:23:09');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
