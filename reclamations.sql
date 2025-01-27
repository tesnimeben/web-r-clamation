-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 27 jan. 2025 à 10:44
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `reclamations`
--

-- --------------------------------------------------------

--
-- Structure de la table `reclamations`
--

CREATE TABLE `reclamations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nomPrenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cin` varchar(8) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `titreReclamation` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `typeReclamation` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamations`
--

INSERT INTO `reclamations` (`id`, `user_id`, `nomPrenom`, `email`, `cin`, `telephone`, `titreReclamation`, `photo`, `service`, `typeReclamation`, `description`, `lieu`, `latitude`, `longitude`, `status`, `date`, `admin_id`) VALUES
(20, 9, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '06391619', '56766936', 'bbbb', '358065864_1490588418360938_6767604961476796049_n.jpg', 'securite_sociale', 'normale', 'sdfghjkl', ', Gouvernorat Béja, Tunisie', 36.5566, 9.59128, 'repondu', '2025-01-26 04:05:23', NULL),
(21, 9, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '15352835', '56766936', 'ell', 'images.jpeg', 'admin_education', 'urgente', 'bbnj cc ghjkl jklmù\r\nghjkl\r\nhjklm\r\nhjklm', ', Gouvernorat Nabeul, Tunisie', 36.7026, 10.4881, 'repondu', '2025-01-26 17:30:36', NULL),
(22, 9, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '15352835', '56766936', 'bbbb', 'images.jpg', 'admin_sante', 'urgente', 'FGHJK', 'Route Nationale Tunis - Hazoua, Gouvernorat Kairouan, Tunisie', 35.7737, 9.97926, 'repondu', '2025-01-26 17:53:52', NULL),
(23, 9, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '06391619', '56766936', 'Pollution / التلوث', 'images.jpg', 'admin_sante', 'normale', '<swdfghj', ', Gouvernorat Béja, Tunisie', 35.9895, 9.5034, 'repondu', '2025-01-27 00:16:02', NULL),
(24, 9, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '06391619', '56766936', 'Hôpitaux / المستشفيات', '', 'admin_sante', 'normale', 'xcvb', ', Gouvernorat Sfax, Tunisie', 34.7024, 10.3163, 'repondu', '2025-01-27 00:17:14', NULL),
(25, 25, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '15352835', '56766936', 'Problèmes de courrier / مشاكل البريد', 'images.jpg', 'admin_sante', 'normale', 'bghn,n;', 'Route Régionale RN 1 - RN 3, Gouvernorat Zaghouan, Tunisie', 36.1514, 10.0672, 'repondu', '2025-01-27 04:00:59', NULL),
(26, 25, '', '', '', '', '', NULL, '', '', '', '', 0, 0, 'nouveau', '2025-01-27 04:26:08', NULL),
(27, 25, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '06391619', '56766936', 'Aide sociale / المساعدة الاجتماعية', 'images.jpg', 'affaires_sociales', 'normale', 'sdfghiop', 'tunisie', 34.3978, 9.05488, 'nouveau', '2025-01-27 04:29:03', NULL),
(28, 9, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '06391619', '56766936', 'Contrôle des prix / مراقبة الأسعار', 'municipalité-de-tunis.jpeg', 'commerce', 'urgente', 'fghjklm', ', Gouvernorat Béja, Tunisie', 36.6743, 9.61325, 'nouveau', '2025-01-27 04:29:30', NULL),
(29, 25, 'tesnime bensalah', 'tasnimebensalah839@gmail.com', '15352835', '56766936', 'Interventions d\'urgence / التدخلات الطارئة', 'images.jpg', 'protection_civile', 'normale', '-y', ', Gouvernorat Sousse, Tunisie', 35.8831, 10.4157, 'nouveau', '2025-01-27 10:25:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','citoyen','admin_police','admin_sante','admin_education','admin_transport','admin_protection_civile','admin_securite_sociale','admin_administration_generale','admin_municipalite','admin_steg','admin_sonede','admin_poste','admin_sports','admin_agriculture','admin_affaires_sociales','admin_affaires_religieuses','admin_affaires_commerce','admin_environnement','admin_technologie','admin_tourisme','admin_banques_publiques') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(9, 'tastousa', '$2y$10$1ztH3Di440pJSqXKGBMX/Oe8BM..9lznNXDa4o6ADoY7.1SRHPnK6', 'admin_sante'),
(17, 'admin', '$2y$10$z8VrQRCnVh1LDUXF0P4v8.7SbzBKeB2OWn.JJScRsOc3KVpkh3MfO', 'admin'),
(20, 'yess', '$2y$10$STghkcrD190bruM73bD4ce6n5Gq9Qjn0yqEwFq921U2lr8/27Qnyq', 'citoyen'),
(25, 'tesnim', '$2y$10$hSYLu6xzRTiPPyOVKmAdo.YXeqkUHKxUfR8tC7KVk0Llx9PusebGK', 'citoyen'),
(26, 'mo', '$2y$10$HWMNxWp4/QklTsUER8hIAexcDk3BVwaT/fTQ58ir3dx7LcG3m/6Cq', 'admin_municipalite');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `reclamations`
--
ALTER TABLE `reclamations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `reclamations`
--
ALTER TABLE `reclamations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reclamations`
--
ALTER TABLE `reclamations`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
