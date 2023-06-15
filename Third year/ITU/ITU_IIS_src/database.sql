-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Pát 18. lis 2022, 11:54
-- Verze serveru: 5.7.36
-- Verze PHP: 7.4.26
-- Author: David Kocman, xkocma08

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `iis`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `match_`
--

DROP TABLE IF EXISTS `match_`;
CREATE TABLE IF NOT EXISTS `match_` (
  `match_id` int(3) NOT NULL AUTO_INCREMENT,
  `date` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `id_team1` int(3) NOT NULL,
  `id_team2` int(3) NOT NULL,
  `result1` int(3) NOT NULL,
  `result2` int(3) NOT NULL,
  `turnament_id` int(3) NOT NULL,
  `finished` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`match_id`),
  KEY `turnament_id` (`turnament_id`),
  KEY `id_team1` (`id_team1`),
  KEY `id_team2` (`id_team2`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `match_`
--

INSERT INTO `match_` (`match_id`, `date`, `id_team1`, `id_team2`, `result1`, `result2`, `turnament_id`, `finished`) VALUES
(1, '2022-10-08 19:04:39.752394', 1, 2, 1, 12, 1, 1),
(3, '2022-10-08 19:04:39.754004', 1, 3, 1, 0, 1, 1),
(4, '2022-10-08 19:04:39.755297', 1, 4, 1, 2, 1, 1),
(5, '2022-10-08 19:04:39.756544', 2, 4, 5, 0, 1, 1),
(93, '2022-11-16 22:44:19.214097', 2, 1, 5, 1, 1, 1),
(94, '2022-10-19 15:01:29.313142', 4, 2, 1, 0, 1, 1),
(95, '2022-11-16 22:44:19.214097', 2, 1, 5, 1, 1, 1),
(96, '2022-11-17 08:36:58.000000', 6, 9, 0, 0, 15, 0),
(97, '2022-11-17 08:36:58.000000', 10, 8, 0, 0, 15, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `team_id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `logo` varchar(200) NOT NULL,
  PRIMARY KEY (`team_id`),
  KEY `team_id` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `team`
--

INSERT INTO `team` (`team_id`, `name`, `logo`) VALUES
(1, 'Team1', 'https://cdn.logojoy.com/wp-content/uploads/2018/05/30161640/1329.png'),
(2, 'Team2', 'https://i.pinimg.com/originals/27/4f/9f/274f9fdab17c756a369fe0a5898ebea6.jpg'),
(3, 'Team3', 'https://i.pinimg.com/474x/07/b1/33/07b133e78156c97e369946d65b4bfef6.jpg'),
(4, 'Team4', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Logo_Brno.svg/512px-Logo_Brno.svg.png'),
(5, 'Team5', 'https://i.pinimg.com/originals/27/4f/9f/274f9fdab17c756a369fe0a5898ebea6.jpg'),
(6, 'jannov', 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(7, 'NewRandomGuy123', 'https://pbs.twimg.com/profile_images/1213639659764559872/1qMkyLNF_400x400.jpg'),
(8, 'RandomGuy123', 'https://live.staticflickr.com/7310/27096074294_534c8ef76d_b.jpg'),
(9, 'LastOne14', 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(10, 'Venca123', 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk=');

-- --------------------------------------------------------

--
-- Struktura tabulky `team_invite`
--

DROP TABLE IF EXISTS `team_invite`;
CREATE TABLE IF NOT EXISTS `team_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `team_invite`
--

INSERT INTO `team_invite` (`id`, `user_id`, `team_id`, `status`) VALUES
(1, 1, 1, -1),
(2, 1, 2, 1),
(3, 2, 1, 1),
(26, 6, 2, 1),
(25, 6, 2, 1),
(24, 6, 2, 1),
(8, 3, 2, 0),
(9, 2, 3, -1),
(23, 6, 2, 1),
(21, 3, 2, 0),
(27, 2, 5, -1),
(28, 7, 5, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `team_turn`
--

DROP TABLE IF EXISTS `team_turn`;
CREATE TABLE IF NOT EXISTS `team_turn` (
  `tm_trn_id` int(3) NOT NULL AUTO_INCREMENT,
  `team_id` int(3) NOT NULL,
  `tournament_id` int(3) NOT NULL,
  `accepted` int(1) NOT NULL,
  PRIMARY KEY (`tm_trn_id`),
  KEY `team_id` (`team_id`),
  KEY `tournament_id` (`tournament_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `team_turn`
--

INSERT INTO `team_turn` (`tm_trn_id`, `team_id`, `tournament_id`, `accepted`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 2, 2, 1),
(8, 2, 4, 0),
(9, 7, 12, 1),
(22, 6, 15, 1),
(23, 6, 11, 0),
(27, 4, 1, 1),
(28, 3, 1, 1),
(30, 9, 15, 1),
(31, 10, 15, 1),
(32, 8, 15, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `turnament`
--

DROP TABLE IF EXISTS `turnament`;
CREATE TABLE IF NOT EXISTS `turnament` (
  `turnament_id` int(3) NOT NULL AUTO_INCREMENT,
  `date` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `sport` varchar(50) NOT NULL,
  `type` int(1) NOT NULL,
  `description` varchar(1500) NOT NULL,
  `min_teams` int(2) NOT NULL DEFAULT '2',
  `max_teams` int(2) NOT NULL,
  `accepted` int(1) NOT NULL,
  `squads` int(1) NOT NULL,
  `size` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `creator` int(3) NOT NULL,
  `matchup_generated` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`turnament_id`),
  KEY `turnament_id` (`turnament_id`),
  KEY `creator` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `turnament`
--

INSERT INTO `turnament` (`turnament_id`, `date`, `sport`, `type`, `description`, `min_teams`, `max_teams`, `accepted`, `squads`, `size`, `name`, `creator`, `matchup_generated`) VALUES
(1, '2022-11-17 10:22:02.243271', 'fotbálek', 1, 'První turnaj ve fotbálku', 2, 16, 1, 2, 1, 'Fotbal1', 6, 1),
(2, '2022-11-17 10:15:22.472589', 'fotbálek', 0, 'První týmový turnaj ve fotbálku', 2, 16, 1, 4, 4, 'TeamFotbalek1', 6, 0),
(4, '2022-11-17 10:15:28.116965', 'Šipky', 0, 'Vánoční turnaj šipek', 2, 16, 1, 4, 2, 'Sipky', 7, 0),
(11, '2022-11-17 10:22:06.279689', 'kulečník', 1, 'První turnaj v kulečníku', 2, 16, 1, 8, 1, 'Kulečník1', 7, 0),
(12, '2022-11-17 10:15:57.717100', 'šachy', 1, 'Hodně štěstí všem zúčastněním.', 2, 4, 1, 6, 1, 'Předvánoční šachový turnaj', 6, 0),
(13, '2022-11-17 10:16:00.347635', 'fotbálek', 1, 'May the best man win', 2, 4, 0, 2, 1, 'Předvánoční fotbálkový turnaj', 6, 0),
(14, '2022-11-17 10:16:03.766544', 'kulečník', 1, 'Good luck to everyone', 2, 4, 1, 8, 1, 'Předvánoční turnaj v kulečníku', 6, 0),
(15, '2022-11-17 10:16:06.626644', 'pivní štafeta', 1, 'Skvělý turnaj pro milovníky pivních štafet', 2, 4, 1, 10, 1, 'Pivní Štafeta 1', 2, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(3) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `surname` varchar(200) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone` int(15) NOT NULL,
  `birthdate` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `school` varchar(200) NOT NULL,
  `faculty` varchar(200) NOT NULL,
  `year` int(1) NOT NULL,
  `password` varchar(50) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `picture` varchar(200) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`user_id`, `username`, `name`, `surname`, `email`, `phone`, `birthdate`, `school`, `faculty`, `year`, `password`, `admin`, `picture`) VALUES
(1, 'RandomGuy123', 'Petr', 'Bílek', 'seconddude@email.cz', 721847288, '2022-11-17 08:19:14.675429', 'VUT', 'FIT', 2, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, 'https://live.staticflickr.com/7310/27096074294_534c8ef76d_b.jpg'),
(2, 'NewRandomGuy123', 'Patrik', 'Novotný', 'seconddude@email.cz', 721847288, '2022-11-16 22:37:44.310422', 'MUNI', 'FI', 3, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, 'https://pbs.twimg.com/profile_images/1213639659764559872/1qMkyLNF_400x400.jpg'),
(3, 'admin', 'Filip', 'Dvořák', 'fil@seznam.cz', 749568984, '2022-11-16 22:36:41.333816', 'VUT', 'FIT', 1, '7ddf32e17a6ac5ce04a8ecbf782ca509', 1, 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(6, 'jannov', 'Jan', 'Novák', 'jannov@seznam.cz', 855905869, '2022-11-17 10:31:45.960429', 'VUT', 'FIT', 1, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(7, 'LastOne14', 'Jiří', 'Zelený', 'lastguy@gmail.com', 721832249, '2022-11-16 22:35:43.832684', 'VUT', 'FP', 1, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(8, 'Koci', 'David', 'Kocman', 'kocman.david@email.cz', 739457212, '2022-11-17 11:04:43.912508', 'VUT', 'FIT', 2, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(9, 'Venca123', 'Václav', 'Modrý', 'venca@gmail.com', 721521496, '1999-09-10 22:00:00.000000', 'VUT', 'FSI', 4, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk='),
(10, 'Andreas', 'Ondys', 'Véča', 'ondys@email.cz', 123456789, '2022-11-17 11:04:38.143451', 'UK', 'SOC', 3, '7ddf32e17a6ac5ce04a8ecbf782ca509', 0, '');

-- --------------------------------------------------------

--
-- Struktura tabulky `user_team`
--

DROP TABLE IF EXISTS `user_team`;
CREATE TABLE IF NOT EXISTS `user_team` (
  `usr_tm_id` int(3) NOT NULL AUTO_INCREMENT,
  `user_id` int(3) NOT NULL,
  `team_id` int(3) NOT NULL,
  `creator` varchar(50) NOT NULL,
  PRIMARY KEY (`usr_tm_id`),
  KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `user_team`
--

INSERT INTO `user_team` (`usr_tm_id`, `user_id`, `team_id`, `creator`) VALUES
(1, 1, 1, '0'),
(2, 2, 2, '1'),
(10, 8, 2, '0'),
(11, 1, 3, '1'),
(28, 6, 2, '0'),
(29, 6, 5, '1'),
(30, 7, 5, '0'),
(31, 6, 6, '1'),
(32, 2, 7, '1'),
(34, 1, 8, '1'),
(35, 7, 9, '1'),
(36, 9, 10, '1'),
(37, 7, 3, '0'),
(38, 9, 4, '1'),
(39, 7, 3, '0'),
(40, 9, 4, '1'),
(41, 1, 4, '0'),
(43, 10, 1, '0');

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `match_`
--
ALTER TABLE `match_`
  ADD CONSTRAINT `team1` FOREIGN KEY (`id_team1`) REFERENCES `team` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team2` FOREIGN KEY (`id_team2`) REFERENCES `team` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `turn` FOREIGN KEY (`turnament_id`) REFERENCES `turnament` (`turnament_id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `team_turn`
--
ALTER TABLE `team_turn`
  ADD CONSTRAINT `tm` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament` FOREIGN KEY (`tournament_id`) REFERENCES `turnament` (`turnament_id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `turnament`
--
ALTER TABLE `turnament`
  ADD CONSTRAINT `tour_creator` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `user_team`
--
ALTER TABLE `user_team`
  ADD CONSTRAINT `team` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
