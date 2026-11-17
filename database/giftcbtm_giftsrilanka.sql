-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2026 at 11:43 PM
-- Server version: 11.4.10-MariaDB-cll-lve
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `giftcbtm_giftsrilanka`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `district`, `avatar_path`, `email_verified_at`, `password`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$9IIDwORcVzuiEWNzhcdHlOLiQ3ghIpyVL3I5qNgsYfk4dLOQFXyd.', 1, NULL, '2026-02-23 02:45:55', '2026-02-23 03:59:13'),
(2, 'Admin', 'admin@test.com', NULL, NULL, NULL, NULL, '2026-02-23 03:40:52', '$2y$12$qPXi91/4MG9ed/3kzP.4BOjKZgde58caTR8oT7GBs9j/86txgrZqi', 1, NULL, '2026-02-23 03:40:52', '2026-02-23 03:40:52'),
(3, 'Customer1', 'customer1@test.com', NULL, NULL, NULL, NULL, '2026-02-23 03:40:52', '$2y$12$VUI6vyqtg8pBOIh9JWWrbOpffK8LxgUK1jQiZvkzdgQAisAKlHowm', 2, NULL, '2026-02-23 03:40:52', '2026-02-23 03:40:52'),
(4, 'Customer2', 'customer2@test.com', NULL, NULL, NULL, NULL, '2026-02-23 03:40:52', '$2y$12$do/2dbhh6Fjx1cjW6bMCauSw2VUr9GH.fahFgYA9iiaDf6y05k5Wa', 2, NULL, '2026-02-23 03:40:52', '2026-02-23 03:40:52'),
(5, 'User', 'user@gmail.com', '0718628191', 'Badulla', 'Badulla', 'avatars/WncGxw5Bous9kQyfLFXcCoF5Poy43ExOkRuput1n.jpg', NULL, '$2y$12$aJieN5cJoxM/r6IuTkLOVeC9ehjZfii6FW4zkox15owUn/YE51Lt6', 2, NULL, '2026-02-23 03:48:25', '2026-03-03 04:08:05'),
(6, 'test user', 'testuser@gmail.com', '0123456789', 'test address', 'test district', NULL, NULL, '$2y$12$.zdImsodD/U.igWLGMUKY.Mit2fb/L3wk87aoB6JESZnQXOGP60Ci', 2, 'ftQdTGR3bhGQNbvoXuvFdYf5qVuCYBGO2SjD1YhLDZbsQ3ZZ56MgzxU1kVS3', '2026-02-23 23:01:05', '2026-03-04 02:34:27'),
(7, 'abc', 'abc@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$Q3MR3GP606.QBsFjXuuALOwp0.97J8ek.Y9AZMJg.WczTr9/aFlpG', 2, NULL, '2026-02-27 00:26:50', '2026-02-27 00:26:50'),
(8, 'sanka eranga', 'isankaerangahtc820@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$hvIcQmCQDpb1bwVrV7FGy.WIcadEAAXHOJOaYLsso0N/vjXvJ4TTW', 2, NULL, '2026-03-03 01:03:42', '2026-03-03 01:03:42'),
(9, 'GiftsSriLanka', 'giftssrilankaa@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$syNq66YoHdvXY0v89wvAj.eUDIUBXz6c0yXV7q6BzOukeJma2uTLm', 1, NULL, '2026-03-10 09:38:39', '2026-03-10 09:38:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
