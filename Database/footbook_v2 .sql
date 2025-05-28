-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 26, 2025 at 07:59 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



--
-- Database: `footbook_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_images`
--

CREATE TABLE `chat_images` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_images`
--

INSERT INTO `chat_images` (`id`, `message_id`, `image_url`, `created_at`) VALUES
(7, 13, 'https://example.com/path/to/your/image.jpg', '2025-04-04 14:28:20'),
(8, 14, 'https://example.com/path/to/your/image2222.jpg', '2025-04-04 14:29:24'),
(9, 15, 'https://example.com/path/to/your/image2222.jpg', '2025-04-05 03:58:05'),
(10, 18, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTpBgKXdHnoDD3suhEGPfjRLtVkS8X1o_3Oem5k8MEsVHxFoXWmJ7LVgHVSWF8Wj7zoi0jS5629_8RjjYZjvdHXQ', '2025-04-26 08:00:20'),
(11, 27, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745748923/jgmxvi2xexydgaxeeaow.jpg', '2025-04-27 10:15:24'),
(12, 33, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745919376/auf6hnsldwdk2rgmzhov.avif', '2025-04-29 09:36:18'),
(13, 38, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746782812/irgoioqewkec0njt05lb.jpg', '2025-05-09 09:26:52'),
(14, 39, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746782846/lkgo0cdjkdcwcnxhjmhm.jpg', '2025-05-09 09:27:26'),
(15, 40, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746782870/brr8kbh7oj070w9vqeo2.jpg', '2025-05-09 09:27:50'),
(16, 47, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1747244156/rhbum0ok5676biqqh1es.jpg', '2025-05-14 17:35:57'),
(17, 48, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1747245426/qddfsnulr7djuq4jnrrg.jpg', '2025-05-14 17:57:07'),
(18, 49, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1747245449/jntijv6xty6fnhq75jvf.jpg', '2025-05-14 17:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`, `parent_id`) VALUES
(2, 4, 1, 'Hay lắm hahahaha', '2025-04-18 09:51:32', NULL),
(3, 8, 1, 'Hay lắm hahahaha', '2025-04-18 10:10:09', NULL),
(4, 8, 2, 'Mãi đỉnh!!!!!!!', '2025-04-18 10:14:10', NULL),
(5, 8, 2, 'TÔI YÊU TÔI', '2025-04-18 10:14:23', NULL),
(6, 8, 2, 'HIHI', '2025-04-18 10:16:21', NULL),
(7, 8, 1, 'sss', '2025-04-19 03:08:35', NULL),
(8, 18, 2, 'Hay quá ahihihi', '2025-04-19 04:33:37', NULL),
(9, 26, 2, 'Xịn sò luôn', '2025-04-22 09:05:15', NULL),
(10, 25, 1, 'Hay quá', '2025-04-23 22:49:51', NULL),
(11, 46, 2, 'Hello', '2025-05-12 23:17:48', NULL),
(12, 46, 1, 'Hay lắm hahahaha', '2025-05-15 03:38:22', NULL),
(13, 46, 2, 'Đây là trả lời cho bình luận gốc', '2025-05-15 03:40:31', 12),
(14, 46, 2, 'Đây là trả lời 2 cho bình luận gốc', '2025-05-15 03:43:59', 12),
(15, 46, 2, 'Tuyệt vời', '2025-05-17 16:19:41', NULL),
(16, 47, 2, 'Oke đấy', '2025-05-17 16:20:20', NULL),
(17, 47, 2, 'Đc đấy', '2025-05-17 16:28:11', NULL),
(18, 46, 2, 'Hay quá', '2025-05-26 02:24:10', NULL),
(19, 46, 2, 'Uk', '2025-05-26 02:24:30', 18),
(20, 46, 2, 'UK công nhận á', '2025-05-26 02:32:39', 18),
(21, 46, 2, 'Ờ ha', '2025-05-26 05:59:55', 18),
(22, 46, 2, 'Thật ý', '2025-05-26 06:34:10', 18);

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user1_id`, `user2_id`, `created_at`) VALUES
(1, 1, 2, '2025-04-04 09:06:23'),
(2, 1, 4, '2025-04-26 07:48:35'),
(3, 2, 4, '2025-04-26 07:48:49');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `avatar_url`, `created_at`) VALUES
(2, 'Nhóm Công Nghệ', 'https://example.com/group-avatar.jpg', '2025-04-03 00:10:22'),
(3, 'Nhóm Công Nghệ', 'https://example.com/group-avatar.jpg', '2025-04-03 07:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `group_chat`
--

CREATE TABLE `group_chat` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_chat`
--

INSERT INTO `group_chat` (`id`, `name`, `avatar_url`, `created_at`) VALUES
(1, 'NHÓM CHAT VIP PRO', 'HIHI.jpg', '2025-05-03 17:00:00'),
(2, 'Nhom 2', NULL, '2025-05-03 17:00:00'),
(3, 'Nhom 2', NULL, '2025-05-03 17:00:00'),
(4, 'Người dùng 3, Nguyễn Đức Anh', NULL, '2025-05-03 17:00:00'),
(5, 'Test avatar', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(6, 'Test ava 2', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(7, 'Người dùng 3, Nguyễn Đức Anh, Người dùng 4, Người dùng 2', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(8, 'Nguyễn Đức Anh, Người dùng 3', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(9, 'Test ava', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(10, 'Test ava', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(11, 'Nguyễn Đức Anh, Người dùng 3', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(12, '12', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(13, 'Người dùng 2, Nguyễn Đức Anh', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(14, 'AAAAAAAAA', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(15, 'CCCCCC', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(16, 'VVV', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(17, '22222222221', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746349736/kyykjassaow6logmsyn3.webp', '2025-05-03 17:00:00'),
(18, 'AAAAAAAAAAAAYYYY', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746360923/gfknos6tnphqcsuriiu7.gif', '2025-05-03 17:00:00'),
(19, 'Nhom 2', NULL, '2025-05-03 17:00:00'),
(20, 'TIME', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-03 17:00:00'),
(21, 'TIME2', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-04 12:27:43'),
(22, 'TIME3', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746200508/group-chat-icon-for-online-messaging-vector_ovjkhx.jpg', '2025-05-04 12:29:39'),
(23, 'Test tạo', 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746434139/z9rnvgbtefaznbbpcens.gif', '2025-05-05 08:35:39');

-- --------------------------------------------------------

--
-- Table structure for table `group_chat_images`
--

CREATE TABLE `group_chat_images` (
  `id` int(11) NOT NULL,
  `group_message_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_chat_images`
--

INSERT INTO `group_chat_images` (`id`, `group_message_id`, `image_url`, `created_at`) VALUES
(1, 5, 'demo.jpg', '2025-04-05 04:05:34'),
(2, 8, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746191490/mc9q1mp2xqqigjjhtsw7.jpg', '2025-05-02 13:11:31'),
(3, 14, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746349842/jjzpjpvlpbrwyfqeiqnk.gif', '2025-05-04 09:10:44'),
(4, 18, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746783663/spolhxaxna0b3qn2hnni.jpg', '2025-05-09 09:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `group_chat_members`
--

CREATE TABLE `group_chat_members` (
  `group_chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_chat_members`
--

INSERT INTO `group_chat_members` (`group_chat_id`, `user_id`, `joined_at`) VALUES
(1, 1, '2025-04-04 21:00:24'),
(1, 2, '2025-04-04 21:00:24'),
(1, 4, '2025-04-04 21:00:24'),
(1, 5, '2025-04-04 21:00:24'),
(2, 1, '2025-04-04 21:00:37'),
(2, 2, '2025-04-04 21:00:37'),
(3, 1, '2025-04-04 21:01:11'),
(3, 2, '2025-04-04 21:01:11'),
(3, 5, '2025-04-04 21:01:11'),
(4, 1, '2025-05-01 20:03:57'),
(4, 2, '2025-05-01 20:03:57'),
(4, 4, '2025-05-01 20:03:57'),
(5, 2, '2025-05-02 10:39:55'),
(5, 4, '2025-05-02 10:39:55'),
(5, 5, '2025-05-02 10:39:55'),
(6, 1, '2025-05-02 11:49:09'),
(6, 2, '2025-05-02 11:49:09'),
(6, 4, '2025-05-02 11:49:09'),
(7, 1, '2025-05-02 22:06:07'),
(7, 2, '2025-05-02 22:06:07'),
(7, 4, '2025-05-02 22:06:07'),
(7, 5, '2025-05-02 22:06:07'),
(8, 1, '2025-05-02 22:06:40'),
(8, 2, '2025-05-02 22:06:40'),
(8, 4, '2025-05-02 22:06:40'),
(9, 1, '2025-05-03 02:10:07'),
(9, 2, '2025-05-03 02:10:07'),
(9, 4, '2025-05-03 02:10:07'),
(10, 1, '2025-05-03 02:10:39'),
(10, 2, '2025-05-03 02:10:39'),
(10, 4, '2025-05-03 02:10:39'),
(11, 1, '2025-05-03 02:12:46'),
(11, 2, '2025-05-03 02:12:46'),
(11, 4, '2025-05-03 02:12:46'),
(12, 1, '2025-05-03 02:15:27'),
(12, 2, '2025-05-03 02:15:27'),
(12, 4, '2025-05-03 02:15:27'),
(13, 1, '2025-05-03 02:21:54'),
(13, 2, '2025-05-03 02:21:54'),
(14, 1, '2025-05-03 10:06:13'),
(14, 2, '2025-05-03 10:06:13'),
(14, 4, '2025-05-03 10:06:13'),
(15, 1, '2025-05-03 10:17:39'),
(15, 2, '2025-05-03 10:17:39'),
(15, 5, '2025-05-03 10:17:39'),
(16, 1, '2025-05-04 01:59:44'),
(16, 2, '2025-05-04 01:59:44'),
(16, 4, '2025-05-04 01:59:44'),
(17, 1, '2025-05-04 02:08:58'),
(17, 2, '2025-05-04 02:08:58'),
(17, 4, '2025-05-04 02:08:58'),
(17, 5, '2025-05-04 02:08:58'),
(18, 1, '2025-05-04 05:15:25'),
(18, 2, '2025-05-04 05:15:25'),
(18, 4, '2025-05-04 05:15:25'),
(18, 5, '2025-05-04 05:15:25'),
(19, 1, '2025-05-04 05:20:40'),
(19, 2, '2025-05-04 05:20:40'),
(19, 5, '2025-05-04 05:20:40'),
(20, 1, '2025-05-04 05:26:22'),
(20, 2, '2025-05-04 05:26:22'),
(20, 4, '2025-05-04 05:26:22'),
(21, 1, '2025-05-04 05:27:43'),
(21, 2, '2025-05-04 05:27:43'),
(21, 4, '2025-05-04 05:27:43'),
(22, 1, '2025-05-04 05:29:39'),
(22, 2, '2025-05-04 05:29:39'),
(22, 5, '2025-05-04 05:29:39'),
(23, 1, '2025-05-05 01:35:39'),
(23, 2, '2025-05-05 01:35:39'),
(23, 4, '2025-05-05 01:35:39'),
(23, 5, '2025-05-05 01:35:39');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`group_id`, `user_id`) VALUES
(2, 1),
(3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `group_messages`
--

CREATE TABLE `group_messages` (
  `id` int(11) NOT NULL,
  `group_chat_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('text','image') NOT NULL DEFAULT 'text',
  `conversation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_03_29_091255_create_cache_table', 1),
(2, '2025_03_29_093112_create_personal_access_tokens_table', 2),
(3, '2025_03_30_161058_create_sessions_table', 3),
(4, '2025_05_25_145102_add_access_token', 4);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
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
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 2, 'auth_token', 'cc41ff17ab0eff5ddf0ea60f8575b48198ce555ffb1851b01d31e6b4ee2782a3', '[\"*\"]', NULL, NULL, '2025-05-24 04:36:25', '2025-05-24 04:36:25'),
(2, 'App\\Models\\User', 2, 'auth_token', 'be67aa089d6be12ba589d53d8f3692361182cafcd37748e7f0b29dadbe1de893', '[\"*\"]', NULL, NULL, '2025-05-24 04:46:50', '2025-05-24 04:46:50'),
(3, 'App\\Models\\User', 11, 'auth_token', '8455cfacebf0f675b898b834df90576b78a0fe52f9654636c516ac2fa8b3cd19', '[\"*\"]', NULL, NULL, '2025-05-24 04:47:48', '2025-05-24 04:47:48'),
(4, 'App\\Models\\User', 2, 'auth_token', '3630afe76edb3b2857bcbb1bcdb8477b2de4eec47817f51a7da28811f2c42ee9', '[\"*\"]', NULL, NULL, '2025-05-24 09:01:56', '2025-05-24 09:01:56'),
(5, 'App\\Models\\User', 2, 'auth_token', 'd8cbbe19fa9ca4c726d1f972b04ac1d6b7891a1b882c751886be5126dcd407bf', '[\"*\"]', NULL, NULL, '2025-05-25 05:36:12', '2025-05-25 05:36:12');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `privacy` enum('public','friends','private') NOT NULL,
  `shareId` int(11) DEFAULT 0,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `group_id`, `content`, `created_at`, `privacy`, `shareId`, `isDeleted`) VALUES
(40, 2, NULL, 'Đăng nè', '2025-05-05 02:40:07', 'friends', 0, 0),
(42, 2, NULL, 'kkk', '2025-05-05 02:44:32', 'friends', 0, 0),
(43, 2, NULL, '0 nè', '2025-05-05 02:46:22', 'friends', NULL, 0),
(44, 2, NULL, NULL, '2025-05-07 10:33:59', 'friends', 43, 0),
(45, 2, NULL, NULL, '2025-05-11 03:18:29', 'friends', NULL, 0),
(46, 2, NULL, 'Hệ thống các tỉnh thành của Việt Nam', '2025-05-12 22:38:19', 'friends', NULL, 0),
(47, 2, NULL, NULL, '2025-05-17 16:20:00', 'friends', 46, 0),
(48, 2, NULL, 'WOW', '2025-05-26 08:58:53', 'friends', NULL, 0),
(49, 2, NULL, NULL, '2025-05-26 10:41:18', 'friends', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `post_images`
--

CREATE TABLE `post_images` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_images`
--

INSERT INTO `post_images` (`id`, `post_id`, `image_url`, `created_at`) VALUES
(2, 2, 'https://example.com/image.jpg', '2025-04-02 23:46:51'),
(3, 3, 'https://example.com/image.jpg', '2025-04-02 23:46:56'),
(4, 6, 'https://cdn2.tuoitre.vn/thumb_w/480/471584752817336320/2025/4/3/afp202504032208233546v2highreslosangelesfootballclubvintermiamiconcacafcha-17436607578171764295235.jpg', '2025-04-18 10:01:04'),
(5, 6, 'https://images2.thanhnien.vn/528068263637045248/2025/4/6/messi-1743911292770865398606.jpg', '2025-04-18 10:01:04'),
(6, 6, 'https://images2.thanhnien.vn/528068263637045248/2025/4/6/messi-1743911292770865398606.jpg', '2025-04-18 10:01:04'),
(7, 7, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4l77GjQ6uaPhs3gbRGV_GlIMVPOW6QG_JMQ&s', '2025-04-18 10:02:38'),
(8, 7, 'https://cdn.24h.com.vn/upload/1-2018/images/2018-03-13/Cau-long-trieu-do-Nong-bong-do-suc-Lee-Chong-Wei---Lin-Dan-lan-thu-40-leechongwei6601-1520939263-380-width660height403.jpg', '2025-04-18 10:02:38'),
(9, 8, 'https://photo.znews.vn/w660/Uploaded/neg_etpyole/2020_04_17/020160728232231.jpg', '2025-04-18 10:03:43'),
(10, 8, 'https://cdn.24h.com.vn/upload/1-2018/images/2018-03-13/Cau-long-trieu-do-Nong-bong-do-suc-Lee-Chong-Wei---Lin-Dan-lan-thu-40-leechongwei6601-1520939263-380-width660height403.jpg', '2025-04-18 10:03:43'),
(11, 11, 'https://photo.znews.vn/w660/Uploaded/neg_etpyole/2020_04_17/020160728232231.jpg', '2025-04-19 03:16:30'),
(12, 11, 'https://cdn.24h.com.vn/upload/1-2018/images/2018-03-13/Cau-long-trieu-do-Nong-bong-do-suc-Lee-Chong-Wei---Lin-Dan-lan-thu-40-leechongwei6601-1520939263-380-width660height403.jpg', '2025-04-19 03:16:30'),
(13, 12, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745061274/g7xlkoxyfsvipl5jbegb.jpg', '2025-04-19 04:14:35'),
(14, 15, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745061895/vnujkiln7azqys7a8s9b.jpg', '2025-04-19 04:24:57'),
(15, 16, 'https://media.tenor.com/mfO1LwBmrZAAAAAC/funny-dog-funny-as-hell.gif', '2025-04-19 04:25:41'),
(16, 18, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745062375/dmjer7goo9giheukqnuw.jpg', '2025-04-19 04:32:57'),
(17, 19, 'https://media.tenor.com/gWb24H8mua8AAAAC/saturday-baby.gif', '2025-04-19 04:34:08'),
(18, 20, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745071692/jyhberpgmigafxhrtwoz.jpg', '2025-04-19 07:08:14'),
(19, 23, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1745074967/dxlx9op5yl6gwsety7ve.jpg', '2025-04-19 08:02:50'),
(20, 25, 'https://photo.znews.vn/w660/Uploaded/neg_etpyole/2020_04_17/020160728232231.jpg', '2025-04-19 08:09:08'),
(21, 25, 'https://cdn.24h.com.vn/upload/1-2018/images/2018-03-13/Cau-long-trieu-do-Nong-bong-do-suc-Lee-Chong-Wei---Lin-Dan-lan-thu-40-leechongwei6601-1520939263-380-width660height403.jpg', '2025-04-19 08:09:08'),
(22, 33, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746436752/viiairihzw6mj0hkeate.jpg', '2025-05-05 02:19:19'),
(23, 45, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746958708/qj2q1idwj7u80owzcs8n.jpg', '2025-05-11 03:18:29'),
(24, 45, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1746958709/chuijj6ghgggvfosjofh.jpg', '2025-05-11 03:18:29'),
(25, 46, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1747114698/iv9ggep9wfocvp2ohsju.jpg', '2025-05-12 22:38:19'),
(26, 48, 'https://media.tenor.com/-LM-3b0ynlsAAAAC/multiversx-x.gif', '2025-05-26 08:58:53'),
(27, 49, 'https://res.cloudinary.com/dzkzebsn7/image/upload/v1748281277/nm3sbme3hrfp0rfjmnk3.jpg', '2025-05-26 10:41:18'),
(28, 49, 'https://media.tenor.com/ti4f67aS1sIAAAAC/damonos-vamonos.gif', '2025-05-26 10:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `private_messages`
--

CREATE TABLE `private_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('text','image') NOT NULL DEFAULT 'text',
  `conversation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `private_messages`
--

INSERT INTO `private_messages` (`id`, `sender_id`, `receiver_id`, `content`, `created_at`, `type`, `conversation_id`) VALUES
(1, 1, 2, 'Hello bạn!', '2025-04-04 09:09:15', 'text', 1),
(2, 2, 1, 'Hello bạn!', '2025-04-04 09:12:31', 'text', 1),
(3, 2, 1, 'Hello bạn!', '2025-04-04 09:23:35', 'text', 1),
(4, 2, 1, 'Hello bạn!', '2025-04-04 09:23:37', 'text', 1),
(5, 2, 1, 'Hello bạn!', '2025-04-04 09:23:39', 'text', 1),
(6, 1, 2, 'Hello there!', '2025-04-04 09:33:07', 'text', 1),
(7, 1, 2, '', '2025-04-04 09:40:45', 'image', 1),
(8, 1, 2, '', '2025-04-04 09:41:02', 'image', 1),
(9, 1, 2, '', '2025-04-04 09:53:16', 'image', 1),
(10, 1, 2, '', '2025-04-04 09:53:30', 'image', 1),
(11, 1, 2, '', '2025-04-04 14:21:17', 'image', 1),
(12, 1, 2, '', '2025-04-04 14:21:37', 'image', 1),
(13, 1, 2, '', '2025-04-04 14:28:20', 'image', 1),
(14, 1, 2, '', '2025-04-04 14:29:24', 'image', 1),
(15, 1, 2, '', '2025-04-05 03:58:05', 'image', 1),
(16, 1, 4, 'Hello there!', '2025-04-26 07:48:35', 'text', 2),
(17, 2, 4, 'Hello there!', '2025-04-26 07:48:49', 'text', 3),
(18, 1, 2, '', '2025-04-26 08:00:20', 'image', 1),
(19, 2, 4, 'HI', '2025-04-26 11:54:37', 'text', 3),
(20, 2, 1, 'Hi', '2025-04-26 11:54:51', 'text', 1),
(21, 2, 1, 'helo', '2025-04-26 12:05:30', 'text', 1),
(22, 2, 4, 'Chào bạn', '2025-04-26 12:10:28', 'text', 3),
(23, 2, 1, 'Tốt', '2025-04-26 12:32:25', 'text', 1),
(24, 2, 1, 'Hay', '2025-04-26 12:32:44', 'text', 1),
(25, 2, 1, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-04-26 14:28:21', 'text', 1),
(26, 2, 1, 'Hello', '2025-04-27 09:46:48', 'text', 1),
(27, 2, 1, '', '2025-04-27 10:15:24', 'image', 1),
(28, 2, 1, 'CHeck', '2025-04-29 08:53:44', 'text', 1),
(29, 2, 4, 'ALO', '2025-04-29 08:58:13', 'text', 3),
(30, 2, 1, 'CHECK REFETCH LIST', '2025-04-29 09:06:09', 'text', 1),
(31, 2, 4, 'uk', '2025-04-29 09:16:04', 'text', 3),
(32, 2, 4, 'kay', '2025-04-29 09:20:47', 'text', 3),
(33, 2, 4, '', '2025-04-29 09:36:18', 'image', 3),
(34, 2, 4, 'hi', '2025-05-02 13:10:17', 'text', 3),
(35, 2, 4, 'Alo', '2025-05-02 14:25:44', 'text', 3),
(36, 2, 1, 'kkkkkk', '2025-05-04 13:06:17', 'text', 1),
(37, 2, 1, 'HELLO', '2025-05-09 09:26:14', 'text', 1),
(38, 2, 1, '', '2025-05-09 09:26:52', 'image', 1),
(39, 2, 1, '', '2025-05-09 09:27:26', 'image', 1),
(40, 2, 1, '', '2025-05-09 09:27:50', 'image', 1),
(41, 2, 1, 'a', '2025-05-12 17:41:00', 'text', 1),
(42, 2, 4, 'Hello there!', '2025-05-12 17:58:29', 'text', 3),
(43, 2, 1, 'Hey', '2025-05-14 16:47:34', 'text', 1),
(44, 2, 1, 'Hey there!', '2025-05-14 16:56:09', 'text', 1),
(45, 2, 1, 'Ờ', '2025-05-14 17:34:50', 'text', 1),
(46, 2, 4, 'Ừ', '2025-05-14 17:35:00', 'text', 3),
(47, 2, 1, '', '2025-05-14 17:35:57', 'image', 1),
(48, 2, 4, '', '2025-05-14 17:57:07', 'image', 3),
(49, 2, 1, '', '2025-05-14 17:57:30', 'image', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reactions`
--

CREATE TABLE `reactions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('LIKE','LOVE','HAHA','WOW','SAD','ANGRY') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reactions`
--

INSERT INTO `reactions` (`id`, `post_id`, `user_id`, `type`) VALUES
(2, 2, 1, 'HAHA'),
(9, 4, 2, 'LOVE'),
(13, 6, 2, 'LIKE'),
(14, 8, 2, 'LIKE'),
(15, 16, 2, 'LIKE'),
(17, 17, 2, 'LIKE'),
(18, 23, 2, 'LIKE'),
(20, 24, 2, 'LOVE'),
(22, 25, 2, 'LOVE'),
(23, 26, 2, 'LIKE'),
(25, 31, 2, 'LIKE'),
(33, 30, 2, 'LOVE'),
(34, 29, 2, 'LIKE'),
(37, 42, 2, 'WOW'),
(38, 40, 2, 'ANGRY'),
(45, 43, 2, 'LIKE'),
(46, 46, 2, 'LOVE');

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE `relationships` (
  `id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `addressee_id` int(11) NOT NULL,
  `status` enum('pending','accepted','blocked','strangers') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relationships`
--

INSERT INTO `relationships` (`id`, `requester_id`, `addressee_id`, `status`) VALUES
(40, 1, 2, 'accepted'),
(41, 4, 2, 'accepted'),
(43, 7, 1, 'accepted'),
(46, 2, 6, 'pending'),
(48, 5, 2, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('B8bAmS1FzP7NIJB7ZbgFpZqpHwRXto7vFXSzWeep', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZThXV2JGSEZjRHZOYUhheDdnMWRWUDVSZUZwNUw4ZjR6dGxkcjN0cyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1743757287),
('mYCnybGzk4KjC2Tw8UxkCNb6MSx8kiKENnohEaLH', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM09URURCVGxmNElhQWhlUkxyWkZ5R0gybkxuMXVINkZKQkxQWTlWNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1748188105),
('qm0BwmYKlclx6kgMQ2D2m8SN9UuXydGyggTiqqD8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmJ6SU5KM1lFNjdwejdyWm9manh2bFljTnlrakpkdlFXTlA2WEsxUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1747116260),
('skhYuCxBd4Tco0LEYGtXcGzm3J0mP1Xkxw0HNoVe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHJNamM5dFE4YlR0ZEFpdnJIOTFUZXBEZnhhRnlxREFjWWZhc3N6ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1743411995),
('tykntBtwtpVpCiLBjGInJf6SMN67xjWCbb0Y9vVE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiblJVUDRtR0x2Z0xtUkZrRHA1b1lrN0RhdkNnNHFEbEYxR3BZTm1NZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1743352998);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_provider` enum('google','facebook','local') DEFAULT NULL,
  `auth_id` varchar(255) DEFAULT NULL,
  `birth_year` int(11) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT 'https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg',
  `cover_photo_url` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `access_token` varchar(80) DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `auth_provider`, `auth_id`, `birth_year`, `profession`, `avatar_url`, `cover_photo_url`, `address`, `status`, `access_token`, `token_expires_at`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Đức Anh', 'nguoidung1@example.com', '$2y$12$houBd6v/ESQa2aZEqfCLaOX9Zk.udSb9OsrA4xgk3lgyBLTC2lq9C', 'local', NULL, 1995, 'Developer', 'https://example.com/new-avatar2.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTpBgKXdHnoDD3suhEGPfjRLtVkS8X1o_3Oem5k8MEsVHxFoXWmJ7LVgHVSWF8Wj7zoi0jS5629_8RjjYZjvdHXQ', 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-04-01 23:46:20', '2025-05-25 05:42:19'),
(2, 'Người dùng 2', 'nguoidung2@example.com', '$2y$12$0dbY0qJeHlZT8dIV8gaFKeAcF3RJxRZt.Koi7qJhU9WnS6z8jNLmW', 'local', NULL, 1999, 'Developerrrrrr', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTpBgKXdHnoDD3suhEGPfjRLtVkS8X1o_3Oem5k8MEsVHxFoXWmJ7LVgHVSWF8Wj7zoi0jS5629_8RjjYZjvdHXQ', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTpBgKXdHnoDD3suhEGPfjRLtVkS8X1o_3Oem5k8MEsVHxFoXWmJ7LVgHVSWF8Wj7zoi0jS5629_8RjjYZjvdHXQ', 'Hanoi, Vietnam', 'available', 'E4rYCfFKRFqGh50Ef9atld8v9p2bcEoxwTAszAKJ1ORgVnTHgXpF7Ap6nysyUok6UCwhcwbRkBVQmvfk', '2025-06-24 12:09:39', '2025-04-01 23:46:39', '2025-05-25 12:09:39'),
(4, 'Người dùng 3', 'nguoidung3@example.com', '$2y$12$FL8j4ygDAPa8KUsqxL3N6eQqZ/LnGnWwxfbpmuF9vts2z6rjHFw9q', 'local', NULL, 1995, 'Developer', 'https://media.vietnamplus.vn/images/c14f6479e83e315b4cf3a2906cc6a51e8c0218388a4fa14bb99ff693072eeaaa43bf40f271860ebe7d6296b3f50df54588d690d026bda4127d1c9917de35a98d/leechongwei.jpg.webp', 'https://example.com/cover.jpg', 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-04-04 20:57:48', '2025-04-22 08:47:44'),
(5, 'Người dùng 4', 'nguoidung4@example.com', '$2y$12$H.i5/A0kPqJtBzZz8ElyTewgyzDrgXA3ctzbyOJxGzVKy.urP0LRu', 'local', NULL, 1995, 'Developer', 'https://vcdn1-thethao.vnecdn.net/2019/06/14/lee-danh-cau-1560454115-7779-1560454467.png?w=1200&h=0&q=100&dpr=1&fit=crop&s=c5-joMLdtMagHhHrlL4aFw', 'https://example.com/cover.jpg', 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-04-04 20:57:55', '2025-04-22 08:48:01'),
(6, 'Người dùng 5', 'nguoidung5@example.com', '$2y$12$mH298CXQOh7j13PLAspqveM7ZeZa5hTssLxHoFHqDzf5OCPoyeX42', 'local', NULL, 1995, 'Developer', 'https://encrypted-tbn3.gstatic.com/licensed-image?q=tbn:ANd9GcR-Txd4gTn54hUGOVNWNdk8r1FRxpUzy8MOpJUs5DcwmzJkmwIVZNfk72td91FSGs8TRgbP95n1ZWk8mJo', 'https://example.com/cover.jpg', 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-05-08 03:43:29', '2025-05-08 12:43:50'),
(7, 'Người dùng 7', 'nguoidung7@example.com', '$2y$12$aUB1UzE6E9M39qvD8DgPxeX7.oMOCwOTHrLp4xyktJAVlhzkUd4si', 'local', NULL, 1995, 'Developer', 'https://example.com/avatar.jpg', 'https://example.com/cover.jpg', 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-05-08 03:45:45', '2025-05-08 03:45:45'),
(8, 'Người dùng 8', 'nguoidung8@example.com', '$2y$12$fs1RQfryf.HcYgaGc.dNMuNGNXaKbiGXHwUDid0UtikvR73c5iWEi', 'local', NULL, 1995, 'Developer', 'https://example.com/avatar.jpg', 'https://example.com/cover.jpg', 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-05-09 01:39:00', '2025-05-09 01:39:00'),
(9, 'Người dùng 9', 'nguoidung9@example.com', '$2y$12$bZ7th/b7BPKVJMtF6RKM3.1Bl2D3AVVwC.WShwoSWIF9RPTTL7TwK', 'local', NULL, 1995, 'Developer', 'https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg', NULL, 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-05-09 01:42:18', '2025-05-09 01:42:18'),
(10, 'Người dùng 10', 'nguoidung10@example.com', '$2y$12$XGvsErGxJOXTAtyTV5vsAufNneY2mva/2dSyGMKGr.A5cBrJlcm7K', 'local', NULL, 1995, 'Developer', NULL, NULL, 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-05-23 21:46:30', '2025-05-23 21:46:30'),
(11, 'Người dùng 11', 'nguoidung11@example.com', '$2y$12$MwaZteSKGvatg7v.Q5AoROgzXtYrM7tsQlrZ6hG6spsLg/RSeKm.i', 'local', NULL, 1995, 'Developer', NULL, NULL, 'Hanoi, Vietnam', 'available', NULL, NULL, '2025-05-24 04:47:48', '2025-05-24 04:47:48'),
(12, 'John Doe', 'john@example.com', '$2y$12$7y1DN4cEHDOxOsSoRRGdAOyt3ulO753texJRpglX.YKgnPQu.w8O2', 'local', NULL, NULL, NULL, NULL, NULL, NULL, 'available', NULL, NULL, '2025-05-25 07:56:02', '2025-05-25 10:38:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `chat_images`
--
ALTER TABLE `chat_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_images_ibfk_1` (`message_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_chat`
--
ALTER TABLE `group_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_chat_images`
--
ALTER TABLE `group_chat_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_message_id` (`group_message_id`);

--
-- Indexes for table `group_chat_members`
--
ALTER TABLE `group_chat_members`
  ADD PRIMARY KEY (`group_chat_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_chat_id` (`group_chat_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indexes for table `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_relationship` (`requester_id`,`addressee_id`),
  ADD KEY `fk_addressee_id` (`addressee_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `users_access_token_unique` (`access_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_images`
--
ALTER TABLE `chat_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `group_chat`
--
ALTER TABLE `group_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `group_chat_images`
--
ALTER TABLE `group_chat_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `private_messages`
--
ALTER TABLE `private_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `relationships`
--
ALTER TABLE `relationships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_images`
--
ALTER TABLE `chat_images`
  ADD CONSTRAINT `chat_images_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `private_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chat_images_private_messages` FOREIGN KEY (`message_id`) REFERENCES `private_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `group_chat_images`
--
ALTER TABLE `group_chat_images`
  ADD CONSTRAINT `group_chat_images_ibfk_1` FOREIGN KEY (`group_message_id`) REFERENCES `group_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_chat_members`
--
ALTER TABLE `group_chat_members`
  ADD CONSTRAINT `group_chat_members_ibfk_1` FOREIGN KEY (`group_chat_id`) REFERENCES `group_chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_chat_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`group_chat_id`) REFERENCES `group_chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_messages_ibfk_3` FOREIGN KEY (`conversation_id`) REFERENCES `group_conversations` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_images`
--
ALTER TABLE `post_images`
  ADD CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `private_messages`
--
ALTER TABLE `private_messages`
  ADD CONSTRAINT `private_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `private_messages_ibfk_3` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`);

--
-- Constraints for table `reactions`
--
ALTER TABLE `reactions`
  ADD CONSTRAINT `reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `fk_addressee_id` FOREIGN KEY (`addressee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_requester_id` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relationships_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `relationships_ibfk_2` FOREIGN KEY (`addressee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

