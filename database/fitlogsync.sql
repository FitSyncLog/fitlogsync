-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 02, 2025 at 02:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fitlogsync`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_name` varchar(255) NOT NULL,
  `coupon_type` enum('percentage','amount','','') NOT NULL,
  `coupon_value` int(10) NOT NULL,
  `number_of_coupons` int(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Active','Deactivated','Cancelled','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `coupon_name`, `coupon_type`, `coupon_value`, `number_of_coupons`, `start_date`, `end_date`, `status`, `created_at`, `created_by`) VALUES
(20, 'Summer Discount 2025', 'percentage', 10, 50, '2025-05-27', '2025-05-31', 'Active', '2025-05-27 14:03:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `coupon_codes`
--

CREATE TABLE `coupon_codes` (
  `coupon_codes_id` int(11) NOT NULL,
  `coupon_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_code` varchar(255) NOT NULL,
  `status` enum('Used','Unused','','') NOT NULL,
  `used_by` varchar(255) NOT NULL,
  `date_time_used` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon_codes`
--

INSERT INTO `coupon_codes` (`coupon_codes_id`, `coupon_id`, `coupon_code`, `status`, `used_by`, `date_time_used`) VALUES
(648, 20, 'SUMMERDI-0001', 'Used', '20', '2025-05-28 19:08:46'),
(649, 20, 'SUMMERDI-0002', 'Used', '41', '2025-05-30 12:13:49'),
(650, 20, 'SUMMERDI-0003', 'Unused', '', '0000-00-00 00:00:00'),
(651, 20, 'SUMMERDI-0004', 'Unused', '', '0000-00-00 00:00:00'),
(652, 20, 'SUMMERDI-0005', 'Unused', '', '0000-00-00 00:00:00'),
(653, 20, 'SUMMERDI-0006', 'Used', '25', '2025-05-27 22:48:38'),
(654, 20, 'SUMMERDI-0007', 'Used', '24', '2025-05-27 22:31:38'),
(655, 20, 'SUMMERDI-0008', 'Used', '25', '2025-05-27 22:39:58'),
(656, 20, 'SUMMERDI-0009', 'Unused', '', '0000-00-00 00:00:00'),
(657, 20, 'SUMMERDI-0010', 'Unused', '', '0000-00-00 00:00:00'),
(658, 20, 'SUMMERDI-0011', 'Unused', '', '0000-00-00 00:00:00'),
(659, 20, 'SUMMERDI-0012', 'Unused', '', '0000-00-00 00:00:00'),
(660, 20, 'SUMMERDI-0013', 'Unused', '', '0000-00-00 00:00:00'),
(661, 20, 'SUMMERDI-0014', 'Unused', '', '0000-00-00 00:00:00'),
(662, 20, 'SUMMERDI-0015', 'Unused', '', '0000-00-00 00:00:00'),
(663, 20, 'SUMMERDI-0016', 'Unused', '', '0000-00-00 00:00:00'),
(664, 20, 'SUMMERDI-0017', 'Unused', '', '0000-00-00 00:00:00'),
(665, 20, 'SUMMERDI-0018', 'Unused', '', '0000-00-00 00:00:00'),
(666, 20, 'SUMMERDI-0019', 'Unused', '', '0000-00-00 00:00:00'),
(667, 20, 'SUMMERDI-0020', 'Unused', '', '0000-00-00 00:00:00'),
(668, 20, 'SUMMERDI-0021', 'Unused', '', '0000-00-00 00:00:00'),
(669, 20, 'SUMMERDI-0022', 'Unused', '', '0000-00-00 00:00:00'),
(670, 20, 'SUMMERDI-0023', 'Unused', '', '0000-00-00 00:00:00'),
(671, 20, 'SUMMERDI-0024', 'Unused', '', '0000-00-00 00:00:00'),
(672, 20, 'SUMMERDI-0025', 'Unused', '', '0000-00-00 00:00:00'),
(673, 20, 'SUMMERDI-0026', 'Unused', '', '0000-00-00 00:00:00'),
(674, 20, 'SUMMERDI-0027', 'Unused', '', '0000-00-00 00:00:00'),
(675, 20, 'SUMMERDI-0028', 'Unused', '', '0000-00-00 00:00:00'),
(676, 20, 'SUMMERDI-0029', 'Unused', '', '0000-00-00 00:00:00'),
(677, 20, 'SUMMERDI-0030', 'Unused', '', '0000-00-00 00:00:00'),
(678, 20, 'SUMMERDI-0031', 'Unused', '', '0000-00-00 00:00:00'),
(679, 20, 'SUMMERDI-0032', 'Unused', '', '0000-00-00 00:00:00'),
(680, 20, 'SUMMERDI-0033', 'Unused', '', '0000-00-00 00:00:00'),
(681, 20, 'SUMMERDI-0034', 'Unused', '', '0000-00-00 00:00:00'),
(682, 20, 'SUMMERDI-0035', 'Unused', '', '0000-00-00 00:00:00'),
(683, 20, 'SUMMERDI-0036', 'Unused', '', '0000-00-00 00:00:00'),
(684, 20, 'SUMMERDI-0037', 'Unused', '', '0000-00-00 00:00:00'),
(685, 20, 'SUMMERDI-0038', 'Unused', '', '0000-00-00 00:00:00'),
(686, 20, 'SUMMERDI-0039', 'Unused', '', '0000-00-00 00:00:00'),
(687, 20, 'SUMMERDI-0040', 'Unused', '', '0000-00-00 00:00:00'),
(688, 20, 'SUMMERDI-0041', 'Unused', '', '0000-00-00 00:00:00'),
(689, 20, 'SUMMERDI-0042', 'Unused', '', '0000-00-00 00:00:00'),
(690, 20, 'SUMMERDI-0043', 'Unused', '', '0000-00-00 00:00:00'),
(691, 20, 'SUMMERDI-0044', 'Unused', '', '0000-00-00 00:00:00'),
(692, 20, 'SUMMERDI-0045', 'Unused', '', '0000-00-00 00:00:00'),
(693, 20, 'SUMMERDI-0046', 'Unused', '', '0000-00-00 00:00:00'),
(694, 20, 'SUMMERDI-0047', 'Unused', '', '0000-00-00 00:00:00'),
(695, 20, 'SUMMERDI-0048', 'Unused', '', '0000-00-00 00:00:00'),
(696, 20, 'SUMMERDI-0049', 'Unused', '', '0000-00-00 00:00:00'),
(697, 20, 'SUMMERDI-0050', 'Unused', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` bigint(20) UNSIGNED NOT NULL,
  `discount_type` enum('percentage','amount','','') NOT NULL,
  `discount_name` varchar(255) NOT NULL,
  `discount_value` int(10) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`discount_id`, `discount_type`, `discount_name`, `discount_value`, `status`) VALUES
(1, 'percentage', 'Person With Disability', 20, 1),
(2, 'percentage', 'Student', 20, 1),
(3, 'percentage', 'Senior Citizen', 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `emergency_contact_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `relationship` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_contacts`
--

INSERT INTO `emergency_contacts` (`emergency_contact_id`, `user_id`, `contact_person`, `contact_number`, `relationship`) VALUES
(1, 1, 'Emelita Orillo', '09361459105', 'Mother'),
(7, 7, 'Ako', '09123456789', 'Husband'),
(19, 19, 'Lowie Jay Orillo', '09913235420', 'Brother'),
(20, 20, 'Lowie Jay Orillo', '09785431251', 'Friend'),
(21, 21, 'Lowie Jay Orillo', '09913235420', 'Daughter'),
(22, 22, 'Elvira', '09273883030', 'Mother'),
(23, 23, 'Lowie Jay Orillo', '09913235420', 'Friend'),
(24, 24, 'Lowie Jay Orillo', '09913235420', 'Friend'),
(25, 25, 'Emelita Orillo', '09361459105', 'Mother'),
(41, 41, 'Lowie Jay Orillo', '09913235420', 'Friend');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `answer` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(9, 'Lorem ipsum dolor sit amet?\n', 'We offer flexible membership plans, including daily, monthly, quarterly, demi-annual, and annual options, tailored to fit your fitness goals and schedule.'),
(10, 'Curabitur non nulla sit amet nisl tempus convallis?\n', 'All memberships include access to our gym equipment, cardio area, free weights, boxing ring, and zumba area.Curabitur aliquet quam id dui posuere blandit. Proin eget tortor risus. Pellentesque in ipsum id orci porta dapibus.\n\n'),
(11, 'Vestibulum ac diam sit amet quam vehicula elementum?\n', 'Nulla porttitor accumsan tincidunt. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a.\n\n'),
(12, 'Quisque velit nisi, pretium ut lacinia in?\n', 'Vivamus suscipit tortor eget felis porttitor volutpat. Nulla quis lorem ut libero malesuada feugiat.\n\n'),
(13, 'Donec sollicitudin molestie malesuada?\n', 'Donec rutrum congue leo eget malesuada. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi.\n\n'),
(14, 'Praesent sapien massa, convallis a pellentesque nec?\n', 'Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Donec rutrum congue leo eget malesuada.'),
(15, 'Sed porttitor lectus nibh?\n', 'Cras ultricies ligula sed magna dictum porta. Nulla quis lorem ut libero malesuada feugiat.\n\n'),
(16, 'Nulla quis lorem ut libero malesuada feugiat?\n', 'Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Donec sollicitudin molestie malesuada.\n\n'),
(17, 'Cras ultricies ligula sed magna dictum porta?', 'Sed porttitor lectus nibh. Proin eget tortor risus. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui.\n\n'),
(18, 'Mauris blandit aliquet elit?\n', 'Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Pellentesque in ipsum id orci porta dapibus.\n\n');

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE `information` (
  `information_id` bigint(20) NOT NULL,
  `information_for` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `information`
--

INSERT INTO `information` (`information_id`, `information_for`, `description`) VALUES
(1, 'address', 'General Santos City'),
(2, 'phone_number', '09845421365'),
(3, 'x', 'x.com/fitlogsync'),
(4, 'facebook', 'facebook.com/fitlogsync'),
(5, 'instagram', 'instagram.com/fitlogsync'),
(6, 'youtube', 'youtube.com/fitlogsync'),
(7, 'tiktok', 'tiktok.com/fitlogsync'),
(8, 'email', 'fitlogsync@gmail.com'),
(9, 'home_video', 'https://www.youtube.com/embed/fu9yk7gCTbc');

-- --------------------------------------------------------

--
-- Table structure for table `medical_backgrounds`
--

CREATE TABLE `medical_backgrounds` (
  `medical_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `medical_conditions` varchar(255) NOT NULL,
  `current_medications` varchar(255) NOT NULL,
  `previous_injuries` varchar(255) NOT NULL,
  `par_q_1` varchar(255) NOT NULL,
  `par_q_2` varchar(255) NOT NULL,
  `par_q_3` varchar(255) NOT NULL,
  `par_q_4` varchar(255) NOT NULL,
  `par_q_5` varchar(255) NOT NULL,
  `par_q_6` varchar(255) NOT NULL,
  `par_q_7` varchar(255) NOT NULL,
  `par_q_8` varchar(255) NOT NULL,
  `par_q_9` varchar(255) NOT NULL,
  `par_q_10` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_backgrounds`
--

INSERT INTO `medical_backgrounds` (`medical_id`, `user_id`, `medical_conditions`, `current_medications`, `previous_injuries`, `par_q_1`, `par_q_2`, `par_q_3`, `par_q_4`, `par_q_5`, `par_q_6`, `par_q_7`, `par_q_8`, `par_q_9`, `par_q_10`) VALUES
(1, 1, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(7, 7, 'none', 'none', 'none', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(18, 19, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(19, 20, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(20, 22, 'N/A', 'N/A', 'N/A', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(21, 23, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(22, 24, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(23, 25, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No'),
(39, 41, 'None', 'None', 'None', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `code`, `token`, `created_at`) VALUES
(26, 'lowie.jaymier@gmail.com', '231242', '0b2585a685295fb3b0b03b0bc5461d45ff60672888723b4b13e74975fee251ab0d742ada0bc31e4d70e3d9037b26d0d38769d948945c54445f94b1a79aee11ff', '2025-05-17 21:37:57'),
(27, 'lowie.jaymier@gmail.com', '975023', 'e5e5324559f8fc851d5fd6f663da0fda72495dec447bbc23f893b0844a4737ba0decc600ef8a016e55d739c33de98202f0d621e9bd8e7eff753d2fc74cad62f8', '2025-05-22 11:00:21'),
(28, 'lowie.jaymier@gmail.com', '415354', 'ee9ab0b63b331def8f8c4cf63baeb4e5becccb1292658f550e742eb160cf02562230069ef8f50df3512a28584d0ac95c9f02d50f9eb765bb66ae8435f00876ed', '2025-05-22 11:00:37'),
(29, 'lowie.jaymier@gmail.com', '482036', 'd5c93cc6dad31fd2b8a0e0b3c9d16b2a9541259e79b15c2b176cdac85ae78f354fdeff64f87fa05a18eb5009c755e9f16c9cb66be259a7b9d932f96c0c5f6496', '2025-05-28 10:12:34'),
(30, 'lowie.jaymier@gmail.com', '865783', '1b7d84bef11458fd8a8ade73e075a2fbf8a1d119af79a5544bf46d0277279f5d4ea0e0c97b38cf8b39e340eb06e9947198cf46c2559bea10cdc7c720f0bf24b0', '2025-05-30 12:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `payment_transaction_id` bigint(20) UNSIGNED NOT NULL,
  `acknowledgement_receipt_number` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plan_name_at_transaction` varchar(255) NOT NULL,
  `plan_description_at_transaction` text NOT NULL,
  `plan_price_at_transaction` decimal(8,2) NOT NULL,
  `plan_duration_at_transaction` int(11) NOT NULL,
  `coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grand_total` decimal(8,2) NOT NULL,
  `transaction_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` varchar(255) NOT NULL,
  `transact_by` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`payment_transaction_id`, `acknowledgement_receipt_number`, `user_id`, `plan_id`, `plan_name_at_transaction`, `plan_description_at_transaction`, `plan_price_at_transaction`, `plan_duration_at_transaction`, `coupon_id`, `discount_id`, `grand_total`, `transaction_date_time`, `payment_method`, `transact_by`) VALUES
(39, 202505270001, 24, 2, '3 Months', 'All access', 1499.00, 3, 20, 2, 3237.84, '2025-05-27 08:31:38', 'cash', 1),
(42, 202505270002, 25, 2, '3 Months', 'All access', 1499.00, 3, NULL, 2, 3597.60, '2025-05-27 08:49:26', 'cash', 1),
(43, 202505280001, 20, 3, '6 Months', 'All access', 1299.00, 6, 20, 2, 5611.68, '2025-05-28 05:08:46', 'cash', 1),
(45, 202505290001, 22, 2, '3 Months', 'All access', 1499.00, 3, NULL, 2, 3597.60, '2025-05-28 19:57:50', 'cash', 1),
(46, 202505290002, 22, 1, '1 Month', 'All access', 1599.00, 1, NULL, NULL, 1599.00, '2025-05-28 20:05:50', 'cash', 1),
(47, 202505290003, 23, 1, '1 Month', 'All access', 1599.00, 1, NULL, NULL, 1599.00, '2025-05-28 22:33:17', 'cash', 1),
(48, 202505290004, 23, 1, '1 Month', 'All access', 1599.00, 1, NULL, NULL, 1599.00, '2025-05-28 22:33:47', 'cash', 1),
(49, 202505300001, 41, 3, '6 Months', 'All access', 1299.00, 6, 20, 2, 5611.68, '2025-05-30 04:13:49', 'cash', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `page_name`, `role_id`, `permission`) VALUES
(60, 'dashboard.php', 1, 1),
(61, 'dashboard.php', 2, 1),
(62, 'dashboard.php', 3, 1),
(63, 'dashboard.php', 4, 0),
(64, 'dashboard.php', 5, 1),
(65, 'profile.php', 1, 1),
(66, 'profile.php', 2, 1),
(67, 'profile.php', 3, 1),
(68, 'profile.php', 4, 0),
(69, 'profile.php', 5, 1),
(70, 'settings.php', 1, 1),
(71, 'settings.php', 2, 1),
(72, 'settings.php', 3, 1),
(73, 'settings.php', 4, 0),
(74, 'settings.php', 5, 1),
(75, 'manage-member', 1, 1),
(76, 'manage-member', 2, 1),
(77, 'manage-member', 3, 1),
(78, 'manage-member', 4, 0),
(79, 'manage-member', 5, 0),
(80, 'manage-members.php', 1, 1),
(81, 'manage-members.php', 2, 1),
(82, 'manage-members.php', 3, 1),
(83, 'manage-members.php', 4, 0),
(84, 'manage-members.php', 5, 0),
(85, 'manage-pending-members.php', 1, 1),
(86, 'manage-pending-members.php', 2, 1),
(87, 'manage-pending-members.php', 3, 1),
(88, 'manage-pending-members.php', 4, 0),
(89, 'manage-pending-members.php', 5, 0),
(90, 'manage-active-members.php', 1, 1),
(91, 'manage-active-members.php', 2, 1),
(92, 'manage-active-members.php', 3, 1),
(93, 'manage-active-members.php', 4, 0),
(94, 'manage-active-members.php', 5, 0),
(95, 'manage-banned-members.php', 1, 1),
(96, 'manage-banned-members.php', 2, 1),
(97, 'manage-banned-members.php', 3, 1),
(98, 'manage-banned-members.php', 4, 0),
(99, 'manage-banned-members.php', 5, 0),
(100, 'manage-suspended-members.php', 1, 1),
(101, 'manage-suspended-members.php', 2, 1),
(102, 'manage-suspended-members.php', 3, 1),
(103, 'manage-suspended-members.php', 4, 0),
(104, 'manage-suspended-members.php', 5, 0),
(105, 'manage-deleted-members.php', 1, 1),
(106, 'manage-deleted-members.php', 2, 1),
(107, 'manage-deleted-members.php', 3, 1),
(108, 'manage-deleted-members.php', 4, 0),
(109, 'manage-deleted-members.php', 5, 0),
(110, 'create-new-member.php', 1, 1),
(111, 'create-new-member.php', 2, 1),
(112, 'create-new-member.php', 3, 1),
(113, 'create-new-member.php', 4, 0),
(114, 'create-new-member.php', 5, 0),
(115, 'edit-member.php', 1, 1),
(116, 'edit-member.php', 2, 1),
(117, 'edit-member.php', 3, 1),
(118, 'edit-member.php', 4, 0),
(119, 'edit-member.php', 5, 0),
(120, 'permission-settings.php', 1, 1),
(121, 'permission-settings.php', 2, 0),
(122, 'permission-settings.php', 3, 0),
(123, 'permission-settings.php', 4, 0),
(124, 'permission-settings.php', 5, 0),
(125, 'manage-front-desk.php', 1, 1),
(126, 'manage-front-desk.php', 2, 1),
(127, 'manage-front-desk.php', 3, 0),
(128, 'manage-front-desk.php', 4, 0),
(129, 'manage-front-desk.php', 5, 0),
(130, 'manage-instructors.php', 1, 0),
(131, 'manage-instructors.php', 2, 0),
(132, 'manage-instructors.php', 3, 0),
(133, 'manage-instructors.php', 4, 0),
(134, 'manage-instructors.php', 5, 0),
(135, 'manage-plan.php', 1, 1),
(136, 'manage-plan.php', 2, 1),
(137, 'manage-plan.php', 3, 1),
(138, 'manage-plan.php', 4, 0),
(139, 'manage-plan.php', 5, 0),
(140, 'manage-coupons.php', 1, 1),
(141, 'manage-coupons.php', 2, 1),
(142, 'manage-coupons.php', 3, 0),
(143, 'manage-coupons.php', 4, 0),
(144, 'manage-coupons.php', 5, 0),
(145, 'create-new-coupon.php', 1, 1),
(146, 'create-new-coupon.php', 2, 1),
(147, 'create-new-coupon.php', 3, 0),
(148, 'create-new-coupon.php', 4, 0),
(149, 'create-new-coupon.php', 5, 0),
(150, 'edit-coupon.php', 1, 1),
(151, 'edit-coupon.php', 2, 0),
(152, 'edit-coupon.php', 3, 0),
(153, 'edit-coupon.php', 4, 0),
(154, 'edit-coupon.php', 5, 0),
(155, 'manage-discount.php', 1, 1),
(156, 'manage-discount.php', 2, 0),
(157, 'manage-discount.php', 3, 0),
(158, 'manage-discount.php', 4, 0),
(159, 'manage-discount.php', 5, 0),
(160, 'manage-payments.php', 1, 1),
(161, 'manage-payments.php', 2, 0),
(162, 'manage-payments.php', 3, 0),
(163, 'manage-payments.php', 4, 0),
(164, 'manage-payments.php', 5, 0),
(165, 'manage-reports.php', 1, 1),
(166, 'manage-reports.php', 2, 0),
(167, 'manage-reports.php', 3, 0),
(168, 'manage-reports.php', 4, 0),
(169, 'manage-reports.php', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `duration` int(20) NOT NULL,
  `status` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`plan_id`, `plan_name`, `description`, `price`, `duration`, `status`) VALUES
(1, '1 Month', 'All access', 1599.00, 1, '1'),
(2, '3 Months', 'All access', 1499.00, 3, '1'),
(3, '6 Months', 'All access', 1299.00, 6, '1'),
(4, '12 Months', 'All access', 1000.00, 12, '1');

-- --------------------------------------------------------

--
-- Table structure for table `plan_history`
--

CREATE TABLE `plan_history` (
  `plan_history_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan_history`
--

INSERT INTO `plan_history` (`plan_history_id`, `plan_id`, `user_id`, `price`, `status`, `date_time`) VALUES
(1, 1, 1, 1300, 1, '2025-05-25 03:48:13'),
(2, 1, 1, 1699, 1, '2025-05-25 04:11:47'),
(3, 2, 1, 1599, 1, '2025-05-25 04:36:53'),
(4, 2, 1, 1599, 0, '2025-05-25 04:37:00'),
(5, 2, 1, 1599, 1, '2025-05-25 04:37:11'),
(6, 2, 1, 1599, 0, '2025-05-25 04:37:42'),
(7, 2, 1, 1599, 1, '2025-05-25 04:37:50'),
(8, 1, 21, 1499, 1, '2025-05-25 04:39:07'),
(9, 1, 1, 0, 1, '2025-05-25 05:55:02'),
(10, 1, 1, 159, 1, '2025-05-25 05:55:14'),
(11, 1, 1, 1599, 1, '2025-05-25 05:55:20'),
(12, 2, 1, 1499, 1, '2025-05-25 05:55:23'),
(13, 1, 1, 2000, 0, '2025-05-25 07:37:57'),
(14, 1, 1, 1599, 1, '2025-05-25 07:38:19'),
(15, 1, 1, 1549, 1, '2025-05-26 16:00:45'),
(16, 1, 1, 1549, 0, '2025-05-26 16:01:03'),
(17, 1, 1, 1599, 1, '2025-05-26 16:01:19'),
(18, 4, 1, 1000, 1, '2025-05-27 02:09:09');

-- --------------------------------------------------------

--
-- Table structure for table `report_logs`
--

CREATE TABLE `report_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `date_range_start` date NOT NULL,
  `date_range_end` date NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_logs`
--

INSERT INTO `report_logs` (`log_id`, `user_id`, `report_type`, `date_range_start`, `date_range_end`, `generated_at`) VALUES
(1, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 04:32:55'),
(2, 1, 'discounts', '2025-05-29', '2025-05-29', '2025-05-29 04:33:19'),
(3, 1, 'coupons', '2025-05-28', '2025-05-28', '2025-05-29 04:36:30'),
(4, 1, 'sales', '2025-04-30', '2025-05-29', '2025-05-29 04:37:18'),
(5, 1, 'discounts', '2025-05-29', '2025-05-29', '2025-05-29 04:41:12'),
(6, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 04:42:23'),
(7, 1, 'discounts', '2025-05-29', '2025-05-29', '2025-05-29 04:43:05'),
(8, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 04:46:16'),
(9, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 04:54:04'),
(10, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 04:55:21'),
(11, 1, 'discounts', '2025-05-29', '2025-05-29', '2025-05-29 04:55:41'),
(12, 1, 'discounts', '2025-05-29', '2025-05-29', '2025-05-29 04:57:40'),
(13, 1, 'discounts', '2025-05-29', '2025-05-29', '2025-05-29 05:02:46'),
(14, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 05:02:59'),
(15, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 05:16:45'),
(16, 1, 'sales', '2025-05-29', '2025-05-29', '2025-05-29 05:16:56'),
(17, 1, 'sales', '2025-05-23', '2025-05-29', '2025-05-29 15:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role`) VALUES
(1, 'Super Admin'),
(2, 'Admin'),
(3, 'Front Desk'),
(4, 'Instructor'),
(5, 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `security_questions`
--

CREATE TABLE `security_questions` (
  `sq_id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sq1` varchar(255) NOT NULL,
  `sq1_res` varchar(255) NOT NULL,
  `sq2` varchar(255) NOT NULL,
  `sq2_res` varchar(255) NOT NULL,
  `sq3` varchar(255) NOT NULL,
  `sq3_res` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_questions`
--

INSERT INTO `security_questions` (`sq_id`, `user_id`, `sq1`, `sq1_res`, `sq2`, `sq2_res`, `sq3`, `sq3_res`) VALUES
(1, 1, 'What is your mother’s maiden name?', 'Emelita Manceras Mier', 'What is your favorite movie?', 'Penthouse', 'In what city did your parents meet?', 'IDK'),
(3, 7, 'What is your favorite food?', 'ako', 'In what city did your parents meet?', 'ako', 'In what city were you born?', 'ako'),
(15, 19, 'What was the first concert you attended?', 'elay', 'In what city did your parents meet?', 'elay', 'What was the make and model of your first car?', 'elay'),
(16, 20, 'What was the first concert you attended?', 'dikoalam', 'In what city did your parents meet?', 'dikoalam', 'What is the name of your first pet?', 'dikoalam'),
(17, 21, 'In what city did your parents meet?', 'Emelita', 'What was the first concert you attended?', 'Emelita', 'What is the name of your first pet?', 'Emelita'),
(18, 22, 'In what city were you born?', 'general santos city', 'What is your favorite food?', 'chicken', 'What is your favorite movie?', 'porn'),
(19, 23, 'What is your mother’s maiden name?', 'di ko alam', 'What is the name of your first pet?', 'di ko alam', 'In what city did your parents meet?', 'di ko alam'),
(20, 24, 'What is your favorite childhood TV show?', 'di ko alam', 'What was the make and model of your first car?', 'di ko alam', 'What was your childhood nickname?', 'di ko alam'),
(21, 25, 'What is your father&#039;s middle name?', 'di ko alam', 'What is your grandmother&#039;s first name?', 'di ko alam', 'In what city did your parents meet?', 'di ko alam'),
(37, 41, 'What was your childhood nickname?', 'di ko alam', 'What is your favorite book?', 'di ko alam', 'What was the name of your childhood best friend?', 'di ko alam');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `payment_transaction_id` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `starting_date` date NOT NULL,
  `expiration_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `payment_transaction_id`, `user_id`, `starting_date`, `expiration_date`) VALUES
(29, 36, 19, '2025-05-29', '2025-11-29'),
(32, 39, 24, '2025-05-29', '2025-08-29'),
(35, 42, 25, '2025-05-29', '2025-08-29'),
(36, 43, 20, '2025-05-29', '2025-11-29'),
(38, 45, 22, '2025-05-30', '2025-08-30'),
(39, 46, 22, '2025-09-09', '2025-10-09'),
(40, 47, 23, '2025-05-29', '2025-06-29'),
(41, 48, 23, '2025-06-30', '2025-07-30'),
(42, 49, 41, '2025-05-30', '2025-11-30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `account_number` varchar(16) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Prefer not to say') NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `otp_code` varchar(255) NOT NULL,
  `otp_code_expiration` datetime NOT NULL,
  `two_factor_authentication` tinyint(1) NOT NULL DEFAULT 1,
  `enrolled_by` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Active','Suspended','Banned','Deleted') DEFAULT NULL,
  `registration_date` date NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `account_number`, `username`, `firstname`, `middlename`, `lastname`, `date_of_birth`, `password`, `gender`, `phone_number`, `email`, `address`, `otp_code`, `otp_code_expiration`, `two_factor_authentication`, `enrolled_by`, `status`, `registration_date`, `profile_image`) VALUES
(1, '2025000317480001', 'lowijiSA', 'Lowie Jay', 'Mier', 'Orillo', '2002-09-12', '$2y$10$IHemlK13YsEOfwRwTLDrpeVGAYwCK/WTZyk9sAkmdPR1rAl2zPvY2', 'Male', '09913235414', 'lowie.jaymier@gmail.com', 'Fatima, General Santos City', '442176', '2025-05-30 12:23:53', 0, 'Online Registration', 'Pending', '2025-03-05', '2025000317480001.png'),
(7, '2025000563870001', 'cholo', 'Kaayo', 'Ko', 'Gwapo', '2002-03-09', '$2y$10$c0K27E2knk4zrxhhVHd/wO3mAEDC2RC6nLQexipHjZxf/Tugmi63u', 'Prefer not to say', '09123456789', 'punaycholo@gmail.com', 'buayam', '172309', '2025-05-22 11:28:20', 1, 'Online Registration', 'Deleted', '2025-05-22', 'default.png'),
(19, '2025000550410002', 'raemyer', 'Leslie', 'Mier', 'Orillo', '2000-08-14', '$2y$10$xnDD.Wv2LBSNUoQfaco9N.p4yjXTLSEnagVu8ep0I1qbjPO11Ej4O', 'Female', '09913235414', 'leslieorillo@gmail.com', 'Fatima General Santos City', '959887', '2025-05-24 18:29:02', 0, 'Lowie Jay Mier Orillo', 'Deleted', '2025-05-24', 'default.png'),
(20, '2025000544900003', 'archie', 'Arch Libee', 'Lupina', 'Mangaron', '2025-05-08', '$2y$10$7h8u8TP/Got8S69lOsY1BuoK5i7IRJkh403UUwWxvsEucgEy122x6', 'Male', '09454545454', 'archlibeemangaron@gmail.com', 'General Santos City', '633242', '2025-05-24 15:25:51', 0, 'Lowie Jay Mier Orillo', 'Active', '2025-05-24', 'default.png'),
(21, '2025000533160004', 'frontdesk', 'Emelita', 'Mier', 'Orillo', '1969-08-24', '$2y$10$8UO6/4d2J/K.5mvpCjP0YudR6TwCqxtNsXb5okjR0jFjogbtgUOfu', 'Female', '09361459105', 'emelitaorillo@gmail.com', 'General Santos City', '136013', '2025-05-24 18:31:49', 0, 'Lowie Jay Mier Orillo', 'Active', '2025-05-24', '2025000533160004.jpg'),
(22, '2025000557810005', 'eljhun', 'Eljhun', 'Procorato', 'Allawan', '2003-05-05', '$2y$10$HQZpJwd2ghBF6mexCLoATeKsC88xKFjngqsMY3V6BevGrT74fYbuC', 'Male', '09273883083', 'gmeljhun20@gmail.com', 'Tubo street purok emiliana barangay calumpang gsc', '477205', '2025-05-25 17:25:21', 0, 'Lowie Jay Mier Orillo', 'Active', '2025-05-25', 'default.png'),
(23, '2025000541550006', 'rodgee_11', 'Rogimore', 'Reyes', 'Corpuz', '2004-08-03', '$2y$10$ajOkt9..l.UQudy/wB89Xe2MCglTyGCzy0XxqmhjNv.gxzHxHbVZq', 'Male', '09685387835', 'rogimore078@gmail.com', 'Polomolok, South Cotabato', '', '0000-00-00 00:00:00', 1, 'Lowie Jay Mier Orillo', 'Active', '2025-05-27', 'default.png'),
(24, '2025000568960007', 'keemois', 'Krizha', '', 'Estorque', '2002-01-20', '$2y$10$w7m2nHqCKlU1FMnBdYLInOEC0U3meeS0vXobHMhOmz/fhUI411eYW', 'Female', '09122880627', 'krizhaestorque02@gmail.com', 'General Santos City', '', '0000-00-00 00:00:00', 0, 'Lowie Jay Mier Orillo', 'Active', '2025-05-27', 'default.png'),
(25, '2025000591570008', 'lowiji', 'Lowie Jay', 'Mier', 'Orillo', '2002-09-12', '$2y$10$yPCHHGhmkEoRBwGDniuCeuOmku/mBk4QhTKpCbbfgWmqVA6i2k2cO', 'Male', '09913235420', 'lowiejay.orillo@msugensan.edu.ph', 'Fatima, General Santos City', '377966', '2025-05-29 16:27:52', 1, 'Lowie Jay Mier Orillo', 'Active', '2025-05-27', 'default.png'),
(41, '2025000582480009', 'lifte15', 'Brian Angelo', 'Delfin', 'Bognot', '2002-04-10', '$2y$10$VQ4uNg8j71mk3xEP6DvqN.XWJ0b9mkFwKFvreUizojHdwF0oXOjBa', 'Male', '09123215236', 'c09651052069@gmail.com', 'General Santos City', '', '0000-00-00 00:00:00', 1, 'Lowie Jay Mier Orillo', 'Active', '2025-05-29', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `role_user_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`role_user_id`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(6, 7, 5),
(18, 19, 5),
(19, 20, 5),
(20, 21, 3),
(21, 22, 5),
(22, 23, 5),
(23, 24, 5),
(24, 25, 5),
(40, 41, 5);

-- --------------------------------------------------------

--
-- Table structure for table `waivers`
--

CREATE TABLE `waivers` (
  `waiver_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `rules_and_policy` int(11) NOT NULL,
  `liability_waiver` int(11) NOT NULL,
  `cancellation_and_refund_policy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waivers`
--

INSERT INTO `waivers` (`waiver_id`, `user_id`, `rules_and_policy`, `liability_waiver`, `cancellation_and_refund_policy`) VALUES
(1, 1, 1, 1, 1),
(7, 7, 1, 1, 1),
(18, 19, 1, 1, 1),
(19, 20, 1, 1, 1),
(20, 22, 1, 1, 1),
(21, 23, 1, 1, 1),
(22, 24, 1, 1, 1),
(23, 25, 1, 1, 1),
(39, 41, 1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `coupon_codes`
--
ALTER TABLE `coupon_codes`
  ADD PRIMARY KEY (`coupon_codes_id`),
  ADD KEY `fk_coupon_id` (`coupon_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`emergency_contact_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`information_id`);

--
-- Indexes for table `medical_backgrounds`
--
ALTER TABLE `medical_backgrounds`
  ADD PRIMARY KEY (`medical_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`payment_transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `fk_payment_discount` (`discount_id`),
  ADD KEY `fk_payment_coupon` (`coupon_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `fk_permissions_role` (`role_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `plan_history`
--
ALTER TABLE `plan_history`
  ADD PRIMARY KEY (`plan_history_id`);

--
-- Indexes for table `report_logs`
--
ALTER TABLE `report_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `security_questions`
--
ALTER TABLE `security_questions`
  ADD PRIMARY KEY (`sq_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`role_user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `waivers`
--
ALTER TABLE `waivers`
  ADD PRIMARY KEY (`waiver_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `coupon_codes`
--
ALTER TABLE `coupon_codes`
  MODIFY `coupon_codes_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=703;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `emergency_contact_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `information_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medical_backgrounds`
--
ALTER TABLE `medical_backgrounds`
  MODIFY `medical_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `payment_transaction_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `plan_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `plan_history`
--
ALTER TABLE `plan_history`
  MODIFY `plan_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `report_logs`
--
ALTER TABLE `report_logs`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `security_questions`
--
ALTER TABLE `security_questions`
  MODIFY `sq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `role_user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `waivers`
--
ALTER TABLE `waivers`
  MODIFY `waiver_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `coupon_codes`
--
ALTER TABLE `coupon_codes`
  ADD CONSTRAINT `fk_coupon_id` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`);

--
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `medical_backgrounds`
--
ALTER TABLE `medical_backgrounds`
  ADD CONSTRAINT `medical_backgrounds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `fk_payment_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_payment_discount` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`plan_id`) ON DELETE SET NULL;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `report_logs`
--
ALTER TABLE `report_logs`
  ADD CONSTRAINT `report_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `security_questions`
--
ALTER TABLE `security_questions`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `waivers`
--
ALTER TABLE `waivers`
  ADD CONSTRAINT `waivers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
