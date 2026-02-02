-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2022 at 02:55 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mason`
--

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `id` int(11) NOT NULL,
  `zone_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `branch_code` varchar(255) DEFAULT NULL,
  `state_id` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1= Active, 0= Disabled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`id`, `zone_id`, `name`, `branch_code`, `state_id`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'ABC1', NULL, NULL, 'ABC1', 1, '2022-11-11 14:16:09', '2022-11-23 06:41:39'),
(3, 2, 'ABC2', NULL, NULL, 'ABC29', 1, '2022-11-11 14:16:09', '2022-11-28 04:13:44'),
(4, 3, 'Branch-4', NULL, NULL, 'Branch-4', 1, '2022-11-11 14:16:09', '2022-11-23 06:41:39'),
(5, 4, 'Branch-5', NULL, NULL, 'Branch-5', 1, '2022-11-11 14:16:09', '2022-11-23 06:41:39'),
(6, 2, 'Vivien Collins', NULL, NULL, 'Totam reprehenderit', 1, '2022-11-28 04:14:22', '2022-11-28 04:14:22'),
(8, 1, 'New Branch', 'NBCG', 28, 'GG', 0, '2022-12-12 05:35:17', '2022-12-13 01:30:14');

-- --------------------------------------------------------

--
-- Table structure for table `catalogues`
--

CREATE TABLE `catalogues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mason_category_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `point` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `catalogues`
--

INSERT INTO `catalogues` (`id`, `mason_category_id`, `name`, `description`, `image`, `point`, `created_at`, `updated_at`) VALUES
(1, NULL, 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, y', 'Test', NULL, '5', '2022-12-02 08:36:06', '2022-12-02 08:36:06'),
(2, NULL, 'Ross Maldonado', 'Sed cumque dolorum i', NULL, '36', '2022-12-02 08:37:13', '2022-12-02 08:37:13'),
(3, NULL, 'Ross Maldonado', 'Sed cumque dolorum i', NULL, '36', '2022-12-02 08:37:39', '2022-12-02 08:37:39'),
(4, 1, 'Ross Maldonado', 'Sed cumque dolorum i', NULL, '36', '2022-12-02 08:37:50', '2022-12-07 04:19:02'),
(6, 2, 'Jamal Schwartz', 'Dignissimos rerum se', 'catalogues/11e1f50a953c3f8a5ca4cef6efbda739.jpg', '57', '2022-12-02 08:52:46', '2022-12-07 04:10:25');

-- --------------------------------------------------------

--
-- Table structure for table `contact_pages`
--

CREATE TABLE `contact_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mobile` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_pages`
--

INSERT INTO `contact_pages` (`id`, `mobile`, `address`, `created_at`, `updated_at`) VALUES
(1, '8989989985', '8989989989', NULL, '2022-12-05 20:10:21');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lifting`
--

CREATE TABLE `lifting` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `lifting_date` varchar(200) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `img` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `mason_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lifting`
--

INSERT INTO `lifting` (`id`, `product_id`, `qty`, `lifting_date`, `remark`, `img`, `user_id`, `mason_id`, `created_at`, `updated_at`) VALUES
(1, 2, 3, '30-11-2022', NULL, NULL, 90, NULL, '2022-11-14 02:57:08', '2022-11-14 02:57:08'),
(2, 2, 3, '30-11-2022', NULL, NULL, 90, NULL, '2022-11-14 02:57:59', '2022-11-14 02:57:59'),
(3, 2, 3, '30-11-2022', NULL, NULL, 90, NULL, '2022-11-14 02:58:21', '2022-11-14 02:58:21'),
(4, 2, 3, '30-11-2022', NULL, 'http://localhost/mason/admin/public/lifting/L4.png', 90, NULL, '2022-11-14 02:59:07', '2022-11-14 02:59:07'),
(5, 2, 3, '30-11-2022', NULL, 'http://localhost/mason/admin/public/lifting/L5.pdf', 90, NULL, '2022-11-14 03:02:33', '2022-11-14 03:02:33'),
(6, 2, 5, '30-11-2022', NULL, 'http://localhost/mason/admin/public/lifting/L6.png', 110, NULL, '2022-11-14 03:05:30', '2022-11-14 03:05:30'),
(7, 2, 747, '2001-05-02', 'Natus est perferend', NULL, 92, NULL, '2022-11-25 08:47:04', '2022-11-25 08:47:04'),
(8, 1, 451, '2013-11-30', 'Itaque ipsa expedit', NULL, 92, NULL, '2022-11-25 08:48:01', '2022-11-25 08:48:01'),
(9, 2, 425, '2004-02-01', 'Cupiditate cumque ab', NULL, 108, NULL, '2022-11-25 08:50:09', '2022-11-25 08:50:09'),
(10, 2, 409, '2013-07-15', 'Atque optio magnam', NULL, 110, NULL, '2022-11-28 00:08:07', '2022-11-28 00:08:07'),
(11, 2, 409, '2013-07-15', 'Atque optio magnam', NULL, 110, NULL, '2022-11-28 00:16:06', '2022-11-28 00:16:06'),
(12, 2, 771, '2007-11-03', 'Qui assumenda veniam', NULL, 86, NULL, '2022-11-28 00:52:29', '2022-11-28 00:52:29'),
(16, 2, 408, '2012-06-04', 'Ex exercitation sunts', 'liftings/41ad65de8e7116661be19a6391a6c6f3.jpeg', 99, NULL, '2022-11-28 01:34:01', '2022-11-28 01:54:05'),
(18, 6, 50, '07-12-2022', 'Done', NULL, 88, NULL, '2022-12-07 06:05:28', '2022-12-07 06:05:28'),
(22, 2, 52, '16-12-2022', 'Done', NULL, 114, NULL, '2022-12-16 07:07:27', '2022-12-16 07:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `mason_categories`
--

CREATE TABLE `mason_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_point` bigint(20) DEFAULT NULL,
  `to_point` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mason_categories`
--

INSERT INTO `mason_categories` (`id`, `name`, `from_point`, `to_point`, `created_at`, `updated_at`) VALUES
(1, 'BRONZE', 200, 500, '2022-12-06 05:22:37', '2022-12-06 05:25:42'),
(2, 'BRONZE +', 501, 1000, '2022-12-06 05:25:24', '2022-12-06 05:25:24'),
(5, 'SILVER', 1001, 2000, '2022-12-08 05:46:48', '2022-12-08 05:46:48'),
(6, 'SILVER +', 2001, 3000, '2022-12-08 05:47:28', '2022-12-08 05:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `mason_dealers`
--

CREATE TABLE `mason_dealers` (
  `id` int(11) NOT NULL,
  `mason_id` int(11) DEFAULT NULL,
  `dealer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mason_dealers`
--

INSERT INTO `mason_dealers` (`id`, `mason_id`, `dealer_id`, `created_at`, `updated_at`) VALUES
(22, 86, 88, '2022-11-10 12:40:48', NULL),
(23, 86, 89, '2022-11-10 12:40:48', NULL),
(24, 92, 90, '2022-11-22 06:39:39', NULL),
(25, 92, 4, '2022-11-22 06:39:39', NULL),
(26, 98, 8, '2022-11-22 09:18:00', NULL),
(27, 98, 9, '2022-11-22 09:18:00', NULL),
(28, 99, 90, '2022-11-22 09:48:59', NULL),
(29, 99, 6, '2022-11-22 09:48:59', NULL),
(30, 4, 4, '2022-11-22 09:49:45', NULL),
(31, 107, 90, '2022-11-22 12:03:58', NULL),
(32, 107, 94, '2022-11-22 12:03:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2016_06_01_000001_create_oauth_auth_codes_table', 2),
(6, '2016_06_01_000002_create_oauth_access_tokens_table', 2),
(7, '2016_06_01_000003_create_oauth_refresh_tokens_table', 2),
(8, '2016_06_01_000004_create_oauth_clients_table', 2),
(9, '2016_06_01_000005_create_oauth_personal_access_clients_table', 2),
(10, '2022_07_12_092026_create_settings_table', 3),
(11, '2022_11_27_104650_create_room_types_table', 4),
(12, '2022_11_28_105950_create_roles_table', 5),
(13, '2022_12_02_133708_create_catalogues_table', 6),
(14, '2022_12_02_142625_create_static_pages_table', 7),
(15, '2022_12_05_101215_create_conntact_pages_table', 8),
(17, '2022_12_05_101215_create_contact_pages_table', 9),
(18, '2022_12_05_121636_create_social_links_table', 10),
(19, '2022_12_05_141035_create_settings_table', 11),
(20, '2022_12_06_090657_create_mason_categories_table', 12),
(21, '2022_12_07_065018_create_reward_points_table', 13),
(22, '2022_12_07_100039_create_rewards_table', 14),
(23, '2022_12_07_074619_create_user_catalogue_redeemtions_table', 15),
(24, '2022_12_12_102657_create_zones_table', 16),
(25, '2022_12_12_111624_create_states_table', 17);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('015331a2bd233c57c90f2ce20a260c88683626cd8ba4793617dea3f5eff701954f0c8e5cc1c5484f', 38, 1, 'authToken', '[]', 0, '2022-08-06 06:09:00', '2022-08-06 06:09:00', '2023-08-06 11:39:00'),
('0272099474d6948ea2d70b3a2201f3431f13574099c913102f0737d4289736855c73707c72c4785c', 55, 1, 'authToken', '[]', 0, '2022-09-07 09:04:21', '2022-09-07 09:04:21', '2023-09-07 11:04:21'),
('02aa0addfc967a993d3f5a286a72f4225182c675515ddf412893d60e14e47da70589da6463d7bd1e', 42, 1, 'authToken', '[]', 0, '2022-08-18 02:50:05', '2022-08-18 02:50:05', '2023-08-18 04:50:05'),
('0332435f88fa9c821a0633931acb0ed3c3654e025d464ac9eee851ae2261aa2997d04616f458e7e3', 67, 1, 'authToken', '[]', 0, '2022-11-09 00:50:14', '2022-11-09 00:50:14', '2023-11-09 06:20:14'),
('04d582445017bb8f069418875c05e15a1a2b54678cfb1b67b3ebe75bbd116f7af52b8e0d27abb9e8', 55, 1, 'authToken', '[]', 0, '2022-09-06 10:15:28', '2022-09-06 10:15:28', '2023-09-06 12:15:28'),
('06502fd07d4e77863aa7816e21d6b8347028ae2524a3fcb506a627cf9a990892ed93762b9998efe0', 55, 1, 'authToken', '[]', 0, '2022-09-07 10:02:16', '2022-09-07 10:02:16', '2023-09-07 12:02:16'),
('06b6381b211cfe9ebbe2f031928a1c4ad9ea5435e84cbb44127f1676bc0049d45e7d91aa7ef9ec80', 60, 1, 'authToken', '[]', 0, '2022-11-08 01:13:29', '2022-11-08 01:13:29', '2023-11-08 06:43:29'),
('06f824491b01a3e57324dfb06b1833481f066baf47ec1becd57e345b7ecf153cd65824d56bce92e7', 50, 1, 'authToken', '[]', 0, '2022-08-30 05:57:21', '2022-08-30 05:57:21', '2023-08-30 07:57:21'),
('079166486fd74b01f963691a30ae5884b01e845eb1bb5ed3d865e1098b4d24b797ff1a4bfb56941e', 38, 1, 'authToken', '[]', 1, '2022-08-06 04:24:52', '2022-08-06 04:24:52', '2023-08-06 09:54:52'),
('083a9746e60a1d70210f4a878893b7907276010140e02a01c6c28e13c142c7de748fea983e76b150', 52, 1, 'authToken', '[]', 0, '2022-08-31 14:50:07', '2022-08-31 14:50:07', '2023-08-31 16:50:07'),
('088d4f25ac977e8a01d98071ce120cbef4e76e9d26d562e125bee598b3395d4726d6d3f9f6da1d10', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:27:10', '2022-08-24 09:27:10', '2023-08-24 11:27:10'),
('08e7944b4839984fda9fdea769d8f755d6794da6438860b41700e361af69897d9b157a429e1c5169', 33, 1, 'authToken', '[]', 0, '2022-08-05 05:52:54', '2022-08-05 05:52:54', '2023-08-05 11:22:54'),
('093b06fe1d67e54416bced4b1530bdacda829316edfe10170e09cfe39ed5e5387af480b097ee18f0', 8, 1, 'authToken', '[]', 0, '2022-07-12 08:59:50', '2022-07-12 08:59:50', '2023-07-12 14:29:50'),
('0a06ae0f8152e25e5d6a0a4d5343e965a2d8d35bbd7ee736de5f0b126186cc74590ced5aa24d9cf1', 28, 1, 'authToken', '[]', 0, '2022-08-05 04:49:33', '2022-08-05 04:49:33', '2023-08-05 10:19:33'),
('0f310f965cc3a238556534ba9ff06f6cebb1232bea23a5e28fb6bdb5422254572ad2f882dcb7decb', 26, 1, 'authToken', '[]', 0, '2022-08-05 04:24:47', '2022-08-05 04:24:47', '2023-08-05 09:54:47'),
('100a4a06749950567e931cc5b2e5deab4f91bbbd268ad28e4679464b4943f542ee76f270fa0636cb', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:24:04', '2022-09-08 10:24:04', '2023-09-08 12:24:04'),
('133fa68b04a74ce9b8d9e0a92d5c03c1fd84a25059ab36e717e39eced2a55e0195d15e29cfcaf58e', 3, 1, 'authToken', '[]', 0, '2022-07-14 08:42:57', '2022-07-14 08:42:57', '2023-07-14 14:12:57'),
('1343bbe4b650b72e910bc21ca7b127da641c5631c95dbbd00ae6bc7260c359a0985cfa803513b858', 10, 1, 'authToken', '[]', 0, '2022-07-12 09:05:09', '2022-07-12 09:05:09', '2023-07-12 14:35:09'),
('15b9c6fc5e51247fcdb8a940b8071f19044b7a60cc06598a82d86f4d1887656a9a37582849ee651d', 45, 1, 'authToken', '[]', 0, '2022-08-24 08:43:41', '2022-08-24 08:43:41', '2023-08-24 10:43:41'),
('169a21d5496cedd7f9ec998a15cf05b0b150f2f95a1059835054b601f76a03225bd4013efb3a44f0', 90, 1, 'authToken', '[]', 0, '2022-11-21 01:13:46', '2022-11-21 01:13:46', '2023-11-21 06:43:46'),
('1730803f87778ba430f6031a5a99a121e16d57292555c1449a18b37ddc6786cc9b9c9980c06e34e9', 48, 1, 'authToken', '[]', 0, '2022-08-22 04:20:44', '2022-08-22 04:20:44', '2023-08-22 06:20:44'),
('17555f37543744ec319c42c1e720131ff5dd2f979f15b8e2471db42b2344c2b8f048fe09995ccce9', 67, 1, 'authToken', '[]', 0, '2022-11-08 08:22:24', '2022-11-08 08:22:24', '2023-11-08 13:52:24'),
('17802fb4324d4947a74833c8103652ee2ab3f191deabdc272806d5339b40d3fa9a86cc7339187fce', 57, 1, 'authToken', '[]', 0, '2022-09-06 09:06:04', '2022-09-06 09:06:04', '2023-09-06 11:06:04'),
('17b7feb5a5f76ea8a6d111f1495301f01bb23339ea89c583e4a8ee206f6ea58a56fdae4f5129f191', 45, 1, 'authToken', '[]', 0, '2022-09-05 04:08:19', '2022-09-05 04:08:19', '2023-09-05 06:08:19'),
('1ce9eefe08daf71bb23bb819f9eebec1e5de64db64a39a9374d191b674fe9b1e2de8630cc13efc92', 57, 1, 'authToken', '[]', 0, '2022-09-08 03:50:11', '2022-09-08 03:50:11', '2023-09-08 05:50:11'),
('1cfa6da55814ba3b66f1920a1942288f577177fd3cbd9bdeee201abb4f327fa49f149e178be7f51d', 1, 1, 'authToken', '[]', 0, '2022-07-12 04:52:08', '2022-07-12 04:52:08', '2023-07-12 10:22:08'),
('1d114ec27fef22c76093caac9fc28c079ab8ee7fc2ae42764586d80f17eac8409719c0d32ef3e3b5', 58, 1, 'authToken', '[]', 0, '2022-09-06 10:18:54', '2022-09-06 10:18:54', '2023-09-06 12:18:54'),
('1d274152496d1d08c4c157bef496a6429edf1dd570d6c15c15131f6c77731e7da2b3c8875a6165e4', 28, 1, 'authToken', '[]', 0, '2022-08-05 04:54:22', '2022-08-05 04:54:22', '2023-08-05 10:24:22'),
('1d5d43a74a8625b0085bce71aee285a70c171a3858c136922734a8bfaf83fd74a93be76b717f6d5a', 90, 1, 'authToken', '[]', 0, '2022-11-14 01:27:17', '2022-11-14 01:27:17', '2023-11-14 06:57:17'),
('1fc9db6e6c03a845bb712bad1b048e497e821e301c4169460c7892810b2ddef3374c392e6cf5273e', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:51:21', '2022-09-08 10:51:21', '2023-09-08 12:51:21'),
('1fe911915f31f5f767db4c03e53fca7728599bfb26c232911e6f190b365bbe49a5bae9be953bbe6b', 45, 1, 'authToken', '[]', 0, '2022-09-02 12:09:53', '2022-09-02 12:09:53', '2023-09-02 14:09:53'),
('20de8d9c5b504f673d55363761b3869a50483824fb9490d9e73e16f0804092b9a9aaeaa739dd10e9', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:06:16', '2022-08-06 00:06:16', '2023-08-06 05:36:16'),
('218a19081af4dbccf2008c5af62daebb36cfa6cdcb42a37b06b9590eb17393913f85572f8be47504', 28, 1, 'authToken', '[]', 0, '2022-08-05 04:54:32', '2022-08-05 04:54:32', '2023-08-05 10:24:32'),
('224b71bfefaec7041810fffc0a2abb8fc9d9efc190a2c4e81e517c2940168d3b2ead1cb4797cacce', 2, 1, 'authToken', '[]', 0, '2022-07-12 07:27:13', '2022-07-12 07:27:13', '2023-07-12 12:57:13'),
('226792305c193c19eeaa4fc93f4bf444f022140fba009a2f047e716e04bd0274e35d18493e85a721', 5, 1, 'authToken', '[]', 0, '2022-07-12 08:44:39', '2022-07-12 08:44:39', '2023-07-12 14:14:39'),
('22d1e8c4cd3330013420e6308b62de900ec9432a5133af4f8fd099c4ce74c30ab1466e873631937d', 45, 1, 'authToken', '[]', 0, '2022-09-02 11:32:18', '2022-09-02 11:32:18', '2023-09-02 13:32:18'),
('241412105577d1f807ae267354c704f719409b01d6e797e9c52f9de3ad14294a667607130c7cb94c', 62, 1, 'authToken', '[]', 0, '2022-11-08 01:57:27', '2022-11-08 01:57:27', '2023-11-08 07:27:27'),
('26174bc6b3d022b890773b0a970d16c45a90d9be1420b6c1ec65c911261b80b3e7b2d965947d579d', 41, 1, 'authToken', '[]', 0, '2022-08-17 07:51:26', '2022-08-17 07:51:26', '2023-08-17 09:51:26'),
('28412db8ba20fe567c31a3b0ad554823bd3e01b5c820b8d25a46eb9f3ea2b529b9b8899f864557c8', 69, 1, 'authToken', '[]', 0, '2022-11-09 01:47:23', '2022-11-09 01:47:23', '2023-11-09 07:17:23'),
('2cd116e9c18c6c034e1237417b39a669e63ea5e5cba106a432b2b3f10d3b175d40aab4b8e65602e2', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:15:23', '2022-07-12 09:15:23', '2023-07-12 14:45:23'),
('2fc5579318a7ff6607025be4d51c42f2e15bf67e054f81de4649415adf22d6505ce438163b5b5979', 48, 1, 'authToken', '[]', 0, '2022-08-24 04:06:00', '2022-08-24 04:06:00', '2023-08-24 06:06:00'),
('321f09e1cefaf207eab8fe50c771a22b1fc1f33c1d2c94d318dadf03e451d6eff19fb380cee0a33c', 52, 1, 'authToken', '[]', 0, '2022-09-01 18:21:57', '2022-09-01 18:21:57', '2023-09-01 20:21:57'),
('329d206e65b49301208fa9639177c90044b83047ab50a4663d5c6279f94b50c87e0855bf2f03b753', 59, 1, 'authToken', '[]', 0, '2022-09-08 07:01:18', '2022-09-08 07:01:18', '2023-09-08 09:01:18'),
('339c3d8eb2ed96b2d6fd937469929ecc0df57a6627347e84d4394e0884e3bdab07ebe7acca2d1118', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:23:11', '2022-08-24 09:23:11', '2023-08-24 11:23:11'),
('343fc33abb49240b7bc8beba422ca24a93e70ec810d5171a117bf7498825849d0be7c21393d51c42', 23, 1, 'authToken', '[]', 0, '2022-08-05 04:14:56', '2022-08-05 04:14:56', '2023-08-05 09:44:56'),
('357c6abe8f60e230e674dd0db7c1f4703186a54463fec81d1e0a3e28cacdbc46a2852d608ccf7d51', 58, 1, 'authToken', '[]', 0, '2022-09-07 03:57:37', '2022-09-07 03:57:37', '2023-09-07 05:57:37'),
('39bf0be11c638432b5e9db3e6d55aff0e03d496be2ea08a71413969fd0300783f1acfaa5d8f90b3d', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:05:02', '2022-08-06 00:05:02', '2023-08-06 05:35:02'),
('39cf02ec63b291955c3634c4c3fa2d23970f0f4f95c34f55f387706d8fcd51196a9d656f2d4a3362', 86, 1, 'authToken', '[]', 0, '2022-11-12 02:11:26', '2022-11-12 02:11:26', '2023-11-12 07:41:26'),
('3c012e88b6a9d75cd63693b12dd6c7bd6f35db7ed6cc8cd9f92a4b0563060e319db48c17bec8b6ed', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:24:40', '2022-08-24 09:24:40', '2023-08-24 11:24:40'),
('3d9dd59437a70060f766a82e2d79ed94dbfb3d55d9e81eb832caa869e1ebc55a00600c73d8ae04b5', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:26:25', '2022-09-08 10:26:25', '2023-09-08 12:26:25'),
('3de09aee530febc314e67e043a3a6173a9d264846d6d5023a94200919628b94caba5bd71276736dc', 55, 1, 'authToken', '[]', 0, '2022-09-07 11:37:03', '2022-09-07 11:37:03', '2023-09-07 13:37:03'),
('3f22b050a15974ef5f76e18de4bf8b3cc5f416e72b59dcd4a1c9e6330ff3cab92ad82ca9907eba32', 43, 1, 'authToken', '[]', 0, '2022-08-18 02:56:45', '2022-08-18 02:56:45', '2023-08-18 04:56:45'),
('3fe40ffc54ae639a8c0245cc5734fe6c0ed8518032f27f93145a1b35372d88689a1be9e9896ee0e2', 11, 1, 'authToken', '[]', 0, '2022-07-12 09:05:27', '2022-07-12 09:05:27', '2023-07-12 14:35:27'),
('4047db5064c713ec6f35fbbedb752921fff74f84699885bed486b11476f82b8a46e7a2968abdf631', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:58:22', '2022-07-12 08:58:22', '2023-07-12 14:28:22'),
('444f152ae14c805a0fb7b073dfaab2f4023d28ae3a60a14858ebff6f7658297428cc4c59acaf9ae0', 38, 1, 'authToken', '[]', 0, '2022-08-06 04:37:30', '2022-08-06 04:37:30', '2023-08-06 10:07:30'),
('46162b2cc25347e5f04b0af46117b97a1d120db1ffa776a28d4e7e5d260695126f88900e16b879ea', 38, 1, 'authToken', '[]', 0, '2022-08-05 07:08:51', '2022-08-05 07:08:51', '2023-08-05 12:38:51'),
('472f865630c0fd81f68c87d5980096d948fd9aaf0ce9c70f324639fbe4448c6a2b989301599cb63d', 4, 1, 'authToken', '[]', 0, '2022-07-12 07:57:18', '2022-07-12 07:57:18', '2023-07-12 13:27:18'),
('49274e9cad5d039649ca82152a11751becb2a50f5f3c36fc928c8a816f3d2c20ce30f67c4cea96b2', 29, 1, 'authToken', '[]', 0, '2022-08-05 05:36:29', '2022-08-05 05:36:29', '2023-08-05 11:06:29'),
('4951e6bce1f0a43f4108d2120065da76c69174cd031a4b042d224bc52b7112565554d2904a70e858', 46, 1, 'authToken', '[]', 0, '2022-08-18 03:32:45', '2022-08-18 03:32:45', '2023-08-18 05:32:45'),
('4a9568a799ebb82848b6d7625b87c11c1965251e6b991cce7fabc730d2b82f18d83bd74afcf11138', 55, 1, 'authToken', '[]', 0, '2022-09-08 09:10:41', '2022-09-08 09:10:41', '2023-09-08 11:10:41'),
('4b50086639a901967afedd65d411fe79758579a84a613f61c7b60f52d271e854efc53a8312d96432', 45, 1, 'authToken', '[]', 0, '2022-09-08 03:52:49', '2022-09-08 03:52:49', '2023-09-08 05:52:49'),
('4dce262ce08757583280b9be77970a001c31071f32d8f91da5f344b046b439f8fe6cca4867c4db6f', 55, 1, 'authToken', '[]', 0, '2022-09-07 08:58:40', '2022-09-07 08:58:40', '2023-09-07 10:58:40'),
('506edba2d4b2f34b7cd6a5cf7bd02569aa3fd246148ae92323c433ca62abecd0f164db096d378fef', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:55:07', '2022-07-12 08:55:07', '2023-07-12 14:25:07'),
('51a4d14900dc50ac3611943a82da4cb3a5be63188f7b5080a31d93cfc8a3ca18bac86a04e48c6b5f', 56, 1, 'authToken', '[]', 0, '2022-09-06 07:38:42', '2022-09-06 07:38:42', '2023-09-06 09:38:42'),
('525abb985b3eb60993ff9dbc4cfc2573f8cda485dae586fa0561fb62838a4a029a1d2c0ac5620c7c', 39, 1, 'authToken', '[]', 0, '2022-08-17 07:25:54', '2022-08-17 07:25:54', '2023-08-17 09:25:54'),
('537ad1add8002caf298729382a6ee95e98c33083542e3b105714ca1efe3b1f097ba880fc1efeb6c7', 38, 1, 'authToken', '[]', 1, '2022-08-05 08:26:04', '2022-08-05 08:26:04', '2023-08-05 13:56:04'),
('547f04aa819615f827db3752c7f8157037ee840e0f88170c881ebed2e03bd50090b10c03b5c25f58', 1, 1, 'authToken', '[]', 0, '2022-07-12 04:54:58', '2022-07-12 04:54:58', '2023-07-12 10:24:58'),
('56995cc3cd03642249a01a0757b9ec846d82f20a58601fb029b43d39eea71886dcdccc3ac9c677a2', 52, 1, 'authToken', '[]', 0, '2022-08-31 14:46:15', '2022-08-31 14:46:15', '2023-08-31 16:46:15'),
('57c2d30c4798a7a17f968b429be713270e06d4f044edfca777820f65e6facfe9a2deb58dd17b024c', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:59:50', '2022-07-12 08:59:50', '2023-07-12 14:29:50'),
('5878e81c7f07724520b698fd5e22de46c20c674d06a0c853a6b69c1b3b0a2e9ade7fae9fe48ff46b', 38, 1, 'authToken', '[]', 0, '2022-08-05 23:50:08', '2022-08-05 23:50:08', '2023-08-06 05:20:08'),
('5a1f1159da9b68f02fba3fedf007ac42cb2e5ddc882b7cb35cb9c799a579ff3c4bdc4a3db0156a61', 38, 1, 'authToken', '[]', 0, '2022-08-08 06:53:13', '2022-08-08 06:53:13', '2023-08-08 12:23:13'),
('5b43fe423def385f696c47e0522f50f698cdb8c9f6c18f704b295fa5ed0e31c63ef753559de26a29', 21, 1, 'authToken', '[]', 0, '2022-08-05 04:13:37', '2022-08-05 04:13:37', '2023-08-05 09:43:37'),
('5c66faa550096275215d14b3027effb46f162fd2bcbcfffe5ab97299b4a15ae8e4bade132359e613', 38, 1, 'authToken', '[]', 1, '2022-08-06 02:10:24', '2022-08-06 02:10:24', '2023-08-06 07:40:24'),
('5cd081ac8dc34deb719f83304cd93a0f85aace98f25de08b9dfc52087cdebd4824c7763939a8f2b6', 3, 1, 'authToken', '[]', 0, '2022-07-13 00:08:30', '2022-07-13 00:08:30', '2023-07-13 05:38:30'),
('5d57aa6d554b002f60ffbec33466eb9e5d7480c3ca121a76e6e73313785abc4e2ec6aa186614f59d', 45, 1, 'authToken', '[]', 0, '2022-08-18 03:24:30', '2022-08-18 03:24:30', '2023-08-18 05:24:30'),
('5ea217f6fa365d428956374572313c73a5d57cd8dce41ea338b2c3d5e63848738c35dc341c0c63a9', 48, 1, 'authToken', '[]', 0, '2022-08-22 05:31:11', '2022-08-22 05:31:11', '2023-08-22 07:31:11'),
('5f985dbdc323cf280219b3ae27a16a0649c2a0f95aad38f29667d6f9f17b4e7795bb93c69c47848f', 45, 1, 'authToken', '[]', 0, '2022-09-06 07:36:54', '2022-09-06 07:36:54', '2023-09-06 09:36:54'),
('60333118fc75772377ff27f04eb9c6eaf654e0989b8af3c209cd143b2542bf7e07c71811acf2a422', 50, 1, 'authToken', '[]', 0, '2022-08-30 06:08:06', '2022-08-30 06:08:06', '2023-08-30 08:08:06'),
('620d5740f27e762d9e8053a37e0a3163c1e8c1a5de6b220ac06f46d60926e84bf7101c24d37e4638', 50, 1, 'authToken', '[]', 0, '2022-08-30 05:39:25', '2022-08-30 05:39:25', '2023-08-30 07:39:25'),
('62f756327c3b2d3c5b537223ecdd20d782c87a0e987b19e284edd918d118a5605ae71d35b1bc4188', 45, 1, 'authToken', '[]', 0, '2022-09-05 11:27:25', '2022-09-05 11:27:25', '2023-09-05 13:27:25'),
('6593e0f8f14cff7a58dd5bb48f3ef18d3cacaafd29e5097bade0b177cb074ce9e7afc5c6e46b5a82', 34, 1, 'authToken', '[]', 0, '2022-08-05 05:54:46', '2022-08-05 05:54:46', '2023-08-05 11:24:46'),
('65ad15c836a76c554e2180bd0a168390d9b801786c5132d3d12be9a2cc413eea8f0dfee967c39afb', 19, 1, 'authToken', '[]', 0, '2022-08-05 03:46:23', '2022-08-05 03:46:23', '2023-08-05 09:16:23'),
('675029922668af48590d0d3d6f00f2407c7610f60da7ccbb82b146b8f7ec5d824da57be231d90c3a', 90, 1, 'authToken', '[]', 0, '2022-11-21 01:11:26', '2022-11-21 01:11:26', '2023-11-21 06:41:26'),
('68499ee3cec69779e028560414f79e3cae1d034f347ccaffd6f523ad2cfcdee4d682ce93dcb0f667', 18, 1, 'authToken', '[]', 0, '2022-08-05 03:32:45', '2022-08-05 03:32:45', '2023-08-05 09:02:45'),
('6a4d82d3c56959d12c3de37817eeca493d29a16cadef91ef61ef577ea0c58d69b5d39c6cd3cfe7a5', 55, 1, 'authToken', '[]', 0, '2022-09-05 07:41:04', '2022-09-05 07:41:04', '2023-09-05 09:41:04'),
('6b89bb494eac3dafdcc3133f97c7e12aa057f0913c9c3d76cc26d5212416250bc1a1cbb17df5ba5d', 16, 1, 'authToken', '[]', 0, '2022-07-13 00:08:21', '2022-07-13 00:08:21', '2023-07-13 05:38:21'),
('6d2ef2dd9efad6627976cdbdffe4788df70b5a1453fd8a84f978baa3f6640d82745aed93edc81753', 57, 1, 'authToken', '[]', 0, '2022-09-07 09:49:56', '2022-09-07 09:49:56', '2023-09-07 11:49:56'),
('6d4e5d7f6f344f8c291620944e800e1227ee53a10787e1b5e3f13c506eec1e7d99afc18f75a984b0', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:14:23', '2022-08-06 00:14:23', '2023-08-06 05:44:23'),
('6e87661350f5bb2c11c64e10d9cbcaac9161dc208ef80914fb49b4ed997cbc2b9df3105e3f91cd29', 38, 1, 'authToken', '[]', 1, '2022-08-06 02:01:27', '2022-08-06 02:01:27', '2023-08-06 07:31:27'),
('6ea50de94c4b07cb6acd764205eae5043c2920f5ac29aa73b90fcb71d428195b416cdaf959a61755', 45, 1, 'authToken', '[]', 0, '2022-09-02 11:26:28', '2022-09-02 11:26:28', '2023-09-02 13:26:28'),
('6f522cea89ed25f158c219c4fb7b8b17dd360f0e9ba5a98496f956b2d703ca8238c39b14e3cd34e2', 56, 1, 'authToken', '[]', 0, '2022-09-06 07:50:59', '2022-09-06 07:50:59', '2023-09-06 09:50:59'),
('707a6f1b714c38f3a2f8c332a415a0c0b1042ea7958cb73c9d334688c6659ae53eb450b470201a16', 50, 1, 'authToken', '[]', 0, '2022-08-30 06:07:40', '2022-08-30 06:07:40', '2023-08-30 08:07:40'),
('70a2f934a69a41d3a6a9ddeae309edad67e3e69b1d48d919b0334006320c2b110b3cc69072ac4ab1', 60, 1, 'authToken', '[]', 0, '2022-11-08 01:22:50', '2022-11-08 01:22:50', '2023-11-08 06:52:50'),
('70ca98f046352e3e061a9c4f121e1307751596ffc96b20ee5c32f9e46f696c8db91f7ccc6945541c', 47, 1, 'authToken', '[]', 0, '2022-08-18 07:21:05', '2022-08-18 07:21:05', '2023-08-18 09:21:05'),
('739b188b7955e664a0e62465bd8ca3f68d331750fcc31ae8ebc54d94c17ed3718e077ffcbff227fb', 28, 1, 'authToken', '[]', 0, '2022-08-05 04:53:49', '2022-08-05 04:53:49', '2023-08-05 10:23:49'),
('74036849333d5da334b75b062a1419924e6bb3834e92ac9088667d5e6075d082bcfb29837f018130', 15, 1, 'authToken', '[]', 0, '2022-07-12 09:15:23', '2022-07-12 09:15:23', '2023-07-12 14:45:23'),
('74c6cba09b43241ce6229ced23d434cf1b0fd850c4de2d654dd27e716d683cd2bb0b31fbaaf4a5e1', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:05:27', '2022-07-12 09:05:27', '2023-07-12 14:35:27'),
('7525c7dd143e4526fa9ae6b84b3cce7002be582875ef6b12cf5c4686cfdda9f22d0c9fb773816696', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:47:04', '2022-07-12 08:47:04', '2023-07-12 14:17:04'),
('76119afdd8fcc148fa2d0ec9ac9f682a136d248ce057a34719786da444a6a340b580f2d4f47814c9', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:02:45', '2022-09-08 10:02:45', '2023-09-08 12:02:45'),
('7d4d42e6d2a8405d0b3da080f334b4315726ee070a6b8119616abf5df6a3e0b59a7b8c3ce13b1fd9', 51, 1, 'authToken', '[]', 0, '2022-08-26 15:42:42', '2022-08-26 15:42:42', '2023-08-26 17:42:42'),
('7dd8cfb372fa2b75fe650fd83f902400f3053c8848cbc433da6c642aa2fb06849591c868327c9d21', 57, 1, 'authToken', '[]', 0, '2022-09-06 09:11:19', '2022-09-06 09:11:19', '2023-09-06 11:11:19'),
('806b8c4cb2ae96daa526a2e5f7f4f980cc8dacbae2dcdd8ea7dcdae60ad48d6488aac62ee7e8008d', 41, 1, 'authToken', '[]', 0, '2022-08-17 07:52:25', '2022-08-17 07:52:25', '2023-08-17 09:52:25'),
('81437ae9014de5d318732eb345e840e97cd5a186a8bcd156262d91a4ab5a1db93f3df658279d60ce', 45, 1, 'authToken', '[]', 0, '2022-08-26 06:22:08', '2022-08-26 06:22:08', '2023-08-26 08:22:08'),
('81538beea89425f94d05de9799af0d86690d21559f15d8658c31978a8aa6d1f0e0908a6fbd7ec6f9', 48, 1, 'authToken', '[]', 0, '2022-08-22 05:23:13', '2022-08-22 05:23:13', '2023-08-22 07:23:13'),
('82a2f0d2af96b53dcb0ef391375c9f4f2fa03e57a1849f7d24bc582dd53e9073891a4e1c5d6af71e', 45, 1, 'authToken', '[]', 0, '2022-08-26 08:12:22', '2022-08-26 08:12:22', '2023-08-26 10:12:22'),
('82c1615cae9019d12108efb1f51cbbfed3609d0d72b21d5f87356b5a673d71c669975312f3d15db5', 25, 1, 'authToken', '[]', 0, '2022-08-05 04:23:29', '2022-08-05 04:23:29', '2023-08-05 09:53:29'),
('82d2a676df9fd2ca5240664f9036708b54c98497145768dcbb4e64764d6f5c7585003725cd95bfaa', 57, 1, 'authToken', '[]', 0, '2022-09-08 03:51:28', '2022-09-08 03:51:28', '2023-09-08 05:51:28'),
('84a31707b5199c25d481792e2f02d8d0c59430b02fe22d68d35a6bf595afda0b6507f998ed954c77', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:54:27', '2022-07-12 08:54:27', '2023-07-12 14:24:27'),
('8590ecb0e7b69799701870355da95527acba0511c5f56dd6f52f763875d7ade1d310a661b60f35b9', 52, 1, 'authToken', '[]', 0, '2022-08-31 14:46:42', '2022-08-31 14:46:42', '2023-08-31 16:46:42'),
('875f3b4e191af94c5e13dd4d49d8e6b8e85730bee1790e81d868cd25dda78546f5915deaee38f704', 45, 1, 'authToken', '[]', 0, '2022-09-08 04:48:07', '2022-09-08 04:48:07', '2023-09-08 06:48:07'),
('890720e4e3e426ecbc9f20840a8980f3e7f5864252ef5903850bd31125f8617a7b7420151520179f', 20, 1, 'authToken', '[]', 0, '2022-08-05 03:48:51', '2022-08-05 03:48:51', '2023-08-05 09:18:51'),
('8b23e5055da0da0ef5ce5ae2ccdb937ec42ecabdcf9bf49de06bb89460c403791e6f287513ab3fbc', 9, 1, 'authToken', '[]', 0, '2022-07-12 09:02:18', '2022-07-12 09:02:18', '2023-07-12 14:32:18'),
('8c14dc5f23a05da6ab6a298ba617ad46f0090c54904dbf7743f819628685d0651f1b4f0fadd2ed4a', 67, 1, 'authToken', '[]', 1, '2022-11-08 08:39:40', '2022-11-08 08:39:40', '2023-11-08 14:09:40'),
('902fab423ef1842b347ce0ddfa1d65013efb4fceda0cf6dcc3966ede8310bbdc11c63a184806dc8f', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:06:10', '2022-08-06 00:06:10', '2023-08-06 05:36:10'),
('915f4ce4c07046ad90f0c398603a9c4e83b1c7b7edab0e0fc71bef20b5ad8b8bc0bb2f3ef4e743ba', 55, 1, 'authToken', '[]', 0, '2022-09-06 07:36:18', '2022-09-06 07:36:18', '2023-09-06 09:36:18'),
('968aa4a7c72a4e23ead869c6210c3d03a750c3ecc1088219817112219d2a9db2cec44518f90a6640', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:15:09', '2022-07-12 09:15:09', '2023-07-12 14:45:09'),
('9989104a7ac3d22be3b348bf5ad0677d5fe547097e8bc3559aea695a5c5872224ceffaffa63eaa1e', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:28:20', '2022-08-24 09:28:20', '2023-08-24 11:28:20'),
('99bd0399a8ae80acb3240c90f3c206bf85eb2dad58ce11b2194cbf35b724ac97cfabca4cc973a9a9', 28, 1, 'authToken', '[]', 0, '2022-08-05 04:52:39', '2022-08-05 04:52:39', '2023-08-05 10:22:39'),
('9a3c4754d98d89e87094a1cbd67878d4b41b1c35b81f80ab05241a185aff6d71a619dbbdcc3b6d8a', 38, 1, 'authToken', '[]', 0, '2022-08-05 23:30:05', '2022-08-05 23:30:05', '2023-08-06 05:00:05'),
('9c33cfbeef29b29db3de1062c3948aa95ffbc77da6c91c1651076764c9bb2c1c454f9522199cba24', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:06:18', '2022-08-06 00:06:18', '2023-08-06 05:36:18'),
('9d239e887df8a38e4a88cc371bfcba19490f6cd8f20cbcf6a94fafa8518cd9aa1f37e67658591b5f', 48, 1, 'authToken', '[]', 0, '2022-08-22 04:21:06', '2022-08-22 04:21:06', '2023-08-22 06:21:06'),
('9e4e4a13a5ccd583491123f23c6481fc9daaccad8bd1a0a7fb9e85e8237a7f0be3d50aef4e44e953', 56, 1, 'authToken', '[]', 0, '2022-09-06 07:50:34', '2022-09-06 07:50:34', '2023-09-06 09:50:34'),
('9f1a05626e578bc0379566eb4ebb82bfb7553d53e8be1bb28b8c63649f878d8444609381a827d5f2', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:25:29', '2022-09-08 10:25:29', '2023-09-08 12:25:29'),
('9f469a7b22fcd6bc917a4502ce438629bd95604d30b527abd3f46bee2a74731cd20aa5a307ac10f4', 42, 1, 'authToken', '[]', 0, '2022-08-18 02:50:29', '2022-08-18 02:50:29', '2023-08-18 04:50:29'),
('9f8d303d84da5cf21a54a17f064ccb19e1c1bf00e93660b021cfd81a89261050865779b7a0e4521c', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:05:09', '2022-07-12 09:05:09', '2023-07-12 14:35:09'),
('a0e5fbf1259e94bccaa693c2b08a95c28ec11f7bca03f293820726f0a44ff7319f8f8d8b07238034', 53, 1, 'authToken', '[]', 0, '2022-09-05 04:11:24', '2022-09-05 04:11:24', '2023-09-05 06:11:24'),
('a1c7803ee880c17b6f91333f185d3f53086c4f6390a42a2bd9405b34e1e2e1a81e7ad4e9c06ed960', 67, 1, 'authToken', '[]', 0, '2022-11-08 08:19:59', '2022-11-08 08:19:59', '2023-11-08 13:49:59'),
('a401a97c09a1408b1b2ce25233cc37e7b87087ebf2487b4998896a9e5967e7ac14cad61ca0eb1b86', 55, 1, 'authToken', '[]', 0, '2022-09-05 07:40:45', '2022-09-05 07:40:45', '2023-09-05 09:40:45'),
('a4d24e2d29eba2ba5f123d5b178cd3c4706fff7039f07c5cb0f98e3054299924b1e106eea84bca20', 50, 1, 'authToken', '[]', 0, '2022-08-30 07:57:11', '2022-08-30 07:57:11', '2023-08-30 09:57:11'),
('a5397a5e7c83935919c2840bf493e262b5b97e4d704062c5b46d67ec940a864054bf2b3b89c25eb8', 44, 1, 'authToken', '[]', 0, '2022-08-18 03:00:47', '2022-08-18 03:00:47', '2023-08-18 05:00:47'),
('a5b9afd4971562fb3336a1e51aaf438fb12986c90be8e0bfe0f199b6d5e0874d43f1a20f0a058f65', 67, 1, 'authToken', '[]', 0, '2022-11-08 08:15:46', '2022-11-08 08:15:46', '2023-11-08 13:45:46'),
('a625d985196f89e056ee098f959fa095ed25ff87d2ab66721bf93135cc9dd5d34dc53c09051d1038', 52, 1, 'authToken', '[]', 0, '2022-08-31 14:49:04', '2022-08-31 14:49:04', '2023-08-31 16:49:04'),
('a7f713d4a8167cac171b84157dc60e92e0ec534b4531aed7a9f9056677fa861b5c0bad62c550f8bc', 45, 1, 'authToken', '[]', 0, '2022-09-06 07:38:00', '2022-09-06 07:38:00', '2023-09-06 09:38:00'),
('a968bf35810f948b402201586247784af36b0cf7b4f11ad591e75a8d4437511e430ffcdb565bdf78', 57, 1, 'authToken', '[]', 0, '2022-09-06 09:05:47', '2022-09-06 09:05:47', '2023-09-06 11:05:47'),
('aa45855e0539b2f338bb5fd196d4aeb4d5579cc1022e7d1632a8295b3abcc17e524951345519f80b', 56, 1, 'authToken', '[]', 0, '2022-09-06 07:38:09', '2022-09-06 07:38:09', '2023-09-06 09:38:09'),
('aa9f2c97a96e48c26d3ee1673c2ef05938d2152991b01ca7184be16a2173c04780e40ab7f79053a5', 56, 1, 'authToken', '[]', 0, '2022-09-06 07:51:45', '2022-09-06 07:51:45', '2023-09-06 09:51:45'),
('ab0441592e79f3a2e0fd75b486bdde8ab72bab278b3e8da8784fd5b784b1383bb118d9024d865c35', 38, 1, 'authToken', '[]', 1, '2022-08-06 01:31:56', '2022-08-06 01:31:56', '2023-08-06 07:01:56'),
('ab5d924960b56822318da47fc4f60e4ea56e23bbd23f28e709c852ccff60ecc4d0852e9c3c372b9c', 49, 1, 'authToken', '[]', 0, '2022-08-24 04:10:20', '2022-08-24 04:10:20', '2023-08-24 06:10:20'),
('abdf11d654ce9de24083c3cac6cd316d59477376c05f8fc1c8be8657ea67a55c41f88e869af98685', 3, 1, 'authToken', '[]', 0, '2022-07-12 07:55:59', '2022-07-12 07:55:59', '2023-07-12 13:25:59'),
('aca40a0c89ddbc85c381dcbd6863c0250c9dc743186c0fb149bce8b1af36d4fc868019b5ccfe624f', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:47:12', '2022-09-08 10:47:12', '2023-09-08 12:47:12'),
('acb428d476ef2ba03e6749a81a22b169239c889571d85fcc4e8e51f1eea9e6c2ccf83a0d8d742161', 35, 1, 'authToken', '[]', 0, '2022-08-05 05:55:55', '2022-08-05 05:55:55', '2023-08-05 11:25:55'),
('add007fc2928e786e6ef9454b1250153f6e7e42203d4e9df02007bed28e2e3a2edb8dbf5416b9059', 24, 1, 'authToken', '[]', 0, '2022-08-05 04:23:19', '2022-08-05 04:23:19', '2023-08-05 09:53:19'),
('adf94bb855ee59c24e913b9268c3d150b7031d089cb00ac683aa90541670bc9ac4c670b1123d2e9c', 28, 1, 'authToken', '[]', 0, '2022-08-05 04:53:35', '2022-08-05 04:53:35', '2023-08-05 10:23:35'),
('b0c471e8e342ab35fa4214b1b8b26bfb4ec20d5c2836febbe6512b044746140e4cafe77a59800ff4', 58, 1, 'authToken', '[]', 0, '2022-09-06 10:18:38', '2022-09-06 10:18:38', '2023-09-06 12:18:38'),
('b24fa5aaf98c7457ab7e14cbea5f792d665d060e1437b1c6f4812d9b20886ecd740580dfa9d4bbaf', 60, 1, 'authToken', '[]', 0, '2022-11-08 01:14:24', '2022-11-08 01:14:24', '2023-11-08 06:44:24'),
('b282ca566f29a92b79cbf6b29a2fbe1f487df699c120a0dbdfa4ce5758a01b040c41baa920c54efc', 40, 1, 'authToken', '[]', 0, '2022-08-17 07:47:25', '2022-08-17 07:47:25', '2023-08-17 09:47:25'),
('b2a917a173997a23560914861a374f7761a2f2bb464acf97a9c9995e9cf5958230413223541ae845', 54, 1, 'authToken', '[]', 0, '2022-09-05 04:12:58', '2022-09-05 04:12:58', '2023-09-05 06:12:58'),
('b38233bf09e5ab23dadf9eb3c32040f7370f19168f758d572ac11a78345c1c12ae0ea4d3a1303a61', 90, 1, 'authToken', '[]', 0, '2022-11-21 01:06:20', '2022-11-21 01:06:20', '2023-11-21 06:36:20'),
('b4bc82ed39708a5a544b8feb6df21c759ce2f7804bab4b9eabe05f070bd316e1aff6309bfd22ec42', 86, 1, 'authToken', '[]', 0, '2022-11-10 07:09:10', '2022-11-10 07:09:10', '2023-11-10 12:39:10'),
('b525e76a9efd3477bd32696fbe7c6f72b2b534c55fc15b45f3237ff1110e910ae64bc762bd6fd37d', 50, 1, 'authToken', '[]', 0, '2022-08-30 06:57:40', '2022-08-30 06:57:40', '2023-08-30 08:57:40'),
('b6321e1ed2b7b19888ecc9b60562b888253e3b35d007cc28fc895120c7455565c3c76bd9cc395af6', 38, 1, 'authToken', '[]', 0, '2022-08-05 23:49:26', '2022-08-05 23:49:26', '2023-08-06 05:19:26'),
('b9dde09332c78b50b13ad026146b62ceb3611442e07803cb9faa727b23686187bd312899de49c8f8', 22, 1, 'authToken', '[]', 0, '2022-08-05 04:13:50', '2022-08-05 04:13:50', '2023-08-05 09:43:50'),
('ba149df38d9df8a1a6a7b130cc5e4d6af2695ce1a10b47b16a3b928c3d200f99ccd2d7230e083131', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:46:47', '2022-09-08 10:46:47', '2023-09-08 12:46:47'),
('bfeabac1fbaa81426ab3410f965df2fbc9dc176a94cd99a304dccdba8b5a27204cc6410931e434d4', 3, 1, 'authToken', '[]', 0, '2022-07-12 07:57:18', '2022-07-12 07:57:18', '2023-07-12 13:27:18'),
('c1428d491d59e111e46b224ec45ae1b493a355550088e3ce8dd4b145e2c73867973910aef037c467', 48, 1, 'authToken', '[]', 0, '2022-08-24 03:51:45', '2022-08-24 03:51:45', '2023-08-24 05:51:45'),
('c1c8f1d79efbf0d101199c1ddc9d78c47e9751e5494a344c2ffe2f41b1a0ab712819b31885e6433e', 3, 1, 'authToken', '[]', 0, '2022-07-13 00:08:21', '2022-07-13 00:08:21', '2023-07-13 05:38:21'),
('c2fe578087e47d26be0f284bcc21d829599b41ac80f3e4edb2048920d82d7ef0de925719749b286f', 6, 1, 'authToken', '[]', 0, '2022-07-12 08:58:06', '2022-07-12 08:58:06', '2023-07-12 14:28:06'),
('c52f9605fa9277b854839d6fadd438c61d0eca247ee23df0010bcc24d34021dfd0cbc969db9ef6d5', 37, 1, 'authToken', '[]', 0, '2022-08-05 05:59:30', '2022-08-05 05:59:30', '2023-08-05 11:29:30'),
('c64add4293606731b1630c5ccb44dc7be0a2c1ff889f257fd8bdb7c63693c196eeaa8ecc1c2f4d2a', 1, 1, 'authToken', '[]', 0, '2022-07-12 04:52:38', '2022-07-12 04:52:38', '2023-07-12 10:22:38'),
('c9d2104ebda704fecfb7bf6d95b49009ef88613d926b9c66fff23ab4eb5484b8c3f9700a95dd8a60', 32, 1, 'authToken', '[]', 0, '2022-08-05 05:44:21', '2022-08-05 05:44:21', '2023-08-05 11:14:21'),
('ca70d6d1d969b115ad321771d18c01c3a546ad13d9f341f3fb0235d14346db59636e7a708b3a9559', 7, 1, 'authToken', '[]', 0, '2022-07-12 08:58:22', '2022-07-12 08:58:22', '2023-07-12 14:28:22'),
('cd30d8a0a9530ab1266ff894c0e2280531d38f0ca0cbce7064753d34da6b20437376468ae61f2ca5', 38, 1, 'authToken', '[]', 0, '2022-08-05 06:00:42', '2022-08-05 06:00:42', '2023-08-05 11:30:42'),
('d342df523260f03407665039db8a75066e15f6dea4b9307721543ba1e58a43fd09f604cc98cd7e55', 55, 1, 'authToken', '[]', 0, '2022-09-08 09:14:50', '2022-09-08 09:14:50', '2023-09-08 11:14:50'),
('d4a27333890c605b534f9649dade36b8e46d976132480f15bf56c37efaff84b28920a58fcb2f29b8', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:21:02', '2022-09-08 10:21:02', '2023-09-08 12:21:02'),
('d5589f9163e5a2253fe422da43a3d8f581a18c38ee54200953a071a0837a3c5f8c3967ba1ae71d41', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:44:39', '2022-07-12 08:44:39', '2023-07-12 14:14:39'),
('d5d1de69417b2314a461109ea2ca01b464384a713b34de68309837e397e98105714cb7d028e33407', 45, 1, 'authToken', '[]', 0, '2022-09-06 07:43:17', '2022-09-06 07:43:17', '2023-09-06 09:43:17'),
('d6be57dd76a51108170b6d53b1c1f72d53541309864b89c250c6f54a29ac93d5c994cbd24f16a213', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:14:06', '2022-07-12 09:14:06', '2023-07-12 14:44:06'),
('d701b0f9f10cde9f9774d2bcfb8891b35a80a683f94465fec14be6487381e8bd6742ba146560399b', 27, 1, 'authToken', '[]', 0, '2022-08-05 04:24:54', '2022-08-05 04:24:54', '2023-08-05 09:54:54'),
('d81fc777298605d8ed31ed5f67759d43674c7eb02871a6593cfd82d9efd956607493a5710b6669ab', 51, 1, 'authToken', '[]', 0, '2022-08-26 15:42:09', '2022-08-26 15:42:09', '2023-08-26 17:42:09'),
('d96cd56f1477cad040eb55552c90270774b317241d33e56bd9e76300e3263e24c8872adf29cdd57f', 55, 1, 'authToken', '[]', 0, '2022-09-08 10:33:43', '2022-09-08 10:33:43', '2023-09-08 12:33:43'),
('d9f54ea080f9481960d6fb1c38da2f3b3505a91500b89108aad5a5efcf3ca831d8f467a3f5ee9061', 50, 1, 'authToken', '[]', 0, '2022-08-30 05:51:30', '2022-08-30 05:51:30', '2023-08-30 07:51:30'),
('da40cd6ad6a7317d1abfa468fd786b7cc59181b1a07b4027648fd3ccf87b80f7ae435e1a4b5728a5', 45, 1, 'authToken', '[]', 0, '2022-09-06 09:37:16', '2022-09-06 09:37:16', '2023-09-06 11:37:16'),
('db578109d52681dc41aa96a2cd9633b91bf9688e0785cb35b13da3852f991ce1d04ebff99641d09c', 45, 1, 'authToken', '[]', 0, '2022-09-05 04:47:29', '2022-09-05 04:47:29', '2023-09-05 06:47:29'),
('dd718b4f8101e997cfbc695eb83447fc8fe41c3b8320d08d5f6a1130c2a067e814d23748b6e15c74', 45, 1, 'authToken', '[]', 0, '2022-09-01 11:44:24', '2022-09-01 11:44:24', '2023-09-01 13:44:24'),
('dec8646565ec41b3879fbdb73117a4cbb1926cf41e386740ba88feeed1cd2c2ddfeef6bfe5702842', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:24:21', '2022-08-24 09:24:21', '2023-08-24 11:24:21'),
('df8f52b6af5b708a9e0925565521ae141f8e38c967d397f5b1b0df38121bd06fc0dfe7cd1cc4fedd', 12, 1, 'authToken', '[]', 0, '2022-07-12 09:09:27', '2022-07-12 09:09:27', '2023-07-12 14:39:27'),
('e0c6a0fa181390df1bc6443a1c682f7de4767cef5fbd4420c72f5c751a90cf4d994e84f29e2f782e', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:05:53', '2022-08-06 00:05:53', '2023-08-06 05:35:53'),
('e3014a1984b14d9c901bd115cb94bd74cfbbb5b4e46a8386c6e633e2c61e10dd26d0e55fc64c5600', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:23:32', '2022-08-24 09:23:32', '2023-08-24 11:23:32'),
('e8ac9ce47e35b953c9d4da0bc61457b1eb9bc04aa9fb4a69d110000b61ec8c74649115c618ec6467', 55, 1, 'authToken', '[]', 0, '2022-09-08 06:00:23', '2022-09-08 06:00:23', '2023-09-08 08:00:23'),
('e9716fd5bc15acc10d64f9d39d50c8cdac2ed26d10e2d06e83676751955e8c57d4983596d08a49bb', 50, 1, 'authToken', '[]', 0, '2022-08-24 09:27:34', '2022-08-24 09:27:34', '2023-08-24 11:27:34'),
('e9b9e4088498fb552939cb468b40bb15bb47e355e1d9b972045076eb42108a2caad8f04359fdcbaf', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:09:27', '2022-07-12 09:09:27', '2023-07-12 14:39:27'),
('ed2d80b0192dbcb37339eee760045c339c7bb621a1183cd8f7ffbf12895663b3ebb3fb53a6d6ad6d', 14, 1, 'authToken', '[]', 0, '2022-07-12 09:15:09', '2022-07-12 09:15:09', '2023-07-12 14:45:09'),
('ee912012db07773671d7fe19affd65048a1aa3148961f4d0a514596c99875fe622213cf217f70eb2', 38, 1, 'authToken', '[]', 0, '2022-08-06 00:07:51', '2022-08-06 00:07:51', '2023-08-06 05:37:51'),
('efd608614b4690ea07a1d6cc3c1b9b9c30e1cfe60d088c01d83be0911e4e0c749cfbd7d7d05fd015', 69, 1, 'authToken', '[]', 0, '2022-11-09 01:48:32', '2022-11-09 01:48:32', '2023-11-09 07:18:32'),
('f1055f57d6af52523a1618ebb244e11163680c7ad955d2cb1362f7f8cedcacf0901877304d1c5450', 38, 1, 'authToken', '[]', 0, '2022-08-05 23:51:09', '2022-08-05 23:51:09', '2023-08-06 05:21:09'),
('f14f08467d57fe71287d98e14510fb6b31c72eebdab141ee630ef8cfd6e6304633f29be52d150ce2', 59, 1, 'authToken', '[]', 0, '2022-09-08 07:01:54', '2022-09-08 07:01:54', '2023-09-08 09:01:54'),
('f23beaa50b0abbab9b719d215dd7dd6dea0d00869b7f7d1e7bd050ddabd94e01574e52e434612f8d', 3, 1, 'authToken', '[]', 0, '2022-07-12 07:32:50', '2022-07-12 07:32:50', '2023-07-12 13:02:50'),
('f2ef3d205ec063b5ba41387cc1e4325c1c812dfb771992c57896f924edf43a445a3afcb55d14fde0', 38, 1, 'authToken', '[]', 0, '2022-08-06 03:51:39', '2022-08-06 03:51:39', '2023-08-06 09:21:39'),
('f446bc060c0410e582287ed16b5112d15832a7be56eebd21131fee5d46c41c7c3108d2e1af3ae48a', 17, 1, 'authToken', '[]', 0, '2022-07-13 00:08:30', '2022-07-13 00:08:30', '2023-07-13 05:38:30'),
('f73a3037dbb23d6565df7d459facf185f079c721b43a2101631d27caf46d4ac28918838bb3748736', 3, 1, 'authToken', '[]', 0, '2022-07-12 08:58:06', '2022-07-12 08:58:06', '2023-07-12 14:28:06'),
('f87a9dc2637f2a415f370cb85dca0ef609379cc2bd4e5d24c005aa45c270cb76090e9c82473d5e1b', 49, 1, 'authToken', '[]', 0, '2022-08-24 04:10:02', '2022-08-24 04:10:02', '2023-08-24 06:10:02'),
('f8a1ce9ab30150a6b7a5fe3e7cc6f0c029f44814ba063942bb0ff6ce4e44ad35db1993985af08294', 13, 1, 'authToken', '[]', 0, '2022-07-12 09:14:06', '2022-07-12 09:14:06', '2023-07-12 14:44:06'),
('f977694ce17824a21ff0ab5a4cc9c6768693928f11e8cc3096c074129efdb5dca6de3f1e13d1b246', 45, 1, 'authToken', '[]', 0, '2022-09-05 04:39:40', '2022-09-05 04:39:40', '2023-09-05 06:39:40'),
('fa4586adfed244023238ca349836a437eddfe478048b5126135b920ea42e82d962885dd704e48750', 3, 1, 'authToken', '[]', 0, '2022-07-12 09:02:18', '2022-07-12 09:02:18', '2023-07-12 14:32:18'),
('fc65e682a6fdeee7351bc4cecc08353e47029f9e62551931107b5fe707137c204bc351645bf2f4da', 55, 1, 'authToken', '[]', 0, '2022-09-07 10:14:53', '2022-09-07 10:14:53', '2023-09-07 12:14:53'),
('fd14003c2545e596278f7457c3d0affb34afd53cc0ad561208ca734acbee69ef438f4000943dc63a', 36, 1, 'authToken', '[]', 0, '2022-08-05 05:57:48', '2022-08-05 05:57:48', '2023-08-05 11:27:48'),
('ff168931b9596593bc0dacc80f6df9fa4019de2aa3970f25bd4e87ab4ca73af2cdaef4f338999882', 54, 1, 'authToken', '[]', 0, '2022-09-05 04:13:11', '2022-09-05 04:13:11', '2023-09-05 06:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'pCYeV4j30fhLdfF7u9IMx0qZ4z3t51i065PoCUMi', NULL, 'http://localhost', 1, 0, 0, '2022-07-12 02:16:15', '2022-07-12 02:16:15'),
(2, NULL, 'Laravel Password Grant Client', 'gC8dOJdlGaoG0skk5kNUHBItkil7Y6heoH7I4lYB', 'users', 'http://localhost', 0, 1, 0, '2022-07-12 02:16:15', '2022-07-12 02:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2022-07-12 02:16:15', '2022-07-12 02:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Test', '55565', '2022-11-23 05:29:28', '2022-11-28 04:26:09'),
(2, 'Product-2', '55', '2022-11-23 05:29:28', '2022-11-23 06:43:36'),
(6, 'ARC', 'ARC', '2022-12-07 02:02:44', '2022-12-07 02:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `point` int(11) DEFAULT NULL,
  `bag` int(11) DEFAULT NULL,
  `lifting_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `point`, `bag`, `lifting_id`, `user_id`, `date`, `is_verified`, `description`, `created_at`, `updated_at`) VALUES
(1, 5, 50, 18, 88, NULL, 0, NULL, '2022-12-07 06:05:28', '2022-12-07 06:14:27'),
(5, 22, NULL, NULL, NULL, NULL, 1, 'Point add', '2022-12-16 01:27:00', '2022-12-16 01:27:00'),
(6, 22, NULL, NULL, 130, NULL, 1, 'Point add', '2022-12-16 02:24:45', '2022-12-16 02:24:45'),
(7, 5, NULL, NULL, 130, NULL, 1, 'Point add', '2022-12-16 02:38:16', '2022-12-16 02:38:16'),
(8, 55, NULL, NULL, 130, NULL, 1, 'Point add', '2022-12-16 02:39:05', '2022-12-16 02:39:05'),
(9, 50, NULL, NULL, 90, NULL, 1, 'Point add', '2022-12-16 02:52:23', '2022-12-16 02:52:23'),
(10, NULL, 52, 22, 114, NULL, 0, NULL, '2022-12-16 07:07:27', '2022-12-16 07:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `reward_points`
--

CREATE TABLE `reward_points` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `bag` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reward_points`
--

INSERT INTO `reward_points` (`id`, `product_id`, `point`, `bag`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 1, NULL, '2022-12-07 02:02:44', '2022-12-07 02:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Technical Engineer', '2022-11-28 05:36:23', '2022-11-28 05:36:23'),
(2, 'Mason', '2022-11-28 05:36:23', '2022-11-28 05:36:23'),
(3, 'Dealer', '2022-11-28 05:36:23', '2022-11-28 05:36:23'),
(4, 'RSSD', '2022-11-28 05:36:23', '2022-11-28 05:36:23'),
(5, 'Admin', '2022-11-28 05:36:23', '2022-11-28 05:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'There are many variations of passages of Lorem Ipsum a', '2022-11-27 05:33:16', '2022-11-27 05:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_name`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'Mason', '2022-12-06 03:44:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fb_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `fb_link`, `twitter_link`, `web_link`, `created_at`, `updated_at`) VALUES
(1, 'http://localhost/phpmyadmin/index.php?route=/sql', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=mason&table=social_links', 'http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=mason&table=social_links', '2022-12-06 02:27:05', '2022-12-05 21:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_name`, `created_at`, `updated_at`) VALUES
(1, 'Andhra Pradesh', NULL, NULL),
(2, 'Arunachal Pradesh', NULL, NULL),
(3, 'Assam', NULL, NULL),
(4, 'Bihar ', NULL, NULL),
(5, 'Chhattisgarh', NULL, NULL),
(6, 'Goa', NULL, NULL),
(7, 'Gujarat', NULL, NULL),
(8, 'Haryana', NULL, NULL),
(9, 'Himachal Pradesh', NULL, NULL),
(10, 'Jharkhand', NULL, NULL),
(11, 'Karnataka', NULL, NULL),
(12, 'Kerala', NULL, NULL),
(13, 'Madhya Pradesh', NULL, NULL),
(14, 'Maharashtra', NULL, NULL),
(15, 'Manipur', NULL, NULL),
(16, 'Meghalaya', NULL, NULL),
(17, 'Mizoram', NULL, NULL),
(18, 'Nagaland', NULL, NULL),
(19, 'Odisha', NULL, NULL),
(20, 'Punjab', NULL, NULL),
(21, 'Rajasthan', NULL, NULL),
(22, 'Sikkim', NULL, NULL),
(23, 'Tamil Nadu', NULL, NULL),
(24, 'Telangana ', NULL, NULL),
(25, 'Tripura', NULL, NULL),
(26, 'Uttarakhand', NULL, NULL),
(27, 'Uttar Pradesh ', NULL, NULL),
(28, 'West Bengal', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE `static_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `static_pages`
--

INSERT INTO `static_pages` (`id`, `page_slug`, `page_name`, `value`, `created_at`, `updated_at`) VALUES
(1, 'about-us', 'About Us', '<p><b>The about us page</b></p>', '2022-12-05 18:33:35', '2022-12-05 18:33:35'),
(2, 'privacy-policy', 'Privacy Policy', '<p><b>Privacy Policy Pagesss.</b><br></p>', '2022-12-05 18:34:41', '2022-12-09 00:57:33'),
(3, 'terms-and-conditions', 'Terms and conditions', '<p>The&nbsp;<b>Terms and conditions</b>&nbsp;Page.</p>', '2022-12-05 18:35:46', '2022-12-09 00:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `points` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `emp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `linked_dealer` bigint(20) DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `aadhaar_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_no` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar_doc` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` tinyint(4) DEFAULT 0 COMMENT '1 = maried; 0 unmaried',
  `spouse_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouse_dob` date DEFAULT NULL,
  `profile_pic` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhar_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `points`, `email`, `email_verified_at`, `emp_code`, `password`, `role`, `linked_dealer`, `designation`, `phone`, `phone_verified_at`, `status`, `remember_token`, `name`, `branch_id`, `address`, `dob`, `aadhaar_no`, `whatsapp_no`, `aadhaar_doc`, `marital_status`, `spouse_name`, `spouse_dob`, `profile_pic`, `aadhar_img`, `created_by`, `parent`, `created_at`, `updated_at`) VALUES
(1, NULL, 'admin@gmail.com', NULL, NULL, '$2y$10$abco7JbUgDrXwYYSoR1KsucK37aDHNiKhom9ROEtwolmv9.iOxQ3y', 5, NULL, NULL, '1000000001', NULL, 1, 'Tw5yiIfPKVjQEh97axgYowTlhRXkyjrs8QGEPXRbTadCMhJmAK3gRybQyKzJ', 'Super Admin', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-23 06:06:52', '2022-11-28 05:36:23'),
(89, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '9999999999', '2022-11-08 07:09:29', 1, NULL, 'te', 1, 'dd 3930 dlk', '1998-03-07', '878787878793', NULL, 'http://localhost/mason/admin/public/aadhaar/M88.pdf', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-10 07:10:48', '2022-11-10 07:10:48'),
(90, '50', 'testemail@gmail.com', NULL, NULL, NULL, 2, NULL, NULL, '9999999999', '2022-11-08 07:09:29', 1, NULL, 'A mason', 1, 'dd 3930 dlk', '1998-03-07', '878787878798', NULL, 'http://localhost/mason/admin/public/aadhaar/M88.pdf', 0, NULL, NULL, NULL, NULL, NULL, 89, '2022-11-10 07:10:48', '2022-12-16 02:52:23'),
(92, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, '9999999999', '2022-11-08 07:09:29', 1, NULL, 'my', 8, 'dd 3930 dlk', '1998-03-07', '878787878798', NULL, 'M92.pdf', 0, NULL, NULL, NULL, NULL, NULL, 86, '2022-11-22 01:09:39', '2022-11-22 01:09:39'),
(98, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, '9999999999', '2022-11-08 07:09:29', 1, NULL, 'my', 8, 'dd 3930 dlk', '1998-03-07', '878787878798', NULL, 'M98.pdf', 0, NULL, NULL, NULL, NULL, NULL, 86, '2022-11-22 03:48:00', '2022-11-22 03:48:00'),
(108, NULL, 'er@mailinator.com', NULL, NULL, '$2y$10$oTHDW8RUKinmEEWRZTnpruM/HIvMsxzT0hPSVw0FBfa6aWzhOlwge', 5, NULL, NULL, '5212521212', NULL, 1, 'yZvSkMVa8mQhbrOrc9hRWTeLd6V0u3TrSHpurUL1WqCBFZqbWgAwZZNLjh78', 'Wade Clemons', 4, 'Dolor sit perspicia', '1994-04-20', '521252125212', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-23 07:53:27', '2022-11-28 05:19:21'),
(113, NULL, 'yo@mailinator.com', NULL, '12566', '$2y$10$Ah2dW9Fji2/PN.XBMffMy.tSHArLvvhEf45i5.YzIkaA8Qw9ZRt4C', 1, NULL, NULL, '8989898989', NULL, 1, NULL, 'Driscoll Holland', 6, 'Explicabo Fugiat n', '2011-08-04', '89898998989', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-28 05:53:38', '2022-11-28 06:07:41'),
(114, '0', 'zaxosy@mailinator.com', NULL, '12565', '$2y$10$QN9cMJX1z7x3CtzjB7Fhh.ojgyPjpJfbi6MQVNAx0D/QNGSRqudE2', 2, NULL, NULL, '8989898989', NULL, 1, NULL, 'Cyrus Sharpe', 6, 'Ipsam sit iusto earu', '1975-02-15', NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-28 06:07:13', '2022-12-09 02:06:20'),
(115, NULL, 'elizabeth @mailinator.com', NULL, '125689', NULL, 1, NULL, 'Est vitae dolor ipsam quae consequatur Libero numquam nostrud illum', '1234567890', NULL, 1, NULL, 'Elizabeth Cote', 8, 'Dolore accusamus in', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-14 07:57:55', '2022-12-14 08:11:05'),
(116, NULL, 'cefeheco@mailinator.com', NULL, '56895', NULL, 1, NULL, 'TE', '7851919853', NULL, 1, NULL, 'Richard English', 8, 'Amet optio duis vo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-14 08:12:33', '2022-12-14 08:12:49'),
(117, NULL, 'sekufy@mailinator.com', NULL, '898998', NULL, 1, NULL, 'Expedita', '1234567890', NULL, 1, NULL, 'Brett Estrada', 3, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-14 08:21:17', '2022-12-14 08:21:17'),
(118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1234567890', NULL, 1, NULL, 'Robin Klein', 1, 'Ipsa sit dolor omn', '1984-06-05', '898989898989', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:29:38', '2022-12-15 02:29:38'),
(119, NULL, NULL, NULL, NULL, '$2y$10$4.CaWcsSX4GGt.7WEKbThOu3.EXp8f5jy0rpmsl4PW0GoMCVQ9zUC', NULL, NULL, NULL, '8998898989', NULL, 1, NULL, 'Sloane Davidson', 6, 'Vel dolores voluptat', '1985-12-12', '898989898989', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:31:32', '2022-12-15 02:31:32'),
(120, NULL, NULL, NULL, NULL, '$2y$10$d6OBTokYWGJIy3gbLA2ZNeonJh2q51Cr2EEaCFqQY4YTVJcE9yOcC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:34:07', '2022-12-15 02:34:07'),
(121, NULL, NULL, NULL, NULL, '$2y$10$sdhL7CczyT86b1r/DyVrLeo1GCNHLJAenTzEnU9HffPO/pEeYDgDS', NULL, NULL, NULL, '8998898989', NULL, 0, NULL, 'Lysandra Wall', 8, 'Quisquam et repudian', '1984-02-04', '898989898989', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:37:35', '2022-12-15 02:37:35'),
(122, NULL, NULL, NULL, NULL, '$2y$10$BexkjTolIYDMkKLXCwrr6.MdznJn9ANBFO8HE0sVmYu1XhHnPXtmK', NULL, NULL, NULL, '8998898989', NULL, 1, NULL, 'Paula Fletcher Officiis dolore id o', 3, 'Architecto commodi d', '1970-12-18', '898989898989', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:40:39', '2022-12-15 02:40:39'),
(123, NULL, NULL, NULL, NULL, '$2y$10$zTUg67R9Bhx6bDa1Gp6BDOYrv7Y7eKCbeoOXhHuv5Q.M5OliA5fjO', NULL, NULL, NULL, '8998898989', NULL, 0, NULL, 'Chadwick Joyce', 5, 'Quia quisquam dolore', '2005-06-15', '898989898989', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:42:51', '2022-12-15 02:42:51'),
(124, NULL, NULL, NULL, NULL, '$2y$10$ZULVj7XOgbVGZP5niq15.Oa7eIDTSgDSC0Hnji9W4KrfDqik4.LPK', NULL, NULL, NULL, '8998898989', NULL, 1, NULL, 'Daquan Parks', 1, 'Fuga Eiusmod dolori', '1986-06-23', '898989898989', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 02:47:22', '2022-12-15 02:47:22'),
(125, NULL, NULL, NULL, NULL, '$2y$10$jg5R2MeHUo6ows/hqNLvNuyoGe1kiMMvlH7gkF3syAmFwVuZky7jC', NULL, NULL, NULL, '8998898989', NULL, 0, NULL, 'Avram Webb', 5, 'Esse aute alias ame', '2007-08-03', '898989898989', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, 109, '2022-12-15 02:54:50', '2022-12-15 02:54:50'),
(126, NULL, NULL, NULL, NULL, '$2y$10$iufdLmLLbpwA1eVxL9KNvOkKoqvotFK9WreXJ8yIN55SZBw3OChY.', 2, NULL, NULL, '8998898989', NULL, 1, NULL, 'Chancellor Mccarthy', 8, 'Laboriosam tempora', '2014-09-09', '898989898989', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 117, '2022-12-15 03:00:07', '2022-12-15 03:00:07'),
(127, NULL, NULL, NULL, NULL, '$2y$10$Avs01fwfYJsETOE6zw3uvOylA3nRABIXDP95A3yvoDkdRw0cgAJS2', 2, NULL, NULL, '5656565565', NULL, 0, NULL, 'Addison Head', 6, 'Numquam optio digni', '1984-04-10', '898989898989', NULL, NULL, 1, 'Alexis Pacheco', '2002-08-16', NULL, 'menu-img.png', NULL, 116, '2022-12-15 03:52:54', '2022-12-15 03:52:54'),
(128, NULL, NULL, NULL, NULL, '$2y$10$xVK8HibfkojRKC/y35jFsO2LQhiqwUQNj18KpeWgYrahGB3agXVz.', 2, NULL, NULL, '8998898989', NULL, 0, NULL, 'Raja Hall', 8, 'Quis velit aut qui r', '2001-07-07', '898989898989', NULL, NULL, 1, 'Lesley Porter', '2017-03-20', NULL, 'menu-img.png', NULL, 113, '2022-12-15 04:03:16', '2022-12-15 04:03:16'),
(129, NULL, NULL, NULL, NULL, '$2y$10$GQW2Vj/trsCobYL/ekoRoOPoDQP7qNTk/o71mQJ.dPk8i8bwtp5FC', 2, NULL, NULL, '8998898989', NULL, 0, NULL, 'Raja Hall', 8, 'Quis velit aut qui r', '2001-07-07', '898989898989', NULL, NULL, 1, 'Lesley Porter', '2017-03-20', NULL, 'menu-img.png', NULL, 113, '2022-12-15 04:04:01', '2022-12-15 04:04:01'),
(130, '60', NULL, NULL, NULL, '$2y$10$XwH4RVnyL6AAiPqQkXnsn.jv8iDeFqtZH6MZAhCwm3d4NAXP5tSQi', 2, NULL, NULL, '8998898989', NULL, 1, NULL, 'Colby Weber', 5, 'Nisi aliquam perfere', '1971-02-26', '898989898985', NULL, NULL, 1, 'Carissa Price', '2011-06-16', NULL, 'aadhar/be2f28e84bd330c6b6c5f2b790c8c906.png', NULL, 113, '2022-12-15 04:04:25', '2022-12-16 02:39:05'),
(132, NULL, NULL, NULL, '5299', NULL, 3, NULL, NULL, '8998898989', NULL, 0, NULL, 'Rama Duffy', 8, NULL, NULL, NULL, '8989989899', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 11:05:41', '2022-12-15 11:05:41'),
(133, NULL, NULL, NULL, NULL, NULL, 4, 132, NULL, '8998898989', NULL, 0, NULL, 'Danielle Mcgowan', 6, NULL, NULL, NULL, '8989989899', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 11:12:02', '2022-12-15 11:12:02'),
(134, NULL, NULL, NULL, '89522', NULL, 3, NULL, NULL, '8998898989', NULL, 1, NULL, 'Alvin Trevino', 3, NULL, NULL, NULL, '8989989899', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 12:05:26', '2022-12-15 12:10:55'),
(135, NULL, NULL, NULL, '8889236', NULL, 4, 134, NULL, '8998898989', NULL, 1, NULL, 'Amery Whitfield', 3, NULL, NULL, NULL, '56565655565', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 12:06:12', '2022-12-15 12:06:12'),
(136, NULL, NULL, NULL, '897529', NULL, 3, NULL, NULL, '88956592325', NULL, 1, NULL, 'Dieter Rivas', 8, NULL, NULL, NULL, '56565655565', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-15 12:17:39', '2022-12-15 12:17:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_catalogue_redeemtions`
--

CREATE TABLE `user_catalogue_redeemtions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `catalogue_id` bigint(20) DEFAULT NULL,
  `redeemed_point` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `support_type` tinyint(4) DEFAULT NULL COMMENT '1= Not Delivered, 2=DEFECTIVE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_catalogue_redeemtions`
--

INSERT INTO `user_catalogue_redeemtions` (`id`, `user_id`, `catalogue_id`, `redeemed_point`, `status`, `comment`, `delivery_date`, `support_type`, `created_at`, `updated_at`) VALUES
(1, 130, 4, 22, NULL, 'Address error', NULL, 1, '2022-12-16 02:26:30', '2022-12-16 05:08:08'),
(2, 130, 2, 22, 1, NULL, '2022-12-16', 1, '2022-12-16 02:26:30', '2022-12-16 05:49:11');

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Zone-A', 1, NULL, NULL),
(2, 'Zone-B', 1, NULL, NULL),
(3, 'Zone-C', 1, NULL, NULL),
(4, 'Zone-D', 0, NULL, '2022-12-16 06:02:42'),
(5, 'Zone-E', 1, '2022-12-16 06:03:44', '2022-12-16 06:03:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catalogues`
--
ALTER TABLE `catalogues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_pages`
--
ALTER TABLE `contact_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `lifting`
--
ALTER TABLE `lifting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mason_categories`
--
ALTER TABLE `mason_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mason_dealers`
--
ALTER TABLE `mason_dealers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reward_points`
--
ALTER TABLE `reward_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_catalogue_redeemtions`
--
ALTER TABLE `user_catalogue_redeemtions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `catalogues`
--
ALTER TABLE `catalogues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_pages`
--
ALTER TABLE `contact_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lifting`
--
ALTER TABLE `lifting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `mason_categories`
--
ALTER TABLE `mason_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mason_dealers`
--
ALTER TABLE `mason_dealers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reward_points`
--
ALTER TABLE `reward_points`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `user_catalogue_redeemtions`
--
ALTER TABLE `user_catalogue_redeemtions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
