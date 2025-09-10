-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 20, 2025 at 04:06 AM
-- Server version: 10.11.13-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ases_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `file_access`
--

CREATE TABLE `file_access` (
  `file_id` int(11) NOT NULL COMMENT 'Merujuk ke stopwatch_stats.id',
  `user_id` int(11) NOT NULL COMMENT 'Merujuk ke users.id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_access`
--

INSERT INTO `file_access` (`file_id`, `user_id`) VALUES
(123, 8),
(123, 10),
(124, 9),
(125, 8),
(127, 9),
(128, 8),
(128, 9),
(128, 10),
(129, 10),
(130, 8),
(130, 9),
(130, 10),
(138, 8),
(138, 9),
(140, 8),
(140, 9),
(140, 10);

-- --------------------------------------------------------

--
-- Table structure for table `komentar`
--

CREATE TABLE `komentar` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `video_id` int(11) NOT NULL,
  `komentar` text NOT NULL,
  `is_like` tinyint(1) DEFAULT 0,
  `is_dislike` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `komentar`
--

INSERT INTO `komentar` (`id`, `username`, `video_id`, `komentar`, `is_like`, `is_dislike`, `created_at`) VALUES
(140, 'indah', 123, 'fdsafdsajflkdsjalkk dfjskla dlksajfdkls;j fdslajfdklsjfdoisk fjdklsmfklcmvc k0fdeojf kjklcs vdjklafkds lfdsa fdsfdsa', 0, 1, '2025-08-11 03:27:11'),
(141, 'sinta', 123, 'fdsfdsaf dsacvd scvds ds dfsa fdsa fds fds dsa fdsfdsa fds ds dsa', 0, 1, '2025-08-11 03:27:11'),
(142, 'ardy', 123, 'fdsafdsa fdsaf ds fewfsa fdsaf ew fewfew e', 1, 0, '2025-08-11 03:27:11'),
(143, 'ardy', 124, 'dsadsadsa', 0, 1, '2025-08-12 00:03:37'),
(144, 'ardy', 125, 'Bagusss', 1, 0, '2025-08-13 03:04:53'),
(146, 'indah', 128, 'Bagus saja', 1, 0, '2025-08-13 03:42:02'),
(147, 'ranty', 129, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 1, 0, '2025-08-13 09:43:28'),
(148, 'ardy', 130, 'tes', 1, 0, '2025-08-15 02:14:15'),
(158, 'Test', 138, 'Ok', 0, 0, '2025-08-18 18:29:45'),
(159, 'ardydethan', 138, 'Jsjsksksk', 1, 0, '2025-08-18 18:29:45'),
(160, 'ardy', 139, 'Bagus film nya', 1, 0, '2025-08-19 15:05:56'),
(161, 'ardy', 139, 'Kurang suka di storytelling nya', 0, 1, '2025-08-19 15:05:56'),
(162, 'chr', 140, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\n', 1, 0, '2025-08-20 10:54:33');

-- --------------------------------------------------------

--
-- Table structure for table `previewpanel`
--

CREATE TABLE `previewpanel` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `usia` int(3) DEFAULT NULL,
  `pekerjaan` varchar(250) DEFAULT NULL,
  `bidang_kerja` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `previewpanel`
--

INSERT INTO `previewpanel` (`id`, `user_id`, `nama_lengkap`, `jenis_kelamin`, `usia`, `pekerjaan`, `bidang_kerja`, `created_at`) VALUES
(1, 4, 'christian', 'Perempuan', 26, 'Sales Y', 'Marketing', '2025-08-04 04:38:19'),
(2, 6, 'rantyyyyy', 'Laki-laki', 23, 'Sales ', 'Marketing', '2025-08-04 04:57:34'),
(3, 5, 'Maria', 'Perempuan', 30, 'Sales IT', 'Marketing', '2025-08-04 06:55:31'),
(4, 7, 'indahh Permata Sari', 'Perempuan', 38, 'Sales Y', 'Marketing', '2025-08-07 02:27:43'),
(5, 3, 'ChR', 'Laki-laki', 35, 'IT', 'admin', '2025-08-08 02:20:09'),
(6, 13, 'Sintaaaaaa', 'Laki-laki', 25, 'Sales ', 'Marketing', '2025-08-08 11:12:43'),
(7, 12, 'Bima Perkasa', 'Laki-laki', 39, 'Pegawai', 'Keuangan', '2025-08-10 11:57:15'),
(9, 16, 'royyyyy', 'Laki-laki', 23, 'Mahasiswa', 'Mahasiswa', '2025-08-17 15:22:35'),
(10, 18, 'RyanL', 'Laki-laki', 33, 'Swasta', 'Swasta', '2025-08-18 11:28:18'),
(11, 19, 'Ardy ', 'Laki-laki', 25, 'Sgshhs', 'Hshshs', '2025-08-18 11:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `stopwatch_stats`
--

CREATE TABLE `stopwatch_stats` (
  `id` int(11) NOT NULL,
  `nama_video` varchar(50) NOT NULL,
  `love_count` int(11) NOT NULL DEFAULT 0,
  `share_count` int(11) NOT NULL DEFAULT 0,
  `response_senang` int(11) NOT NULL DEFAULT 0,
  `response_biasa` int(11) NOT NULL DEFAULT 0,
  `response_sedih` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `like_details` text DEFAULT NULL,
  `share_details` text DEFAULT NULL,
  `session_male_count` int(11) NOT NULL DEFAULT 0,
  `session_female_count` int(11) NOT NULL DEFAULT 0,
  `session_average_age` float NOT NULL DEFAULT 0,
  `pref_senang` int(11) NOT NULL DEFAULT 0,
  `pref_biasa` int(11) NOT NULL DEFAULT 0,
  `pref_marah` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stopwatch_stats`
--

INSERT INTO `stopwatch_stats` (`id`, `nama_video`, `love_count`, `share_count`, `response_senang`, `response_biasa`, `response_sedih`, `last_updated`, `like_details`, `share_details`, `session_male_count`, `session_female_count`, `session_average_age`, `pref_senang`, `pref_biasa`, `pref_marah`) VALUES
(123, '8 11 2025, 5:24', 34, 31, 0, 0, 3, '2025-08-11 03:27:11', '{\"5\":3,\"10\":3,\"14\":2,\"22\":3,\"27\":2,\"28\":2,\"29\":2,\"33\":4,\"34\":1,\"35\":1,\"39\":1,\"40\":6,\"49\":1,\"50\":2,\"51\":1}', '{\"12\":1,\"15\":5,\"16\":5,\"17\":3,\"22\":2,\"23\":2,\"27\":2,\"29\":1,\"30\":1,\"34\":2,\"37\":2,\"50\":2,\"51\":3}', 2, 1, 33.3, 0, 2, 1),
(124, 'Uji Coba 12 8 2025', 44, 19, 0, 0, 1, '2025-08-12 00:03:37', '{\"3\":1,\"5\":2,\"6\":1,\"7\":3,\"8\":4,\"10\":3,\"12\":3,\"14\":2,\"15\":1,\"17\":4,\"18\":6,\"19\":5,\"20\":5,\"21\":4}', '{\"6\":1,\"12\":1,\"13\":5,\"16\":3,\"25\":2,\"26\":1,\"27\":4,\"28\":2}', 1, 0, 37, 3, 1, 0),
(125, 'Uji Coba 13 8 2025', 37, 15, 0, 0, 1, '2025-08-13 03:04:53', '{\"6\":2,\"8\":2,\"9\":1,\"11\":4,\"12\":1,\"14\":3,\"16\":1,\"17\":2,\"18\":1,\"19\":2,\"20\":2,\"22\":1,\"23\":1,\"24\":3,\"25\":4,\"26\":6,\"27\":1}', '{\"10\":2,\"12\":2,\"13\":3,\"18\":1,\"19\":1,\"20\":1,\"28\":2,\"29\":3}', 1, 0, 37, 1, 0, 0),
(127, 'Uji coba 3 13 8 2025', 16, 8, 0, 1, 0, '2025-08-13 03:28:09', '{\"1\":2,\"2\":3,\"3\":3,\"5\":1,\"6\":1,\"7\":3,\"9\":2,\"10\":1}', '{\"4\":3,\"5\":1,\"8\":2,\"9\":2}', 1, 0, 37, 0, 0, 1),
(128, 'Ujicoba4 13 8 2025', 28, 13, 0, 1, 0, '2025-08-13 03:42:02', '{\"2\":1,\"5\":1,\"10\":1,\"12\":1,\"15\":1,\"18\":1,\"19\":1,\"21\":1,\"25\":1,\"27\":1,\"28\":2,\"29\":1,\"30\":1,\"31\":3,\"32\":2,\"33\":1,\"37\":2,\"38\":1,\"39\":2,\"40\":1,\"41\":2}', '{\"4\":1,\"22\":1,\"23\":5,\"24\":4,\"40\":2}', 0, 1, 38, 1, 0, 0),
(129, 'Uji coba lagiii 13 8 2025', 31, 19, 0, 0, 1, '2025-08-13 09:43:28', '{\"0\":3,\"2\":1,\"3\":1,\"4\":1,\"5\":4,\"6\":3,\"7\":6,\"10\":2,\"11\":1,\"12\":1,\"13\":3,\"15\":2,\"16\":2,\"17\":1}', '{\"1\":4,\"2\":1,\"3\":3,\"8\":4,\"9\":1,\"11\":2,\"14\":2,\"15\":1,\"16\":1}', 1, 0, 23, 0, 1, 0),
(130, '15 8 2025, 10;11', 32, 22, 1, 0, 0, '2025-08-15 02:14:15', '{\"11\":3,\"12\":1,\"15\":2,\"16\":1,\"25\":2,\"26\":2,\"27\":2,\"29\":2,\"32\":1,\"37\":1,\"49\":1,\"50\":1,\"57\":1,\"58\":2,\"60\":2,\"61\":1,\"63\":1,\"68\":1,\"76\":2,\"77\":1,\"96\":1,\"103\":1}', '{\"21\":3,\"22\":1,\"24\":2,\"28\":1,\"30\":1,\"33\":1,\"38\":1,\"57\":1,\"58\":1,\"60\":1,\"61\":1,\"62\":1,\"69\":1,\"76\":1,\"77\":1,\"78\":1,\"97\":2,\"103\":1}', 1, 0, 30, 1, 0, 0),
(138, 'Uji coba 18 7 2025', 265, 198, 2, 0, 0, '2025-08-18 18:29:45', '{\"3\":1,\"8\":2,\"9\":3,\"10\":4,\"14\":1,\"15\":1,\"17\":1,\"18\":2,\"20\":1,\"22\":2,\"23\":2,\"24\":3,\"25\":7,\"26\":7,\"27\":9,\"28\":6,\"29\":4,\"30\":6,\"31\":1,\"32\":6,\"33\":6,\"34\":6,\"35\":5,\"36\":6,\"37\":6,\"38\":6,\"39\":5,\"40\":5,\"41\":6,\"42\":7,\"43\":10,\"44\":12,\"45\":10,\"46\":10,\"47\":11,\"48\":6,\"49\":11,\"50\":8,\"51\":12,\"52\":13,\"53\":16,\"54\":11,\"55\":5,\"56\":3}', '{\"5\":1,\"9\":1,\"10\":3,\"15\":1,\"16\":2,\"17\":1,\"18\":1,\"19\":2,\"21\":1,\"22\":1,\"23\":2,\"24\":3,\"25\":1,\"26\":2,\"27\":5,\"28\":11,\"29\":11,\"30\":4,\"31\":2,\"32\":10,\"33\":12,\"34\":10,\"35\":7,\"36\":12,\"37\":9,\"38\":10,\"39\":9,\"40\":11,\"41\":10,\"42\":7,\"43\":6,\"44\":5,\"45\":5,\"46\":5,\"47\":5,\"48\":4,\"49\":2,\"50\":4}', 2, 0, 29, 2, 0, 0),
(139, 'Testing 19 Agustus 2025', 8, 0, 0, 1, 1, '2025-08-19 15:05:56', '{\"4\":1,\"5\":1,\"7\":1,\"8\":1,\"10\":1,\"12\":1,\"29\":1,\"31\":1}', '{}', 1, 1, 30, 0, 0, 0),
(140, 'test 20 8 2025', 330, 175, 1, 0, 0, '2025-08-20 10:54:33', '{\"1\":1,\"5\":1,\"30\":4,\"31\":5,\"32\":1,\"40\":2,\"41\":5,\"42\":3,\"86\":3,\"94\":5,\"95\":2,\"102\":1,\"103\":5,\"104\":2,\"133\":2,\"137\":4,\"138\":3,\"145\":2,\"146\":4,\"172\":3,\"183\":1,\"184\":4,\"185\":1,\"188\":1,\"189\":2,\"197\":3,\"198\":5,\"199\":3,\"208\":4,\"209\":2,\"214\":4,\"215\":4,\"217\":5,\"218\":4,\"221\":5,\"222\":5,\"223\":2,\"241\":1,\"242\":3,\"243\":1,\"252\":1,\"253\":2,\"254\":1,\"255\":2,\"258\":1,\"259\":2,\"269\":2,\"275\":3,\"276\":4,\"286\":1,\"287\":2,\"290\":2,\"291\":3,\"293\":3,\"296\":1,\"297\":4,\"307\":2,\"317\":1,\"318\":2,\"321\":5,\"322\":2,\"324\":1,\"325\":1,\"330\":2,\"332\":2,\"333\":3,\"335\":3,\"336\":4,\"355\":3,\"356\":2,\"359\":3,\"360\":4,\"362\":2,\"363\":5,\"367\":1,\"368\":1,\"370\":3,\"391\":4,\"392\":2,\"397\":3,\"398\":4,\"401\":3,\"402\":1,\"409\":4,\"421\":3,\"422\":2,\"424\":2,\"425\":4,\"427\":1,\"428\":3,\"429\":1,\"430\":1,\"432\":2,\"433\":1,\"450\":1,\"451\":2,\"452\":1,\"453\":4,\"460\":1,\"461\":2,\"464\":4,\"465\":1,\"466\":3,\"467\":2,\"468\":2,\"469\":6,\"470\":5,\"471\":5,\"476\":3,\"477\":6,\"478\":5,\"479\":5,\"487\":3,\"491\":2,\"492\":4,\"493\":2,\"495\":2,\"496\":5,\"497\":5,\"498\":3,\"499\":1}', '{\"7\":2,\"8\":4,\"35\":4,\"36\":3,\"45\":3,\"46\":6,\"87\":5,\"88\":2,\"100\":4,\"106\":1,\"134\":4,\"135\":1,\"136\":2,\"143\":2,\"144\":4,\"172\":2,\"176\":1,\"177\":1,\"193\":5,\"194\":3,\"210\":2,\"211\":1,\"223\":1,\"241\":2,\"242\":2,\"243\":1,\"253\":1,\"254\":3,\"255\":1,\"256\":1,\"260\":4,\"271\":4,\"272\":5,\"273\":1,\"287\":1,\"288\":5,\"289\":3,\"295\":2,\"308\":3,\"319\":5,\"320\":2,\"324\":2,\"332\":2,\"338\":1,\"339\":2,\"356\":1,\"357\":3,\"365\":1,\"366\":2,\"369\":1,\"370\":1,\"393\":5,\"394\":4,\"402\":1,\"403\":1,\"411\":4,\"423\":4,\"428\":1,\"429\":1,\"430\":1,\"451\":3,\"452\":2,\"461\":2,\"462\":3,\"474\":1,\"480\":3,\"490\":3,\"491\":2,\"493\":2,\"494\":4,\"495\":1,\"498\":1,\"499\":1}', 0, 1, 26, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','client') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `image`) VALUES
(3, 'admin', '$2y$10$26dVfS0GriK7lWHBFR4wzeMwq9vbuB7QcGWpgE3nwfHVugsPujqMm', 'admin', '2025-07-11 13:57:50', 'default.jpg'),
(4, 'chr', '$2y$10$4/heCYSCDVs3VitovsiYEuimdqV3AHCBMs2nR9NPk4Og719lXMXwu', 'user', '2025-07-11 13:57:50', 'default.jpg'),
(5, 'ardy', '$2y$10$aKzpIqj2Fa5KaTyUdfJpNeeWUkaLrUXVnsBk.1jfQQYH0KTr5poKu', 'user', '2025-07-11 15:27:54', 'default.jpg'),
(6, 'ranty', '$2y$10$bmnlPb3YZUuVQw6WvwWV9eao5qY2yA7Qv40SagAcZhUF5FD9q.zt.', 'user', '2025-07-11 16:16:50', 'default.jpg'),
(7, 'indah', '$2y$10$Bv1oPTzJfy/Dy9MYWqqNNeGDqzypWUR.qSBZkAtJKQz25aGrZEMYy', 'user', '2025-07-21 02:20:17', 'default.jpg'),
(8, 'client1', '$2y$10$DuiYaMXkzdS5pbr4zaSjr.McEShv5nQ.zGq50i0WejKr8muZJe39e', 'client', '2025-08-07 16:41:08', 'default.jpg'),
(9, 'client2', '$2y$10$DwsLOtRCtrDlur4n4uIiWenriYy6b8ClDPsnbsJn33x2EvOl69cum', 'client', '2025-08-08 10:50:31', 'default.jpg'),
(10, 'client3', '$2y$10$sr6TwB.dyp9iYG5lS/YPoe6g0jAwt9C.cUL9iW7.s9RZZ6oOqb42S', 'client', '2025-08-08 11:06:24', 'default.jpg'),
(12, 'bima', '$2y$10$MuxUGtVNKbFHsAZZhTLH9uWOA0CpK/oQx5p8SJEkDrtNCZLV4qCtO', 'user', '2025-08-08 11:07:40', 'default.jpg'),
(13, 'sinta', '$2y$10$RROnR9DDwfwsw3LoTdmQ6.UM6.CSr9EINwofhu9qkPnFzo0vllFS.', 'user', '2025-08-08 11:07:49', 'default.jpg'),
(16, 'roy', '$2y$10$o4aqfzwDktzKVWXhzKKhde540mu7qJTRNQjyzieFP97aGRmX5Q5Xa', 'user', '2025-08-12 02:57:29', 'default.jpg'),
(18, 'Test', '$2y$10$XkyoecARy8fwW2far963MO8TtsjZQG7soUDCqNFZ4gIVLYCzFkf5a', 'user', '2025-08-18 11:26:27', 'default.jpg'),
(19, 'ardydethan', '$2y$10$Kp39yvlxVyKdQzKURey8KuaEr9SNvh1S57OJ5bP9YZ6ppuGvoaele', 'user', '2025-08-18 11:27:22', 'default.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file_access`
--
ALTER TABLE `file_access`
  ADD PRIMARY KEY (`file_id`,`user_id`),
  ADD KEY `user_id_fk_idx` (`user_id`);

--
-- Indexes for table `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `previewpanel`
--
ALTER TABLE `previewpanel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_unique` (`user_id`);

--
-- Indexes for table `stopwatch_stats`
--
ALTER TABLE `stopwatch_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `komentar`
--
ALTER TABLE `komentar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `previewpanel`
--
ALTER TABLE `previewpanel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stopwatch_stats`
--
ALTER TABLE `stopwatch_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file_access`
--
ALTER TABLE `file_access`
  ADD CONSTRAINT `file_access_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `stopwatch_stats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `file_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_file_access_file` FOREIGN KEY (`file_id`) REFERENCES `stopwatch_stats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_file_access_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `komentar`
--
ALTER TABLE `komentar`
  ADD CONSTRAINT `komentar_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `komentar_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `stopwatch_stats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `previewpanel`
--
ALTER TABLE `previewpanel`
  ADD CONSTRAINT `fk_previewpanel_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
