-- phpMyAdmin SQL Dump
-- version 4.4.13.1
-- http://www.phpmyadmin.net
--
-- Client :  lacitadekz8thw.mysql.db
-- Généré le :  Jeu 14 Janvier 2016 à 14:36
-- Version du serveur :  5.5.46-0+deb7u1-log
-- Version de PHP :  5.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `lacitadekz8thw`
--

-- --------------------------------------------------------

--
-- Structure de la table `abac_attributes`
--

CREATE TABLE IF NOT EXISTS `abac_attributes` (
  `id` int(11) NOT NULL,
  `table_name` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `column_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `criteria_column` varchar(40) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `abac_attributes`
--

INSERT INTO `abac_attributes` (`id`, `table_name`, `column_name`, `criteria_column`) VALUES
(1, 'groups', 'contact_id', 'id'),
(2, 'users', 'is_enabled', 'id'),
(3, 'users', 'is_banned', 'id');

-- --------------------------------------------------------

--
-- Structure de la table `abac_attributes_data`
--

CREATE TABLE IF NOT EXISTS `abac_attributes_data` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `abac_attributes_data`
--

INSERT INTO `abac_attributes_data` (`id`, `created_at`, `updated_at`, `name`, `slug`) VALUES
(1, '2015-11-12 00:00:00', '2015-11-12 00:00:00', 'Group Owner', 'group-owner'),
(2, '2016-01-02 00:00:00', '2016-01-02 00:00:00', 'User Account Activation', 'user-account-activation'),
(3, '2016-01-02 00:00:00', '2016-01-02 00:00:00', 'User Banishment', 'user-banishment');

-- --------------------------------------------------------

--
-- Structure de la table `abac_environment_attributes`
--

CREATE TABLE IF NOT EXISTS `abac_environment_attributes` (
  `id` int(11) NOT NULL,
  `variable_name` varchar(65) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abac_policy_rules`
--

CREATE TABLE IF NOT EXISTS `abac_policy_rules` (
  `id` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `abac_policy_rules`
--

INSERT INTO `abac_policy_rules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'group-management', '2015-11-12 00:00:00', '2015-11-12 00:00:00'),
(2, 'citizenship', '2016-01-02 00:00:00', '2016-01-02 00:00:00');

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
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `abac_policy_rules_attributes`
--

INSERT INTO `abac_policy_rules_attributes` (`policy_rule_id`, `attribute_id`, `type`, `comparison_type`, `comparison`, `value`) VALUES
(1, 1, 'object', 'Numeric', 'isEqual', 'dynamic'),
(2, 2, 'user', 'Boolean', 'boolAnd', '1'),
(2, 3, 'user', 'Numeric', 'isEqual', '0');

-- --------------------------------------------------------

--
-- Structure de la table `citizen_groups`
--

CREATE TABLE IF NOT EXISTS `citizen_groups` (
  `citizen_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL,
  `code` varchar(2) NOT NULL,
  `fr` varchar(255) CHARACTER SET latin1 NOT NULL,
  `en` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `label` varchar(75) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `countries`
--

INSERT INTO `countries` (`id`, `code`, `fr`, `en`, `label`) VALUES
(1, 'AF', 'Afghanistan', 'Afghanistan', 'countries.afghanistan'),
(2, 'ZA', 'Afrique du Sud', 'South Africa', 'countries.south_africa'),
(3, 'AL', 'Albanie', 'Albania', 'countries.albania'),
(4, 'DZ', 'AlgÃƒÂ©rie', 'Algeria', 'countries.algeria'),
(5, 'DE', 'Allemagne', 'Germany', 'countries.germany'),
(6, 'AD', 'Andorre', 'Andorra', 'countries.andorra'),
(7, 'AO', 'Angola', 'Angola', 'countries.angola'),
(8, 'AI', 'Anguilla', 'Anguilla', 'countries.anguilla'),
(9, 'AQ', 'Antarctique', 'Antarctica', 'countries.antartica'),
(10, 'AG', 'Antigua-et-Barbuda', 'Antigua & Barbuda', 'countries.antigua_and_barbuda'),
(11, 'AN', 'Antilles nÃƒÂ©erlandaises', 'Netherlands Antilles', 'countries.netherlands_antilles'),
(12, 'SA', 'Arabie saoudite', 'Saudi Arabia', 'countries.saudi_arabia'),
(13, 'AR', 'Argentine', 'Argentina', 'countries.argentina'),
(14, 'AM', 'ArmÃƒÂ©nie', 'Armenia', 'countries.armenia'),
(15, 'AW', 'Aruba', 'Aruba', 'countries.aruba'),
(16, 'AU', 'Australie', 'Australia', 'countries.australia'),
(17, 'AT', 'Autriche', 'Austria', 'countries.austria'),
(18, 'AZ', 'AzerbaÃƒÂ¯djan', 'Azerbaijan', 'countries.azerbaijan'),
(19, 'BJ', 'BÃƒÂ©nin', 'Benin', 'countries.benin'),
(20, 'BS', 'Bahamas', 'Bahamas, The', 'countries.bahamas'),
(21, 'BH', 'BahreÃƒÂ¯n', 'Bahrain', 'countries.bahrain'),
(22, 'BD', 'Bangladesh', 'Bangladesh', 'countries.bangladesh'),
(23, 'BB', 'Barbade', 'Barbados', 'countries.barbados'),
(24, 'PW', 'Belau', 'Palau', 'countries.palau'),
(25, 'BE', 'Belgique', 'Belgium', 'countries.belgium'),
(26, 'BZ', 'Belize', 'Belize', 'countries.belize'),
(27, 'BM', 'Bermudes', 'Bermuda', 'countries.bermuda'),
(28, 'BT', 'Bhoutan', 'Bhutan', 'countries.bhutan'),
(29, 'BY', 'BiÃƒÂ©lorussie', 'Belarus', 'countries.belarus'),
(30, 'MM', 'Birmanie', 'Myanmar (ex-Burma)', 'countries.myanmar'),
(31, 'BO', 'Bolivie', 'Bolivia', 'countries.bolivia'),
(32, 'BA', 'Bosnie-HerzÃƒÂ©govine', 'Bosnia and Herzegovina', 'countries.bosnia_and_herzegovina'),
(33, 'BW', 'Botswana', 'Botswana', 'countries.botswana'),
(34, 'BR', 'BrÃƒÂ©sil', 'Brazil', 'countries.brazil'),
(35, 'BN', 'Brunei', 'Brunei Darussalam', 'countries.brunei_darussalam'),
(36, 'BG', 'Bulgarie', 'Bulgaria', 'countries.bulgaria'),
(37, 'BF', 'Burkina Faso', 'Burkina Faso', 'countries.burkina_faso'),
(38, 'BI', 'Burundi', 'Burundi', 'countries.burundi'),
(39, 'CI', 'CÃƒÂ´te d''Ivoire', 'Ivory Coast (see Cote d''Ivoire)', 'countries.ivory_coast'),
(40, 'KH', 'Cambodge', 'Cambodia', 'countries.cambodia'),
(41, 'CM', 'Cameroun', 'Cameroon', 'countries.cameroon'),
(42, 'CA', 'Canada', 'Canada', 'countries.canada'),
(43, 'CV', 'Cap-Vert', 'Cape Verde', 'countries.cape_verde'),
(44, 'CL', 'Chili', 'Chile', 'countries.chile'),
(45, 'CN', 'Chine', 'China', 'countries.china'),
(46, 'CY', 'Chypre', 'Cyprus', 'countries.cyprus'),
(47, 'CO', 'Colombie', 'Colombia', 'countries.colombia'),
(48, 'KM', 'Comores', 'Comoros', 'countries.comoros'),
(49, 'CG', 'Congo', 'Congo', 'countries.congo'),
(50, 'KP', 'CorÃƒÂ©e du Nord', 'Korea, Demo. People''s Rep. of', 'countries.north_korea'),
(51, 'KR', 'CorÃƒÂ©e du Sud', 'Korea, (South) Republic of', 'countries.south_korea'),
(52, 'CR', 'Costa Rica', 'Costa Rica', 'countries.costa_rica'),
(53, 'HR', 'Croatie', 'Croatia', 'countries.croatia'),
(54, 'CU', 'Cuba', 'Cuba', 'countries.cuba'),
(55, 'DK', 'Danemark', 'Denmark', 'countries.denmark'),
(56, 'DJ', 'Djibouti', 'Djibouti', 'countries.djibouti'),
(57, 'DM', 'Dominique', 'Dominica', 'countries.dominica'),
(58, 'EG', 'Egypte', 'Egypt', 'countries.egypt'),
(59, 'AE', 'Emirats arabes unis', 'United Arab Emirates', 'countries.united_arab_emirates'),
(60, 'EC', 'Equateur', 'Ecuador', 'countries.ecuador'),
(61, 'ER', 'ErythrÃƒÂ©e', 'Eritrea', 'countries.eritrea'),
(62, 'ES', 'Espagne', 'Spain', 'countries.spain'),
(63, 'EE', 'Estonie', 'Estonia', 'countries.estonia'),
(64, 'US', 'Etats-Unis', 'United States', 'countries.united_states'),
(65, 'ET', 'Ethiopie', 'Ethiopia', 'countries.ethiopia'),
(66, 'FI', 'Finlande', 'Finland', 'countries.finland'),
(67, 'FR', 'France', 'France', 'countries.france'),
(68, 'GE', 'GÃƒÂ©orgie', 'Georgia', 'countries.georgia'),
(69, 'GA', 'Gabon', 'Gabon', 'countries.gabon'),
(70, 'GM', 'Gambie', 'Gambia, the', 'countries.gambia'),
(71, 'GH', 'Ghana', 'Ghana', 'countries.ghana'),
(72, 'GI', 'Gibraltar', 'Gibraltar', 'countries.gibraltar'),
(73, 'GR', 'GrÃƒÂ¨ce', 'Greece', 'countries.greece'),
(74, 'GD', 'Grenade', 'Grenada', 'countries.grenada'),
(75, 'GL', 'Groenland', 'Greenland', 'countries.greenland'),
(77, 'GU', 'Guam', 'Guam', 'countries.guam'),
(78, 'GT', 'Guatemala', 'Guatemala', 'countries.guatemala'),
(79, 'GN', 'GuinÃƒÂ©e', 'Guinea', 'countries.guinea'),
(80, 'GQ', 'GuinÃƒÂ©e ÃƒÂ©quatoriale', 'Equatorial Guinea', 'countries.equatorial_guinea'),
(81, 'GW', 'GuinÃƒÂ©e-Bissao', 'Guinea-Bissau', 'countries.guinea_bissau'),
(82, 'GY', 'Guyana', 'Guyana', 'countries.guyana'),
(84, 'HT', 'HaÃƒÂ¯ti', 'Haiti', 'countries.haiti'),
(85, 'HN', 'Honduras', 'Honduras', 'countries.honduras'),
(86, 'HK', 'Hong Kong', 'Hong Kong, (China)', 'countries.hong_kong'),
(87, 'HU', 'Hongrie', 'Hungary', 'countries.hungary'),
(88, 'BV', 'Ile Bouvet', 'Bouvet Island', 'countries.bouvet_island'),
(89, 'CX', 'Ile Christmas', 'Christmas Island', 'countries.christmas_island'),
(90, 'NF', 'Ile Norfolk', 'Norfolk Island', 'countries.norfolk_island'),
(91, 'KY', 'Iles Cayman', 'Cayman Islands', 'countries.cayman_islands'),
(92, 'CK', 'Iles Cook', 'Cook Islands', 'countries.cook_islands'),
(93, 'FO', 'Iles FÃƒÂ©roÃƒÂ©', 'Faroe Islands', 'countries.faroe_islands'),
(94, 'FK', 'Iles Falkland', 'Falkland Islands (Malvinas)', 'countries.malvinas'),
(95, 'FJ', 'Iles Fidji', 'Fiji', 'countries.fiji'),
(96, 'GS', 'Iles GÃƒÂ©orgie du Sud et Sandwich du Sud', 'S. Georgia and S. Sandwich Is.', 'countries.south_georgia_and_south_sandwich_islands'),
(97, 'HM', 'Iles Heard et McDonald', 'Heard and McDonald Islands', 'countries.heard_and_mcdonald_islands'),
(98, 'MH', 'Iles Marshall', 'Marshall Islands', 'countries.marshall_islands'),
(99, 'PN', 'Iles Pitcairn', 'Pitcairn Island', 'countries.pitcairn_island'),
(100, 'SB', 'Iles Salomon', 'Solomon Islands', 'countries.solomon_islands'),
(101, 'SJ', 'Iles Svalbard et Jan Mayen', 'Svalbard and Jan Mayen Islands', 'countries.svalbard_and_jan_mayen_islands'),
(102, 'TC', 'Iles Turks-et-Caicos', 'Turks and Caicos Islands', 'countries.turks_and_caicos_islands'),
(103, 'VI', 'Iles Vierges amÃƒÂ©ricaines', 'Virgin Islands, U.S.', 'countries.virgin_islands_us'),
(104, 'VG', 'Iles Vierges britanniques', 'Virgin Islands, British', 'countries.virgin_islands_uk'),
(105, 'CC', 'Iles des Cocos (Keeling)', 'Cocos (Keeling) Islands', 'countries.cocos_islands'),
(106, 'UM', 'Iles mineures ÃƒÂ©loignÃƒÂ©es des Etats-Unis', 'US Minor Outlying Islands', 'countries.us_minor_outlying_islands'),
(107, 'IN', 'Inde', 'India', 'countries.india'),
(108, 'ID', 'IndonÃƒÂ©sie', 'Indonesia', 'countries.indonesia'),
(109, 'IR', 'Iran', 'Iran, Islamic Republic of', 'countries.iran'),
(110, 'IQ', 'Iraq', 'Iraq', 'countries.iraq'),
(111, 'IE', 'Irlande', 'Ireland', 'countries.ireland'),
(112, 'IS', 'Islande', 'Iceland', 'countries.iceland'),
(113, 'IL', 'IsraÃƒÂ«l', 'Israel', 'countries.israel'),
(114, 'IT', 'Italie', 'Italy', 'countries.italy'),
(115, 'JM', 'JamaÃƒÂ¯que', 'Jamaica', 'countries.jamaica'),
(116, 'JP', 'Japon', 'Japan', 'countries.japan'),
(117, 'JO', 'Jordanie', 'Jordan', 'countries.jordan'),
(118, 'KZ', 'Kazakhstan', 'Kazakhstan', 'countries.kazakhstan'),
(119, 'KE', 'Kenya', 'Kenya', 'countries.kenya'),
(120, 'KG', 'Kirghizistan', 'Kyrgyzstan', 'countries.kyrgyzstan'),
(121, 'KI', 'Kiribati', 'Kiribati', 'countries.kiribati'),
(122, 'KW', 'KoweÃƒÂ¯t', 'Kuwait', 'countries.kuwait'),
(123, 'LA', 'Laos', 'Lao People''s Democratic Republic', 'countries.lao'),
(124, 'LS', 'Lesotho', 'Lesotho', 'countries.lesoto'),
(125, 'LV', 'Lettonie', 'Latvia', 'countries.latvia'),
(126, 'LB', 'Liban', 'Lebanon', 'countries.lebanon'),
(127, 'LR', 'Liberia', 'Liberia', 'countries.liberia'),
(128, 'LY', 'Libye', 'Libyan Arab Jamahiriya', 'countries.libya'),
(129, 'LI', 'Liechtenstein', 'Liechtenstein', 'countries.liechtenstein'),
(130, 'LT', 'Lituanie', 'Lithuania', 'countries.lithuania'),
(131, 'LU', 'Luxembourg', 'Luxembourg', 'countries.luxembourg'),
(132, 'MO', 'Macao', 'Macao, (China)', 'countries.macao'),
(133, 'MG', 'Madagascar', 'Madagascar', 'countries.madagascar'),
(134, 'MY', 'Malaisie', 'Malaysia', 'countries.malaysia'),
(135, 'MW', 'Malawi', 'Malawi', 'countries.malawi'),
(136, 'MV', 'Maldives', 'Maldives', 'countries.maldives'),
(137, 'ML', 'Mali', 'Mali', 'countries.mali'),
(138, 'MT', 'Malte', 'Malta', 'countries.malta'),
(139, 'MP', 'Mariannes du Nord', 'Northern Mariana Islands', 'countries.northern_mariana_islands'),
(140, 'MA', 'Maroc', 'Morocco', 'countries.morocco'),
(141, 'MQ', 'Martinique', 'Martinique', 'countries.martinique'),
(142, 'MU', 'Maurice', 'Mauritius', 'countries.mauritius'),
(143, 'MR', 'Mauritanie', 'Mauritania', 'countries.mauritania'),
(144, 'YT', 'Mayotte', 'Mayotte', 'countries.mayotte'),
(145, 'MX', 'Mexique', 'Mexico', 'countries.mexico'),
(146, 'FM', 'MicronÃƒÂ©sie', 'Micronesia, Federated States of', 'countries.micronesia'),
(147, 'MD', 'Moldavie', 'Moldova, Republic of', 'countries.moldova'),
(148, 'MC', 'Monaco', 'Monaco', 'countries.monaco'),
(149, 'MN', 'Mongolie', 'Mongolia', 'countries.mongolia'),
(150, 'MS', 'Montserrat', 'Montserrat', 'countries.montserrat'),
(151, 'MZ', 'Mozambique', 'Mozambique', 'countries.mozanbique'),
(152, 'NP', 'NÃƒÂ©pal', 'Nepal', 'countries.nepal'),
(153, 'NA', 'Namibie', 'Namibia', 'countries.namibia'),
(154, 'NR', 'Nauru', 'Nauru', 'countries.nauru'),
(155, 'NI', 'Nicaragua', 'Nicaragua', 'countries.nicaragua'),
(156, 'NE', 'Niger', 'Niger', 'countries.niger'),
(157, 'NG', 'Nigeria', 'Nigeria', 'countries.nigeria'),
(158, 'NU', 'NiouÃƒÂ©', 'Niue', 'countries.niue'),
(159, 'NO', 'NorvÃƒÂ¨ge', 'Norway', 'countries.norway'),
(160, 'NC', 'Nouvelle-CalÃƒÂ©donie', 'New Caledonia', 'countries.new_caledonia'),
(161, 'NZ', 'Nouvelle-ZÃƒÂ©lande', 'New Zealand', 'countries.new_zealand'),
(162, 'OM', 'Oman', 'Oman', 'countries.oman'),
(163, 'UG', 'Ouganda', 'Uganda', 'countries.uganda'),
(164, 'UZ', 'OuzbÃƒÂ©kistan', 'Uzbekistan', 'countries.uzbekistan'),
(165, 'PE', 'PÃƒÂ©rou', 'Peru', 'countries.peru'),
(166, 'PK', 'Pakistan', 'Pakistan', 'countries.pakistan'),
(167, 'PA', 'Panama', 'Panama', 'countries.panama'),
(168, 'PG', 'Papouasie-Nouvelle-GuinÃƒÂ©e', 'Papua New Guinea', 'countries.papua_new_guinea'),
(169, 'PY', 'Paraguay', 'Paraguay', 'countries.paraguay'),
(170, 'NL', 'Pays-Bas', 'Netherlands', 'countries.netherlands'),
(171, 'PH', 'Philippines', 'Philippines', 'countries.philippines'),
(172, 'PL', 'Pologne', 'Poland', 'countries.poland'),
(173, 'PF', 'PolynÃƒÂ©sie franÃƒÂ§aise', 'French Polynesia', 'countries.french_polynesia'),
(174, 'PR', 'Porto Rico', 'Puerto Rico', 'countries.puerto_rico'),
(175, 'PT', 'Portugal', 'Portugal', 'countries.portugal'),
(176, 'QA', 'Qatar', 'Qatar', 'countries.qatar'),
(177, 'CF', 'RÃƒÂ©publique centrafricaine', 'Central African Republic', 'countries.central_african_republic'),
(178, 'CD', 'RÃƒÂ©publique dÃƒÂ©mocratique du Congo', 'Congo, Democratic Rep. of the', 'countries.congo'),
(179, 'DO', 'RÃƒÂ©publique dominicaine', 'Dominican Republic', 'countries.dominican_republic'),
(180, 'CZ', 'RÃƒÂ©publique tchÃƒÂ¨que', 'Czech Republic', 'countries.czech_republic'),
(181, 'RE', 'RÃƒÂ©union', 'Reunion', 'countries.reunion'),
(182, 'RO', 'Roumanie', 'Romania', 'countries.romania'),
(183, 'GB', 'Royaume-Uni', 'Saint Pierre and Miquelon', 'countries.united_kingdom'),
(184, 'RU', 'Russie', 'Russia (Russian Federation)', 'countries.russia'),
(185, 'RW', 'Rwanda', 'Rwanda', 'countries.rwanda'),
(186, 'SN', 'SÃƒÂ©nÃƒÂ©gal', 'Senegal', 'countries.senegal'),
(187, 'EH', 'Sahara occidental', 'Western Sahara', 'countries.western_sahara'),
(188, 'KN', 'Saint-Christophe-et-NiÃƒÂ©vÃƒÂ¨s', 'Saint Kitts and Nevis', 'countries.saint_kitts_and_nevis'),
(189, 'SM', 'Saint-Marin', 'San Marino', 'countries.san_marino'),
(190, 'PM', 'Saint-Pierre-et-Miquelon', 'Saint Pierre and Miquelon', 'countries.saint_pierre_and_miquelon'),
(191, 'VA', 'Saint-SiÃƒÂ¨ge ', 'Vatican City State (Holy See)', 'countries.vatican_city_state'),
(192, 'VC', 'Saint-Vincent-et-les-Grenadines', 'Saint Vincent and the Grenadines', 'countries.saint_vicent_and_the_grenadines'),
(193, 'SH', 'Sainte-HÃƒÂ©lÃƒÂ¨ne', 'Saint Helena', 'countries.saint_helena'),
(194, 'LC', 'Sainte-Lucie', 'Saint Lucia', 'countries.saint_lucia'),
(195, 'SV', 'Salvador', 'El Salvador', 'countries.el_salvador'),
(196, 'WS', 'Samoa', 'Samoa', 'countries.samoa'),
(197, 'AS', 'Samoa amÃƒÂ©ricaines', 'American Samoa', 'countries.american_samoa'),
(198, 'ST', 'Sao TomÃƒÂ©-et-Principe', 'Sao Tome and Principe', 'countries.sao_tome_and_principe'),
(199, 'SC', 'Seychelles', 'Seychelles', 'countries.seychelles'),
(200, 'SL', 'Sierra Leone', 'Sierra Leone', 'countries.sierra_leone'),
(201, 'SG', 'Singapour', 'Singapore', 'countries.singapore'),
(202, 'SI', 'SlovÃƒÂ©nie', 'Slovenia', 'countries.slovenia'),
(203, 'SK', 'Slovaquie', 'Slovakia', 'countries.slovakia'),
(204, 'SO', 'Somalie', 'Somalia', 'countries.somalia'),
(205, 'SD', 'Soudan', 'Sudan', 'countries.sudan'),
(206, 'LK', 'Sri Lanka', 'Sri Lanka (ex-Ceilan)', 'countries.sri_lanka'),
(207, 'SE', 'SuÃƒÂ¨de', 'Sweden', 'countries.sweden'),
(208, 'CH', 'Suisse', 'Switzerland', 'countries.switzerland'),
(209, 'SR', 'Suriname', 'Suriname', 'countries.suriname'),
(210, 'SZ', 'Swaziland', 'Swaziland', 'countries.swaziland'),
(211, 'SY', 'Syrie', 'Syrian Arab Republic', 'countries.syria'),
(212, 'TW', 'TaÃƒÂ¯wan', 'Taiwan', 'countries.taiwan'),
(213, 'TJ', 'Tadjikistan', 'Tajikistan', 'countries.taijikistan'),
(214, 'TZ', 'Tanzanie', 'Tanzania, United Republic of', 'countries.tanzania'),
(215, 'TD', 'Tchad', 'Chad', 'countries.chad'),
(216, 'TF', 'Terres australes franÃƒÂ§aises', 'French Southern Territories - TF', 'countries.french_southern_territories'),
(217, 'IO', 'Territoire britannique de l''OcÃƒÂ©an Indien', 'British Indian Ocean Territory', 'countries.british_indian_ocean_territory'),
(218, 'TH', 'ThaÃƒÂ¯lande', 'Thailand', 'countries.thailand'),
(219, 'TL', 'Timor Oriental', 'Timor-Leste (East Timor)', 'countries.timor_leste'),
(220, 'TG', 'Togo', 'Togo', 'countries.togo'),
(221, 'TK', 'TokÃƒÂ©laou', 'Tokelau', 'countries.tokelau'),
(222, 'TO', 'Tonga', 'Tonga', 'countries.tonga'),
(223, 'TT', 'TrinitÃƒÂ©-et-Tobago', 'Trinidad & Tobago', 'countries.trinidad_and_tobago'),
(224, 'TN', 'Tunisie', 'Tunisia', 'countries.tunisia'),
(225, 'TM', 'TurkmÃƒÂ©nistan', 'Turkmenistan', 'countries.turkmenistan'),
(226, 'TR', 'Turquie', 'Turkey', 'countries.turkey'),
(227, 'TV', 'Tuvalu', 'Tuvalu', 'countries.tuvalu'),
(228, 'UA', 'Ukraine', 'Ukraine', 'countries.ukraine'),
(229, 'UY', 'Uruguay', 'Uruguay', 'countries.uruguay'),
(230, 'VU', 'Vanuatu', 'Vanuatu', 'countries.vanuatu'),
(231, 'VE', 'Venezuela', 'Venezuela', 'countries.venezuela'),
(232, 'VN', 'ViÃƒÂªt Nam', 'Viet Nam', 'countries.viet_nam'),
(233, 'WF', 'Wallis-et-Futuna', 'Wallis and Futuna', 'countries.wallis_and_futuna'),
(234, 'YE', 'YÃƒÂ©men', 'Yemen', 'countries.yemen'),
(235, 'YU', 'Yougoslavie', 'Saint Pierre and Miquelon', 'countries.yougoslavia'),
(236, 'ZM', 'Zambie', 'Zambia', 'countries.zambia'),
(237, 'ZW', 'Zimbabwe', 'Zimbabwe', 'countries.zimbabwe'),
(238, 'MK', 'ex-RÃƒÂ©publique yougoslave de MacÃƒÂ©doine', 'Macedonia, TFYR', 'countries.macedonia');

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `description` varchar(250) NOT NULL,
  `name` varchar(65) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `groups`
--

INSERT INTO `groups` (`id`, `type_id`, `description`, `name`, `contact_id`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 2, 'Groupe prive regroupant les developpeurs du site web', 'Développeurs', 1, 0, '2012-01-27 10:14:42', '0000-00-00 00:00:00'),
(2, 1, 'Groupe regional', 'Ile-de-France', 7, 1, '2012-02-07 20:08:35', '2015-10-09 12:39:14'),
(10, 1, 'Groupe regional', 'Midi-Pyrénées', 1, 1, '2012-02-07 19:37:13', '0000-00-00 00:00:00'),
(15, 1, 'Groupe regional', 'Picardie', 1, 1, '2012-02-07 21:28:54', '0000-00-00 00:00:00'),
(25, 1, 'Groupe regional', 'Bretagne', 1, 1, '2012-02-07 22:44:06', '0000-00-00 00:00:00'),
(55, 1, 'Groupe regional', 'Languedoc-Roussillon', 1, 1, '2012-02-08 14:33:20', '0000-00-00 00:00:00'),
(56, 1, 'Groupe regional', 'Aquitaine', 1, 1, '2012-02-08 15:26:56', '0000-00-00 00:00:00'),
(57, 1, 'Groupe regional', 'Centre', 1, 1, '2012-02-08 16:22:27', '0000-00-00 00:00:00'),
(58, 1, 'Groupe regional', 'Nord-Pas-de-Calais', 1, 1, '2012-02-08 17:40:33', '0000-00-00 00:00:00'),
(59, 1, 'Groupe regional', 'Rhône-Alpes', 1, 1, '2012-02-08 17:41:02', '0000-00-00 00:00:00'),
(60, 1, 'Groupe regional', 'Lorraine', 1, 1, '2012-02-08 18:20:35', '0000-00-00 00:00:00'),
(61, 1, 'Groupe regional', 'Basse-Normandie', 1, 1, '2012-02-08 18:26:53', '0000-00-00 00:00:00'),
(62, 1, 'Groupe regional', 'Corse', 1, 1, '2012-02-08 18:37:35', '0000-00-00 00:00:00'),
(63, 1, 'Groupe regional', 'Pays de la Loire', 1, 1, '2012-02-08 21:07:08', '0000-00-00 00:00:00'),
(64, 1, 'Groupe regional', 'Alsace', 1, 1, '2012-02-09 00:40:44', '0000-00-00 00:00:00'),
(65, 1, 'Groupe regional', 'La province de Luxembourg', 1, 1, '2012-02-09 01:44:11', '0000-00-00 00:00:00'),
(66, 1, 'Groupe regional', 'Départements d''Outre-Mer', 1, 1, '2012-02-09 08:51:49', '0000-00-00 00:00:00'),
(67, 1, 'Groupe regional', 'Auvergne', 1, 1, '2012-02-09 10:18:56', '0000-00-00 00:00:00'),
(68, 1, 'Groupe regional', 'Haute-Normandie', 1, 1, '2012-02-09 13:14:03', '0000-00-00 00:00:00'),
(69, 1, 'Groupe regional', 'Neuchâtel', 1, 1, '2012-02-09 14:37:24', '0000-00-00 00:00:00'),
(70, 1, 'Groupe regional', 'Champagne-Ardenne', 1, 1, '2012-02-09 15:23:59', '0000-00-00 00:00:00'),
(71, 1, 'Groupe regional', 'Le Hainaut', 1, 1, '2012-02-09 15:42:12', '0000-00-00 00:00:00'),
(72, 1, 'Groupe regional', 'Bourgogne', 1, 1, '2012-02-09 20:18:48', '0000-00-00 00:00:00'),
(73, 1, 'Groupe regional', 'Vaud', 1, 1, '2012-02-09 21:18:10', '0000-00-00 00:00:00'),
(74, 1, 'Groupe regional', 'Poitou-Charentes', 1, 1, '2012-02-09 21:33:13', '0000-00-00 00:00:00'),
(75, 1, 'Groupe regional', 'Grand Casablanca', 1, 1, '2012-02-09 22:26:49', '0000-00-00 00:00:00'),
(76, 1, 'Groupe regional', 'Franche-Comté', 1, 1, '2012-02-09 23:14:00', '0000-00-00 00:00:00'),
(77, 1, 'Groupe regional', 'Provence-Alpes-Côte-d''Azur', 1, 1, '2012-02-09 23:40:00', '0000-00-00 00:00:00'),
(78, 1, 'Groupe regional', 'Algeria', 1, 1, '2012-02-10 11:51:02', '0000-00-00 00:00:00'),
(79, 1, 'Groupe regional', 'Québec', 1, 1, '2012-02-10 15:04:40', '0000-00-00 00:00:00'),
(80, 1, 'Groupe regional', 'Province Sud', 1, 1, '2012-02-11 09:43:39', '0000-00-00 00:00:00'),
(81, 1, 'Groupe regional', 'La province de Namur', 1, 1, '2012-02-11 22:15:04', '0000-00-00 00:00:00'),
(82, 1, 'Groupe regional', 'La province de Liège', 1, 1, '2012-02-12 11:37:35', '0000-00-00 00:00:00'),
(83, 1, 'Groupe regional', 'Lisboa', 1, 1, '2012-02-12 17:20:48', '0000-00-00 00:00:00'),
(84, 1, 'Groupe regional', 'Limousin', 1, 1, '2012-02-12 19:16:27', '0000-00-00 00:00:00'),
(85, 1, 'Groupe regional', 'Le Brabant wallon', 1, 1, '2012-02-14 08:35:45', '0000-00-00 00:00:00'),
(86, 1, 'Groupe regional', 'Fribourg', 1, 1, '2012-02-14 22:44:27', '0000-00-00 00:00:00'),
(87, 1, 'Groupe regional', 'La Flandre orientale', 1, 1, '2012-02-15 12:24:00', '0000-00-00 00:00:00'),
(88, 2, 'Groupe d''informations', 'Groupe d''informations', 1, 0, '2012-02-16 16:35:55', '0000-00-00 00:00:00'),
(89, 1, 'Groupe regional', 'Tunis', 1, 1, '2012-02-17 23:27:36', '0000-00-00 00:00:00'),
(90, 1, 'Groupe regional', 'Le Brabant flamand', 1, 1, '2012-02-20 23:22:07', '0000-00-00 00:00:00'),
(91, 1, 'Groupe regional', 'Guyane', 1, 1, '2012-02-21 16:44:48', '0000-00-00 00:00:00'),
(92, 1, 'Groupe regional', 'Jura', 1, 1, '2012-02-22 17:37:38', '0000-00-00 00:00:00'),
(93, 1, 'Groupe regional', 'Haute-Savoie', 1, 1, '2012-02-26 20:27:46', '0000-00-00 00:00:00'),
(94, 1, 'Groupe regional', 'Valais', 1, 1, '2012-02-26 22:55:48', '0000-00-00 00:00:00'),
(96, 1, 'Groupe regional', 'Nordrhein-Westfalen', 1, 1, '2012-03-02 23:47:24', '0000-00-00 00:00:00'),
(97, 1, 'Groupe regional', 'Meknès-Tafilalet', 1, 1, '2012-03-04 02:51:49', '0000-00-00 00:00:00'),
(98, 1, 'Groupe regional', 'Martinique', 1, 1, '2012-03-08 02:59:27', '0000-00-00 00:00:00'),
(99, 1, 'Groupe regional', 'Berlin', 1, 1, '2012-03-09 00:40:13', '0000-00-00 00:00:00'),
(100, 1, 'Groupe regional', 'Guadeloupe', 1, 1, '2012-03-15 02:50:34', '0000-00-00 00:00:00'),
(101, 1, 'Groupe regional', 'Territoires d''Outre-Mer', 1, 1, '2012-03-17 04:37:48', '0000-00-00 00:00:00'),
(102, 1, 'Groupe regional', 'Baden-Württemberg', 1, 1, '2012-03-19 21:21:47', '0000-00-00 00:00:00'),
(103, 1, 'Groupe regional', 'Reunion', 1, 1, '2012-03-25 23:54:37', '0000-00-00 00:00:00'),
(104, 1, 'Groupe regional', 'Rabat-Salé-Zemmour-Zaër', 1, 1, '2012-03-26 00:59:31', '0000-00-00 00:00:00'),
(105, 1, 'Groupe regional', 'Tanger-Tétouan', 1, 1, '2012-03-26 20:22:42', '0000-00-00 00:00:00'),
(106, 1, 'Groupe regional', 'Genève', 1, 1, '2012-04-01 08:27:03', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `group_types`
--

CREATE TABLE IF NOT EXISTS `group_types` (
  `id` int(11) NOT NULL,
  `label` varchar(45) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `group_types`
--

INSERT INTO `group_types` (`id`, `label`) VALUES
(1, 'r&Atilde;&copy;gional'),
(2, 'Thematiques');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `msg` varchar(2500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `timelogs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted_by_author` tinyint(1) NOT NULL,
  `deleted_by_recipient` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `motions`
--

CREATE TABLE IF NOT EXISTS `motions` (
  `id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `means` text NOT NULL,
  `created_at` datetime NOT NULL,
  `ended_at` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '1 : vote ouvert; 0 : vote fermÃ©',
  `is_approved` tinyint(1) NOT NULL COMMENT '1: acceptÃ©e; 0: refusÃ©e',
  `Score` varchar(6) DEFAULT NULL COMMENT 'score du vote (base64_encode)'
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `motions_votes`
--

CREATE TABLE IF NOT EXISTS `motions_votes` (
  `motion_id` int(11) NOT NULL,
  `choice` int(11) NOT NULL,
  `hash` varchar(129) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `motions_vote_tokens`
--

CREATE TABLE IF NOT EXISTS `motions_vote_tokens` (
  `motion_id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `browser` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `motion_themes`
--

CREATE TABLE IF NOT EXISTS `motion_themes` (
  `id` smallint(6) NOT NULL,
  `label` varchar(125) NOT NULL,
  `duration` tinyint(4) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `motion_themes`
--

INSERT INTO `motion_themes` (`id`, `label`, `duration`) VALUES
(1, 'motion_themes.organization', 8),
(2, 'motion_themes.justice', 8),
(3, 'motion_themes.constitutional', 8),
(4, 'motion_themes.actions', 8);

-- --------------------------------------------------------

--
-- Structure de la table `regions`
--

CREATE TABLE IF NOT EXISTS `regions` (
  `id` int(11) NOT NULL,
  `country_id` smallint(6) NOT NULL,
  `name` varchar(85) CHARACTER SET utf8 NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=371 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `regions`
--

INSERT INTO `regions` (`id`, `country_id`, `name`, `longitude`, `latitude`, `created_at`) VALUES
(1, 67, 'Alsace', '48.5000000', '7.5000000', '2012-01-25 09:37:05'),
(2, 67, 'Aquitaine', '44.5833330', '0.0166670', '2012-01-25 09:37:05'),
(3, 67, 'Auvergne', '45.3333330', '3.0000000', '2012-01-25 09:37:05'),
(4, 67, 'Basse-Normandie', '49.0000000', '-1.0000000', '2012-01-25 09:37:05'),
(5, 67, 'Bourgogne', '47.2475000', '4.1513900', '2012-01-25 09:37:05'),
(6, 67, 'Bretagne', '48.0000000', '-3.0000000', '2012-01-25 09:37:05'),
(7, 67, 'Centre', '47.5000000', '1.7500000', '2012-01-25 09:37:05'),
(8, 67, 'Champagne-Ardenne', '49.0000000', '4.5000000', '2012-01-25 09:37:05'),
(9, 67, 'Corse', '42.1500000', '9.0833330', '2012-01-25 09:37:05'),
(10, 67, 'DÃ©partements d''Outre-Mer', NULL, NULL, '2012-01-25 09:37:05'),
(11, 67, 'Franche-ComtÃ©', '47.0000000', '6.0000000', '2012-01-25 09:37:05'),
(12, 67, 'Haute-Normandie', '49.5000000', '1.0000000', '2012-01-25 09:37:05'),
(14, 67, 'Ile-de-France', '48.5000000', '2.5000000', '2012-01-25 09:37:05'),
(15, 67, 'Languedoc-Roussillon', '43.6666670', '3.1666670', '2012-01-25 09:37:05'),
(16, 67, 'Limousin', '45.6879500', '1.6204830', '2012-01-25 09:37:05'),
(17, 67, 'Lorraine', '49.0000000', '6.0000000', '2012-01-25 09:37:05'),
(18, 67, 'Midi-PyrÃ©nÃ©es', '43.5000000', '1.3333330', '2012-01-25 09:37:05'),
(19, 67, 'Nord-Pas-de-Calais', '50.4666670', '2.7166670', '2012-01-25 09:37:05'),
(20, 67, 'Pays de la Loire', '47.4280000', '-1.1430000', '2012-01-25 09:37:05'),
(21, 67, 'Picardie', '49.5000000', '2.8333330', '2012-01-25 09:37:05'),
(22, 67, 'Poitou-Charentes', '46.0833330', '0.1666670', '2012-01-25 09:37:05'),
(23, 67, 'Provence-Alpes-CÃ´te-d''Azur', '44.0000000', '6.0000000', '2012-01-25 09:37:05'),
(24, 67, 'RhÃ´ne-Alpes', '45.5000000', '5.3333330', '2012-01-25 09:37:05'),
(25, 67, 'Territoires d''Outre-Mer', NULL, NULL, '2012-01-25 09:37:05'),
(13, 67, 'Haute-Savoie', '46.0000000', '6.3333330', '2012-02-07 09:32:36'),
(26, 0, 'Appenzell Rh.-Ext', '47.3664810', '9.3000916', '2012-02-07 10:09:57'),
(27, 0, 'Appenzell Rh.-Int', '47.3161925', '9.4316573', '2012-02-07 10:09:57'),
(28, 0, 'Argovie', '47.3907380', '8.0455830', '2012-02-07 10:09:57'),
(29, 0, 'BÃ¢le-Campagne', '47.4819450', '7.7403550', '2012-02-07 10:09:57'),
(30, 0, 'BÃ¢le-Ville', '47.5666670', '7.6000000', '2012-02-07 10:09:57'),
(31, 0, 'Bern', '46.9500000', '7.4500000', '2012-02-07 10:09:57'),
(32, 0, 'Fribourg', '46.8000000', '7.1500000', '2012-02-07 10:09:57'),
(33, 0, 'GenÃ¨ve', '46.2000130', '6.1499850', '2012-02-07 10:09:57'),
(34, 0, 'Glaris', '47.0333330', '9.0666670', '2012-02-07 10:09:57'),
(35, 0, 'Grisons', '46.7500000', '9.5000000', '2012-02-07 10:09:57'),
(36, 0, 'Jura', '47.3444474', '7.1430608', '2012-02-07 10:09:57'),
(37, 0, 'Lucerne', '47.0500000', '8.3000000', '2012-02-07 10:09:57'),
(38, 0, 'NeuchÃ¢tel', '46.9902810', '6.9305670', '2012-02-07 10:09:57'),
(39, 0, 'Nidwald', '46.9333330', '8.0666670', '2012-02-07 10:09:57'),
(40, 0, 'Obwald', '46.8666670', '8.0333330', '2012-02-07 10:09:57'),
(41, 0, 'Schaffhouse', '47.7000010', '8.6333330', '2012-02-07 10:09:57'),
(42, 0, 'Schywtz', '47.0198346', '8.6473977', '2012-02-07 10:09:57'),
(43, 0, 'Soleure', '47.2083310', '7.5375130', '2012-02-07 10:09:57'),
(44, 0, 'St-Gall', '47.4166670', '9.3666670', '2012-02-07 10:09:57'),
(45, 0, 'Tessin', '46.3317340', '8.8004529', '2012-02-07 10:09:57'),
(46, 0, 'Thurgovie', '47.5833330', '9.0666670', '2012-02-07 10:09:57'),
(47, 0, 'Uri', '46.7738629', '8.6025153', '2012-02-07 10:09:57'),
(48, 0, 'Valais', '46.0666670', '7.6000000', '2012-02-07 10:09:57'),
(49, 0, 'Vaud', '46.6166670', '6.5500000', '2012-02-07 10:09:57'),
(50, 0, 'Zoug', '47.1666670', '8.5166670', '2012-02-07 10:09:57'),
(51, 0, 'Zurich', '47.3778950', '8.5411830', '2012-02-07 10:09:57'),
(52, 0, 'Le Brabant flamand', '50.8815434', '4.5645970', '2012-02-07 10:28:21'),
(53, 0, 'Le Brabant wallon', '50.6332410', '4.5243150', '2012-02-07 10:28:21'),
(54, 0, 'La Flandre occidentale', '51.0536024', '3.1457942', '2012-02-07 10:28:21'),
(55, 0, 'La Flandre orientale', '51.0362101', '3.7373124', '2012-02-07 10:28:21'),
(56, 0, 'Le Hainaut', '50.5257076', '4.0621017', '2012-02-07 10:28:21'),
(57, 0, 'Le Limbourg', '50.9738973', '5.3419677', '2012-02-07 10:28:21'),
(58, 0, 'La province d''Anvers', '51.2194933', '4.4024500', '2012-02-07 10:28:21'),
(59, 0, 'La province de LiÃ¨ge', '50.6325574', '5.5796662', '2012-02-07 10:28:21'),
(60, 0, 'La province de Luxembourg', '49.6568287', '6.0333955', '2012-02-07 10:28:21'),
(61, 0, 'La province de Namur', '50.4653280', '4.8676650', '2012-02-07 10:28:21'),
(62, 0, 'Monaco', '43.7326220', '7.4182170', '2012-02-07 10:35:10'),
(63, 0, 'Liechtenstein', '47.1450000', '9.5538890', '2012-02-07 10:35:10'),
(64, 0, 'Andorra la Vella', '42.5075000', '1.5169400', '2012-02-07 10:35:10'),
(65, 0, 'Andalucia', '37.3833330', '-5.9833330', '2012-02-07 15:20:18'),
(66, 0, 'Aragon', '41.0000000', '-1.0000000', '2012-02-07 15:20:18'),
(67, 0, 'Principado de Asturias', '3.3333330', '-6.0000000', '2012-02-07 15:20:18'),
(68, 0, 'Las Islas de Baleares', NULL, NULL, '2012-02-07 15:20:18'),
(69, 0, 'Canarias', '28.1000000', '-15.4000000', '2012-02-07 15:20:18'),
(70, 0, 'Cantabria', '43.3333330', '-4.0000000', '2012-02-07 15:20:18'),
(71, 0, 'CataluÃ±a', '41.8166670', '1.4666670', '2012-02-07 15:20:18'),
(72, 0, 'La Ciudad Autonoma de Ceuta', '35.8882475', '-5.3162201', '2012-02-07 15:20:18'),
(73, 0, 'Extremadura', '39.0000000', '-6.0000000', '2012-02-07 15:20:18'),
(74, 0, 'Galicia', '42.5000000', '-8.1000000', '2012-02-07 15:20:18'),
(75, 0, 'Comunidad de Madrid', '40.5000000', '-3.6666700', '2012-02-07 15:20:18'),
(76, 0, 'La Ciudad Autonoma de Melilla', '35.2888670', '-2.9467832', '2012-02-07 15:20:18'),
(77, 0, 'Region de Murcia', '8.0000000', '-1.0000000', '2012-02-07 15:20:18'),
(78, 0, 'Comunidad Foral de Navarra', '2.8166670', '-1.6500000', '2012-02-07 15:20:18'),
(79, 0, 'Pais Vasco', '2.8333330', '-2.6833330', '2012-02-07 15:20:18'),
(80, 0, 'La Rioja', '42.2500000', '-2.5000000', '2012-02-07 15:20:18'),
(81, 0, 'Comunidad Valenciana', '39.5000000', '-0.7500000', '2012-02-07 15:20:18'),
(82, 0, 'Alentejo', '40.3135915', '-7.8015006', '2012-02-07 15:55:30'),
(83, 0, 'Algarve', '37.0144440', '-7.9352780', '2012-02-07 15:55:30'),
(84, 0, 'Centro', '40.2069920', '-8.4293810', '2012-02-07 15:55:30'),
(85, 0, 'Lisboa', '38.7000000', '-9.1666670', '2012-02-07 15:55:30'),
(86, 0, 'Norte', '41.1499680', '-8.6102426', '2012-02-07 15:55:30'),
(87, 0, 'RegiÃ£o autonoma de aÃ§ores', NULL, NULL, '2012-02-07 15:55:30'),
(88, 0, 'RegiÃ£o autonoma de Madeira', '32.6511111', '-16.9097222', '2012-02-07 15:55:30'),
(89, 0, 'Baden-WÃ¼rttemberg', '48.5300000', '9.0500000', '2012-02-07 16:35:50'),
(90, 0, 'Bayern', '49.0000000', '11.5000000', '2012-02-07 16:35:50'),
(91, 0, 'Berlin', '52.5186000', '13.4081000', '2012-02-07 16:35:50'),
(92, 0, 'Brandenburg', '52.3619440', '13.0080560', '2012-02-07 16:35:50'),
(93, 0, 'Bremen', '7.5833330', '60.2000000', '2012-02-07 16:35:50'),
(94, 0, 'Hamburg', '53.5652780', '10.0013890', '2012-02-07 16:35:50'),
(95, 0, 'Hessen', '50.6661110', '8.5911110', '2012-02-07 16:35:50'),
(96, 0, 'CataluÃ±a', '41.8166670', '1.4666670', '2012-02-07 16:35:50'),
(97, 0, 'Mecklenburg-Vorpommern', '53.6121000', '12.7002000', '2012-02-07 16:35:50'),
(98, 0, 'Niedersachsen', '52.7562000', '9.3933100', '2012-02-07 16:35:50'),
(99, 0, 'Nordrhein-Westfalen', '51.4783000', '7.5550000', '2012-02-07 16:35:50'),
(100, 0, 'Rheinland-Pfalz', '49.9131000', '7.4497200', '2012-02-07 16:35:50'),
(101, 0, 'Saarland', '49.3833000', '6.8333300', '2012-02-07 16:35:50'),
(102, 0, 'Sachsen', '51.0269440', '13.3588890', '2012-02-07 16:35:50'),
(103, 0, 'Sachsen-Anhalt', '51.9713000', '11.4697000', '2012-02-07 16:35:50'),
(104, 0, 'Schleswig-Holstein', '54.4700000', '9.5141600', '2012-02-07 16:35:50'),
(105, 0, 'ThÃ¼ringen', '50.8611110', '11.0519440', '2012-02-07 16:35:50'),
(106, 0, 'Hovedstaden', '55.9398330', '12.3000000', '2012-02-07 18:57:27'),
(107, 0, 'Midtjylland', '56.1666670', '9.5000000', '2012-02-07 18:57:27'),
(108, 0, 'Nordjylland', '56.8307416', '9.4930528', '2012-02-07 18:57:27'),
(109, 0, 'SjÃ¦lland', '55.5000000', '11.7500000', '2012-02-07 18:57:27'),
(110, 0, 'Syddanmark', '55.3333330', '9.6666670', '2012-02-07 18:57:27'),
(111, 0, 'Burgenland', '47.5000000', '16.4166670', '2012-02-07 19:02:10'),
(112, 0, 'KÃ¤rnten', '46.7619000', '13.8189000', '2012-02-07 19:02:10'),
(113, 0, 'NiederÃ¶sterreich', '48.3333330', '15.7500000', '2012-02-07 19:02:10'),
(114, 0, 'OberÃ¶sterreich', '48.0258540', '13.9723665', '2012-02-07 19:02:10'),
(115, 0, 'Salzburg', '47.8025000', '13.0458330', '2012-02-07 19:02:10'),
(116, 0, 'Steiermark', '47.2500000', '15.1666670', '2012-02-07 19:02:10'),
(117, 0, 'Tirol', '47.2537414', '11.6014870', '2012-02-07 19:02:10'),
(118, 0, 'Vorarlberg', '47.2436000', '9.8938900', '2012-02-07 19:02:10'),
(119, 0, 'Wien', '48.2083330', '16.3730560', '2012-02-07 19:02:10'),
(120, 0, 'East Midlands', '2.9800000', '-0.7500000', '2012-02-07 19:19:25'),
(121, 0, 'East of England', '2.2400000', '0.4100000', '2012-02-07 19:19:25'),
(122, 0, 'Greater London', '51.5084100', '-0.1253600', '2012-02-07 19:19:25'),
(123, 0, 'North East England', '54.8892460', '-1.3842770', '2012-02-07 19:19:25'),
(124, 0, 'North West England', '53.6316110', '-2.6037600', '2012-02-07 19:19:25'),
(125, 0, 'South East England', '50.9238130', '-0.3405760', '2012-02-07 19:19:25'),
(126, 0, 'South West England', '0.9600000', '-3.2200000', '2012-02-07 19:19:25'),
(127, 0, 'West Midlands', '52.4700000', '-2.2900000', '2012-02-07 19:19:25'),
(128, 0, 'Yorkshire and the Humber', '53.7006543', '-0.4493882', '2012-02-07 19:19:25'),
(129, 0, 'Connacht', '53.5729167', '-8.9900352', '2012-02-07 19:29:09'),
(130, 0, 'Leinster', '53.3477780', '-6.2597220', '2012-02-07 19:29:09'),
(131, 0, 'Munster', '52.2500000', '-9.0000000', '2012-02-07 19:29:09'),
(132, 0, 'Ulster', '54.5969440', '-5.9300000', '2012-02-07 19:29:09'),
(133, 0, 'Friesland', '53.1641642', '5.7817542', '2012-02-07 19:45:30'),
(134, 0, 'Gelre', '52.0705520', '6.0276918', '2012-02-07 19:45:30'),
(135, 0, 'Holland', '52.2500000', '4.6670000', '2012-02-07 19:45:30'),
(136, 0, 'Overijssel', '52.4387814', '6.5016411', '2012-02-07 19:45:30'),
(137, 0, 'Stad en Lande', '52.2477712', '5.2389583', '2012-02-07 19:45:30'),
(138, 0, 'Utrecht', '52.0833330', '5.1000000', '2012-02-07 19:45:30'),
(139, 0, 'Zeeland', '51.6976800', '5.6738000', '2012-02-07 19:45:30'),
(140, 0, 'Diekirch', '49.8680000', '6.1566670', '2012-02-07 20:00:57'),
(141, 0, 'Grevenmacher', '49.6666670', '6.4500000', '2012-02-07 20:00:57'),
(142, 0, 'Luxembourg', '49.6000000', '6.1166670', '2012-02-07 20:00:57'),
(143, 0, 'Ontario', '50.7000000', '-86.0500000', '2012-02-07 22:50:09'),
(144, 0, 'QuÃ©bec', '53.7500000', '-71.9833330', '2012-02-07 22:50:09'),
(145, 0, 'Nouvelle-Ecosse', '44.6911120', '-63.5668950', '2012-02-07 22:50:09'),
(146, 0, 'Nouveau-Brunswick', '45.9575940', '-66.6444400', '2012-02-07 22:50:09'),
(147, 0, 'Manitoba', '55.0666670', '-97.5166670', '2012-02-07 22:50:09'),
(148, 0, 'Colombie-Britannique', '54.0000000', '-125.0000000', '2012-02-07 22:50:09'),
(149, 0, 'Ile-du-prince-Edouard', '46.3333330', '-63.5000000', '2012-02-07 22:50:09'),
(150, 0, 'Saskatchewan', '54.5000000', '-105.6813890', '2012-02-07 22:50:09'),
(151, 0, 'Alberta', '55.1666670', '-114.4000000', '2012-02-07 22:50:09'),
(152, 0, 'Chaouia-Ouardigha', '33.0473251', '-7.2652858', '2012-02-09 08:30:26'),
(153, 0, 'Doukkala-Abda', '32.5997754', '-8.6600586', '2012-02-09 08:30:26'),
(154, 0, 'FÃ¨s-Boulemane', '33.1870471', '-4.2333355', '2012-02-09 08:30:26'),
(155, 0, 'Gharb-Chrarda-Beni Hssen', '34.5434461', '-5.8987139', '2012-02-09 08:30:26'),
(156, 0, 'Grand Casablanca', '33.5205933', '-7.5680595', '2012-02-09 08:30:26'),
(157, 0, 'Guelmim-Es Smara', '28.7082053', '-9.5450974', '2012-02-09 08:30:26'),
(158, 0, 'LaÃ¢youne-Boujdour-Sakia el Hamra', '26.1333330', '-14.5000000', '2012-02-09 08:30:26'),
(159, 0, 'Marrakech-Tensift-Al Haouz', '31.5628076', '-7.9592863', '2012-02-09 08:30:26'),
(160, 0, 'MeknÃ¨s-Tafilalet', '31.9051275', '-4.7277528', '2012-02-09 08:30:26'),
(161, 0, 'Oriental', '34.6964610', '-2.4499510', '2012-02-09 08:30:26'),
(162, 0, 'Oued Ed-Dahab-Lagouira', '22.7337892', '-14.2861116', '2012-02-09 08:30:26'),
(163, 0, 'Rabat-SalÃ©-Zemmour-ZaÃ«r', '33.8175173', '-6.2375947', '2012-02-09 08:30:26'),
(164, 0, 'Souss-Massa-DrÃ¢a', '31.1200185', '-6.0679194', '2012-02-09 08:30:26'),
(165, 0, 'Tadla-Azilal', '32.0042620', '-6.5783387', '2012-02-09 08:30:26'),
(166, 0, 'Tanger-TÃ©touan', '35.2629558', '-5.5617279', '2012-02-09 08:30:26'),
(167, 0, 'Taza-Al Hoceima-Taounate', '34.2581709', '-4.2333355', '2012-02-09 08:30:26'),
(168, 0, 'Algeria', '36.7000000', '3.2166700', '2012-02-10 08:25:34'),
(169, 0, 'Province Nord', '-9.0000000', '148.0833330', '2012-02-10 12:30:59'),
(170, 0, 'Province Sud', '21.9166670', '166.3333330', '2012-02-10 12:30:59'),
(171, 0, 'Province des Ã®les LoyautÃ©', '-21.0000000', '167.0000000', '2012-02-10 12:30:59'),
(172, 0, 'Adamaoua', '6.9181954', '12.8054753', '2012-02-11 08:29:45'),
(173, 0, 'Centre', '4.5764250', '12.0080570', '2012-02-11 08:29:45'),
(174, 0, 'Est', '3.4585910', '14.5678710', '2012-02-11 08:29:45'),
(175, 0, 'ExtrÃªme-Nord', '10.7577630', '14.5568850', '2012-02-11 08:29:45'),
(176, 0, 'Littoral', '4.1682138', '10.0807298', '2012-02-11 08:29:45'),
(177, 0, 'Nord', '8.5809013', '13.9143990', '2012-02-11 08:29:45'),
(178, 0, 'Nord-Ouest', '6.4703739', '10.4396560', '2012-02-11 08:29:45'),
(179, 0, 'Ouest', '5.4638158', '10.8000051', '2012-02-11 08:29:45'),
(180, 0, 'Sud', '2.7202832', '11.7068294', '2012-02-11 08:29:45'),
(181, 0, 'Sud-Ouest', '5.1573493', '9.3673084', '2012-02-11 08:29:45'),
(182, 0, 'Amazonas', '-5.1151460', '-78.1108279', '2012-02-11 08:43:20'),
(210, 0, 'Svealand', '61.2491020', '15.0732420', '2012-02-11 08:55:14'),
(184, 0, 'Ancash', '-9.5500000', '-77.6166670', '2012-02-11 08:43:44'),
(185, 0, 'Apurimac', '-14.0504533', '-73.0877490', '2012-02-11 08:43:44'),
(186, 0, 'Arequipa', '-16.4308160', '-71.5155030', '2012-02-11 08:43:44'),
(187, 0, 'Ayacucho', '-13.1630560', '-44.2244440', '2012-02-11 08:43:44'),
(188, 0, 'Cajamarca', '-7.1644440', '-78.5105560', '2012-02-11 08:43:44'),
(189, 0, 'Callao', '-12.0584000', '-77.1484000', '2012-02-11 08:43:44'),
(190, 0, 'Cuzco', '-13.5250000', '-71.9722220', '2012-02-11 08:43:44'),
(191, 0, 'Huancavelica', '-12.7850000', '-74.9713890', '2012-02-11 08:43:44'),
(192, 0, 'Huanuco', '-9.9330000', '-76.2330000', '2012-02-11 08:43:44'),
(193, 0, 'Ica', '-14.0666670', '-75.7333330', '2012-02-11 08:43:44'),
(194, 0, 'Junin', '-11.3357980', '-75.3412179', '2012-02-11 08:43:44'),
(195, 0, 'La Libertad', '-8.1435933', '-78.4751945', '2012-02-11 08:43:44'),
(196, 0, 'Lambayeque', '-6.4776528', '-79.9192702', '2012-02-11 08:43:44'),
(197, 0, 'Lima', '-12.0452990', '-77.0311370', '2012-02-11 08:43:44'),
(198, 0, 'Loreto', '-4.2324729', '-74.2179326', '2012-02-11 08:43:44'),
(199, 0, 'Madre de Dios', '-11.7668705', '-70.8119953', '2012-02-11 08:43:44'),
(200, 0, 'Moquegua', '-17.2000000', '-70.9333330', '2012-02-11 08:43:44'),
(201, 0, 'Pasco', '-10.4475753', '-75.1545381', '2012-02-11 08:43:44'),
(202, 0, 'Piura', '-5.2000000', '-80.6333330', '2012-02-11 08:43:44'),
(203, 0, 'Puno', '-15.8375000', '-70.0216000', '2012-02-11 08:43:44'),
(204, 0, 'San Martin', '-7.2444881', '-76.8259652', '2012-02-11 08:43:44'),
(205, 0, 'Tacna', '-18.0555560', '-70.2483330', '2012-02-11 08:43:44'),
(206, 0, 'Tumbes', '-3.5666670', '-80.4500000', '2012-02-11 08:43:44'),
(207, 0, 'Ucayali', '-8.5918800', '-74.3664600', '2012-02-11 08:43:44'),
(209, 0, 'GÃ¶taland', '57.4684121', '18.4867447', '2012-02-11 08:55:14'),
(211, 0, 'Norrland', '65.3301780', '18.1933590', '2012-02-11 08:55:14'),
(212, 0, 'Adrar', '19.8652176', '-12.8054753', '2012-02-11 09:00:14'),
(213, 0, 'Assaba', '16.6000000', '-11.9166670', '2012-02-11 09:00:14'),
(214, 0, 'BrÃ¢kna', '17.2317561', '-13.1740348', '2012-02-11 09:00:14'),
(215, 0, 'Dakhlet Nouadhibou', '0.9500000', '-16.2333330', '2012-02-11 09:00:14'),
(216, 0, 'Gorgol', '15.9717357', '-12.6216211', '2012-02-11 09:27:22'),
(217, 0, 'Guidimaka', '15.3833330', '-12.3500000', '2012-02-11 09:27:22'),
(218, 0, 'Hodh ech Chargui', '9.0000000', '-7.2500000', '2012-02-11 09:27:22'),
(219, 0, 'Hodh el Gharbi', '6.5000000', '-10.0000000', '2012-02-11 09:27:22'),
(220, 0, 'Inchiri', '20.0666670', '-15.0666670', '2012-02-11 09:27:22'),
(221, 0, 'Nouakchott', '18.1000000', '-15.9500000', '2012-02-11 09:27:22'),
(222, 0, 'Tagant', '18.7000000', '-10.2000000', '2012-02-11 09:27:22'),
(223, 0, 'Tiris Zemmour', '4.0000000', '-9.0000000', '2012-02-11 09:27:22'),
(224, 0, 'Trarza', '17.9833330', '-14.7333330', '2012-02-11 09:27:22'),
(225, 0, 'Ariana', '36.8500000', '10.2000000', '2012-02-16 09:16:11'),
(226, 0, 'BÃ©ja', '36.7250000', '9.1820000', '2012-02-16 09:16:11'),
(227, 0, 'Ben Arous', '36.7400000', '10.2100000', '2012-02-16 09:16:11'),
(228, 0, 'Bizerte', '37.2700000', '9.8700000', '2012-02-16 09:16:11'),
(229, 0, 'GabÃ¨s', '33.8900000', '10.1100000', '2012-02-16 09:16:11'),
(230, 0, 'Gafsa', '34.4166670', '8.7833330', '2012-02-16 09:16:11'),
(231, 0, 'Jendouba', '36.4900000', '8.7800000', '2012-02-16 09:16:11'),
(232, 0, 'Kairouan', '35.6700000', '10.0900000', '2012-02-16 09:16:11'),
(233, 0, 'Kasserine', '35.1600000', '8.8300000', '2012-02-16 09:16:11'),
(234, 0, 'KÃ©bili', '33.7050000', '8.9650000', '2012-02-16 09:16:11'),
(235, 0, 'Kef', '36.1860000', '8.7000000', '2012-02-16 09:16:11'),
(236, 0, 'Mahdia', '35.5000000', '11.0600000', '2012-02-16 09:16:11'),
(237, 0, 'Manouba', '36.8071900', '10.1008100', '2012-02-16 09:16:11'),
(238, 0, 'MÃ©denine', '33.3450000', '10.4900000', '2012-02-16 09:16:11'),
(239, 0, 'Monastir', '35.7600000', '10.8100000', '2012-02-16 09:16:11'),
(240, 0, 'Nabeul', '36.4500000', '10.7400000', '2012-02-16 09:16:11'),
(241, 0, 'Sfax', '34.7400000', '10.7600000', '2012-02-16 09:16:11'),
(242, 0, 'Sidi Bouzid', '5.0333330', '9.5000000', '2012-02-16 09:16:11'),
(243, 0, 'Siliana', '36.0700000', '9.3600000', '2012-02-16 09:16:11'),
(244, 0, 'Sousse', '35.8260000', '10.6400000', '2012-02-16 09:16:11'),
(245, 0, 'Tataouine', '32.9278240', '10.4492430', '2012-02-16 09:16:11'),
(246, 0, 'Tozeur', '33.9200000', '8.1400000', '2012-02-16 09:16:11'),
(247, 0, 'Tunis', '36.7974500', '10.1657850', '2012-02-16 09:16:11'),
(248, 0, 'Zaghouan', '36.4000000', '10.1500000', '2012-02-16 09:16:11'),
(249, 0, 'Andorra', '40.9772220', '-0.4447220', '2012-02-16 09:20:29'),
(250, 0, 'Reunion', '-21.1151410', '55.5363840', '2012-02-16 09:29:31'),
(251, 0, 'Guadeloupe', '16.2650000', '-61.5510000', '2012-02-19 18:00:40'),
(252, 0, 'Martinique', '14.6415280', '-61.0241740', '2012-02-19 18:00:40'),
(253, 0, 'Guyane', '3.9338890', '-53.1257820', '2012-02-19 18:01:13'),
(254, 0, 'Mayotte', '-12.8275000', '45.1662440', '2012-02-19 18:01:13'),
(255, 0, 'Kayes', NULL, NULL, '2012-02-21 08:47:24'),
(256, 0, 'Koulikoro', NULL, NULL, '2012-02-21 08:47:24'),
(257, 0, 'Sikasso', NULL, NULL, '2012-02-21 08:47:24'),
(258, 0, 'SÃ©gou', NULL, NULL, '2012-02-21 08:47:24'),
(259, 0, 'Mopti', NULL, NULL, '2012-02-21 08:47:24'),
(260, 0, 'Gao', NULL, NULL, '2012-02-21 08:47:24'),
(261, 0, 'Tombouctou', NULL, NULL, '2012-02-21 08:47:24'),
(262, 0, 'Kidal', NULL, NULL, '2012-02-21 08:47:24'),
(263, 0, 'Hokkaido', NULL, NULL, '2012-02-21 09:11:07'),
(264, 0, 'Tohoku', NULL, NULL, '2012-02-21 09:11:07'),
(265, 0, 'Chubu', NULL, NULL, '2012-02-21 09:11:07'),
(266, 0, 'Kanto', NULL, NULL, '2012-02-21 09:11:07'),
(267, 0, 'Kansai', NULL, NULL, '2012-02-21 09:11:07'),
(268, 0, 'Chugoku', NULL, NULL, '2012-02-21 09:11:07'),
(269, 0, 'Shikoku', NULL, NULL, '2012-02-21 09:11:07'),
(270, 0, 'Kyushu', NULL, NULL, '2012-02-21 09:11:07'),
(271, 0, 'Alabama', NULL, NULL, '2012-02-24 11:34:42'),
(272, 0, 'Alaska', NULL, NULL, '2012-02-24 11:34:42'),
(273, 0, 'Arizona', NULL, NULL, '2012-02-24 11:34:42'),
(274, 0, 'Arkansas', NULL, NULL, '2012-02-24 11:34:42'),
(275, 0, 'Californie', NULL, NULL, '2012-02-24 11:34:42'),
(276, 0, 'Caroline du Nord', NULL, NULL, '2012-02-24 11:34:42'),
(277, 0, 'Caroline du Sud', NULL, NULL, '2012-02-24 11:34:42'),
(278, 0, 'Colorado', NULL, NULL, '2012-02-24 11:34:42'),
(279, 0, 'Connecticut', NULL, NULL, '2012-02-24 11:34:42'),
(280, 0, 'Dakota du Nord', NULL, NULL, '2012-02-24 11:34:42'),
(281, 0, 'Dakota du Sud', NULL, NULL, '2012-02-24 11:34:42'),
(282, 0, 'Delaware', NULL, NULL, '2012-02-24 11:34:42'),
(283, 0, 'Floride', NULL, NULL, '2012-02-24 11:34:42'),
(284, 0, 'Georgie', NULL, NULL, '2012-02-24 11:34:42'),
(285, 0, 'HawaÃ¯', NULL, NULL, '2012-02-24 11:34:42'),
(286, 0, 'Idaho', NULL, NULL, '2012-02-24 11:34:42'),
(287, 0, 'Illinois', NULL, NULL, '2012-02-24 11:34:42'),
(288, 0, 'Indiana', NULL, NULL, '2012-02-24 11:34:42'),
(289, 0, 'Iowa', NULL, NULL, '2012-02-24 11:34:42'),
(290, 0, 'Kansas', NULL, NULL, '2012-02-24 11:34:42'),
(291, 0, 'Kentucky', NULL, NULL, '2012-02-24 11:34:42'),
(292, 0, 'Louisiane', NULL, NULL, '2012-02-24 11:34:42'),
(293, 0, 'Maine', NULL, NULL, '2012-02-24 11:34:42'),
(294, 0, 'Massachusetts', NULL, NULL, '2012-02-24 11:34:42'),
(295, 0, 'Michigan', NULL, NULL, '2012-02-24 11:34:42'),
(296, 0, 'Minnesota', NULL, NULL, '2012-02-24 11:34:42'),
(297, 0, 'Mississippi', NULL, NULL, '2012-02-24 11:34:42'),
(298, 0, 'Missouri', NULL, NULL, '2012-02-24 11:34:42'),
(299, 0, 'Montana', NULL, NULL, '2012-02-24 11:34:42'),
(300, 0, 'Nebraska', NULL, NULL, '2012-02-24 11:34:42'),
(301, 0, 'Nevada', NULL, NULL, '2012-02-24 11:34:42'),
(302, 0, 'New Hampshire', NULL, NULL, '2012-02-24 11:34:42'),
(303, 0, 'New Jersey', NULL, NULL, '2012-02-24 11:34:42'),
(304, 0, 'New York', NULL, NULL, '2012-02-24 11:34:42'),
(305, 0, 'Nouveau Mexique', NULL, NULL, '2012-02-24 11:34:42'),
(306, 0, 'Ohio', NULL, NULL, '2012-02-24 11:34:42'),
(307, 0, 'Oklahoma', NULL, NULL, '2012-02-24 11:34:42'),
(308, 0, 'Oregon', NULL, NULL, '2012-02-24 11:34:42'),
(309, 0, 'Pennsylvanie', NULL, NULL, '2012-02-24 11:34:42'),
(310, 0, 'Rhode Island', NULL, NULL, '2012-02-24 11:34:42'),
(311, 0, 'Tenessee', NULL, NULL, '2012-02-24 11:34:42'),
(312, 0, 'Texas', NULL, NULL, '2012-02-24 11:34:42'),
(313, 0, 'Utah', NULL, NULL, '2012-02-24 11:34:42'),
(314, 0, 'Vermont', NULL, NULL, '2012-02-24 11:34:42'),
(315, 0, 'Virginie', NULL, NULL, '2012-02-24 11:34:42'),
(316, 0, 'Virginie occidentale', NULL, NULL, '2012-02-24 11:34:42'),
(317, 0, 'Washington', NULL, NULL, '2012-02-24 11:34:42'),
(318, 0, 'Wisconsin', NULL, NULL, '2012-02-24 11:34:42'),
(319, 0, 'Wyoming', NULL, NULL, '2012-02-24 11:34:42'),
(320, 0, 'Western Cape', NULL, NULL, '2012-02-24 11:40:15'),
(321, 0, 'Northern Cape', NULL, NULL, '2012-02-24 11:40:15'),
(322, 0, 'Eastern Cape', NULL, NULL, '2012-02-24 11:40:15'),
(323, 0, 'KwaZulu-Natal', NULL, NULL, '2012-02-24 11:40:15'),
(324, 0, 'Free State', NULL, NULL, '2012-02-24 11:40:15'),
(325, 0, 'North West', NULL, NULL, '2012-02-24 11:40:15'),
(326, 0, 'Gauteng', NULL, NULL, '2012-02-24 11:40:15'),
(327, 0, 'Mpumalanga', NULL, NULL, '2012-02-24 11:40:15'),
(328, 0, 'Limpopo', NULL, NULL, '2012-02-24 11:40:15'),
(329, 0, 'Dakar', NULL, NULL, '2012-02-27 10:24:13'),
(330, 0, 'Diourbel', NULL, NULL, '2012-02-27 10:24:13'),
(331, 0, 'Fatick', NULL, NULL, '2012-02-27 10:24:13'),
(332, 0, 'Kaolack', NULL, NULL, '2012-02-27 10:24:13'),
(333, 0, 'Kolda', NULL, NULL, '2012-02-27 10:24:13'),
(334, 0, 'Louga', NULL, NULL, '2012-02-27 10:24:13'),
(335, 0, 'Matam', NULL, NULL, '2012-02-27 10:24:13'),
(336, 0, 'Saint Louis', NULL, NULL, '2012-02-27 10:24:13'),
(337, 0, 'Tambacounda', NULL, NULL, '2012-02-27 10:24:13'),
(338, 0, 'ThiÃ¨s', NULL, NULL, '2012-02-27 10:24:13'),
(339, 0, 'Ziguinchor', NULL, NULL, '2012-02-27 10:24:13'),
(340, 0, 'Al Bahah', NULL, NULL, '2012-02-27 10:28:45'),
(341, 0, 'Al-Hudud ach-Chamaliya', NULL, NULL, '2012-02-27 10:28:45'),
(342, 0, 'Al Jawf', NULL, NULL, '2012-02-27 10:28:45'),
(343, 0, 'MÃ©dine', NULL, NULL, '2012-02-27 10:28:45'),
(344, 0, 'Al Qasim', NULL, NULL, '2012-02-27 10:28:45'),
(345, 0, 'HaÃ¯l', NULL, NULL, '2012-02-27 10:28:45'),
(346, 0, 'Asir', NULL, NULL, '2012-02-27 10:28:45'),
(347, 0, 'Ach-Charqiya', NULL, NULL, '2012-02-27 10:28:45'),
(348, 0, 'Riyad', NULL, NULL, '2012-02-27 10:28:45'),
(349, 0, 'Tabuk', NULL, NULL, '2012-02-27 10:28:45'),
(350, 0, 'Najran', NULL, NULL, '2012-02-27 10:28:45'),
(351, 0, 'La Mecque', NULL, NULL, '2012-02-27 10:28:45'),
(352, 0, 'Jizan', NULL, NULL, '2012-02-27 10:28:45'),
(354, 0, 'Grande plaine septentrionale', NULL, NULL, '2012-03-10 14:54:49'),
(355, 0, 'Grande plaine mÃ©ridionale', NULL, NULL, '2012-03-10 14:54:49'),
(356, 0, 'Hongrie centrale', NULL, NULL, '2012-03-10 14:54:49'),
(357, 0, 'Hongrie septentrionale', NULL, NULL, '2012-03-10 14:54:49'),
(358, 0, 'Transdanubie centrale', NULL, NULL, '2012-03-10 14:54:49'),
(359, 0, 'Transdanubie occidentale', NULL, NULL, '2012-03-10 14:54:49'),
(360, 0, 'Transdanubie mÃ©ridionale', NULL, NULL, '2012-03-10 14:54:49'),
(361, 0, 'Bouenza', NULL, NULL, '2012-03-14 07:23:09'),
(362, 0, 'Cuvette', NULL, NULL, '2012-03-14 07:23:09'),
(363, 0, 'Cuvette Ouest', NULL, NULL, '2012-03-14 07:23:09'),
(364, 0, 'Kouilou', NULL, NULL, '2012-03-14 07:23:09'),
(365, 0, 'Lekoumou', NULL, NULL, '2012-03-14 07:23:09'),
(366, 0, 'Likouala', NULL, NULL, '2012-03-14 07:23:09'),
(367, 0, 'Niari', NULL, NULL, '2012-03-14 07:23:09'),
(368, 0, 'Plateaux', NULL, NULL, '2012-03-14 07:23:09'),
(369, 0, 'Pool', NULL, NULL, '2012-03-14 07:23:09'),
(370, 0, 'Sangha', NULL, NULL, '2012-03-14 07:23:09');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(85) NOT NULL,
  `identity` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `language` varchar(3) NOT NULL,
  `country_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `last_connected_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_banned` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `abac_attributes`
--
ALTER TABLE `abac_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `abac_attributes_data`
--
ALTER TABLE `abac_attributes_data`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `abac_environment_attributes`
--
ALTER TABLE `abac_environment_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `abac_policy_rules`
--
ALTER TABLE `abac_policy_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `abac_policy_rules_attributes`
--
ALTER TABLE `abac_policy_rules_attributes`
  ADD KEY `policy_rule_id` (`policy_rule_id`,`attribute_id`),
  ADD KEY `attributes` (`attribute_id`);

--
-- Index pour la table `citizen_groups`
--
ALTER TABLE `citizen_groups`
  ADD KEY `Citizen_id` (`citizen_id`,`group_id`);

--
-- Index pour la table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_type_index` (`type_id`);

--
-- Index pour la table `group_types`
--
ALTER TABLE `group_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `label` (`label`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `level` (`level`),
  ADD KEY `time` (`timelogs`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_index` (`recipient_id`),
  ADD KEY `author_index` (`author_id`);

--
-- Index pour la table `motions`
--
ALTER TABLE `motions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `motion_themes`
--
ALTER TABLE `motion_themes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `abac_attributes`
--
ALTER TABLE `abac_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `abac_attributes_data`
--
ALTER TABLE `abac_attributes_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `abac_environment_attributes`
--
ALTER TABLE `abac_environment_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `abac_policy_rules`
--
ALTER TABLE `abac_policy_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=239;
--
-- AUTO_INCREMENT pour la table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=107;
--
-- AUTO_INCREMENT pour la table `group_types`
--
ALTER TABLE `group_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `motions`
--
ALTER TABLE `motions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT pour la table `motion_themes`
--
ALTER TABLE `motion_themes`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=371;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;