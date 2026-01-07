-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 25 déc. 2025 à 17:40
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nutrition_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `identifiant` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `maladie_chroniques`
--

CREATE TABLE `maladie_chroniques` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `maladie_chroniques`
--

INSERT INTO `maladie_chroniques` (`id`, `nom`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Obésité', 'obésité modérée', NULL, NULL),
(2, 'Obésité', 'obésité sévère', NULL, NULL),
(3, 'Obésité', 'obésité morbide', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2024_08_12_143424_create_maladie_chroniques_table', 1),
(3, '2024_08_12_143738_create_administrateurs-table', 1),
(4, '2024_08_12_143901_create_questions_table', 1),
(5, '2024_08_12_144050_create_utilisateurs_table', 1),
(6, '2024_08_12_144448_create_reponses_table', 1),
(7, '2024_09_01_151253_create_plan_nutritionnels_table', 1),
(8, '2024_11_08_203838_create_password_resets_table', 1),
(9, '2025_02_19_102346_create_question_possible_answers_table', 1),
(10, '2025_12_23_124213_add_notification_and_tdee_columns_to_utilisateurs_table', 2);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Utilisateur', 2, 'auth_token', '96f1209bf41550b7384ec85f6b6aee2f220c8d3f04d6d48b20c66be3612fb19d', '[\"*\"]', NULL, NULL, '2025-12-03 14:09:13', '2025-12-03 14:09:13'),
(2, 'App\\Models\\Utilisateur', 2, 'API Token', '7b4af22b98d03977b8ac0ec2f36f1fc572df709d44de715b1910d940157d9670', '[\"*\"]', '2025-12-03 16:48:05', NULL, '2025-12-03 14:10:20', '2025-12-03 16:48:05'),
(3, 'App\\Models\\Utilisateur', 2, 'API Token', 'e758be191953cfd3ade06f3d86c49ecfc943aca33f06163778441a121536ed42', '[\"*\"]', '2025-12-11 20:42:27', NULL, '2025-12-11 20:40:18', '2025-12-11 20:42:27'),
(4, 'App\\Models\\Utilisateur', 3, 'auth_token', '36fc198eb5d1e37046890aaae1faf91a1be4d33e4a55b47c8033449adff93ccd', '[\"*\"]', NULL, NULL, '2025-12-11 23:39:44', '2025-12-11 23:39:44'),
(5, 'App\\Models\\Utilisateur', 3, 'API Token', '3bf16ef35b1fed9bd5cc31081e372172c9804422621a3cf05b14d2a73cc51b24', '[\"*\"]', NULL, NULL, '2025-12-11 23:40:06', '2025-12-11 23:40:06'),
(6, 'App\\Models\\Utilisateur', 3, 'API Token', '64adc9414033295820a5221b2df0de1f778c62965a6bcd66b6b7c5c1a5f735a5', '[\"*\"]', '2025-12-12 17:33:25', NULL, '2025-12-12 17:29:03', '2025-12-12 17:33:25'),
(7, 'App\\Models\\Utilisateur', 3, 'API Token', '45f48cfeafb6aa743f83e9cadebe1bf191e7b51ce7179e425ff4935d2cf45d8f', '[\"*\"]', '2025-12-14 22:16:21', NULL, '2025-12-14 21:29:47', '2025-12-14 22:16:21'),
(8, 'App\\Models\\Utilisateur', 3, 'API Token', 'd391f1d66daa1f19906e05225616655d134d2aed8926eaa7a46eee046e5e9dfd', '[\"*\"]', NULL, NULL, '2025-12-15 16:35:47', '2025-12-15 16:35:47');

-- --------------------------------------------------------

--
-- Structure de la table `plan_nutritionnels`
--

CREATE TABLE `plan_nutritionnels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `utilisateur_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `texte_question` varchar(255) NOT NULL,
  `maladie_chronique_id` bigint(20) UNSIGNED NOT NULL,
  `has_possible_answers` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `texte_question`, `maladie_chronique_id`, `has_possible_answers`, `created_at`, `updated_at`) VALUES
(66, 'Avez-vous des antécédents de diabète de type 2, d\'hypertension artérielle  ou de maladies cardiovasculaires ? ', 1, 1, NULL, NULL),
(67, 'Si oui, lequel ? ', 1, 0, NULL, NULL),
(68, 'Prenez-vous actuellement des médicaments qui sont liés à votre antécédent précédent ?', 1, 1, NULL, NULL),
(69, 'Si oui, lesquels ? ', 1, 0, NULL, NULL),
(70, 'Prenez-vous actuellement l’un des types de médicaments suivants ?', 1, 1, NULL, NULL),
(71, 'Pouvez-vous décrire une journée typique de vos repas et collations ?', 1, 0, NULL, NULL),
(72, 'Combien de portions de fruits et légumes consommez-vous par jour ?', 1, 1, NULL, NULL),
(73, 'Grignotez-vous entre les repas ? ', 1, 1, NULL, NULL),
(74, 'Si oui, quels aliments consommez-vous ?', 1, 0, NULL, NULL),
(75, 'A quelle fréquence consommez-vous des boissons sucrées ou alcoolisées ?', 1, 1, NULL, NULL),
(76, 'Combien d\'heures de sommeil avez-vous par nuit ?', 1, 1, NULL, NULL),
(77, 'Avez-vous l’apnée du sommeil ? ', 1, 1, NULL, NULL),
(78, 'Avez-vous récemment fait un régime de perte du poids ? ', 1, 1, NULL, NULL),
(79, 'Si oui, combien de poids avez-vous perdu ? ', 1, 1, NULL, NULL),
(80, 'Combien de jours par semaine pratiquez-vous une activité physique ?', 1, 1, NULL, NULL),
(81, 'Combien de temps passez-vous en position assise chaque jour ?  ', 1, 1, NULL, NULL),
(82, 'Quelles activités physiques appréciez-vous ? ', 1, 1, NULL, NULL),
(83, 'Connaissez-vous votre fréquence cardiaque au repos ? ', 1, 1, NULL, NULL),
(84, 'Si oui, veuillez- nous la donner ?', 1, 1, NULL, NULL),
(85, 'Mangez-vous souvent par stress ou ennui ?', 1, 1, NULL, NULL),
(86, 'Avez-vous tendance à finir votre assiette même si vous n’avez plus faim ?', 1, 1, NULL, NULL),
(87, 'Avez-vous déjà suivi un régime alimentaire particulier ?', 1, 1, NULL, NULL),
(88, 'Si oui, lequel, et a-t-il été efficace pour vous ? Pourquoi ?', 1, 0, NULL, NULL),
(89, 'Quel est votre objectif principal en matière de santé ? ', 1, 1, NULL, NULL),
(90, 'Combien de kg voulez-vous perdre ? ', 1, 1, NULL, NULL),
(91, 'Selon votre objectif de perte de poids, quel niveau de changement êtes-vous prêt à suivre ?', 1, 1, NULL, NULL),
(92, 'Avez-vous été diagnostiqué avec des problèmes de santé liés à l\'obésité, comme le diabète de type 2, l\'hypertension ou l\'hypercholestérolémie ? ', 2, 1, NULL, NULL),
(93, 'Si oui lequel ?', 2, 0, NULL, NULL),
(94, 'Avez-vous subi des opérations chirurgicales liées à votre poids comme une chirurgie bariatrique ?', 2, 1, NULL, NULL),
(95, 'Avez-vous des restrictions alimentaires ? ', 2, 1, NULL, NULL),
(96, 'Si oui, choisissez l\'option qui s\'appliquent ?', 2, 1, NULL, NULL),
(97, 'Consommez-vous régulièrement des aliments riches en calories vides, comme les boissons sucrées, les fast-foods, ou les desserts ?', 2, 1, NULL, NULL),
(98, 'Suivez-vous déjà un plan nutritionnel ou avez-vous consulté un diététicien auparavant ?', 2, 1, NULL, NULL),
(99, 'Si oui, parlez-nous de ça brièvement en quelques lignes', 2, 0, NULL, NULL),
(100, 'Buvez-vous régulièrement des boissons sucrées ou alcoolisées ? ', 2, 1, NULL, NULL),
(101, 'Si oui, Quand ? Quelle quantité ?', 2, 0, NULL, NULL),
(102, 'Combien d\' heures de sommeil avez-vous par nuit ? ', 2, 1, NULL, NULL),
(103, 'Avez-vous l’apnée du sommeil ?', 2, 1, NULL, NULL),
(104, 'Avez-vous des douleurs articulaires ou des limitations de mobilité ? ', 2, 1, NULL, NULL),
(105, 'Si oui, A quelle fréquence ?', 2, 1, NULL, NULL),
(106, 'Avez-vous récemment fait un régime de perte du poids ?', 2, 1, NULL, NULL),
(107, 'Si oui, combien de poids avez-vous perdu ?', 2, 0, NULL, NULL),
(108, 'Combien de jours par semaine pratiquez-vous une activité physique ? ', 2, 1, NULL, NULL),
(109, 'Combien de temps passez-vous en position assise chaque jour ?  ', 2, 1, NULL, NULL),
(110, 'Quelles activités physiques appréciez-vous ? ', 2, 1, NULL, NULL),
(111, 'Connaissez-vous votre fréquence cardiaque au repos ? ', 2, 1, NULL, NULL),
(112, 'Si oui, veuillez- nous la donner ?', 2, 0, NULL, NULL),
(113, 'Comment décririez-vous votre ressenti personnel par rapport à votre poids ?', 2, 0, NULL, NULL),
(114, 'Mangez-vous souvent par stress ou ennui ?', 2, 1, NULL, NULL),
(115, 'Avez-vous tendance à finir votre assiette même si vous n’avez plus faim ?', 2, 1, NULL, NULL),
(116, 'Avez-vous des habitudes alimentaires liées à des émotions, comme le stress ou la dépression ?', 2, 1, NULL, NULL),
(117, 'Avez-vous déjà suivi un programme ou tenté de perdre du poids auparavant ?', 2, 1, NULL, NULL),
(118, 'Quels sont vos objectifs à court et à long terme pour votre poids et votre santé ? ', 2, 1, NULL, NULL),
(119, 'Selon votre objectif de perte de poids, quel niveau de changement êtes-vous prêt à suivre ?', 2, 1, NULL, NULL),
(120, 'Avez-vous un réseau de soutien (famille, amis) pour vous aider dans votre parcours de perte de poids ?', 2, 1, NULL, NULL),
(121, 'Souffrez-vous de maladies chroniques importantes pouvant être liées à votre poids ? ', 3, 1, NULL, NULL),
(122, 'Avez-vous l’apnée du sommeil ? ', 3, 1, NULL, NULL),
(123, 'Utilisez-vous un appareil CPAP pour l\'apnée du sommeil ?', 3, 1, NULL, NULL),
(124, 'Avez-vous des difficultés à effectuer des activités quotidiennes de base en raison de votre poids ?', 3, 1, NULL, NULL),
(125, 'Avez-vous récemment perdu ou pris du poids ?', 3, 1, NULL, NULL),
(126, 'Si oui, combien et en combien de temps ? ', 3, 0, NULL, NULL),
(127, 'Avez-vous des épisodes de compulsions alimentaires ou de boulimie ?', 3, 1, NULL, NULL),
(128, 'Avez-vous déjà suivi des régimes très stricts ou des régimes en yo-yo ?', 3, 1, NULL, NULL),
(129, 'Avez-vous des difficultés à contrôler les portions ou à manger de manière équilibrée ?', 3, 1, NULL, NULL),
(130, 'Buvez-vous régulièrement des boissons sucrées ou alcoolisées ?', 3, 1, NULL, NULL),
(131, 'Si oui, Quand ? Quelle quantité ? ', 3, 0, NULL, NULL),
(132, 'Votre poids vous empêche-t-il de pratiquer certaines activités physiques ? ', 3, 1, NULL, NULL),
(133, 'Quelle est votre capacité à vous déplacer de manière autonome (marche, escaliers, etc.) ?', 3, 1, NULL, NULL),
(134, 'Avez-vous besoin d\'un équipement spécialisé pour vous aider à bouger ou à vous asseoir confortablement ?', 3, 1, NULL, NULL),
(135, 'Quelle est votre fréquence cardiaque au repos (si vous la connaissez) ?', 3, 0, NULL, NULL),
(136, 'Avez-vous un suivi psychologique ou psychiatrique en lien avec votre poids ou votre alimentation ?', 3, 1, NULL, NULL),
(137, 'Si oui, parlez-nous en si cela ne vous gêne pas ', 3, 0, NULL, NULL),
(138, 'Avez-vous déjà participé à des groupes de soutien pour la perte de poids ou l\'obésité ?', 3, 1, NULL, NULL),
(139, 'Comment faites-vous face aux remarques liées à votre poids ?', 3, 1, NULL, NULL),
(140, 'Avez-vous envisagé une chirurgie bariatrique ou d\'autres interventions médicales pour gérer votre poids ?', 3, 1, NULL, NULL),
(141, 'Quels sont vos objectifs de santé immédiats et à long terme ? ', 3, 1, NULL, NULL),
(142, 'Selon votre objectif de perte de poids, quel niveau de changement êtes-vous prêt à suivre ?', 3, 1, NULL, NULL),
(143, 'Quels aspects de votre vie vous motivent le plus pour changer vos habitudes alimentaires et physiques ?', 3, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `question_possible_answers`
--

CREATE TABLE `question_possible_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `question_possible_answers`
--

INSERT INTO `question_possible_answers` (`id`, `question_id`, `value`, `created_at`, `updated_at`) VALUES
(157, 66, 'Oui', NULL, NULL),
(158, 66, 'Non', NULL, NULL),
(159, 68, 'Oui', NULL, NULL),
(160, 68, 'Non', NULL, NULL),
(161, 70, 'Insuline', NULL, NULL),
(162, 70, 'Médicaments pour perdre du poids (ex : orlistat, liraglutide…)', NULL, NULL),
(163, 70, 'Médicaments pour la tension artérielle (ex : IEC, ARA2, bêta-bloquants…)', NULL, NULL),
(164, 70, 'Médicaments pour le cholestérol (ex : statines)', NULL, NULL),
(165, 70, 'Médicaments pour baisser la glycémie (ex : metformine, glimepiride…)', NULL, NULL),
(166, 72, '1-2', NULL, NULL),
(167, 72, '3-4', NULL, NULL),
(168, 72, '5 ou plus', NULL, NULL),
(169, 73, 'Oui', NULL, NULL),
(170, 73, 'Non', NULL, NULL),
(171, 75, 'Tous les jours', NULL, NULL),
(172, 75, 'Une fois par semaine', NULL, NULL),
(173, 75, 'Occasionnellement', NULL, NULL),
(178, 76, 'Moins de 6 h', NULL, NULL),
(179, 76, '6h -7h', NULL, NULL),
(180, 76, '8h -9h', NULL, NULL),
(181, 76, 'Plus de 9h', NULL, NULL),
(182, 77, 'Oui', NULL, NULL),
(183, 77, 'Non', NULL, NULL),
(184, 78, 'Oui', NULL, NULL),
(185, 78, 'Non', NULL, NULL),
(186, 79, 'Moins de 5 kg.', NULL, NULL),
(187, 79, '5 - 10 kg.', NULL, NULL),
(188, 79, 'Plus de 10 kg.', NULL, NULL),
(189, 80, 'Aucun', NULL, NULL),
(190, 80, '1j-3j', NULL, NULL),
(191, 80, '4j-6j ', NULL, NULL),
(192, 80, '7j', NULL, NULL),
(193, 81, 'Moins de 4h', NULL, NULL),
(194, 81, '4h-6h', NULL, NULL),
(195, 81, '6h-8h', NULL, NULL),
(196, 81, 'Plus de 8h', NULL, NULL),
(197, 82, 'Marche', NULL, NULL),
(198, 82, 'Yoga ', NULL, NULL),
(199, 82, 'Autre', NULL, NULL),
(200, 83, 'Oui', NULL, NULL),
(201, 83, 'Non ', NULL, NULL),
(202, 85, 'Oui', NULL, NULL),
(203, 85, 'Non ', NULL, NULL),
(204, 86, 'Oui', NULL, NULL),
(205, 86, 'Non ', NULL, NULL),
(206, 87, 'Oui', NULL, NULL),
(207, 87, 'Non ', NULL, NULL),
(208, 89, 'Perdre du poids', NULL, NULL),
(209, 89, 'Améliorer votre forme physique  ', NULL, NULL),
(210, 90, 'Moins de 5 kg.', NULL, NULL),
(211, 90, '5 - 10 kg.', NULL, NULL),
(212, 90, 'Plus de 10 kg.', NULL, NULL),
(213, 91, 'Perdre moins de 5 Kg: Léger 300 kcal', NULL, NULL),
(214, 91, 'Perdre moins de 5Kg: Moyen 400 kcal', NULL, NULL),
(215, 91, 'Perdre moins de 5Kg: Serein 500 kcal', NULL, NULL),
(216, 91, 'Perdre entre 5-10Kg: Léger 500 kcal', NULL, NULL),
(217, 91, 'Perdre entre 5-10Kg: Moyen 600 kcal', NULL, NULL),
(218, 91, 'Perdre entre 5-10Kg: Serein 700 kcal', NULL, NULL),
(219, 91, 'Perdre plus de 10Kg: Léger 700 kcal', NULL, NULL),
(220, 91, 'Perdre plus de 10Kg: Moyen 800 kcal', NULL, NULL),
(221, 91, 'Perdre plus de 10Kg: Serein 900 kcal', NULL, NULL),
(222, 92, 'Oui', NULL, NULL),
(223, 92, 'Non', NULL, NULL),
(224, 94, 'Oui', NULL, NULL),
(225, 94, 'Non', NULL, NULL),
(226, 95, 'Oui', NULL, NULL),
(227, 95, 'Non', NULL, NULL),
(228, 96, 'Allergies', NULL, NULL),
(229, 96, 'Intolérances ', NULL, NULL),
(230, 96, 'Préférences culturelles ', NULL, NULL),
(231, 96, 'Religieuses', NULL, NULL),
(232, 97, 'Oui', NULL, NULL),
(233, 97, 'Non', NULL, NULL),
(234, 98, 'Oui', NULL, NULL),
(235, 98, 'Non', NULL, NULL),
(236, 100, 'Oui', NULL, NULL),
(237, 100, 'Non', NULL, NULL),
(238, 102, 'Moins de 6 h', NULL, NULL),
(239, 102, '6h -7h', NULL, NULL),
(240, 102, '8h -9h', NULL, NULL),
(241, 102, 'Plus de 9h', NULL, NULL),
(242, 103, 'Oui', NULL, NULL),
(243, 103, 'Non', NULL, NULL),
(244, 104, 'Oui', NULL, NULL),
(245, 104, 'Non', NULL, NULL),
(246, 105, 'Tous les jours', NULL, NULL),
(247, 105, 'Parfois', NULL, NULL),
(248, 106, 'Oui', NULL, NULL),
(249, 106, 'Non', NULL, NULL),
(250, 108, 'Aucun', NULL, NULL),
(251, 108, '1j-3j', NULL, NULL),
(252, 108, '4j-6j', NULL, NULL),
(253, 108, '7j', NULL, NULL),
(254, 109, 'Moins de 4h', NULL, NULL),
(255, 109, '4h-6h', NULL, NULL),
(256, 109, '6h-8h', NULL, NULL),
(257, 109, 'Plus de 8h', NULL, NULL),
(258, 110, 'Marche', NULL, NULL),
(259, 110, 'Yoga', NULL, NULL),
(260, 110, 'Autre', NULL, NULL),
(261, 111, 'Oui', NULL, NULL),
(262, 111, 'Non', NULL, NULL),
(263, 114, 'Oui', NULL, NULL),
(264, 114, 'Non', NULL, NULL),
(265, 115, 'Oui', NULL, NULL),
(266, 115, 'Non', NULL, NULL),
(267, 116, 'Oui', NULL, NULL),
(268, 116, 'Non', NULL, NULL),
(269, 117, 'Oui', NULL, NULL),
(270, 117, 'Non', NULL, NULL),
(271, 118, 'Perdre moins 5 kg à court terme et maintenir un poids sain à long terme', NULL, NULL),
(272, 118, 'Perdre entre 5 et 10 kg à court terme et maintenir un poids sain à long terme', NULL, NULL),
(273, 118, 'Perdre plus de 10 kg à court terme et maintenir un poids sain à long terme', NULL, NULL),
(274, 119, 'Perdre moins 5 kg à court terme et maintenir un poids sain à long terme: Léger 300 Kcal', NULL, NULL),
(275, 119, 'Perdre moins 5 kg à court terme et maintenir un poids sain à long terme: Moyen 400 Kcal', NULL, NULL),
(276, 119, 'Perdre moins 5 kg à court terme et maintenir un poids sain à long terme: Serein 500 Kcal', NULL, NULL),
(277, 119, 'Perdre entre 5 et 10kg à court terme et maintenir un poids sain à long terme: Léger 500 Kcal', NULL, NULL),
(278, 119, 'Perdre entre 5 et 10kg à court terme et maintenir un poids sain à long terme: Moyen 600 Kcal', NULL, NULL),
(279, 119, 'Perdre entre 5 et 10kg à court terme et maintenir un poids sain à long terme: Serein 700 Kcal', NULL, NULL),
(280, 119, 'Perdre plus de 10kg à court terme et maintenir un poids sain à long terme: Léger 700 Kcal', NULL, NULL),
(281, 119, 'Perdre plus de 10kg à court terme et maintenir un poids sain à long terme: Moyen 800 Kcal', NULL, NULL),
(282, 119, 'Perdre plus de 10kg à court terme et maintenir un poids sain à long terme: Serein 900 Kcal', NULL, NULL),
(283, 120, 'Oui', NULL, NULL),
(284, 120, 'Non', NULL, NULL),
(285, 121, 'Cardiaques', NULL, NULL),
(286, 121, 'Rénales', NULL, NULL),
(287, 123, 'Oui', NULL, NULL),
(288, 123, 'Non', NULL, NULL),
(289, 124, 'Oui', NULL, NULL),
(290, 124, 'Non', NULL, NULL),
(291, 125, 'Oui', NULL, NULL),
(292, 125, 'Non', NULL, NULL),
(293, 126, 'Oui', NULL, NULL),
(294, 126, 'Non', NULL, NULL),
(295, 127, 'Oui', NULL, NULL),
(296, 127, 'Non', NULL, NULL),
(297, 128, 'Oui', NULL, NULL),
(298, 128, 'Non', NULL, NULL),
(299, 129, 'Oui', NULL, NULL),
(300, 129, 'Non', NULL, NULL),
(301, 130, 'Oui', NULL, NULL),
(302, 130, 'Non', NULL, NULL),
(303, 132, 'Oui', NULL, NULL),
(304, 132, 'Non', NULL, NULL),
(305, 134, 'Oui', NULL, NULL),
(306, 134, 'Non', NULL, NULL),
(307, 136, 'Oui', NULL, NULL),
(308, 136, 'Non', NULL, NULL),
(309, 138, 'Oui', NULL, NULL),
(310, 138, 'Non', NULL, NULL),
(311, 139, 'Je reste indifférent(e).', NULL, NULL),
(312, 139, 'Je me sens stressé(e) ou frustré(e).', NULL, NULL),
(313, 139, 'Je me sens triste ou découragé(e).', NULL, NULL),
(314, 139, 'Je cherche un soutien (amis, famille, professionnels).', NULL, NULL),
(315, 140, 'Oui', NULL, NULL),
(316, 140, 'Non', NULL, NULL),
(317, 141, 'Mon objectif immédiat est de perdre moins de 5 kg et à long terme améliorer ma condition physique', NULL, NULL),
(318, 141, 'Mon objectif immédiat est de perdre entre 5 et 10 kg et à long terme améliorer ma condition physique', NULL, NULL),
(319, 141, 'Mon objectif immédiat est de perdre plus de 10 kg et à long terme améliorer ma condition physique', NULL, NULL),
(320, 142, 'Mon objectif immédiat est de perdre moins de 5 kg et à long terme améliorer ma condition physique: Léger 300 Kcal', NULL, NULL),
(321, 142, 'Mon objectif immédiat est de perdre moins de 5 kg et à long terme améliorer ma condition physique: Moyen 400 Kcal', NULL, NULL),
(322, 142, 'Mon objectif immédiat est de perdre moins de 5 kg et à long terme améliorer ma condition physique: Serein 500 Kcal', NULL, NULL),
(323, 142, 'Mon objectif immédiat est de perdre entre 5 et 10 kg et à long terme améliorer ma condition physique: Léger 500 Kcal', NULL, NULL),
(324, 142, 'Mon objectif immédiat est de perdre entre 5 et 10 kg et à long terme améliorer ma condition physique: Moyen 600 Kcal', NULL, NULL),
(325, 142, 'Mon objectif immédiat est de perdre entre 5 et 10 kg et à long terme améliorer ma condition physique: Serein 700 Kcal', NULL, NULL),
(326, 142, 'Mon objectif immédiat est de perdre plus de 10 kg et à long terme améliorer ma condition physique: Léger 700 Kcal', NULL, NULL),
(327, 142, 'Mon objectif immédiat est de perdre plus de 10 kg et à long terme améliorer ma condition physique: Moyen 800 Kcal', NULL, NULL),
(328, 142, 'Mon objectif immédiat est de perdre plus de 10 kg et à long terme améliorer ma condition physique: Serein 900 Kcal', NULL, NULL),
(329, 70, 'Aucun de ces médicaments', NULL, NULL),
(330, 75, 'Jamais', NULL, NULL),
(331, 121, 'Respiratoires', NULL, NULL),
(332, 121, 'Aucun', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reponses`
--

CREATE TABLE `reponses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `question_possible_answer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `utilisateur_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Utilisateur',
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `sexe` varchar(255) NOT NULL,
  `poids` int(11) NOT NULL,
  `taille` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `tour_de_taille` double(8,2) NOT NULL,
  `tour_de_hanche` double(8,2) NOT NULL,
  `tour_du_cou` double(8,2) NOT NULL,
  `niveau_d_activite_physique` varchar(255) NOT NULL,
  `maladie_chronique_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tdee` double(8,2) DEFAULT NULL,
  `img_notification` tinyint(1) NOT NULL DEFAULT 0,
  `imc_notification` tinyint(1) NOT NULL DEFAULT 0,
  `rth_notification` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `role`, `nom`, `prenom`, `age`, `sexe`, `poids`, `taille`, `email`, `mot_de_passe`, `tour_de_taille`, `tour_de_hanche`, `tour_du_cou`, `niveau_d_activite_physique`, `maladie_chronique_id`, `last_login_date`, `created_at`, `updated_at`, `tdee`, `img_notification`, `imc_notification`, `rth_notification`) VALUES
(1, 'Administrateur', 'Admin', 'Nutrition', 20, 'M', 110, 110, 'adminnutrition@gmail.com', '$2y$12$1s8heSH.76pEtcQRlsiVbOfj3f6Zg/97LovFuDdNG3BttFmsLaZv2', 100.00, 100.00, 100.00, '2', NULL, NULL, NULL, NULL, NULL, 0, 0, 0),
(2, 'Utilisateur', 'ATCHEGBE', 'Eneck', 27, 'M', 70, 171, 'Nutrition123@gmail.com', '$2y$12$OUOrZ4Eumype44O7a/4.a.rZj3DXA6II17wAqtdn3av8wggIEnym2', 34.00, 25.00, 20.00, 'Sédentaire', 1, '2025-12-11 21:40:18', '2025-12-03 14:09:13', '2025-12-11 20:41:57', NULL, 0, 0, 0),
(3, 'Utilisateur', 'HUGOSS', 'Henry', 12, 'M', 70, 171, 'eckodance25@gmail.com', '$2y$12$nnHJh8zGu/hbLvQ.I4CQAuF1ULB2rrlvG0vr37H.wv1KIQHsUdgN2', 34.00, 25.00, 20.00, 'Sédentaire', 3, '2025-12-15 17:35:47', '2025-12-11 23:39:44', '2025-12-15 16:35:47', NULL, 0, 0, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `administrateurs_identifiant_unique` (`identifiant`);

--
-- Index pour la table `maladie_chroniques`
--
ALTER TABLE `maladie_chroniques`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `plan_nutritionnels`
--
ALTER TABLE `plan_nutritionnels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_nutritionnels_utilisateur_id_foreign` (`utilisateur_id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_maladie_chronique_id_foreign` (`maladie_chronique_id`);

--
-- Index pour la table `question_possible_answers`
--
ALTER TABLE `question_possible_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_possible_answers_question_id_foreign` (`question_id`);

--
-- Index pour la table `reponses`
--
ALTER TABLE `reponses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reponses_question_id_foreign` (`question_id`),
  ADD KEY `reponses_utilisateur_id_foreign` (`utilisateur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utilisateurs_email_unique` (`email`),
  ADD KEY `utilisateurs_maladie_chronique_id_foreign` (`maladie_chronique_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `maladie_chroniques`
--
ALTER TABLE `maladie_chroniques`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `plan_nutritionnels`
--
ALTER TABLE `plan_nutritionnels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT pour la table `question_possible_answers`
--
ALTER TABLE `question_possible_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT pour la table `reponses`
--
ALTER TABLE `reponses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `plan_nutritionnels`
--
ALTER TABLE `plan_nutritionnels`
  ADD CONSTRAINT `plan_nutritionnels_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_maladie_chronique_id_foreign` FOREIGN KEY (`maladie_chronique_id`) REFERENCES `maladie_chroniques` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `question_possible_answers`
--
ALTER TABLE `question_possible_answers`
  ADD CONSTRAINT `question_possible_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Contraintes pour la table `reponses`
--
ALTER TABLE `reponses`
  ADD CONSTRAINT `reponses_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reponses_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_maladie_chronique_id_foreign` FOREIGN KEY (`maladie_chronique_id`) REFERENCES `maladie_chroniques` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
