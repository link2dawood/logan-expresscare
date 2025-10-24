-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 24, 2025 at 03:34 PM
-- Server version: 10.6.23-MariaDB-cll-lve
-- PHP Version: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `initial_balance` double NOT NULL,
  `account_number` varchar(191) DEFAULT NULL,
  `branch_code` varchar(191) DEFAULT NULL,
  `branch_address` varchar(191) DEFAULT NULL,
  `balance` double DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_entries`
--

CREATE TABLE `account_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` double NOT NULL,
  `is_debit` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `deposit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payroll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pre_payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asset_id` bigint(20) UNSIGNED DEFAULT NULL,
  `appreciation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `initial_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','hr','manager') DEFAULT 'hr',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `is_active`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$l0YiuIhp/XpM/nOdXsK0WuHVph0kbkzSdeyNk6dsiVM57NgkWSQRC', 'admin@loganexpresscare.com.au', 'System Administrator', 'admin', 1, '2025-10-21 14:59:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appreciations`
--

CREATE TABLE `appreciations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `award_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `letterhead_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generates_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `price_amount` double DEFAULT NULL,
  `price_given` text DEFAULT NULL,
  `profile_image` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `asset_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `serial_number` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'working',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `broken_by` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `price` double DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `asset_user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_types`
--

CREATE TABLE `asset_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_users`
--

CREATE TABLE `asset_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lent_to` bigint(20) UNSIGNED DEFAULT NULL,
  `lent_by` bigint(20) UNSIGNED DEFAULT NULL,
  `return_by` bigint(20) UNSIGNED DEFAULT NULL,
  `broken_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `lend_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `is_holiday` tinyint(1) NOT NULL DEFAULT 0,
  `is_leave` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_id` bigint(20) UNSIGNED DEFAULT NULL,
  `leave_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `holiday_id` bigint(20) UNSIGNED DEFAULT NULL,
  `clock_in_date_time` datetime DEFAULT NULL,
  `clock_out_date_time` datetime DEFAULT NULL,
  `clock_in_ip_address` varchar(191) DEFAULT NULL,
  `total_duration` int(11) DEFAULT NULL,
  `clock_out_ip_address` varchar(191) DEFAULT NULL,
  `clock_in_time` time DEFAULT NULL,
  `clock_out_time` time DEFAULT NULL,
  `office_clock_in_time` time DEFAULT NULL,
  `office_clock_out_time` time DEFAULT NULL,
  `is_half_day` tinyint(1) NOT NULL DEFAULT 0,
  `half_leave` varchar(20) NOT NULL DEFAULT 'no_leave',
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `late_time_duration` int(11) DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(191) NOT NULL DEFAULT 'present',
  `reason` text DEFAULT NULL,
  `actual_clock_in_date` date DEFAULT NULL,
  `early_clock_in_time` int(11) DEFAULT NULL,
  `allow_clock_out_till` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `clock_in_latitude` decimal(18,15) DEFAULT NULL,
  `clock_in_longitude` decimal(18,15) DEFAULT NULL,
  `clock_in_location_accuracy` double DEFAULT NULL,
  `clock_in_location_name` text DEFAULT NULL,
  `clock_in_browser` varchar(191) DEFAULT NULL,
  `clock_in_platform` varchar(191) DEFAULT NULL,
  `clock_in_device_type` varchar(191) DEFAULT NULL,
  `clock_in_user_agent` text DEFAULT NULL,
  `clock_out_latitude` decimal(18,15) DEFAULT NULL,
  `clock_out_longitude` decimal(18,15) DEFAULT NULL,
  `clock_out_location_accuracy` double DEFAULT NULL,
  `clock_out_location_name` text DEFAULT NULL,
  `clock_out_browser` varchar(191) DEFAULT NULL,
  `clock_out_platform` varchar(191) DEFAULT NULL,
  `clock_out_device_type` varchar(191) DEFAULT NULL,
  `clock_out_user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `award_price` double DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `basic_salary_details`
--

CREATE TABLE `basic_salary_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_component_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `value_type` varchar(191) DEFAULT NULL,
  `monthly` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `short_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `light_logo` varchar(191) DEFAULT NULL,
  `dark_logo` varchar(191) DEFAULT NULL,
  `small_dark_logo` varchar(191) DEFAULT NULL,
  `small_light_logo` varchar(191) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `app_layout` varchar(10) NOT NULL DEFAULT 'sidebar',
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `left_sidebar_theme` varchar(20) NOT NULL DEFAULT 'dark',
  `primary_color` varchar(20) NOT NULL DEFAULT '#1890ff',
  `date_format` varchar(20) NOT NULL DEFAULT 'DD-MM-YYYY',
  `time_format` varchar(20) NOT NULL DEFAULT 'hh:mm a',
  `auto_detect_timezone` tinyint(1) NOT NULL DEFAULT 1,
  `timezone` varchar(191) NOT NULL DEFAULT 'Asia/Kolkata',
  `session_driver` varchar(20) NOT NULL DEFAULT 'file',
  `app_debug` tinyint(1) NOT NULL DEFAULT 0,
  `update_app_notification` tinyint(1) NOT NULL DEFAULT 1,
  `login_image` varchar(191) DEFAULT NULL,
  `rtl` tinyint(1) NOT NULL DEFAULT 0,
  `shortcut_menus` varchar(20) NOT NULL DEFAULT 'top_bottom',
  `mysqldump_command` varchar(191) NOT NULL DEFAULT '/usr/bin/mysqldump',
  `profile_image` varchar(191) DEFAULT NULL,
  `stripe_id` varchar(191) DEFAULT NULL,
  `pm_type` varchar(191) DEFAULT NULL,
  `pm_last_four` varchar(4) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `subscription_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `package_type` enum('monthly','annual') NOT NULL DEFAULT 'monthly',
  `licence_expire_on` date DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'active',
  `total_users` int(11) NOT NULL DEFAULT 1,
  `email_verification_code` varchar(191) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `clock_in_time` time DEFAULT '09:30:00',
  `clock_out_time` time DEFAULT '18:00:00',
  `leave_start_month` varchar(2) NOT NULL DEFAULT '01',
  `late_mark_after` int(11) DEFAULT NULL,
  `early_clock_in_time` int(11) DEFAULT NULL,
  `allow_clock_out_till` int(11) DEFAULT NULL,
  `self_clocking` tinyint(1) NOT NULL DEFAULT 1,
  `allowed_ip_address` text DEFAULT NULL,
  `letterhead_header_color` varchar(20) NOT NULL DEFAULT '#1890ff',
  `letterhead_show_company_name` tinyint(1) NOT NULL DEFAULT 1,
  `letterhead_show_company_email` tinyint(1) NOT NULL DEFAULT 1,
  `letterhead_show_company_phone` tinyint(1) NOT NULL DEFAULT 1,
  `letterhead_show_company_address` tinyint(1) NOT NULL DEFAULT 1,
  `letterhead_left_space` int(11) DEFAULT 15,
  `letterhead_right_space` int(11) DEFAULT 15,
  `letterhead_top_space` int(11) DEFAULT 40,
  `letterhead_bottom_space` int(11) DEFAULT 20,
  `holiday_pdf_font_size` int(11) DEFAULT 20,
  `holiday_pdf_line_height` int(11) DEFAULT 40,
  `generate_pdf_font_size` int(11) DEFAULT 17,
  `generate_pdf_line_height` int(11) DEFAULT 28,
  `letterhead_title_show_in_pdf` tinyint(1) DEFAULT 1,
  `employee_id_prefix` varchar(191) DEFAULT 'emp',
  `employee_id_start` varchar(191) DEFAULT '0001',
  `employee_id_digits` int(11) DEFAULT 4,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pdf_font_id` bigint(20) UNSIGNED DEFAULT NULL,
  `use_custom_font` tinyint(1) DEFAULT 0,
  `capture_location` tinyint(1) NOT NULL DEFAULT 0,
  `google_geo_coding_api_key` varchar(191) DEFAULT NULL,
  `export_pdf_left_space` int(11) DEFAULT 15,
  `export_pdf_right_space` int(11) DEFAULT 15,
  `export_pdf_top_space` int(11) DEFAULT 40,
  `export_pdf_bottom_space` int(11) DEFAULT 20,
  `export_pdf_line_height` int(11) DEFAULT 25,
  `export_pdf_font_size` int(11) DEFAULT 14
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `short_name`, `email`, `phone`, `website`, `light_logo`, `dark_logo`, `small_dark_logo`, `small_light_logo`, `address`, `app_layout`, `currency_id`, `lang_id`, `left_sidebar_theme`, `primary_color`, `date_format`, `time_format`, `auto_detect_timezone`, `timezone`, `session_driver`, `app_debug`, `update_app_notification`, `login_image`, `rtl`, `shortcut_menus`, `mysqldump_command`, `profile_image`, `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at`, `subscription_plan_id`, `package_type`, `licence_expire_on`, `is_global`, `admin_id`, `status`, `total_users`, `email_verification_code`, `verified`, `clock_in_time`, `clock_out_time`, `leave_start_month`, `late_mark_after`, `early_clock_in_time`, `allow_clock_out_till`, `self_clocking`, `allowed_ip_address`, `letterhead_header_color`, `letterhead_show_company_name`, `letterhead_show_company_email`, `letterhead_show_company_phone`, `letterhead_show_company_address`, `letterhead_left_space`, `letterhead_right_space`, `letterhead_top_space`, `letterhead_bottom_space`, `holiday_pdf_font_size`, `holiday_pdf_line_height`, `generate_pdf_font_size`, `generate_pdf_line_height`, `letterhead_title_show_in_pdf`, `employee_id_prefix`, `employee_id_start`, `employee_id_digits`, `created_at`, `updated_at`, `pdf_font_id`, `use_custom_font`, `capture_location`, `google_geo_coding_api_key`, `export_pdf_left_space`, `export_pdf_right_space`, `export_pdf_top_space`, `export_pdf_bottom_space`, `export_pdf_line_height`, `export_pdf_font_size`) VALUES
(1, 'Logan Express Care', 'Logan Exp. C.', 'company@example.com', '+9199999999', NULL, 'company_rjkjazheszxbqmuql9gj.png', 'company_9djfkz8vdnohkypq97ic.png', 'company_uswjfi0s78budvsnfnkl.png', 'company_lgu5zkf8sub6pwgtn9ji.png', '7 street, city, state, 762782', 'sidebar', 1, 1, 'dark', '#1890ff', 'DD-MM-YYYY', 'hh:mm a', 1, 'Asia/Kolkata', 'file', 0, 1, 'company_ioyzqkmlhp6rd0inygeh.jpg', 0, 'top_bottom', '/usr/bin/mysqldump', NULL, NULL, NULL, NULL, NULL, NULL, 'monthly', NULL, 0, 1, 'active', 2, NULL, 0, '09:30:00', '18:00:00', '01', NULL, NULL, NULL, 1, NULL, '#1890ff', 1, 1, 1, 1, 15, 15, 40, 20, 20, 40, 17, 28, 1, 'emp', '0002', 4, NULL, '2025-10-07 22:35:38', NULL, 0, 0, NULL, 15, 15, 40, 20, 25, 14);

-- --------------------------------------------------------

--
-- Table structure for table `company_policies`
--

CREATE TABLE `company_policies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `policy_document` varchar(191) DEFAULT NULL,
  `method_type` varchar(191) DEFAULT 'upload',
  `letter_description` longtext DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `from_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `letterhead_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generates_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `proff_of_document` varchar(191) DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reply_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `symbol` varchar(191) NOT NULL,
  `position` varchar(191) NOT NULL,
  `is_deletable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `company_id`, `name`, `code`, `symbol`, `position`, `is_deletable`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dollar', 'USD', '$', 'front', 0, NULL, NULL),
(2, 1, 'Rupee', 'INR', '₹', 'front', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deposit_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` double NOT NULL,
  `date_time` datetime NOT NULL,
  `payer_id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(191) DEFAULT NULL,
  `file` varchar(191) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposit_categories`
--

CREATE TABLE `deposit_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_fields`
--

CREATE TABLE `employee_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `field_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `type` varchar(20) NOT NULL,
  `is_required` tinyint(1) NOT NULL,
  `default_value` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_specific_leave_counts`
--

CREATE TABLE `employee_specific_leave_counts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `leave_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_leaves` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_work_status`
--

CREATE TABLE `employee_work_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_work_status`
--

INSERT INTO `employee_work_status` (`id`, `company_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fulltime', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(2, 1, 'Contract', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(3, 1, 'Probation', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(4, 1, 'Work From Home', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(5, 1, 'Casual', '2025-10-17 15:31:40', '2025-10-17 15:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bill` varchar(191) DEFAULT NULL,
  `expense_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` float NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(1000) DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `reference_number` varchar(191) DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `expense_type` varchar(20) DEFAULT 'employee_claims',
  `payment_date` datetime DEFAULT NULL,
  `payment_status` tinyint(1) DEFAULT 0,
  `payroll_month` int(11) DEFAULT NULL,
  `payroll_year` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `expense_for` varchar(25) NOT NULL DEFAULT 'all',
  `description` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visible_to` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `last_date` datetime DEFAULT NULL,
  `feedback_form_fields` longtext NOT NULL,
  `indicator_fields` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_users`
--

CREATE TABLE `feedback_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `feedback_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `feedback_given` tinyint(1) NOT NULL DEFAULT 0,
  `allowed` tinyint(1) NOT NULL DEFAULT 0,
  `submit_date` date DEFAULT NULL,
  `rating` varchar(191) DEFAULT NULL,
  `rating_details` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `field_types`
--

CREATE TABLE `field_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `visible_to_employee` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `form_fields` longtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `generates`
--

CREATE TABLE `generates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `letterhead_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `left_space` int(11) DEFAULT NULL,
  `right_space` int(11) DEFAULT NULL,
  `top_space` int(11) DEFAULT NULL,
  `bottom_space` int(11) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `date` date NOT NULL,
  `is_weekend` tinyint(1) NOT NULL DEFAULT 0,
  `is_half_day` tinyint(1) NOT NULL DEFAULT 0,
  `half_holiday` varchar(20) NOT NULL DEFAULT 'before_break',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `increments_promotions`
--

CREATE TABLE `increments_promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'promotion',
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `net_salary` int(11) DEFAULT NULL,
  `promoted_designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `letterhead_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indicators`
--

CREATE TABLE `indicators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `fields` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `langs`
--

CREATE TABLE `langs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `key` varchar(191) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `langs`
--

INSERT INTO `langs` (`id`, `image`, `name`, `key`, `enabled`, `created_at`, `updated_at`) VALUES
(1, NULL, 'English', 'en', 1, '2025-10-07 19:17:18', '2025-10-07 19:17:18');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `total_days` float NOT NULL DEFAULT 0,
  `is_half_day` tinyint(1) NOT NULL DEFAULT 0,
  `half_leave` varchar(20) NOT NULL DEFAULT 'before_break',
  `reason` text NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `total_leaves` int(11) NOT NULL,
  `max_leaves_per_month` int(11) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `count_type` varchar(191) NOT NULL DEFAULT 'same_for_all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letterhead_templates`
--

CREATE TABLE `letterhead_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `description` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2021_01_02_193004_create_langs_table', 1),
(3, '2021_01_02_193005_create_translations_table', 1),
(4, '2021_01_02_193006_create_subscription_plans_table', 1),
(5, '2021_01_02_193007_create_companies_table', 1),
(6, '2021_02_01_092358_create_employee_work_status_table', 1),
(7, '2021_02_02_062906_create_locations_table', 1),
(8, '2021_02_02_072700_create_salary_groups_table', 1),
(9, '2021_02_02_072701_create_users_table', 1),
(10, '2021_04_01_040700_create_currencies_table', 1),
(11, '2021_04_05_190756_entrust_setup_tables', 1),
(12, '2021_04_08_084520_create_accounts_table', 1),
(13, '2021_05_08_044842_create_payees_table', 1),
(14, '2021_05_08_052842_create_expense_categories_table', 1),
(15, '2021_05_08_052858_create_expenses_table', 1),
(16, '2022_01_01_045359_create_pdf_fonts_table', 1),
(17, '2022_02_01_094402_create_settings_table', 1),
(18, '2022_02_24_075933_create_notifications_table', 1),
(19, '2022_02_24_122754_create_jobs_table', 1),
(20, '2023_08_04_114601_create_letterhead_templates_table', 1),
(21, '2023_08_04_114602_create_generates_table', 1),
(22, '2023_08_05_070152_create_leave_types_table', 1),
(23, '2023_08_21_100631_create_indicators_table', 1),
(24, '2023_09_04_072643_create_departments_table', 1),
(25, '2023_09_04_072702_create_designations_table', 1),
(26, '2023_09_04_072714_create_shifts_table', 1),
(27, '2023_09_04_093125_create_holidays_table', 1),
(28, '2023_09_05_070018_create_leaves_table', 1),
(29, '2023_09_05_070241_create_pre_payments_table', 1),
(30, '2023_09_05_070242_create_attendances_table', 1),
(31, '2023_09_05_070315_create_awards_table', 1),
(32, '2023_09_05_070351_create_increments_promotions_table', 1),
(33, '2023_09_05_070352_create_payrolls_table', 1),
(34, '2023_09_05_070356_create_appreciations_table', 1),
(35, '2023_09_05_094820_add_coloumns_in_users_table', 1),
(36, '2023_09_10_093430_create_asset_types_table', 1),
(37, '2023_09_11_044801_create_assets_table', 1),
(38, '2023_09_13_072657_create_news_table', 1),
(39, '2023_09_13_075847_create_news_users_table', 1),
(40, '2023_09_20_110838_create_complaints_table', 1),
(41, '2023_09_21_043658_create_company_policies_table', 1),
(42, '2023_09_21_075055_create_forms_table', 1),
(43, '2023_09_21_100632_create_feedbacks_table', 1),
(44, '2023_09_21_102941_create_feedback_users_table', 1),
(45, '2023_09_23_050634_create_payers_table', 1),
(46, '2023_09_23_051605_create_deposit_categories_table', 1),
(47, '2023_09_23_063804_create_deposits_table', 1),
(48, '2024_03_22_072118_create_work_durations_table', 1),
(49, '2024_06_23_100516_create_warnings_table', 1),
(50, '2024_06_23_111108_create_offboardings_table', 1),
(51, '2024_06_24_064213_create_account_entries_table', 1),
(52, '2024_11_20_084630_create_salary_components_table', 1),
(53, '2024_11_22_074849_create_salary_group_components_table', 1),
(54, '2024_11_22_110855_create_salary_group_users_table', 1),
(55, '2024_11_26_085734_create_basic_salary_details_table', 1),
(56, '2024_11_27_072906_add_columns_in_users_table', 1),
(57, '2024_12_05_070438_create_payroll_components_table', 1),
(58, '2025_03_22_072117_add_column_is_next_day_in_shifts_table', 1),
(59, '2025_07_07_060457_create_field_types_table', 1),
(60, '2025_07_07_064846_create_employee_fields_table', 1),
(61, '2025_07_07_121819_change_coloumn_type_in_leaves_table', 1),
(62, '2025_07_08_075517_create_user_documnets_table', 1),
(63, '2025_07_19_045304_create_employee_specific_leave_counts_table', 1),
(64, '2025_08_13_083042_add_column_in_companies_table', 1),
(65, '2025_10_14_000001_create_staff_onboarding_table', 2),
(66, '2025_10_21_144107_add_password_to_staff_onboarding_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `visible_to_all` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'both',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_users`
--

CREATE TABLE `news_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offboardings`
--

CREATE TABLE `offboardings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `letterhead_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generates_id` bigint(20) UNSIGNED DEFAULT NULL,
  `submit_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(10) DEFAULT 'pending',
  `reply_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payees`
--

CREATE TABLE `payees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `phone_number` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payers`
--

CREATE TABLE `payers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `phone_number` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `basic_salary` double NOT NULL,
  `salary_amount` double NOT NULL,
  `pre_payment_amount` double NOT NULL DEFAULT 0,
  `expense_amount` double NOT NULL DEFAULT 0,
  `net_salary` double NOT NULL,
  `total_days` double NOT NULL,
  `working_days` double NOT NULL,
  `present_days` double NOT NULL,
  `total_office_time` int(11) NOT NULL,
  `total_worked_time` int(11) NOT NULL,
  `half_days` int(11) NOT NULL,
  `late_days` double NOT NULL,
  `paid_leaves` double NOT NULL,
  `unpaid_leaves` double NOT NULL,
  `holiday_count` double NOT NULL,
  `payment_date` date DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'generated',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_components`
--

CREATE TABLE `payroll_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payroll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pre_payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_component_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `value_type` varchar(191) NOT NULL DEFAULT 'fixed',
  `amount` double NOT NULL,
  `monthly_value` double DEFAULT NULL,
  `is_earning` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(20) NOT NULL DEFAULT 'pre_payments',
  `flags` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pdf_fonts`
--

CREATE TABLE `pdf_fonts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `file` varchar(191) DEFAULT NULL,
  `user_kashida` tinyint(4) DEFAULT 0,
  `use_otl` smallint(5) UNSIGNED DEFAULT 255,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `module_name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `module_name`, `created_at`, `updated_at`) VALUES
(1, 'salary_components_view', 'Salary Component View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(2, 'salary_components_create', 'Salary Component Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(3, 'salary_components_edit', 'Salary Component Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(4, 'salary_components_delete', 'Salary Component Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(5, 'manage_company_expense', 'Manage Company Expenses', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(6, 'salary_groups_view', 'Salary Group View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(7, 'salary_groups_create', 'Salary Group Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(8, 'salary_groups_edit', 'Salary Group Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(9, 'salary_groups_delete', 'Salary Group Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(10, 'users_view', 'Staff Member View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(11, 'users_create', 'Staff Member Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(12, 'users_edit', 'Staff Member Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(13, 'users_delete', 'Staff Member Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(14, 'assets_view', 'Asset View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(15, 'assets_create', 'Asset Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(16, 'assets_edit', 'Asset Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(17, 'assets_delete', 'Asset Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(18, 'asset_types_view', 'Asset Type View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(19, 'asset_types_create', 'Asset Type Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(20, 'asset_types_edit', 'Asset Type Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(21, 'asset_types_delete', 'Asset Type Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(22, 'holidays_view', 'Weekend/Holiday View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(23, 'holidays_create', 'Weekend/Holiday Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(24, 'holidays_edit', 'Weekend/Holiday Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(25, 'holidays_delete', 'Weekend/Holiday Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(26, 'leaves_view', 'Leaves View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(27, 'leaves_create', 'Leaves Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(28, 'leaves_edit', 'Leaves Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(29, 'leaves_delete', 'Leaves Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(30, 'leaves_approve_reject', 'Leaves Approve/Reject', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(31, 'work_durations_view', 'Work Durations View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(32, 'work_durations_create', 'Work Durations Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(33, 'work_durations_edit', 'Work Durations Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(34, 'work_durations_delete', 'Work Durations Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(35, 'leave_types_view', 'Leave Types View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(36, 'leave_types_create', 'Leave Types Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(37, 'leave_types_edit', 'Leave Types Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(38, 'leave_types_delete', 'Leave Types Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(39, 'attendances_view', 'Attendance View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(40, 'attendances_create', 'Attendance Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(41, 'attendances_edit', 'Attendance Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(42, 'attendances_delete', 'Attendance Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(43, 'news_view', 'News View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(44, 'news_create', 'News Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(45, 'news_edit', 'News Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(46, 'news_delete', 'News Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(47, 'payrolls_view', 'Payrolls View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(48, 'payrolls_create', 'Payrolls Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(49, 'payrolls_edit', 'Payrolls Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(50, 'payrolls_delete', 'Payrolls Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(51, 'pre_payments_view', 'Pre Payments View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(52, 'pre_payments_create', 'Pre Payments Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(53, 'pre_payments_edit', 'Pre Payments Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(54, 'pre_payments_delete', 'Pre Payments Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(55, 'increments_promotions_view', 'Increments Promotions View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(56, 'increments_promotions_create', 'Increments Promotions Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(57, 'increments_promotions_edit', 'Increments Promotions Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(58, 'increments_promotions_delete', 'Increments Promotions Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(59, 'company_policies_view', 'Company Policy View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(60, 'company_policies_create', 'Company Policy Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(61, 'company_policies_edit', 'Company Policy Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(62, 'company_policies_delete', 'Company Policy Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(63, 'warnings_view', 'Warnings View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(64, 'warnings_create', 'Warnings Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(65, 'warnings_edit', 'Warnings Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(66, 'warnings_delete', 'Warnings Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(67, 'resignations_view', 'Resignations View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(68, 'resignations_create', 'Resignations Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(69, 'resignations_edit', 'Resignations Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(70, 'resignations_delete', 'Resignations Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(71, 'terminations_view', 'Terminations View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(72, 'terminations_create', 'Terminations Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(73, 'terminations_edit', 'Terminations Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(74, 'terminations_delete', 'Terminations Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(75, 'indicators_view', 'Indicators View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(76, 'indicators_create', 'Indicators Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(77, 'indicators_edit', 'Indicators Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(78, 'indicators_delete', 'Indicators Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(79, 'appreciations_view', 'Appreciation View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(80, 'appreciations_create', 'Appreciation Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(81, 'appreciations_edit', 'Appreciation Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(82, 'appreciations_delete', 'Appreciation Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(83, 'awards_view', 'Award View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(84, 'awards_create', 'Award Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(85, 'awards_edit', 'Award Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(86, 'awards_delete', 'Award Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(87, 'salary_settings', 'Salary Settings', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(88, 'hrm_settings', 'Hrm Settings', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(89, 'font_settings', 'Font Settings', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(90, 'pdf_fonts_view', 'Pdf Fonts View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(91, 'pdf_fonts_create', 'Pdf Fonts Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(92, 'pdf_fonts_edit', 'Pdf Fonts Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(93, 'pdf_fonts_delete', 'Pdf Fonts Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(94, 'complaints_view', 'Complaint View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(95, 'complaints_create', 'Complaint Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(96, 'complaints_edit', 'Complaint Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(97, 'complaints_delete', 'Complaint Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(98, 'letter_head_templates_view', 'Letter Head Templates View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(99, 'letter_head_templates_create', 'Letter Head Templates Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(100, 'letter_head_templates_edit', 'Letter Head Templates Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(101, 'letter_head_templates_delete', 'Letter Head Templates Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(102, 'generates_view', 'Generates View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(103, 'generates_create', 'Generates Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(104, 'generates_edit', 'Generates Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(105, 'generates_delete', 'Generates Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(106, 'feedbacks_view', 'Feedback View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(107, 'feedbacks_create', 'Feedback Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(108, 'feedbacks_edit', 'Feedback Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(109, 'feedbacks_delete', 'Feedback Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(110, 'accounts_view', 'Account View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(111, 'accounts_create', 'Account Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(112, 'accounts_edit', 'Account Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(113, 'accounts_delete', 'Account Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(114, 'payees_view', 'Form View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(115, 'payees_create', 'Form Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(116, 'payees_edit', 'Form Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(117, 'payees_delete', 'Payee Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(118, 'payers_view', 'Form View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(119, 'payers_create', 'Form Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(120, 'payers_edit', 'Form Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(121, 'payers_delete', 'Payers Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(122, 'deposit_categories_view', 'Form View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(123, 'deposit_categories_create', 'Form Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(124, 'deposit_categories_edit', 'Form Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(125, 'deposit_categories_delete', 'Deposit Categories Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(126, 'expense_categories_view', 'Expense Category View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(127, 'expense_categories_create', 'Expense Category Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(128, 'expense_categories_edit', 'Expense Category Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(129, 'expense_categories_delete', 'Expense Category Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(130, 'deposits_view', 'depoait View', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(131, 'deposits_create', 'depoait Create', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(132, 'deposits_edit', 'depoait Edit', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(133, 'deposits_delete', 'Deposit Delete', NULL, NULL, '2025-10-07 19:17:35', '2025-10-07 19:17:35'),
(134, 'employee_work_status_view', 'Employee Work Status View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(135, 'employee_work_status_create', 'Employee Work Status Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(136, 'employee_work_status_edit', 'Employee Work Status Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(137, 'employee_work_status_delete', 'Employee Work Status Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(138, 'expenses_view', 'Expense View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(139, 'expenses_create', 'Expense Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(140, 'expenses_edit', 'Expense Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(141, 'expenses_delete', 'Expense Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(142, 'expenses_approve_reject', 'Expenses Approve/Reject', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(143, 'currencies_view', 'Currency View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(144, 'currencies_create', 'Currency Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(145, 'currencies_edit', 'Currency Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(146, 'currencies_delete', 'Currency Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(147, 'locations_view', 'Location View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(148, 'locations_create', 'Location Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(149, 'locations_edit', 'Location Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(150, 'locations_delete', 'Location Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(151, 'forms_view', 'Form View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(152, 'forms_create', 'Form Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(153, 'forms_edit', 'Form Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(154, 'forms_delete', 'Form Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(155, 'modules_view', 'Modules View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(156, 'roles_view', 'Role View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(157, 'roles_create', 'Role Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(158, 'roles_edit', 'Role Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(159, 'roles_delete', 'Role Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(160, 'companies_edit', 'Company Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(161, 'translations_view', 'Translation View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(162, 'translations_create', 'Translation Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(163, 'translations_edit', 'Translation Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(164, 'translations_delete', 'Translation Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(165, 'asset_lent_return_add_edit', 'Asset Lent/Return Settings', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(166, 'mark_holidays_add_edit', 'Mark Holiday Settings', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(167, 'storage_edit', 'Storage Settings Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(168, 'email_edit', 'Email Settings Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(169, 'update_app', 'Update App', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(170, 'departments_view', 'Department View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(171, 'departments_create', 'Department Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(172, 'departments_edit', 'Department Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(173, 'departments_delete', 'Department Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(174, 'designations_view', 'Designation View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(175, 'designations_create', 'Designation Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(176, 'designations_edit', 'Designation Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(177, 'designations_delete', 'Designation Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(178, 'shifts_view', 'Shift View', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(179, 'shifts_create', 'Shift Create', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(180, 'shifts_edit', 'Shift Edit', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36'),
(181, 'shifts_delete', 'Shift Delete', NULL, NULL, '2025-10-07 19:17:36', '2025-10-07 19:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pre_payments`
--

CREATE TABLE `pre_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double NOT NULL,
  `date_time` datetime NOT NULL,
  `deduct_from_payroll` tinyint(1) NOT NULL DEFAULT 1,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payroll_month` int(11) NOT NULL,
  `payroll_year` int(11) NOT NULL,
  `notes` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `company_id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'Admin', 'Admin is allowed to manage everything of the app.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `salary_components`
--

CREATE TABLE `salary_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `value_type` varchar(191) NOT NULL,
  `monthly` varchar(191) NOT NULL,
  `semi_monthly` varchar(191) DEFAULT NULL,
  `weekly` varchar(191) DEFAULT NULL,
  `bi_weekly` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_groups`
--

CREATE TABLE `salary_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_group_components`
--

CREATE TABLE `salary_group_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_component_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_group_users`
--

CREATE TABLE `salary_group_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `setting_type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `name_key` varchar(191) NOT NULL,
  `credentials` text DEFAULT NULL,
  `other_data` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_id`, `is_global`, `setting_type`, `name`, `name_key`, `credentials`, `other_data`, `status`, `verified`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'storage', 'Local', 'local', NULL, NULL, 1, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(2, 1, 0, 'storage', 'AWS', 'aws', '{\"driver\":\"s3\",\"key\":\"\",\"secret\":\"\",\"region\":\"\",\"bucket\":\"\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(3, 1, 0, 'email', 'SMTP', 'smtp', '{\"from_name\":\"\",\"from_email\":\"\",\"host\":\"\",\"port\":\"\",\"encryption\":\"\",\"username\":\"\",\"password\":\"\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(4, 1, 0, 'send_mail_settings', 'Send mail to company', 'company', '[]', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(5, 1, 0, 'shortcut_menus', 'Add Menu', 'shortcut_menus', '[\"user\",\"currency\",\"language\",\"role\"]', NULL, 1, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(6, 1, 0, 'email_templates', 'Welcome to the Company!', 'employee_welcome_mail', '{\"title\":\"Welcome to the Company!\",\"content\":\"<p>Hello <strong>##EMPLOYEE_NAME##<\\/strong>,<\\/p>\\n<p>We are excited to welcome you to our team at <strong>##COMPANY_NAME##!<\\/strong> We believe your skills and experience will be a valuable addition to our company.<\\/p>\\n<p>Here\\u2019s a quick summary of your onboarding:<\\/p>\\n<ul>\\n    <li><strong>Department:<\\/strong> ##EMPLOYEE_DEPARTMENT##<\\/li>\\n    <li><strong>Designation:<\\/strong> ##EMPLOYEE_DESIGNATION##<\\/li>\\n    <li><strong>Shift:<\\/strong> ##EMPLOYEE_SHIFT##<\\/li>\\n    <li><strong>Joining Date:<\\/strong> ##EMPLOYEE_JOINING_DATE##<\\/li>\\n    <li><strong>Employee ID:<\\/strong> ##EMPLOYEE_ID##<\\/li>\\n<\\/ul>\\n\\n<p>Below is the login details to company account:<\\/p>\\n<ul>\\n    <li><strong>Email:<\\/strong> ##EMPLOYEE_EMAIL##<\\/li>\\n    <li><strong>Password:<\\/strong> ##EMPLOYEE_PASSWORD##<\\/li>\\n<\\/ul>\\n\\n<p>To get started, please log in to your employee portal using the button below:<\\/p>\\n<p><a href=\\\"##LOGIN_URL##\\\" rel=\\\"noopener noreferrer\\\" target=\\\"_blank\\\">Login to Portal<\\/a> If you have any questions, feel free to reach out to HR.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(7, 1, 0, 'email_templates', 'Asset Handover Confirmation', 'employee_asset_lent', '{\"title\":\"Asset Handover Confirmation\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This is to acknowledge that the following asset has been handed over to you by <strong>##ASSET_LENT_BY##<\\/strong>:<\\/p>\\n\\n<ul>\\n    <li><strong>Asset Name:<\\/strong> ##ASSET_NAME##<\\/li>\\n    <li><strong>Asset ID \\/ Serial No:<\\/strong> ##ASSET_SERIAL_NUMBER##<\\/li>\\n    <li><strong>Condition:<\\/strong> ##ASSET_STATUS##<\\/li>\\n    <li><strong>Handover Date:<\\/strong> ##ASSET_LENT_DATE##<\\/li>\\n    <li><strong>Return Date:<\\/strong> ##ASSET_RETURN_DATE##<\\/li>\\n<\\/ul>\\n\\n<p>Please ensure proper usage and safekeeping of the asset as per company policy. You may be held responsible for any damage or loss during the lending period.<\\/p>\\n\\n<p>If you have any questions or need support regarding the asset, feel free to contact the IT\\/Admin department.<\\/p>\\n\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(8, 1, 0, 'email_templates', 'Asset Return Confirmation', 'employee_asset_return', '{\"title\":\"Asset Return Confirmation\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This is to acknowledge that the following asset has been successfully returned by you to <strong>##ASSET_RETURN_BY##<\\/strong>:<\\/p>\\n\\n<ul>\\n    <li><strong>Asset Name:<\\/strong> ##ASSET_NAME##<\\/li>\\n    <li><strong>Asset ID \\/ Serial No:<\\/strong> ##ASSET_SERIAL_NUMBER##<\\/li>\\n    <li><strong>Condition at Return:<\\/strong> ##ASSET_STATUS##<\\/li>\\n    <li><strong>Return Date:<\\/strong> ##ASSET_RETURN_DATE##<\\/li>\\n    <li><strong>Actual Return Date:<\\/strong> ##ASSET_ACTUAL_RETURN_DATE##<\\/li>\\n<\\/ul>\\n\\n<p>Thank you for ensuring the asset was returned in <strong>##ASSET_STATUS##<\\/strong> condition. This helps us maintain accurate inventory records and ensure accountability.<\\/p>\\n\\n<p>If you have any questions or need support regarding the asset, feel free to contact the IT\\/Admin department.<\\/p>\\n\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(9, 1, 0, 'email_templates', 'Congratulations on Your Appreciation!', 'employee_on_appreciation', '{\"title\":\"Congratulations on Your Appreciation!\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We are pleased to inform you that you have been recognized and appreciated for your outstanding performance and contribution to our company.<\\/p>\\n\\n<p><strong>Award Name:<\\/strong> ##AWARD_NAME##<\\/p>\\n\\n<p>##APPRECIATION_DESCRIPTION##<\\/p>\\n\\n<p>Your dedication, hard work, and positive attitude have not gone unnoticed. You continue to be an inspiration to your peers and a valuable asset to the team.<\\/p>\\n\\n<p>On behalf of all of us at <strong>##COMPANY_NAME##<\\/strong>, congratulations once again. Keep up the amazing work!<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(10, 1, 0, 'email_templates', 'Your Leave Request Has Been Approved', 'employee_leave_approve', '{\"title\":\"Your Leave Request Has Been Approved\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We are pleased to inform you that your leave request has been <strong>approved<\\/strong>.<\\/p>\\n\\n<p><strong>Leave Type:<\\/strong> ##LEAVE_TYPE_NAME##<\\/p>\\n<p><strong>From:<\\/strong> ##LEAVE_START_DATE## &nbsp;&nbsp; <strong>To:<\\/strong> ##LEAVE_END_DATE##<\\/p>\\n<p><strong>Total Days:<\\/strong> ##LEAVE_TOTAL_DAYS##<\\/p>\\n<p><strong>Leave Reason:<\\/strong> ##LEAVE_REASON##<\\/p>\\n\\n<p>Please ensure all responsibilities are managed before your leave begins, and keep in touch in case of any urgent requirements.<\\/p>\\n\\n<p>We hope you enjoy your time off and return refreshed.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(11, 1, 0, 'email_templates', 'Your Leave Request Has Been Rejected', 'employee_leave_reject', '{\"title\":\"Your Leave Request Has Been Rejected\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We regret to inform you that your leave request has been <strong>rejected<\\/strong> due to the following reason:<\\/p>\\n\\n<p><strong>Leave Type:<\\/strong> ##LEAVE_TYPE_NAME##<\\/p>\\n<p><strong>From:<\\/strong> ##LEAVE_START_DATE## &nbsp;&nbsp; <strong>To:<\\/strong> ##LEAVE_END_DATE##<\\/p>\\n<p><strong>Total Days:<\\/strong> ##LEAVE_TOTAL_DAYS##<\\/p>\\n<p><strong>Leave Reason:<\\/strong> ##LEAVE_REASON##<\\/p>\\n\\n<p>We understand this may be disappointing, and we encourage you to reach out if you have any concerns or if you\'d like to reapply for a different date.<\\/p>\\n\\n<p>Thank you for your understanding.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(12, 1, 0, 'email_templates', 'New Company News Published', 'employee_news_published', '{\"title\":\"New Company News Published\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We\\u2019re excited to share a new update published within <strong>##COMPANY_NAME##<\\/strong>!<\\/p>\\n\\n<p><strong>News Title:<\\/strong> ##NEWS_TITLE##<\\/p>\\n\\n<p>Login to your company account and view the full news details.<\\/p>\\n\\n<p>We encourage you to stay informed and engaged with all company happenings. Your involvement is key to our collective success.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(13, 1, 0, 'email_templates', 'Your Payroll Has Been Processed and Paid', 'employee_payroll_paid', '{\"title\":\"Your Payroll Has Been Processed and Paid\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We are pleased to inform you that your payroll for the period of <strong>##PAYROLL_MONTH## ##PAYROLL_YEAR##<\\/strong> has been successfully <strong>processed and credited<\\/strong> to your registered bank account.<\\/p>\\n\\n<p><strong>Employee ID:<\\/strong> ##EMPLOYEE_ID##<\\/p>\\n<p><strong>Pay Period:<\\/strong> ##PAYROLL_MONTH## ##PAYROLL_YEAR##<\\/p>\\n<p><strong>Net Salary:<\\/strong> ##PAYROLL_NET_SALARY##<\\/p>\\n\\n<p>You can view or download your detailed salary slip by logging to your account.<\\/p>\\n\\n<p>If you have any questions regarding your payroll or deductions, please reach out to the HR or Accounts department.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(14, 1, 0, 'email_templates', 'New Company Policy', 'employee_company_policies_create', '{\"title\":\"New Company Policy\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We would like to inform you that a new company policy titled <strong>\\\"##COMPANY_POLICY_TITLE##\\\"<\\/strong> has been officially created and published as part of our organizational guidelines.<\\/p>\\n\\n<p><strong>Policy Title:<\\/strong> ##COMPANY_POLICY_TITLE<\\/p>\\n\\n<p>You are advised to read the policy thoroughly and ensure compliance. You can view or download the full policy document by login to your account:<\\/p>\\n\\n<p>If you have any questions or need further clarification, please feel free to contact the HR team.<\\/p>\\n\\n<p>Thank you for your attention and cooperation.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(15, 1, 0, 'email_templates', 'Company Policy Updated', 'employee_company_policies_update', '{\"title\":\"Company Policy Updated\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We would like to inform you that a company policy titled <strong>\\\"##COMPANY_POLICY_TITLE##\\\"<\\/strong> has been revised and published as part of our organizational guidelines.<\\/p>\\n\\n<p><strong>Policy Title:<\\/strong> ##COMPANY_POLICY_TITLE<\\/p>\\n\\n<p>You are advised to read the policy thoroughly and ensure compliance. You can view or download the full policy document by login to your account:<\\/p>\\n\\n<p>If you have any questions or need further clarification, please feel free to contact the HR team.<\\/p>\\n\\n<p>Thank you for your attention and cooperation.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(16, 1, 0, 'email_templates', 'Official Warning Notice', 'employee_warning', '{\"title\":\"Official Warning Notice\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This email serves as an official notice regarding a warning issued to you based on the following concern:<\\/p>\\n\\n<p><strong>Warning Title:<\\/strong> ##WARNING_TITLE<\\/p>\\n\\n<p>Please consider this a formal communication intended to address the concern and provide an opportunity for improvement. We encourage you to reflect on the matter and take the necessary steps to align with company policies and expectations.<\\/p>\\n\\n<p>Continued non-compliance or recurrence of such behavior may lead to further disciplinary action, including suspension or termination as per company policy.<\\/p>\\n\\n<p>If you would like to discuss this further or need clarification, please feel free to reach out to your manager or the HR department.<\\/p>\\n\\n<p>We believe in fairness and growth, and we hope to see positive changes moving forward.<\\/p>\\n\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(17, 1, 0, 'email_templates', 'Employment Termination Notice', 'employee_terminations', '{\"title\":\"Employment Termination Notice\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This letter serves as formal notification that your employment with <strong>##COMPANY_NAME##<\\/strong> will be terminated, effective <strong>##TERMINATION_LAST_WORKING_DATE##<\\/strong>.<\\/p>\\n\\n<p><strong>Title for Termination:<\\/strong> ##TERMINATION_TITLE##<\\/p>\\n<p><strong>Reason for Termination:<\\/strong> ##TERMINATION_DESCRIPTION##<\\/p>\\n\\n<p>This decision has been made after a thorough review and consideration of all relevant factors. Despite previous discussions and opportunities for improvement, the necessary standards and expectations have not been met, leading to this difficult but necessary step.<\\/p>\\n\\n<p>Please complete all offboarding procedures, including return of company property and clearance documentation, before your final working day. Your final settlement, including any outstanding dues (if applicable), will be processed in accordance with company policy.<\\/p>\\n\\n<p>If you have any questions or would like to discuss the matter further, you may reach out to the HR department.<\\/p>\\n\\n<p>We thank you for your efforts during your tenure and wish you the best for your future endeavors.<\\/p>\\n\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(18, 1, 0, 'email_templates', 'Resignation Acceptance Confirmation', 'employee_resignation_approve', '{\"title\":\"Resignation Acceptance Confirmation\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This is to formally acknowledge and confirm that your resignation request, submitted on <strong>##RESIGNATION_DATE##<\\/strong> has been <strong>approved<\\/strong> and your last working date will be <strong>##RESIGNATION_LAST_WORKING_DATE##<\\/strong>.<\\/p>\\n\\n<p>We thank you for your contributions during your time at <strong>##COMPANY_NAME##<\\/strong>. Your dedication and efforts have been appreciated, and we wish you success in your future endeavors.<\\/p>\\n\\n<p>Kindly ensure that all handover procedures, clearance processes, and documentation are completed before your final working day. If you need any assistance during this transition, feel free to reach out to the HR department.<\\/p>\\n\\n<p>If applicable, your full and final settlement will be processed as per company policy.<\\/p>\\n\\n<p>We wish you the very best in your career ahead.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(19, 1, 0, 'email_templates', 'Resignation Request Rejected', 'employee_resignation_reject', '{\"title\":\"Resignation Request Rejected\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This is to inform you that your resignation request submitted on <strong>##RESIGNATION_DATE##<\\/strong> has been reviewed and <strong>rejected<\\/strong> by the management.<\\/p>\\n\\n<p><strong>Reason for Rejection:<\\/strong> ##RESIGNATION_REPLY_NOTE##<\\/p>\\n\\n<p>We value your role and contributions to <strong>##COMPANY_NAME##<\\/strong> and believe that continued collaboration will be beneficial for both you and the organization.<\\/p>\\n\\n<p>If you have any concerns or issues that led to your decision to resign, we encourage you to discuss them openly with your reporting manager or the HR department. We are willing to offer support and explore possible solutions.<\\/p>\\n\\n<p>Should you still wish to pursue this decision, kindly reach out for a further discussion.<\\/p>\\n\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(20, 1, 0, 'email_templates', 'Your Complaint Has Been Reviewed and Approved for Resolution', 'employee_complaint_approve', '{\"title\":\"Your Complaint Has Been Reviewed and Approved for Resolution\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This is to inform you that your complaint submitted on <strong>##COMPLAINT_DATE##<\\/strong> regarding <strong>##COMPLAINT_TITLE##<\\/strong> has been <strong>reviewed and approved<\\/strong> for further action.<\\/p>\\n\\n<p><strong>Complaint From:<\\/strong> ##COMPLAINT_FROM##<\\/p>\\n<p><strong>Complaint Against:<\\/strong> ##COMPLAINT_AGAINST##<\\/p>\\n<p><strong>Complaint Title:<\\/strong> ##COMPLAINT_TITLE##<\\/p>\\n<p><strong>Complaint Description:<\\/strong> ##COMPLAINT_DESCRIPTION##<\\/p>\\n\\n<p>We appreciate you taking the time to report this matter. Our HR team has assessed the complaint and determined that it warrants formal resolution under the company\\u2019s grievance policy.<\\/p>\\n\\n<p>A dedicated representative will now begin addressing the issue, and you may be contacted shortly for any additional information or clarification if required.<\\/p>\\n\\n<p>Please be assured that your concerns are being handled confidentially and with care. We are committed to maintaining a safe, respectful, and supportive work environment for all employees.<\\/p>\\n\\n<p>If you have any further questions or would like to discuss this in more detail, feel free to reach out to the HR department.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(21, 1, 0, 'email_templates', 'Complaint Request Rejected', 'employee_complaint_reject', '{\"title\":\"Complaint Request Rejected\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>This is to inform you that your complaint submitted on <strong>##COMPLAINT_DATE##<\\/strong> regarding <strong>##COMPLAINT_TITLE##<\\/strong> has been <strong>reviewed but will not be approved for further action at this time<\\/strong>.<\\/p>\\n\\n<p><strong>Complaint From:<\\/strong> ##COMPLAINT_FROM##<\\/p>\\n<p><strong>Complaint Against:<\\/strong> ##COMPLAINT_AGAINST##<\\/p>\\n<p><strong>Complaint Title:<\\/strong> ##COMPLAINT_TITLE##<\\/p>\\n<p><strong>Complaint Description:<\\/strong> ##COMPLAINT_DESCRIPTION##<\\/p>\\n\\n<p><strong>Reply Note:<\\/strong> ##COMPLAINT_REPLY_NOTES##<\\/p>\\n\\n<p>We assure you that your complaint was reviewed thoroughly in accordance with company policy. Based on the available information, it was determined that the matter does not fall under a violation or actionable concern as defined by our internal procedures.<\\/p>\\n\\n<p>If you believe there is additional information or evidence that should be considered, you are welcome to reach out to the HR department for further discussion.<\\/p>\\n\\n<p>We appreciate your initiative in raising concerns and encourage you to continue using the proper channels whenever you feel uncomfortable or face issues at work.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(22, 1, 0, 'email_templates', 'Your Expense Request Has Been Approved', 'employee_expense_approve', '{\"title\":\"Your Expense Request Has Been Approved\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We are pleased to inform you that your expense reimbursement request submitted on <strong>##EXPENSE_DATE##<\\/strong> has been <strong>approved<\\/strong>.<\\/p>\\n\\n<p><strong>Expense Category:<\\/strong> ##EXPENSE_CATEGORY##<\\/p>\\n<p><strong>Expense Date:<\\/strong> ##EXPENSE_DATE##<\\/p>\\n<p><strong>Approved Amount:<\\/strong> ##EXPENSE_AMOUNT##<\\/p>\\n<p><strong>Expense Description:<\\/strong> ##EXPENSE_DESCRIPTION##<\\/p>\\n\\n<p>The approved amount will be processed for reimbursement as per the standard payment schedule. You will receive a confirmation once the payment has been completed.<\\/p>\\n\\n<p>If you have any questions regarding the status of your reimbursement or need further clarification, please contact the finance team at ##COMPANY_NAME##.<\\/p>\\n\\n<p>Thank you for your timely submission and adherence to the expense policy.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(23, 1, 0, 'email_templates', 'Your Expense Request Has Been Rejected', 'employee_expense_reject', '{\"title\":\"Your Expense Request Has Been Rejected\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We are pleased to inform you that your expense reimbursement request submitted on <strong>##EXPENSE_DATE##<\\/strong> has been <strong>rejected<\\/strong>.<\\/p>\\n\\n<p><strong>Expense Category:<\\/strong> ##EXPENSE_CATEGORY##<\\/p>\\n<p><strong>Expense Date:<\\/strong> ##EXPENSE_DATE##<\\/p>\\n<p><strong>Approved Amount:<\\/strong> ##EXPENSE_AMOUNT##<\\/p>\\n<p><strong>Expense Description:<\\/strong> ##EXPENSE_DESCRIPTION##<\\/p>\\n\\n<p>We recommend reviewing the expense policy to ensure that all future claims meet the required criteria. If you believe this decision was made in error or have additional documentation to support your claim, you are welcome to contact the finance team for clarification or appeal.<\\/p>\\n\\n<p>If you have any questions regarding the status of your reimbursement or need further clarification, please contact the finance team at ##COMPANY_NAME##.<\\/p>\\n\\n<p>Thank you for your timely submission and adherence to the expense policy.<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(24, 1, 0, 'email_templates', 'New Survey Now Available', 'employee_survey_forms_assign', '{\"title\":\"New Survey Now Available\",\"content\":\"<p>Dear ##EMPLOYEE_NAME##,<\\/p>\\n\\n<p>We are pleased to invite you to participate in our latest survey. Your feedback is incredibly valuable and helps us improve the work environment, policies, and overall employee experience at <strong>##COMPANY_NAME##<\\/strong>.<\\/p>\\n\\n<p><strong>Survey Title:<\\/strong> ##SURVEY_TITLE##<\\/p>\\n<p><strong>Survey Title:<\\/strong> ##SURVEY_DESCRIPTION##<\\/p>\\n<p><strong>Last Date:<\\/strong> ##SURVEY_LAST_DATE##<\\/p>\\n\\n<p>Please take a few moments to complete the survey by login to your account.<\\/p>\\n\\n<p>Your responses will remain confidential, and the data will be used solely for improvement purposes.<\\/p>\\n\\n<p>Thank you in advance for your time and honest feedback!<\\/p>\\n\\n<p>Best regards,<br>\\nHR Team<\\/p>\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(25, 1, 0, 'email_templates', 'New Leave Request Submitted by ##EMPLOYEE_NAME##', 'company_employee_leave_apply', '{\"title\":\"New Leave Request Submitted by ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> has submitted a new leave request.<\\/p>\\n\\n<p><strong>Employee Id:<\\/strong> ##EMPLOYEE_ID##<\\/p>\\n<p><strong>Employee Name:<\\/strong> ##EMPLOYEE_NAME##<\\/p>\\n<p><strong>Leave Type:<\\/strong> ##LEAVE_TYPE_NAME##<\\/p>\\n<p><strong>From:<\\/strong> ##LEAVE_START_DATE## &nbsp;&nbsp; <strong>To:<\\/strong> ##LEAVE_END_DATE##<\\/p>\\n<p><strong>Total Days:<\\/strong> ##LEAVE_TOTAL_DAYS##<\\/p>\\n<p><strong>Reason:<\\/strong> ##LEAVE_REASON##<\\/p>\\n\\n<p>You may review and take action on this request by login to your company account and by visiting the leave management system<\\/p>\\n\\n<p>For any urgent concerns, please contact the employee directly.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(26, 1, 0, 'email_templates', 'Employee Clock-In Notification - ##EMPLOYEE_NAME##', 'company_employee_clock_in', '{\"title\":\"Employee Clock-In Notification - ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> has successfully <strong>clocked in<\\/strong> for the day.<\\/p>\\n\\n<p><strong>Employee Id:<\\/strong> ##EMPLOYEE_ID##<\\/p>\\n<p><strong>Employee Name:<\\/strong> ##EMPLOYEE_NAME##<\\/p>\\n<p><strong>Clock-In DateTime:<\\/strong> ##ATTENDANCE_CLOCK_IN_TIME##<\\/p>\\n<p><strong>Clock-In IP:<\\/strong> ##ATTENDANCE_CLOCK_IN_IP##<\\/p>\\n\\n<p>This entry has been recorded in the attendance system.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(27, 1, 0, 'email_templates', 'Employee Clock-Out Notification - ##EMPLOYEE_NAME##', 'company_employee_clock_out', '{\"title\":\"Employee Clock-Out Notification - ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> has successfully <strong>clocked out<\\/strong> for the day.<\\/p>\\n\\n<p><strong>Employee Id:<\\/strong> ##EMPLOYEE_ID##<\\/p>\\n<p><strong>Employee Name:<\\/strong> ##EMPLOYEE_NAME##<\\/p>\\n<p><strong>Clock-Out DateTime:<\\/strong> ##ATTENDANCE_CLOCK_OUT_TIME##<\\/p>\\n<p><strong>Clock-Out IP:<\\/strong> ##ATTENDANCE_CLOCK_OUT_IP##<\\/p>\\n\\n<p>This entry has been recorded in the attendance system.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(28, 1, 0, 'email_templates', 'Resignation Submitted by ##EMPLOYEE_NAME##', 'company_employee_resignation_apply', '{\"title\":\"Resignation Submitted by ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> has submitted a resignation request.<\\/p>\\n\\n<p><strong>Employee ID:<\\/strong> ##EMPLOYEE_ID##<\\/p>\\n<p><strong>Employee Name:<\\/strong> ##EMPLOYEE_NAME##<\\/p>\\n<p><strong>Designation:<\\/strong> ##EMPLOYEE_DESIGNATION##<\\/p>\\n<p><strong>Department:<\\/strong> ##EMPLOYEE_DEPARTMENT##<\\/p>\\n<p><strong>Resignation date:<\\/strong> ##RESIGNATION_DATE##<\\/p>\\n<p><strong>Reason for Resignation:<\\/strong> ##RESIGNATION_REASON##<\\/p>\\n\\n<p>Please review the request and begin the necessary clearance and exit process.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(29, 1, 0, 'email_templates', 'New Complaint Submitted by ##EMPLOYEE_NAME##', 'company_employee_complaint_apply', '{\"title\":\"New Complaint Submitted by ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> (Employee ID: ##EMPLOYEE_ID##) has submitted a new complaint through the company system.<\\/p>\\n\\n<p><strong>Complaint Against:<\\/strong> ##COMPLAINT_AGAINST##<\\/p>\\n<p><strong>Complaint Subject:<\\/strong> ##COMPLAINT_TITLE##<\\/p>\\n<p><strong>Complaint Date:<\\/strong> ##COMPLAINT_DATE##<\\/p>\\n\\n<p><strong>Details:<\\/strong><\\/p>\\n<p>##COMPLAINT_DESCRIPTION##<\\/p>\\n\\n<p>Please log in to the your accoujt and take appropriate action:<\\/p>\\n\\n<p>We recommend addressing this matter promptly and following internal complaint resolution procedures.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(30, 1, 0, 'email_templates', 'New Expense Request Submitted by ##EMPLOYEE_NAME##', 'company_employee_expense_apply', '{\"title\":\"New Expense Request Submitted by ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> (Employee ID: ##EMPLOYEE_ID##) has submitted a new expense reimbursement request.<\\/p>\\n\\n<p><strong>Expense Category:<\\/strong> ##EXPENSE_CATEGORY##<\\/p>\\n<p><strong>Expense Amount:<\\/strong> ##EXPENSE_AMOUNT##<\\/p>\\n<p><strong>Expense Date:<\\/strong> ##EXPENSE_DATE##<\\/p>\\n<p><strong>Description:<\\/strong> ##EXPENSE_DESCRIPTION##<\\/p>\\n\\n<p>You can review the full request and attached documents using by login to your account<\\/p>\\n\\n<p>Please process the request as per company reimbursement policy.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37'),
(31, 1, 0, 'email_templates', 'Survey Feedback Submitted by ##EMPLOYEE_NAME##', 'company_employee_survey_submit', '{\"title\":\"Survey Feedback Submitted by ##EMPLOYEE_NAME##\",\"content\":\"<p>Hello,<\\/p>\\n\\n<p>This is to inform you that <strong>##EMPLOYEE_NAME##<\\/strong> (Employee ID: ##EMPLOYEE_ID##) has successfully submitted feedback for the <strong>##SURVEY_TITLE##<\\/strong>.<\\/p>\\n\\n<p><strong>Survey Title:<\\/strong> ##SURVEY_TITLE##<\\/p>\\n\\n<p>You may view the full feedback details and response data by login to your account.<\\/p>\\n\"}', NULL, 0, 0, '2025-10-07 19:17:37', '2025-10-07 19:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `clock_in_time` time NOT NULL,
  `clock_out_time` time NOT NULL,
  `late_mark_after` int(11) DEFAULT NULL,
  `early_clock_in_time` int(11) DEFAULT NULL,
  `allow_clock_out_till` int(11) DEFAULT NULL,
  `self_clocking` tinyint(1) NOT NULL DEFAULT 1,
  `is_next_day` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_ip_address` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `capture_location` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_onboarding`
--

CREATE TABLE `staff_onboarding` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `password` varchar(191) DEFAULT NULL,
  `position` enum('disability_support_worker','nurse','other') NOT NULL,
  `employment_type` enum('permanent','casual','contractor') NOT NULL,
  `abn` varchar(191) DEFAULT NULL,
  `business_name` varchar(191) DEFAULT NULL,
  `ndis_screening` varchar(191) DEFAULT NULL,
  `children_check` varchar(191) DEFAULT NULL,
  `children_check_expiry` date DEFAULT NULL,
  `passport` varchar(191) DEFAULT NULL,
  `visa` varchar(191) DEFAULT NULL,
  `drivers_license` varchar(191) DEFAULT NULL,
  `ndis_orientation` varchar(191) DEFAULT NULL,
  `meals_certificate` varchar(191) DEFAULT NULL,
  `meals_certificate_expiry` date DEFAULT NULL,
  `ipc_certificate` varchar(191) DEFAULT NULL,
  `ipc_certificate_expiry` date DEFAULT NULL,
  `first_aid_certificate` varchar(191) DEFAULT NULL,
  `first_aid_expiry` date DEFAULT NULL,
  `cpr_certificate` varchar(191) DEFAULT NULL,
  `cpr_expiry` date DEFAULT NULL,
  `resume` varchar(191) DEFAULT NULL,
  `staff_contract` varchar(191) DEFAULT NULL,
  `police_check` varchar(191) DEFAULT NULL,
  `police_check_expiry` date DEFAULT NULL,
  `ack_staff_details` tinyint(1) NOT NULL DEFAULT 0,
  `ack_induction` tinyint(1) NOT NULL DEFAULT 0,
  `ack_code_conduct` tinyint(1) NOT NULL DEFAULT 0,
  `ack_training_tracker` tinyint(1) NOT NULL DEFAULT 0,
  `ack_handbook` tinyint(1) NOT NULL DEFAULT 0,
  `ack_tax_declaration` tinyint(1) NOT NULL DEFAULT 0,
  `ack_bank_details` tinyint(1) NOT NULL DEFAULT 0,
  `final_consent` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','under_review','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_onboarding`
--

INSERT INTO `staff_onboarding` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `position`, `employment_type`, `abn`, `business_name`, `ndis_screening`, `children_check`, `children_check_expiry`, `passport`, `visa`, `drivers_license`, `ndis_orientation`, `meals_certificate`, `meals_certificate_expiry`, `ipc_certificate`, `ipc_certificate_expiry`, `first_aid_certificate`, `first_aid_expiry`, `cpr_certificate`, `cpr_expiry`, `resume`, `staff_contract`, `police_check`, `police_check_expiry`, `ack_staff_details`, `ack_induction`, `ack_code_conduct`, `ack_training_tracker`, `ack_handbook`, `ack_tax_declaration`, `ack_bank_details`, `final_consent`, `status`, `admin_notes`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 'Jena', 'Neal', 'tesafoqum@mailinator.com', '+1 (111) 832-6365', '', 'other', 'casual', NULL, NULL, 'ndis_screening_1760560386_5732.pdf', 'children_check_1760560386_4019.pdf', '1980-05-21', 'passport_1760560386_2041.pdf', 'visa_1760560386_7750.pdf', 'drivers_license_1760560386_7859.pdf', 'ndis_orientation_1760560386_2038.pdf', 'meals_certificate_1760560386_4873.pdf', '2016-05-23', 'ipc_certificate_1760560386_3708.pdf', '1977-04-01', 'first_aid_certificate_1760560386_3141.pdf', '2013-01-01', 'cpr_certificate_1760560387_8242.pdf', '1988-08-24', 'resume_1760560387_1663.pdf', 'staff_contract_1760560387_2888.pdf', 'police_check_1760560387_6456.pdf', '1994-01-16', 1, 1, 1, 1, 1, 1, 1, 1, 'under_review', NULL, NULL, '2025-10-16 16:58:10', '2025-10-16 03:33:07', '2025-10-16 16:58:10'),
(2, 'Daryl', 'Schmidt', 'suxynov@mailinator.com', '+1 (723) 139-2433', '', 'nurse', 'casual', NULL, NULL, 'ndis_screening_1760609202_5352.pdf', 'children_check_1760609202_6279.pdf', '1991-03-25', 'passport_1760609202_3545.pdf', 'visa_1760609202_3673.pdf', 'drivers_license_1760609202_6923.pdf', 'ndis_orientation_1760609202_9823.pdf', 'meals_certificate_1760609202_9870.pdf', '1998-11-24', 'ipc_certificate_1760609202_9572.pdf', '1981-09-08', 'first_aid_certificate_1760609202_6866.pdf', '2002-06-13', 'cpr_certificate_1760609202_4240.pdf', '1985-02-14', 'resume_1760609202_2070.pdf', 'staff_contract_1760609202_9835.pdf', 'police_check_1760609203_6178.pdf', '2023-01-17', 1, 1, 1, 1, 1, 1, 1, 1, 'pending', NULL, NULL, NULL, '2025-10-16 17:06:43', '2025-10-16 17:06:43'),
(3, 'Cailin', 'Beach', 'puqojovig@mailinator.com', '+1 (683) 482-1449', '', 'disability_support_worker', 'casual', NULL, NULL, 'ndis_screening_1760609625_8137.pdf', 'children_check_1760609625_7744.pdf', '2024-01-18', 'passport_1760609625_3141.pdf', 'visa_1760609625_6831.pdf', 'drivers_license_1760609625_4050.pdf', 'ndis_orientation_1760609625_2228.pdf', 'meals_certificate_1760609625_3798.pdf', '1990-10-20', 'ipc_certificate_1760609626_3970.pdf', '2012-09-16', 'first_aid_certificate_1760609626_7836.pdf', '2019-06-17', 'cpr_certificate_1760609626_8824.pdf', '2023-05-06', 'resume_1760609626_4178.pdf', 'staff_contract_1760609626_3057.pdf', 'police_check_1760609626_7394.pdf', '1995-07-27', 1, 1, 1, 1, 1, 1, 1, 1, 'pending', NULL, NULL, NULL, '2025-10-16 17:13:46', '2025-10-16 17:13:46'),
(4, 'Basia', 'Acosta', 'voviqy@mailinator.com', '+1 (938) 482-6079', '', 'nurse', 'contractor', 'Amet fuga Fugiat', 'Mohammad Reilly', 'ndis_screening_1760680548_8096.pdf', 'children_check_1760680548_8754.pdf', '1998-07-04', 'passport_1760680548_3711.pdf', 'visa_1760680548_6385.pdf', 'drivers_license_1760680548_5251.pdf', 'ndis_orientation_1760680548_7706.pdf', 'meals_certificate_1760680548_8992.pdf', '2015-10-20', 'ipc_certificate_1760680548_8303.pdf', '1982-01-02', 'first_aid_certificate_1760680548_2609.pdf', '1996-04-10', 'cpr_certificate_1760680549_5153.pdf', '2021-07-09', 'resume_1760680549_7712.pdf', 'staff_contract_1760680549_2714.pdf', 'police_check_1760680549_3259.pdf', '1977-10-06', 1, 1, 1, 1, 1, 1, 1, 1, 'under_review', NULL, NULL, '2025-10-17 12:56:48', '2025-10-17 12:55:49', '2025-10-17 12:56:48'),
(9, 'Muhammad', 'Ahmar', 'mirzaahmar1@gmail.com', '03227134554', '$2y$10$jP3RPJ1JJBaVFL.qM.jnbeWS9Z5ovbCFd1g5vteQ8lEdzTvUKybiO', 'other', 'contractor', 'Quidem et nisi do ma', 'Madeline Morris', 'ndis_screening_1761153414_5014.pdf', 'children_check_1761153414_8993.pdf', '1980-08-18', 'passport_1761153414_3128.pdf', 'visa_1761153415_2669.pdf', 'drivers_license_1761153415_4643.pdf', 'ndis_orientation_1761153415_4729.pdf', 'meals_certificate_1761153415_6811.pdf', '2024-03-18', 'ipc_certificate_1761153415_6601.pdf', '2018-03-19', 'first_aid_certificate_1761153415_2579.pdf', '1973-09-09', 'cpr_certificate_1761153415_1137.pdf', '2023-06-19', 'resume_1761153415_7828.pdf', 'staff_contract_1761153415_1936.pdf', 'police_check_1761153415_1169.pdf', '2000-01-21', 1, 1, 1, 1, 1, 1, 1, 1, 'under_review', NULL, NULL, '2025-10-23 00:18:14', '2025-10-22 17:14:54', '2025-10-23 00:18:14'),
(10, 'Sochi', 'Ugwu', 'cj1900a@gmail.com', '009889982', '$2y$10$lvICxrbjsHq1tcfQWBffQep69cKNWbz1OkeBTru4xXZoJr/hWo3Zu', 'disability_support_worker', 'permanent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 'under_review', NULL, NULL, '2025-10-24 19:07:47', '2025-10-23 08:01:23', '2025-10-24 19:07:47'),
(11, 'Esomchi', 'Ezugwu', 'ezueso@gmail.com', '0415805112', '$2y$10$mczhzUNmvOFc9EnVivZflOKB/.Ij62dYbsU1.elKlA.xFcJjIeU2C', 'disability_support_worker', 'permanent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 'approved', 'Welcome,\nKindly complete your uploads. \nCheers!', NULL, '2025-10-24 19:06:03', '2025-10-23 08:05:54', '2025-10-24 19:06:03');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `annual_price` double NOT NULL DEFAULT 0,
  `monthly_price` double NOT NULL DEFAULT 0,
  `max_products` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `modules` text DEFAULT NULL,
  `default` varchar(20) NOT NULL DEFAULT 'no',
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `billing_cycle` tinyint(4) DEFAULT NULL,
  `stripe_monthly_plan_id` varchar(191) DEFAULT NULL,
  `stripe_annual_plan_id` varchar(191) DEFAULT NULL,
  `razorpay_monthly_plan_id` varchar(191) DEFAULT NULL,
  `razorpay_annual_plan_id` varchar(191) DEFAULT NULL,
  `paystack_monthly_plan_id` varchar(191) DEFAULT NULL,
  `paystack_annual_plan_id` varchar(191) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `duration` int(11) DEFAULT 30,
  `notify_before` int(11) DEFAULT NULL,
  `position` smallint(6) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `currency_code` varchar(191) DEFAULT 'USD',
  `currency_symbol` varchar(191) DEFAULT '$',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group` varchar(191) NOT NULL,
  `key` text NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `lang_id`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'common', 'enabled', 'Enabled', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(2, 1, 'common', 'disabled', 'Disabled', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(3, 1, 'common', 'working', 'Working', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(4, 1, 'common', 'not_working', 'Not Working', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(5, 1, 'common', 'id', 'Id', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(6, 1, 'common', 'action', 'Action', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(7, 1, 'common', 'placeholder_default_text', 'Please Enter {0}', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(8, 1, 'common', 'placeholder_social_text', 'Please Enter {0} Url', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(9, 1, 'common', 'placeholder_search_text', 'Search By {0}', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(10, 1, 'common', 'select_default_text', 'Select {0}...', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(11, 1, 'common', 'create', 'Create', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(12, 1, 'common', 'edit', 'Edit', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(13, 1, 'common', 'update', 'Update', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(14, 1, 'common', 'cancel', 'Cancel', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(15, 1, 'common', 'delete', 'Delete', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(16, 1, 'common', 'success', 'Success', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(17, 1, 'common', 'error', 'Error', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(18, 1, 'common', 'yes', 'Yes', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(19, 1, 'common', 'no', 'No', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(20, 1, 'common', 'fix_errors', 'Please Fix Below Errors.', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(21, 1, 'common', 'cancelled', 'Cancelled', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(22, 1, 'common', 'pending', 'Pending', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(23, 1, 'common', 'paid', 'Paid', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(24, 1, 'common', 'completed', 'Completed', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(25, 1, 'common', 'save', 'Save', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(26, 1, 'common', 'all', 'All', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(27, 1, 'common', 'name', 'Name', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(28, 1, 'common', 'back', 'Back', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(29, 1, 'common', 'max_amount', 'Max. Amount', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(30, 1, 'common', 'date_time', 'Date Time', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(31, 1, 'common', 'select_time', 'Select Time', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(32, 1, 'common', 'start_date', 'Start Date', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(33, 1, 'common', 'end_date', 'End Date', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(34, 1, 'common', 'search', 'Search', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(35, 1, 'common', 'date', 'Date', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(36, 1, 'common', 'confirm', 'Confirm', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(37, 1, 'common', 'title', 'Title', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(38, 1, 'common', 'value', 'Value', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(39, 1, 'common', 'add', 'Add', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(40, 1, 'common', 'view', 'View', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(41, 1, 'common', 'download', 'Download', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(42, 1, 'common', 'total', 'Total', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(43, 1, 'common', 'email', 'Email', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(44, 1, 'common', 'phone', 'Phone', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(45, 1, 'common', 'purchase_code', 'Purchase Code', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(46, 1, 'common', 'verify_purchase', 'Verify Purchase', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(47, 1, 'common', 'install', 'Install', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(48, 1, 'common', 'installing', 'Installing', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(49, 1, 'common', 'updating', 'Updating', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(50, 1, 'common', 'free', 'Free', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(51, 1, 'common', 'domain', 'Domain', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(52, 1, 'common', 'verify', 'Verify', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(53, 1, 'common', 'send', 'Send', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(54, 1, 'common', 'upload', 'Upload', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(55, 1, 'common', 'view_all', 'View All', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(56, 1, 'common', 'unpaid', 'Unpaid', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(57, 1, 'common', 'loading', 'Loading', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(58, 1, 'common', 'update_app', 'Update App', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(59, 1, 'common', 'welcome_back', 'Welcome {0}', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(60, 1, 'common', 'off', 'Off', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(61, 1, 'common', 'on_create', 'On Create', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(62, 1, 'common', 'on_update', 'On Update', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(63, 1, 'common', 'on_delete', 'On Delete', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(64, 1, 'common', 'demo_account_credentials', 'Demo account login credentials', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(65, 1, 'common', 'created_by', 'Created By', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(66, 1, 'common', 'import', 'Import', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(67, 1, 'common', 'file', 'File', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(68, 1, 'common', 'copy_url', 'Copy Url', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(69, 1, 'common', 'print', 'Print', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(70, 1, 'common', 'amount', 'Amount', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(71, 1, 'common', 'status', 'Status', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(72, 1, 'common', 'active', 'Active', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(73, 1, 'common', 'inactive', 'Inactive', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(74, 1, 'common', 'verified', 'Verified', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(75, 1, 'common', 'configure', 'Configure', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(76, 1, 'common', 'logo', 'Logo', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(77, 1, 'common', 'slug', 'Slug', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(78, 1, 'common', 'description', 'Description', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(79, 1, 'common', 'image', 'Image', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(80, 1, 'common', 'address', 'Address', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(81, 1, 'common', 'not_allowed', 'Not Allowed', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(82, 1, 'common', 'details', 'Details', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(83, 1, 'common', 'minutes', 'Minute', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(84, 1, 'common', 'approved', 'Approved', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(85, 1, 'common', 'rejected', 'Rejected', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(86, 1, 'common', 'january', 'January', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(87, 1, 'common', 'february', 'February', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(88, 1, 'common', 'march', 'March', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(89, 1, 'common', 'april', 'April', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(90, 1, 'common', 'may', 'May', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(91, 1, 'common', 'june', 'June', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(92, 1, 'common', 'july', 'July', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(93, 1, 'common', 'august', 'August', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(94, 1, 'common', 'september', 'September', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(95, 1, 'common', 'october', 'October', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(96, 1, 'common', 'november', 'November', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(97, 1, 'common', 'december', 'December', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(98, 1, 'common', 'hrm_permissions', 'HRM Permissions', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(99, 1, 'common', 'am', 'AM', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(100, 1, 'common', 'pm', 'PM', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(101, 1, 'common', 'sunday', 'Sunday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(102, 1, 'common', 'monday', 'Monday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(103, 1, 'common', 'tuesday', 'Tuesday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(104, 1, 'common', 'wednesday', 'Wednesday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(105, 1, 'common', 'thursday', 'Thursday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(106, 1, 'common', 'friday', 'Friday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(107, 1, 'common', 'saturday', 'Saturday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(108, 1, 'common', 'holiday', 'Holiday', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(109, 1, 'common', 'weekends', 'Weekends', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(110, 1, 'common', 'both', 'Both', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(111, 1, 'common', 'publicly', 'Publicly', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(112, 1, 'common', 'internal_employee', 'Internal Employee', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(113, 1, 'common', 'open', 'Open', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(114, 1, 'common', 'closed', 'Closed', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(115, 1, 'common', 'initial', 'Initial', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(116, 1, 'common', 'shortlist', 'Shortlist', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(117, 1, 'common', 'interview', 'Interview', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(118, 1, 'common', 'selected', 'Selected', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(119, 1, 'common', 'close', 'Closed', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(120, 1, 'common', 'draft', 'Draft', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(121, 1, 'common', 'published', 'Published', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(122, 1, 'common', 'select_employee', 'Select Employees', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(123, 1, 'common', 'text', 'Text', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(124, 1, 'common', 'add_edit', 'Add/Edit', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(125, 1, 'common', 'name_email', 'Name/Email/Phone', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(126, 1, 'common', 'submit', 'Submit', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(127, 1, 'common', 'input', 'Input', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(128, 1, 'common', 'text_area', 'Text Area', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(129, 1, 'common', 'self', 'Self', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(130, 1, 'common', 'manager', 'Manager', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(131, 1, 'common', 'preview', 'Preview', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(132, 1, 'common', 'generated', 'Payroll Generated Sucessfully', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(133, 1, 'common', 'regenerated', 'Payroll Regenerated Sucessfully', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(134, 1, 'common', 'broken_by_you', 'Broken Assets By You', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(135, 1, 'common', 'all_asset', 'All Lend Assets', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(136, 1, 'common', 'expired', 'Expired', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(137, 1, 'common', 'approved_message', 'are you sure you want to approved this request?', '2025-10-07 19:17:19', '2025-10-07 19:17:19'),
(138, 1, 'common', 'rejected_message', 'are you sure you want to reject this request?', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(139, 1, 'common', 'manage_company_expense', 'Manage Company Expense', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(140, 1, 'common', 'status_changed', 'Status Changed', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(141, 1, 'common', 'mm', 'mm', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(142, 1, 'common', 'px', 'px', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(143, 1, 'common', 'approve', 'Approve', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(144, 1, 'common', 'reject', 'Reject', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(145, 1, 'common', 'approve/reject', 'Approve or Reject', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(146, 1, 'common', 'before_break', 'Before Half', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(147, 1, 'common', 'after_break', 'After Half', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(148, 1, 'common', 'select_date', 'Select Date', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(149, 1, 'common', 'ok', 'Ok', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(150, 1, 'common', 'clear', 'Clear', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(151, 1, 'common', 'filters', 'Filters', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(152, 1, 'common', 'visible', 'Visible', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(153, 1, 'common', 'hidden', 'Hidden', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(154, 1, 'common', 'message', 'Message', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(155, 1, 'common', 'employee_specific', 'Employee Specific', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(156, 1, 'common', 'company_specific', 'Company Specific', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(157, 1, 'common', 'override_existing_data', 'Override Existing Data', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(158, 1, 'common', 'month', 'Month', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(159, 1, 'common', 'sample_attendance_file', 'Sample Attendance File', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(160, 1, 'common', 'sample_lang_file', 'Sample Language File', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(161, 1, 'common', 'export', 'Export', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(162, 1, 'common', 'show_company_header_footer', 'Show Company Header & Footer', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(163, 1, 'common', 'doc_type', 'Document Type', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(164, 1, 'common', 'csv', 'CSV', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(165, 1, 'common', 'excel', 'Excel', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(166, 1, 'common', 'pdf', 'PDF', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(167, 1, 'menu', 'dashboard', 'Dashboard', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(168, 1, 'menu', 'users', 'Users', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(169, 1, 'menu', 'staff_members', 'Employees', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(170, 1, 'menu', 'settings', 'Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(171, 1, 'menu', 'company', 'Company Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(172, 1, 'menu', 'profile', 'Profile', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(173, 1, 'menu', 'translations', 'Translations', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(174, 1, 'menu', 'languages', 'Languages', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(175, 1, 'menu', 'roles', 'Role & Permissions', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(176, 1, 'menu', 'currencies', 'Currencies', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(177, 1, 'menu', 'login', 'Login', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(178, 1, 'menu', 'logout', 'Logout', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(179, 1, 'menu', 'verify_product', 'Verify Product', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(180, 1, 'menu', 'modules', 'Modules', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(181, 1, 'menu', 'storage_settings', 'Storage Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(182, 1, 'menu', 'email_settings', 'Email Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(183, 1, 'menu', 'update_app', 'Update App', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(184, 1, 'menu', 'database_backup', 'Database Backup', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(185, 1, 'menu', 'companies', 'Companies', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(186, 1, 'menu', 'departments', 'Departments', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(187, 1, 'menu', 'designations', 'Designations', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(188, 1, 'menu', 'hrm', 'HRM', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(189, 1, 'menu', 'holidays', 'Holidays', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(190, 1, 'menu', 'weekends', 'Weekends', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(191, 1, 'menu', 'leaves_types', 'Leave Types', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(192, 1, 'menu', 'leaves', 'Leaves', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(193, 1, 'menu', 'shifts', 'Shifts', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(194, 1, 'menu', 'pre_payments', 'Pre Payments', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(195, 1, 'menu', 'attendances', 'Attendance', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(196, 1, 'menu', 'staff', 'Staff', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(197, 1, 'menu', 'all_holidays', 'All Holidays', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(198, 1, 'menu', 'leave_types', 'Leave Types', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(199, 1, 'menu', 'remaining_leaves', 'Remaining Leaves', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(200, 1, 'menu', 'unpaid_leaves', 'Unpaid Leaves', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(201, 1, 'menu', 'summary', 'Summary', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(202, 1, 'menu', 'awards', 'Awards', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(203, 1, 'menu', 'appreciations', 'Appreciations', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(204, 1, 'menu', 'increments_promotions', 'Increment/Promotion', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(205, 1, 'menu', 'payrolls', 'Payroll', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(206, 1, 'menu', 'dashboards', 'HRM Dashboard', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(207, 1, 'menu', 'attendance_details', 'Attendance Details', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(208, 1, 'menu', 'attendance_summary', 'Attendance Summary', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(209, 1, 'menu', 'basic_salaries', 'Employee Salaries', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(210, 1, 'menu', 'hrm_settings', 'HRM Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(211, 1, 'menu', 'expense_manager', 'Expenses', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(212, 1, 'menu', 'expense_categories', 'Expense Categories', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(213, 1, 'menu', 'expenses', 'Expenses', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(214, 1, 'menu', 'locations', 'Locations', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(215, 1, 'menu', 'assets', 'Assets', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(216, 1, 'menu', 'jobs', 'Jobs', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(217, 1, 'menu', 'news', 'News', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(218, 1, 'menu', 'asset_types', 'Asset Types', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(219, 1, 'menu', 'complaints', 'Complaints', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(220, 1, 'menu', 'forms', 'Forms', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(221, 1, 'menu', 'survey_forms', 'Survey Forms', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(222, 1, 'menu', 'assigned_survey', 'Assigned Survey', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(223, 1, 'menu', 'feedbacks', 'Survey Forms', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(224, 1, 'menu', 'company_policies', 'Company Policies', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(225, 1, 'menu', 'accountings', 'Accountings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(226, 1, 'menu', 'accounts', 'Accounts', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(227, 1, 'menu', 'payees', 'Payees', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(228, 1, 'menu', 'payers', 'Payers', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(229, 1, 'menu', 'deposit_categories', 'Deposit Categories', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(230, 1, 'menu', 'deposits', 'Deposits', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(231, 1, 'menu', 'salary_setting', 'Salary Setting', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(232, 1, 'menu', 'employees', 'Employees', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(233, 1, 'menu', 'asset_lent_return', 'Asset Lent/Return Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(234, 1, 'menu', 'mark_holiday_settings', 'Mark Holiday Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(235, 1, 'menu', 'finance', 'Finance', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(236, 1, 'menu', 'warnings', 'Warnings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(237, 1, 'menu', 'offboardings', 'Offboardings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(238, 1, 'menu', 'resignations', 'Resignations', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(239, 1, 'menu', 'terminations', 'Terminations', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(240, 1, 'menu', 'application', 'Application Form', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(241, 1, 'menu', 'letter_heads', 'Letter Heads', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(242, 1, 'menu', 'templates', 'Templates', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(243, 1, 'menu', 'generates', 'Generates', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(244, 1, 'menu', 'indicators', 'Review Indicators', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(245, 1, 'menu', 'upcoming_holidays', 'Upcoming Holidays', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(246, 1, 'menu', 'upcoming_birthdays', 'Upcoming Birthdays', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(247, 1, 'menu', 'search_title', 'Search Title', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(248, 1, 'menu', 'weekend_holiday', 'Weekend/Holiday', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(249, 1, 'menu', 'company_policy', 'Company Policy', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(250, 1, 'menu', 'download', 'Download', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(251, 1, 'menu', 'salary_groups', 'Salary Group', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(252, 1, 'menu', 'salary_components', 'Salary Component', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(253, 1, 'menu', 'payroll_settings', 'Payroll Settings', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(254, 1, 'menu', 'leave_approve_reject', 'Leave Approve/Reject', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(255, 1, 'menu', 'pdf_fonts', 'Pdf Fonts', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(256, 1, 'menu', 'company_expense', 'Company Expense', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(257, 1, 'menu', 'expense_approve_reject', 'Expense Approve/Reject', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(258, 1, 'menu', 'employee_work_status', 'Employee Work Status', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(259, 1, 'menu', 'performance', 'Performance', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(260, 1, 'menu', 'team_birthdays', 'Team Birthdays', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(261, 1, 'menu', 'send_wishes', 'Send Wishes', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(262, 1, 'menu', 'font_settings', 'Pdf Font Setting', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(263, 1, 'menu', 'work_hour_details', 'Working Hour Details', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(264, 1, 'menu', 'reports', 'Reports', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(265, 1, 'menu', 'employee_custom_fields', 'Employee Custom Fields', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(266, 1, 'menu', 'email_templates', 'Email Templates', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(267, 1, 'menu', 'custom_data_fields', 'Custom Data Fields', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(268, 1, 'menu', 'menu', 'Menu', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(269, 1, 'menu', 'self', 'Self', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(270, 1, 'menu', 'paid_leaves', 'Paid Leaves', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(271, 1, 'dashboard', 'dashboard', 'Dashboard', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(272, 1, 'dashboard', 'total_employees', 'Total Employees', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(273, 1, 'dashboard', 'total_active_employees', 'Total Active Employees', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(274, 1, 'dashboard', 'payment_sent', 'Payment Sent', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(275, 1, 'dashboard', 'payment_received', 'Payment Received', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(276, 1, 'dashboard', 'total_inactive_employees', 'Total Inactive Employees', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(277, 1, 'dashboard', 'work_anniversary', 'Work Anniversary', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(278, 1, 'dashboard', 'employee_under_you', 'Employee Under You', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(279, 1, 'dashboard', 'total_sales', 'Total Sales', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(280, 1, 'dashboard', 'total_expenses', 'Total Expenses', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(281, 1, 'user', 'add', 'Add New Employee', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(282, 1, 'user', 'edit', 'Edit Employee', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(283, 1, 'user', 'created', 'Employee Created Successfully', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(284, 1, 'user', 'updated', 'Employee Updated Successfully', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(285, 1, 'user', 'deleted', 'Employee Deleted Successfully', '2025-10-07 19:17:20', '2025-10-07 19:17:20'),
(286, 1, 'user', 'staff_members_details', 'Employee Details', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(287, 1, 'user', 'staff_member', 'Employee', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(288, 1, 'user', 'delete_message', 'Are you sure you want to delete this employee?', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(289, 1, 'user', 'selected_delete_message', 'Are you sure you want to delete selected employee?', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(290, 1, 'user', 'import_staff_members', 'Import Employees', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(291, 1, 'user', 'email_phone', 'Email or Phone', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(292, 1, 'user', 'user', 'User', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(293, 1, 'user', 'name', 'Name', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(294, 1, 'user', 'created_at', 'Created At', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(295, 1, 'user', 'working_email', 'Working Email', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(296, 1, 'user', 'password', 'Password', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(297, 1, 'user', 'login_enabled', 'Login Enabled', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(298, 1, 'user', 'profile_image', 'Profile Image', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(299, 1, 'user', 'company_name', 'Company Name', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(300, 1, 'user', 'working_phone', 'Working Phone', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(301, 1, 'user', 'allow_login', 'Allow Login', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(302, 1, 'user', 'address', 'Address', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(303, 1, 'user', 'city', 'City', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(304, 1, 'user', 'state', 'State', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(305, 1, 'user', 'country', 'Country', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(306, 1, 'user', 'zipcode', 'Zipcode', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(307, 1, 'user', 'status', 'Status', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(308, 1, 'user', 'role', 'Role', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(309, 1, 'user', 'profile_updated', 'Profile Updated Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(310, 1, 'user', 'password_blank', 'Leave blank if you don\'t want to update password.', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(311, 1, 'user', 'admin_account_details', 'Admin Account Details', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(312, 1, 'user', 'import_users', 'Import Users', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(313, 1, 'user', 'department_id', 'Department', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(314, 1, 'user', 'designation_id', 'Designation', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(315, 1, 'user', 'shift_id', 'Shift', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(316, 1, 'user', 'staff', 'Staff', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(317, 1, 'user', 'basic_salary', 'Basic Salary', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(318, 1, 'user', 'email', 'Email', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(319, 1, 'user', 'location_id', 'Location', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(320, 1, 'user', 'employee_id', 'Employee Id', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(321, 1, 'user', 'date', 'Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(322, 1, 'user', 'gender', 'Gender', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(323, 1, 'user', 'date_of_birth', 'Date Of Birth', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(324, 1, 'user', 'joining_date', 'Joining Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(325, 1, 'user', 'male', 'Male', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(326, 1, 'user', 'female', 'Female', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(327, 1, 'user', 'other', 'Other', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(328, 1, 'user', 'basic_info', 'Basic Info', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(329, 1, 'user', 'personal_info', 'Personal Info', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(330, 1, 'user', 'work_info', 'Work Info', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(331, 1, 'user', 'is_married', 'Is Married', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(332, 1, 'user', 'marriage_date', 'Marriage Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(333, 1, 'user', 'personal_email', 'Personal Email', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(334, 1, 'user', 'personal_phone', 'Personal Phone', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(335, 1, 'user', 'report_to', 'Report To', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(336, 1, 'user', 'is_manager', 'Is Manager', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(337, 1, 'user', 'visibility', 'Visibilty', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(338, 1, 'user', 'company_relation', 'Company Relation', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(339, 1, 'user', 'end_date', 'End Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(340, 1, 'user', 'manager', 'Manager', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(341, 1, 'user', 'department', 'Department', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(342, 1, 'user', 'location', 'Location', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(343, 1, 'user', 'company', 'Company', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(344, 1, 'user', 'employee_profile', 'Employee Profile', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(345, 1, 'user', 'about', 'About', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(346, 1, 'user', 'apprecation', 'Apprecation', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(347, 1, 'user', 'assets', 'Assets', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(348, 1, 'user', 'leaves', 'Leaves', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(349, 1, 'user', 'award', 'Award', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(350, 1, 'user', 'price_amount', 'Price Amount', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(351, 1, 'user', 'price_given', 'Price Given', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(352, 1, 'user', 'description', 'Description', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(353, 1, 'user', 'leave_type', 'Leave Type', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(354, 1, 'user', 'start_date', 'Start Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(355, 1, 'user', 'is_half_day', 'Is Half Day', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(356, 1, 'user', 'reason', 'Reason', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(357, 1, 'user', 'is_paid', 'Is Paid', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(358, 1, 'user', 'total_leaves', 'Total Leaves', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(359, 1, 'user', 'max_leaves_per_month', 'Max Leaves Per Month', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(360, 1, 'user', 'remaining_leaves', 'Remaining Leaves', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(361, 1, 'user', 'unpaid_leaves', 'Unpaid Leaves', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(362, 1, 'user', 'asset_type', 'Asset Type', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(363, 1, 'user', 'asset_name', 'Asset Name', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(364, 1, 'user', 'serial_number', 'Serial Number', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(365, 1, 'user', 'lent_to', 'Lent To', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(366, 1, 'user', 'awards', 'Awards', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(367, 1, 'user', 'active', 'Active', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(368, 1, 'user', 'award_price', 'Award Price', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(369, 1, 'user', 'assets_type', 'Assets Type', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(370, 1, 'user', 'phone', 'Phone', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(371, 1, 'user', 'probation_start_date', 'Probation Start Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(372, 1, 'user', 'probation_end_date', 'Probation End Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(373, 1, 'user', 'notice_start_date', 'Notice Start Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(374, 1, 'user', 'notice_end_date', 'Notice End Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(375, 1, 'user', 'duration', 'Duration', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(376, 1, 'user', 'quick_add', 'Quick Add', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(377, 1, 'user', 'add_field', 'Add Field', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(378, 1, 'user', 'salary_details', 'Salary Details', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(379, 1, 'user', 'employee_work_status', 'Employee Work Status', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(380, 1, 'user', 'custom_data_fields', 'Custom Data Fields', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(381, 1, 'user', 'value', 'Value', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(382, 1, 'user', 'profile', 'Profile', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(383, 1, 'user', 'sample_users_file', 'Sample Users File', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(384, 1, 'role', 'add', 'Add New Role', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(385, 1, 'role', 'edit', 'Edit Role', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(386, 1, 'role', 'created', 'Role Created Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(387, 1, 'role', 'updated', 'Role Updated Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(388, 1, 'role', 'deleted', 'Role Deleted Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(389, 1, 'role', 'role_details', 'Role Details', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(390, 1, 'role', 'delete_message', 'Are you sure you want to delete this role?', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(391, 1, 'role', 'selected_delete_message', 'Are you sure you want to delete selected role?', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(392, 1, 'role', 'display_name', 'Display Name', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(393, 1, 'role', 'role_name', 'Role Name', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(394, 1, 'role', 'description', 'Description', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(395, 1, 'role', 'user_management', 'User Management', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(396, 1, 'role', 'permissions', 'Permissions', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(397, 1, 'role', 'search_permissions', 'Search Permissions', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(398, 1, 'assigned_surve', 'add', 'Add New Assigned Survey', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(399, 1, 'assigned_surve', 'edit', 'Edit Assigned Survey', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(400, 1, 'assigned_surve', 'created', 'Assigned Survey Created Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(401, 1, 'assigned_surve', 'updated', 'Assigned Survey Updated Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(402, 1, 'assigned_surve', 'deleted', 'Assigned Survey Deleted Successfully', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(403, 1, 'assigned_surve', 'assigned_survey_details', 'Assigned Survey Details', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(404, 1, 'assigned_surve', 'delete_message', 'Are you sure you want to delete this Assigned Survey?', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(405, 1, 'assigned_surve', 'selected_delete_message', 'Are you sure you want to delete selected Assigned Survey?', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(406, 1, 'assigned_surve', 'title', 'Title', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(407, 1, 'assigned_surve', 'publish_date', 'Publish Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(408, 1, 'assigned_surve', 'last_date', 'Last Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(409, 1, 'assigned_surve', 'submit_date', 'Submit Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(410, 1, 'assigned_surve', 'link', 'Link', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(411, 1, 'assigned_surve', 'start_date', 'Start Date', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(412, 1, 'assigned_surve', 'rating', 'Rating', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(413, 1, 'assigned_surve', 'assigned_info', 'Assigned Info', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(414, 1, 'assigned_surve', 'there_is_no_assigned_survey', 'There is no assigned survey yet.', '2025-10-07 19:17:21', '2025-10-07 19:17:21'),
(415, 1, 'assigned_surve', 'no_active_assigned_survey', 'There is no active assigned survey yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(416, 1, 'assigned_surve', 'no_expired_assigned_survey', 'There is no expired assigned survey yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(417, 1, 'form', 'add', 'Add New Form', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(418, 1, 'form', 'edit', 'Edit Form', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(419, 1, 'form', 'created', 'Form Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(420, 1, 'form', 'updated', 'Form Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(421, 1, 'form', 'deleted', 'Form Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(422, 1, 'form', 'form_details', 'Form Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(423, 1, 'form', 'delete_message', 'Are you sure you want to delete this form?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(424, 1, 'form', 'selected_delete_message', 'Are you sure you want to delete selected form?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(425, 1, 'form', 'name', 'Name', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(426, 1, 'form', 'form_fields', 'Form Fields', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(427, 1, 'form', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(428, 1, 'form', 'status', 'Status', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(429, 1, 'form', 'add_form_field', 'Add Form Field', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(430, 1, 'form', 'field_name', 'Field Name', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(431, 1, 'form', 'field_type', 'Field Type', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(432, 1, 'form', 'status_update', 'Status Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(433, 1, 'indicator', 'add', 'Add New Review Indicator', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(434, 1, 'indicator', 'edit', 'Edit Review Indicator', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(435, 1, 'indicator', 'created', 'Review Indicator Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(436, 1, 'indicator', 'updated', 'Review Indicator Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(437, 1, 'indicator', 'deleted', 'Review Indicator Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(438, 1, 'indicator', 'indicator_details', 'Review Indicator Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(439, 1, 'indicator', 'delete_message', 'Are you sure you want to delete this review indicator?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(440, 1, 'indicator', 'selected_delete_message', 'Are you sure you want to delete selected review indicator?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(441, 1, 'indicator', 'name', 'Name', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(442, 1, 'indicator', 'fields', 'Fields', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(443, 1, 'template', 'add', 'Add New Template', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(444, 1, 'template', 'edit', 'Edit Template', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(445, 1, 'template', 'created', 'Template Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(446, 1, 'template', 'updated', 'Template Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(447, 1, 'template', 'deleted', 'Template Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(448, 1, 'template', 'template_details', 'Template Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(449, 1, 'template', 'delete_message', 'Are you sure you want to delete this template?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(450, 1, 'template', 'selected_delete_message', 'Are you sure you want to delete selected template?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(451, 1, 'template', 'title', 'Title', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(452, 1, 'template', 'template_fields', 'Template Fields', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(453, 1, 'template', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(454, 1, 'template', 'available_keys', 'Available Keys', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(455, 1, 'template', 'available_employee_keys', 'Available Employee Keys', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(456, 1, 'template', 'available_other_keys', 'Available Other Keys', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(457, 1, 'template', 'available_appreciation_keys', 'Available Appreciation Keys', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(458, 1, 'generate', 'add', 'Add New Generate', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(459, 1, 'generate', 'edit', 'Edit Generate', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(460, 1, 'generate', 'created', 'Generate Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(461, 1, 'generate', 'updated', 'Generate Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(462, 1, 'generate', 'deleted', 'Generate Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(463, 1, 'generate', 'generate_details', 'Generate Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(464, 1, 'generate', 'delete_message', 'Are you sure you want to delete this generate?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(465, 1, 'generate', 'selected_delete_message', 'Are you sure you want to delete selected generate?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(466, 1, 'generate', 'title', 'Title', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(467, 1, 'generate', 'user_id', 'Staff', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(468, 1, 'generate', 'letter_head_template', 'Letter Head Template', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(469, 1, 'generate', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(470, 1, 'generate', 'left_space', 'Left Space', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(471, 1, 'generate', 'right_space', 'Right Space', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(472, 1, 'generate', 'bottom_space', 'Bottom Space', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(473, 1, 'generate', 'top_space', 'Top Space', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(474, 1, 'generate', 'details', 'Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(475, 1, 'generate', 'generate_letter', 'Generate Letter', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(476, 1, 'generate', 'update_letter', 'Update Letter', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(477, 1, 'generate', 'created_date', 'Created Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(478, 1, 'generate', 'preview', 'Preview', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(479, 1, 'generate', 'download_header_footer', 'Download (Header & Footer)', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(480, 1, 'generate', 'print_header_footer', 'Print (Header & Footer)', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(481, 1, 'generate', 'created_at', 'Created', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(482, 1, 'generate', 'no_letter_heads', 'There is no letter heads yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(483, 1, 'warning', 'add', 'Add New Warning', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(484, 1, 'warning', 'edit', 'Edit Warning', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(485, 1, 'warning', 'created', 'Warning Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(486, 1, 'warning', 'updated', 'Warning Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(487, 1, 'warning', 'deleted', 'Warning Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(488, 1, 'warning', 'warning_details', 'Warning Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(489, 1, 'warning', 'delete_message', 'Are you sure you want to delete this warning?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(490, 1, 'warning', 'selected_delete_message', 'Are you sure you want to delete selected warning?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(491, 1, 'warning', 'user', 'User', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(492, 1, 'warning', 'title', 'Title', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(493, 1, 'warning', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(494, 1, 'warning', 'warning_date', 'Warning Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(495, 1, 'warning', 'no_warnings', 'There is no warnings yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(496, 1, 'resignation', 'add', 'Add New Resignation', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(497, 1, 'resignation', 'edit', 'Edit Resignation', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(498, 1, 'resignation', 'created', 'Resignation Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(499, 1, 'resignation', 'updated', 'Resignation Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(500, 1, 'resignation', 'deleted', 'Resignation Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(501, 1, 'resignation', 'resignation_details', 'Resignation Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(502, 1, 'resignation', 'delete_message', 'Are you sure you want to delete this resignation?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(503, 1, 'resignation', 'selected_delete_message', 'Are you sure you want to delete selected resignation?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(504, 1, 'resignation', 'user', 'User', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(505, 1, 'resignation', 'title', 'Title', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(506, 1, 'resignation', 'resignation_date', 'Resignation Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(507, 1, 'resignation', 'last_working_date', 'Last Working Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(508, 1, 'resignation', 'status', 'Status', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(509, 1, 'resignation', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(510, 1, 'resignation', 'reply_notes', 'Reply Notes', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(511, 1, 'resignation', 'resignation_status_updated', 'Resignation Status Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(512, 1, 'resignation', 'update_resignation_status', 'Update Resignation Status', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(513, 1, 'resignation', 'reply', 'Reply', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(514, 1, 'resignation', 'no_resignations', 'There is no resignations yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(515, 1, 'resignation', 'no_pending_resignations', 'There is no pending resignations yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(516, 1, 'resignation', 'no_approved_resignations', 'There is no approved resignations yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(517, 1, 'resignation', 'no_rejected_resignations', 'There is no rejected resignations yet.', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(518, 1, 'termination', 'add', 'Add New Termination', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(519, 1, 'termination', 'edit', 'Edit Termination', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(520, 1, 'termination', 'created', 'Termination Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(521, 1, 'termination', 'updated', 'Termination Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22');
INSERT INTO `translations` (`id`, `lang_id`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(522, 1, 'termination', 'deleted', 'Termination Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(523, 1, 'termination', 'termination_details', 'Termination Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(524, 1, 'termination', 'delete_message', 'Are you sure you want to delete this termination?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(525, 1, 'termination', 'selected_delete_message', 'Are you sure you want to delete selected termination?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(526, 1, 'termination', 'user', 'User', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(527, 1, 'termination', 'title', 'Title', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(528, 1, 'termination', 'notice_date', 'Notice Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(529, 1, 'termination', 'termination_date', 'Termination Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(530, 1, 'termination', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(531, 1, 'feedback', 'add', 'Add New Survey Form', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(532, 1, 'feedback', 'edit', 'Edit Survey Form', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(533, 1, 'feedback', 'created', 'Survey Form Created Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(534, 1, 'feedback', 'updated', 'Survey Form Updated Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(535, 1, 'feedback', 'deleted', 'Survey Form Deleted Successfully', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(536, 1, 'feedback', 'feedback_details', 'Survey Form Details', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(537, 1, 'feedback', 'delete_message', 'Are you sure you want to delete this survey form?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(538, 1, 'feedback', 'selected_delete_message', 'Are you sure you want to delete selected survey form?', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(539, 1, 'feedback', 'title', 'Title', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(540, 1, 'feedback', 'form_id', 'Form Fields', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(541, 1, 'feedback', 'description', 'Description', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(542, 1, 'feedback', 'status', 'Status', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(543, 1, 'feedback', 'visible_to', 'Visible To', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(544, 1, 'feedback', 'user_id', 'Employees', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(545, 1, 'feedback', 'Feedback_id', 'Feedback', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(546, 1, 'feedback', 'all', 'All', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(547, 1, 'feedback', 'select_employee', 'Select Employees', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(548, 1, 'feedback', 'publish_date', 'Publish Date', '2025-10-07 19:17:22', '2025-10-07 19:17:22'),
(549, 1, 'feedback', 'add_form_field', 'Add Form Field', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(550, 1, 'feedback', 'field_name', 'Field Name', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(551, 1, 'feedback', 'field_type', 'Field Type', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(552, 1, 'feedback', 'form', 'Feedback Form', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(553, 1, 'feedback', 'feedback_form', 'Generate Form', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(554, 1, 'feedback', 'answers', 'Your Answer', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(555, 1, 'feedback', 'dates', 'Assign Dates', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(556, 1, 'feedback', 'start_date', 'Start Date', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(557, 1, 'feedback', 'last_date', 'Last Date', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(558, 1, 'feedback', 'expired', 'Expired', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(559, 1, 'feedback', 'active', 'Active', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(560, 1, 'feedback', 'submit_date', 'Submit Date', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(561, 1, 'feedback', 'replied', 'Replied', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(562, 1, 'feedback', 'not_replied', 'Not Replied', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(563, 1, 'feedback', 'indicator_fields', 'Review Indicator Fields', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(564, 1, 'feedback', 'rating_updated', 'Rating Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(565, 1, 'feedback', 'rating', 'Rating', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(566, 1, 'feedback', 'indicator_field', 'Review Indicator Field', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(567, 1, 'feedback', 'survey_info', 'Survey Info', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(568, 1, 'feedback', 'employee_details', 'Employee Details', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(569, 1, 'feedback', 'submittion_date_expired', 'Submission date is expired !', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(570, 1, 'feedback', 'all_employee', 'All Employee', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(571, 1, 'feedback', 'employee', 'Employee', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(572, 1, 'feedback', 'feedback_info', 'Feedback Info', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(573, 1, 'feedback', 'selected_employees', 'Selected Employees', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(574, 1, 'complaint', 'add', 'Add New Complaint', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(575, 1, 'complaint', 'edit', 'Edit Complaint', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(576, 1, 'complaint', 'created', 'Complaint Created Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(577, 1, 'complaint', 'updated', 'Complaint Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(578, 1, 'complaint', 'deleted', 'Complaint Deleted Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(579, 1, 'complaint', 'complaint_details', 'Complaint Details', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(580, 1, 'complaint', 'delete_message', 'Are you sure you want to delete this complaint?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(581, 1, 'complaint', 'selected_delete_message', 'Are you sure you want to delete selected complaint?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(582, 1, 'complaint', 'title', 'Title', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(583, 1, 'complaint', 'date_time', 'Date Time', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(584, 1, 'complaint', 'description', 'Description', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(585, 1, 'complaint', 'status', 'Status', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(586, 1, 'complaint', 'to_user_id', 'To Staff', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(587, 1, 'complaint', 'pending', 'Pending', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(588, 1, 'complaint', 'approved', 'Approved', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(589, 1, 'complaint', 'rejected', 'Rejected', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(590, 1, 'complaint', 'from_staff', 'From Staff', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(591, 1, 'complaint', 'against_you', 'Against You', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(592, 1, 'complaint', 'by_you', 'By You', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(593, 1, 'complaint', 'proff_of_document', 'Proff Of Document', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(594, 1, 'complaint', 'to_staff', 'To Staff', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(595, 1, 'complaint', 'approved_message', 'Are you sure you want to approved this complaint?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(596, 1, 'complaint', 'approved_success', 'Complaint Approved Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(597, 1, 'complaint', 'reply_notes', 'Reply Notes', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(598, 1, 'complaint', 'update_complaint_status', 'Update Complaint Status', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(599, 1, 'complaint', 'complaint_status_updated', 'Complaint Status Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(600, 1, 'complaint', 'reply', 'Reply', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(601, 1, 'complaint', 'no_complaints', 'There is no complaints yet.', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(602, 1, 'complaint', 'no_complaints_against_you', 'There is no complaints against you yet.', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(603, 1, 'complaint', 'no_complaints_by_you', 'There is no complaints by you yet.', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(604, 1, 'news', 'add', 'Add New News', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(605, 1, 'news', 'edit', 'Edit News', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(606, 1, 'news', 'created', 'News Created Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(607, 1, 'news', 'updated', 'News Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(608, 1, 'news', 'deleted', 'News Deleted Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(609, 1, 'news', 'news_details', 'News Details', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(610, 1, 'news', 'delete_message', 'Are you sure you want to delete this news?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(611, 1, 'news', 'selected_delete_message', 'Are you sure you want to delete selected news?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(612, 1, 'news', 'title', 'Title', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(613, 1, 'news', 'News', 'News', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(614, 1, 'news', 'description', 'Description', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(615, 1, 'news', 'status', 'Status', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(616, 1, 'news', 'visible_to_all', 'Visible To All', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(617, 1, 'news', 'user_id', 'Employees', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(618, 1, 'news', 'news_id', 'News', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(619, 1, 'news', 'all', 'All', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(620, 1, 'news', 'select_employee', 'Select Employees', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(621, 1, 'news', 'publish', 'Publish', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(622, 1, 'news', 'cancel', 'Cancel', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(623, 1, 'news', 'confirm_message', 'Are you sure want to publish this news?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(624, 1, 'news', 'news_published', 'News Published', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(625, 1, 'news', 'news_published_success', 'The News is published sucessfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(626, 1, 'news', 'no_news', 'No News Found', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(627, 1, 'location', 'add', 'Add New Location', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(628, 1, 'location', 'edit', 'Edit Location', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(629, 1, 'location', 'created', 'Location Created Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(630, 1, 'location', 'updated', 'Location Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(631, 1, 'location', 'deleted', 'Location Deleted Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(632, 1, 'location', 'location_details', 'Location Details', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(633, 1, 'location', 'delete_message', 'Are you sure you want to delete this location?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(634, 1, 'location', 'selected_delete_message', 'Are you sure you want to delete selected location?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(635, 1, 'location', 'name', 'Name', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(636, 1, 'location', 'address', 'Address', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(637, 1, 'asset_type', 'add', 'Add New Asset Type', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(638, 1, 'asset_type', 'edit', 'Edit Asset Type', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(639, 1, 'asset_type', 'created', 'Asset Type Created Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(640, 1, 'asset_type', 'updated', 'Asset Type Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(641, 1, 'asset_type', 'deleted', 'Asset Type Deleted Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(642, 1, 'asset_type', 'asset_type_details', 'Asset Type Details', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(643, 1, 'asset_type', 'delete_message', 'Are you sure you want to delete this asset type?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(644, 1, 'asset_type', 'selected_delete_message', 'Are you sure you want to delete selected asset type?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(645, 1, 'asset_type', 'name', 'Name', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(646, 1, 'company', 'add', 'Add New Company', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(647, 1, 'company', 'edit', 'Edit Company', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(648, 1, 'company', 'created', 'Company Created Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(649, 1, 'company', 'updated', 'Company Updated Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(650, 1, 'company', 'deleted', 'Company Deleted Successfully', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(651, 1, 'company', 'currency_details', 'Company Details', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(652, 1, 'company', 'delete_message', 'Are you sure you want to delete this company?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(653, 1, 'company', 'selected_delete_message', 'Are you sure you want to delete selected company?', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(654, 1, 'company', 'name', 'Company Name', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(655, 1, 'company', 'short_name', 'Company Short Name', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(656, 1, 'company', 'email', 'Company Email', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(657, 1, 'company', 'phone', 'Company Phone', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(658, 1, 'company', 'address', 'Company Address', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(659, 1, 'company', 'currency', 'Currency', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(660, 1, 'company', 'logo', 'Company Logo', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(661, 1, 'company', 'left_sidebar_theme', 'Left Sidebar Theme', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(662, 1, 'company', 'dark', 'Dark', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(663, 1, 'company', 'light', 'Light', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(664, 1, 'company', 'dark_logo', 'Dark Logo', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(665, 1, 'company', 'light_logo', 'Light Logo', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(666, 1, 'company', 'small_dark_logo', 'Small Dark Logo', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(667, 1, 'company', 'small_light_logo', 'Small Light Logo', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(668, 1, 'company', 'primary_color', 'Primary Color', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(669, 1, 'company', 'default_timezone', 'Default Timezone', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(670, 1, 'company', 'date_format', 'Date Format', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(671, 1, 'company', 'time_format', 'Time Format', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(672, 1, 'company', 'auto_detect_timezone', 'Auto Detect Timezone', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(673, 1, 'company', 'app_debug', 'App Debug', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(674, 1, 'company', 'update_app_notification', 'Update App Notitication', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(675, 1, 'company', 'login_image', 'Login Image', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(676, 1, 'company', 'layout', 'Layout', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(677, 1, 'company', 'rtl', 'RTL', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(678, 1, 'company', 'ltr', 'LTR', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(679, 1, 'company', 'shortcut_menu_Placement', 'Add Menu Placement', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(680, 1, 'company', 'top_and_bottom', 'Top & Bottom', '2025-10-07 19:17:23', '2025-10-07 19:17:23'),
(681, 1, 'company', 'top_header', 'Top Header', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(682, 1, 'company', 'bottom_corner', 'Bottom Conrer', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(683, 1, 'company', 'shortcut_menu_setting', 'Add Menu Settings', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(684, 1, 'company', 'menu_setting_updated', 'Menu Setting Updated', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(685, 1, 'company', 'basic_details', 'Basic Details', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(686, 1, 'company', 'details', 'Details', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(687, 1, 'company', 'register_date', 'Register Date', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(688, 1, 'company', 'total_users', 'Total Users', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(689, 1, 'company', 'general_settings', 'General Settings', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(690, 1, 'company', 'appearance', 'Appearance', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(691, 1, 'company', 'preferences', 'Preferences', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(692, 1, 'company', 'localization', 'Localization', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(693, 1, 'company', 'advanced', 'Advanced', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(694, 1, 'company', 'google_geo_coding_api_key', 'Google Geocoding API KEY', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(695, 1, 'company', 'geo_coding_api', 'Google Geocoding API', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(696, 1, 'currency', 'add', 'Add New Currency', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(697, 1, 'currency', 'edit', 'Edit Currency', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(698, 1, 'currency', 'created', 'Currency Created Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(699, 1, 'currency', 'updated', 'Currency Updated Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(700, 1, 'currency', 'deleted', 'Currency Deleted Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(701, 1, 'currency', 'currency_details', 'Currency Details', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(702, 1, 'currency', 'delete_message', 'Are you sure you want to delete this currency?', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(703, 1, 'currency', 'selected_delete_message', 'Are you sure you want to delete selected currency?', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(704, 1, 'currency', 'name', 'Currency Name', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(705, 1, 'currency', 'symbol', 'Currency Symbol', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(706, 1, 'currency', 'position', 'Currency Position', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(707, 1, 'currency', 'front', 'Front', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(708, 1, 'currency', 'front_position_example', 'Example : $100', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(709, 1, 'currency', 'behind', 'Behind', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(710, 1, 'currency', 'behind_position_example', 'Example : 100$', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(711, 1, 'currency', 'code', 'Currency Code', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(712, 1, 'asset', 'add', 'Add New Asset', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(713, 1, 'asset', 'edit', 'Edit Asset', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(714, 1, 'asset', 'created', 'Asset Created Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(715, 1, 'asset', 'updated', 'Asset Updated Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(716, 1, 'asset', 'deleted', 'Asset Deleted Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(717, 1, 'asset', 'asset_details', 'Asset Details', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(718, 1, 'asset', 'delete_message', 'Are you sure you want to delete this asset?', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(719, 1, 'asset', 'selected_delete_message', 'Are you sure you want to delete selected asset?', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(720, 1, 'asset', 'name', 'Asset Name', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(721, 1, 'asset', 'image', 'Image', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(722, 1, 'asset', 'description', 'Description', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(723, 1, 'asset', 'status', 'Status', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(724, 1, 'asset', 'location_id', 'Location', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(725, 1, 'asset', 'serial_number', 'Serial Number', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(726, 1, 'asset', 'to_lend', 'To Lend', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(727, 1, 'asset', 'lent_to', 'Lent To', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(728, 1, 'asset', 'lend_date', 'Lend Date', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(729, 1, 'asset', 'return_date', 'Return Date', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(730, 1, 'asset', 'user_id', 'User', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(731, 1, 'asset', 'lend', 'Asset Lent Successfully', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(732, 1, 'asset', 'return', 'Return', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(733, 1, 'asset', 'asset_type_id', 'Asset Type', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(734, 1, 'asset', 'notes', 'Notes', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(735, 1, 'asset', 'actual_return_date', 'Actual Return Date', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(736, 1, 'asset', 'lend_by', 'Lend By', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(737, 1, 'asset', 'return_by', 'Return By', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(738, 1, 'asset', 'asset_history', 'Asset History', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(739, 1, 'asset', 'returned', 'Asset returned Successfully !', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(740, 1, 'asset', 'account_number', 'Account Number', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(741, 1, 'asset', 'purchase_date', 'Purchase Date', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(742, 1, 'asset', 'price', 'Price', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(743, 1, 'asset', 'transaction_details', 'Transaction Details', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(744, 1, 'asset', 'is_broken', 'Is Broken?', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(745, 1, 'asset', 'location', 'Location', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(746, 1, 'asset', 'broken_by', 'Broken By', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(747, 1, 'asset', 'no_assets', 'There is no asset yet.', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(748, 1, 'asset', 'no_broken_assets', 'There is no broken asset yet.', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(749, 1, 'module', 'name', 'Module Name', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(750, 1, 'module', 'verified', 'Verified', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(751, 1, 'module', 'verify_purchase_code', 'Verify Purchase Code', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(752, 1, 'module', 'current_version', 'Current Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(753, 1, 'module', 'latest_version', 'Latest Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(754, 1, 'module', 'status', 'Status', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(755, 1, 'module', 'installed_modules', 'Installed Modules', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(756, 1, 'module', 'other_modules', 'Other Modules', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(757, 1, 'module', 'module_status_updated', 'Modules Status Updated', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(758, 1, 'module', 'downloading_completed', 'Download Completed', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(759, 1, 'module', 'extract_zip_file', 'Extract Zip File', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(760, 1, 'module', 'file_extracted', 'Zip File Extracted', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(761, 1, 'update_app', 'app_details', 'App Details', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(762, 1, 'update_app', 'name', 'Name', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(763, 1, 'update_app', 'value', 'Value', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(764, 1, 'update_app', 'php_version', 'PHP Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(765, 1, 'update_app', 'app_version', 'App Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(766, 1, 'update_app', 'laravel_version', 'Laravel Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(767, 1, 'update_app', 'mysql_version', 'MySQL Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(768, 1, 'update_app', 'vue_version', 'Vue Version', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(769, 1, 'update_app', 'update_app', 'Update App', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(770, 1, 'update_app', 'update_now', 'Update Now', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(771, 1, 'update_app', 'update_available', 'Update Available', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(772, 1, 'update_app', 'verify_again', 'Verify Again', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(773, 1, 'update_app', 'verify_failed', 'Verification Failed', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(774, 1, 'update_app', 'verify_failed_message', 'Your application is not registerd with us. Please verify it', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(775, 1, 'update_app', 'verified_with_other_domain', 'Your purchase code is registerd with other doamin. Please verfiy your purhcase code', '2025-10-07 19:17:24', '2025-10-07 19:17:24'),
(776, 1, 'langs', 'add', 'Add New Language', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(777, 1, 'langs', 'edit', 'Edit Language', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(778, 1, 'langs', 'details', 'Language Details', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(779, 1, 'langs', 'created', 'Language Created Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(780, 1, 'langs', 'updated', 'Language Updated Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(781, 1, 'langs', 'deleted', 'Language Deleted Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(782, 1, 'langs', 'delete_message', 'Are you sure you want to delete this language?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(783, 1, 'langs', 'selected_delete_message', 'Are you sure you want to delete selected language?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(784, 1, 'langs', 'view_all_langs', 'View All Languages', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(785, 1, 'langs', 'status_updated', 'Langugage status updated', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(786, 1, 'langs', 'name', 'Name', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(787, 1, 'langs', 'key', 'Key', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(788, 1, 'langs', 'flag', 'Flag', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(789, 1, 'langs', 'enabled', 'Enabled', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(790, 1, 'translations', 'fetch_new_translations', 'Fetch New Translations', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(791, 1, 'translations', 'reload_translations', 'Reload Translations', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(792, 1, 'translations', 'reload_successfully', 'Translations Reload Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(793, 1, 'translations', 'fetched_successfully', 'Translations Fetch Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(794, 1, 'translations', 'import_translations', 'Import Translations', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(795, 1, 'storage_settings', 'updated', 'Storage Settings Updated', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(796, 1, 'storage_settings', 'storage', 'Storage', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(797, 1, 'storage_settings', 'local', 'Local', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(798, 1, 'storage_settings', 'aws', 'AWS S3 Storage', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(799, 1, 'storage_settings', 'aws_key', 'AWS Key', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(800, 1, 'storage_settings', 'aws_secret', 'AWS Secret', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(801, 1, 'storage_settings', 'aws_region', 'AWS Region', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(802, 1, 'storage_settings', 'aws_bucket', 'AWS Bucket', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(803, 1, 'staff_member', 'add', 'Add New Employee', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(804, 1, 'staff_member', 'edit', 'Edit Employee', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(805, 1, 'staff_member', 'created', 'Employee Created Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(806, 1, 'staff_member', 'updated', 'Employee Updated Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(807, 1, 'staff_member', 'deleted', 'Employee Deleted Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(808, 1, 'staff_member', 'staff_member_details', 'Employee Details', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(809, 1, 'staff_member', 'staff_member', 'Employee', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(810, 1, 'staff_member', 'delete_message', 'Are you sure you want to delete this employee?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(811, 1, 'staff_member', 'selected_delete_message', 'Are you sure you want to delete selected employee?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(812, 1, 'staff_member', 'import_staff_members', 'Import Employees', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(813, 1, 'expense_category', 'add', 'Add New Expense Category', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(814, 1, 'expense_category', 'edit', 'Edit Expense Category', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(815, 1, 'expense_category', 'created', 'Expense Category Created Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(816, 1, 'expense_category', 'updated', 'Expense Category Updated Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(817, 1, 'expense_category', 'deleted', 'Expense Category Deleted Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(818, 1, 'expense_category', 'delete_message', 'Are you sure you want to delete this expense category?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(819, 1, 'expense_category', 'selected_delete_message', 'Are you sure you want to delete selected expense category?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(820, 1, 'expense_category', 'expense_category_details', 'Expense Category Details', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(821, 1, 'expense_category', 'name', 'Expense Category Name', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(822, 1, 'expense_category', 'description', 'Description', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(823, 1, 'expense_category', 'expense_for', 'Expense For', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(824, 1, 'expense_category', 'employee_specific', 'Employee Specific', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(825, 1, 'expense_category', 'company_specific', 'Company Specific', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(826, 1, 'expense', 'add', 'Add New Expense', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(827, 1, 'expense', 'edit', 'Edit Expense', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(828, 1, 'expense', 'created', 'Expense Created Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(829, 1, 'expense', 'updated', 'Expense Updated Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(830, 1, 'expense', 'deleted', 'Expense Deleted Successfully', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(831, 1, 'expense', 'delete_message', 'Are you sure you want to delete this expense?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(832, 1, 'expense', 'selected_delete_message', 'Are you sure you want to delete selected expense?', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(833, 1, 'expense', 'details', 'Expense Details', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(834, 1, 'expense', 'warehouse', 'Warehouse', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(835, 1, 'expense', 'expense_category', 'Expense Category', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(836, 1, 'expense', 'date_time', 'Date Time', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(837, 1, 'expense', 'amount', 'Amount', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(838, 1, 'expense', 'bill', 'Expense Bill', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(839, 1, 'expense', 'user', 'User', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(840, 1, 'expense', 'created_by_user', 'User', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(841, 1, 'expense', 'notes', 'Notes', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(842, 1, 'expense', 'payee', 'Payee', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(843, 1, 'expense', 'account', 'Account', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(844, 1, 'expense', 'reference_number', 'Reference Number', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(845, 1, 'expense', 'employee_claims', 'Employee Claims', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(846, 1, 'expense', 'company_claims', 'Company Claims', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(847, 1, 'expense', 'expense_type', 'Expense Type', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(848, 1, 'expense', 'expense_details', 'Expense Details', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(849, 1, 'expense', 'payment_date', 'Payment Date', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(850, 1, 'expense', 'status', 'Status', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(851, 1, 'expense', 'transaction_type', 'Transaction Type', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(852, 1, 'expense', 'pending', 'Pending', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(853, 1, 'expense', 'approved', 'Approved', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(854, 1, 'expense', 'rejected', 'Rejected', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(855, 1, 'expense', 'update_expense_status', 'Update Expense Status', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(856, 1, 'expense', 'payment_status', 'Payment Status', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(857, 1, 'expense', 'right_now', 'Right Now', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(858, 1, 'expense', 'decuct_from_payroll', 'Decuct From Payroll', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(859, 1, 'expense', 'approve_reject', 'Approve/Reject', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(860, 1, 'expense', 'no_pending_expenses', 'There is no pending expenses yet.', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(861, 1, 'expense', 'no_approved_expenses', 'There is no approved expenses yet.', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(862, 1, 'expense', 'no_rejected_expenses', 'There is no rejected expenses yet.', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(863, 1, 'expense', 'expense_date', 'Expense Date', '2025-10-07 19:17:25', '2025-10-07 19:17:25'),
(864, 1, 'expense', 'expense_records', 'Expense Record', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(865, 1, 'expense', 'expense_statement', 'Expense Statement', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(866, 1, 'expense', 'no_expense_data', 'There is no expense record found.', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(867, 1, 'company_policy', 'add', 'Add New Company Policy', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(868, 1, 'company_policy', 'edit', 'Edit Company Policy', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(869, 1, 'company_policy', 'created', 'Company Policy Created Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(870, 1, 'company_policy', 'updated', 'Company Policy Updated Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(871, 1, 'company_policy', 'deleted', 'Company Policy Deleted Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(872, 1, 'company_policy', 'delete_message', 'Are you sure you want to delete this company policy?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(873, 1, 'company_policy', 'selected_delete_message', 'Are you sure you want to delete selected company policy?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(874, 1, 'company_policy', 'location', 'Location', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(875, 1, 'company_policy', 'title', 'Title', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(876, 1, 'company_policy', 'policy_document', 'Policy Document', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(877, 1, 'company_policy', 'description', 'Description', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(878, 1, 'company_policy', 'company_policy_details', 'Company Policy Details', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(879, 1, 'company_policy', 'latest_compony_policy', 'Latest Compony Policy', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(880, 1, 'company_policy', 'method_type', 'Method Type', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(881, 1, 'company_policy', 'upload', 'Upload File', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(882, 1, 'company_policy', 'create', 'Create File', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(883, 1, 'company_policy', 'letter_description', 'Letter Description', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(884, 1, 'company_policy', 'no_company_policy', 'There is no company policy yet.', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(885, 1, 'account', 'add', 'Add New Account', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(886, 1, 'account', 'edit', 'Edit Account', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(887, 1, 'account', 'created', 'Account Created Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(888, 1, 'account', 'updated', 'Account Updated Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(889, 1, 'account', 'deleted', 'Account Deleted Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(890, 1, 'account', 'delete_message', 'Are you sure you want to delete this account?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(891, 1, 'account', 'selected_delete_message', 'Are you sure you want to delete selected account?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(892, 1, 'account', 'name', 'Name', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(893, 1, 'account', 'initial_balance', 'Initial Balance', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(894, 1, 'account', 'account_number', 'Account Number', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(895, 1, 'account', 'branch_code', 'Branch Code', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(896, 1, 'account', 'branch_address', 'Branch Address', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(897, 1, 'account', 'available_balance', 'Available Balance', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(898, 1, 'account', 'type', 'Transaction Type', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(899, 1, 'account', 'date', 'Date', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(900, 1, 'account', 'asset', 'Asset', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(901, 1, 'account', 'deposit', 'Deposit', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(902, 1, 'account', 'payroll', 'Payroll', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(903, 1, 'account', 'pre_payment', 'Pre Payment', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(904, 1, 'account', 'expense', 'Expense', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(905, 1, 'account', 'appreciation', 'Appreciation', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(906, 1, 'account', 'amount', 'Amount', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(907, 1, 'account', 'all', 'All', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(908, 1, 'account', 'account_statement', 'Account Statement', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(909, 1, 'account', 'transition', 'Transition Types', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(910, 1, 'account', 'credit', 'Credit', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(911, 1, 'account', 'debit', 'Debit', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(912, 1, 'account', 'balance', 'Balance', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(913, 1, 'account', 'opening_balance', 'Opening Balance', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(914, 1, 'account', 'description', 'Description', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(915, 1, 'account', 'transactions', 'Transactions', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(916, 1, 'account', 'total_credit', 'Total Credit Amount', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(917, 1, 'account', 'total_debit', 'Total Debit Amount', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(918, 1, 'account', 'closing_balance', 'Closing Balance', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(919, 1, 'account', 'statement_date', 'Statement Date', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(920, 1, 'account', 'statement_period', 'Statement Period', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(921, 1, 'employee_custom_field', 'add', 'Add New Employee Custom Fields', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(922, 1, 'employee_custom_field', 'edit', 'Edit Employee Custom Fields', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(923, 1, 'employee_custom_field', 'created', 'Employee Custom Fields Created Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(924, 1, 'employee_custom_field', 'updated', 'Employee Custom Fields Updated Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(925, 1, 'employee_custom_field', 'deleted', 'Employee Custom Fields Deleted Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(926, 1, 'employee_custom_field', 'delete_message', 'Are you sure you want to delete this employee custom fields?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(927, 1, 'employee_custom_field', 'selected_delete_message', 'Are you sure you want to delete selected employee custom fields?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(928, 1, 'employee_custom_field', 'name', 'Name', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(929, 1, 'employee_custom_field', 'field_type_id', 'Field Type', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(930, 1, 'employee_custom_field', 'is_required', 'Is Required', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(931, 1, 'employee_custom_field', 'default_value', 'Default Value', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(932, 1, 'employee_custom_field', 'text', 'Text', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(933, 1, 'employee_custom_field', 'large_text', 'Large Text', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(934, 1, 'employee_custom_field', 'file', 'File', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(935, 1, 'employee_custom_field', 'date', 'Date', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(936, 1, 'employee_custom_field', 'select', 'Select', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(937, 1, 'employee_custom_field', 'add_employee_fields', 'Add Employee Fields', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(938, 1, 'employee_custom_field', 'employee_field_name', 'Employee Field Name', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(939, 1, 'employee_custom_field', 'type', 'Type', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(940, 1, 'employee_custom_field', 'yes', 'Yes', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(941, 1, 'employee_custom_field', 'no', 'No', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(942, 1, 'employee_custom_field', 'employee_fields', 'Employee Fields', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(943, 1, 'employee_custom_field', 'visible_to_employee', 'Visible To Employee', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(944, 1, 'employee_custom_field', 'visible_to_employee_status', 'Status Updated Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(945, 1, 'employee_custom_field', 'no_employee_custom_fields', 'There are no employee custom fields.', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(946, 1, 'employee_custom_field', 'no_data_found', 'No Date Found', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(947, 1, 'payee', 'add', 'Add New Payee', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(948, 1, 'payee', 'edit', 'Edit Payee', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(949, 1, 'payee', 'created', 'Payee Created Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(950, 1, 'payee', 'updated', 'Payee Updated Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(951, 1, 'payee', 'deleted', 'Payee Deleted Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(952, 1, 'payee', 'delete_message', 'Are you sure you want to delete this payee?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(953, 1, 'payee', 'selected_delete_message', 'Are you sure you want to delete selected payee?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(954, 1, 'payee', 'name', 'Name', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(955, 1, 'payee', 'phone_number', 'Phone Number', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(956, 1, 'salary_group', 'add', 'Add New Salary Group', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(957, 1, 'salary_group', 'edit', 'Edit Salary Group', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(958, 1, 'salary_group', 'created', 'Salary Group Created Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(959, 1, 'salary_group', 'updated', 'Salary Group Updated Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(960, 1, 'salary_group', 'deleted', 'Salary Group Deleted Successfully', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(961, 1, 'salary_group', 'delete_message', 'Are you sure you want to delete this salary group?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(962, 1, 'salary_group', 'selected_delete_message', 'Are you sure you want to delete selected salary group?', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(963, 1, 'salary_group', 'name', 'Name', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(964, 1, 'salary_group', 'salary_component_id', 'Salary Component', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(965, 1, 'salary_group', 'user_id', 'Users', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(966, 1, 'salary_group', 'add_new_value', 'Add New Salary Component', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(967, 1, 'salary_group', 'salary_group_id', 'Salary Group', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(968, 1, 'salary_group', 'total_users', 'Total Users', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(969, 1, 'salary_group', 'validation_name', 'Name field is required', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(970, 1, 'salary_group', 'validation_type', 'Type field is required', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(971, 1, 'salary_group', 'validation_monthly', 'Monthly field is required', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(972, 1, 'salary_group', 'validation_value_type', 'Value type field is required', '2025-10-07 19:17:26', '2025-10-07 19:17:26'),
(973, 1, 'salary_component', 'add', 'Add New Salary Component', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(974, 1, 'salary_component', 'edit', 'Edit Salary Component', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(975, 1, 'salary_component', 'created', 'Salary Component Created Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(976, 1, 'salary_component', 'updated', 'Salary Component Updated Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(977, 1, 'salary_component', 'deleted', 'Salary Component Deleted Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(978, 1, 'salary_component', 'delete_message', 'Are you sure you want to delete this salary component?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(979, 1, 'salary_component', 'selected_delete_message', 'Are you sure you want to delete selected salary component?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(980, 1, 'salary_component', 'name', 'Name', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(981, 1, 'salary_component', 'type', 'Type', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(982, 1, 'salary_component', 'value_type', 'Value Type', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(983, 1, 'salary_component', 'weekly', 'Weekly', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(984, 1, 'salary_component', 'bi_weekly', 'Bi-weekly', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(985, 1, 'salary_component', 'monthly', 'Monthly', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(986, 1, 'salary_component', 'semi_monthly', 'Semi-monthly', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(987, 1, 'salary_component', 'ctc_percent', 'CTC Percent', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(988, 1, 'salary_component', 'basic_percent', 'Basic Percent', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(989, 1, 'salary_component', 'fixed', 'Fixed', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(990, 1, 'salary_component', 'variable', 'Variable', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(991, 1, 'salary_component', 'earnings', 'Earnings', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(992, 1, 'salary_component', 'deductions', 'Deductions', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(993, 1, 'salary_component', 'value', 'Value', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(994, 1, 'payer', 'add', 'Add New Payer', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(995, 1, 'payer', 'edit', 'Edit Payer', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(996, 1, 'payer', 'created', 'Payer Created Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(997, 1, 'payer', 'updated', 'Payer Updated Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(998, 1, 'payer', 'deleted', 'Payer Deleted Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27');
INSERT INTO `translations` (`id`, `lang_id`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(999, 1, 'payer', 'delete_message', 'Are you sure you want to delete this payer?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1000, 1, 'payer', 'selected_delete_message', 'Are you sure you want to delete selected payer?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1001, 1, 'payer', 'name', 'Name', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1002, 1, 'payer', 'phone_number', 'Phone Number', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1003, 1, 'deposit_category', 'add', 'Add New Deposit Category', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1004, 1, 'deposit_category', 'edit', 'Edit Deposit Category', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1005, 1, 'deposit_category', 'created', 'Deposit Category Created Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1006, 1, 'deposit_category', 'updated', 'Deposit Category Updated Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1007, 1, 'deposit_category', 'deleted', 'Deposit Category Deleted Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1008, 1, 'deposit_category', 'delete_message', 'Are you sure you want to delete this deposit category?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1009, 1, 'deposit_category', 'selected_delete_message', 'Are you sure you want to delete selected deposit category?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1010, 1, 'deposit_category', 'name', 'Name', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1011, 1, 'deposit', 'add', 'Add New Deposit', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1012, 1, 'deposit', 'edit', 'Edit Deposit', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1013, 1, 'deposit', 'created', 'Deposit Created Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1014, 1, 'deposit', 'updated', 'Deposit Updated Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1015, 1, 'deposit', 'deleted', 'Deposit Deleted Successfully', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1016, 1, 'deposit', 'delete_message', 'Are you sure you want to delete this deposit?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1017, 1, 'deposit', 'selected_delete_message', 'Are you sure you want to delete selected deposit?', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1018, 1, 'deposit', 'account', 'Account', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1019, 1, 'deposit', 'payer', 'Payer', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1020, 1, 'deposit', 'amount', 'Amount', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1021, 1, 'deposit', 'date_time', 'Date Time', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1022, 1, 'deposit', 'reference_number', 'Reference Number', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1023, 1, 'deposit', 'file', 'File', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1024, 1, 'deposit', 'notes', 'Notes', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1025, 1, 'deposit', 'deposit_category', 'Deposit Category', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1026, 1, 'deposit', 'deposit_details', 'Deposit Details', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1027, 1, 'hrm_dashboard', 'today_attendance', 'Attendances', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1028, 1, 'hrm_dashboard', 'current_ip_address', 'Current IP Address', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1029, 1, 'hrm_dashboard', 'current_time', 'Current Time', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1030, 1, 'hrm_dashboard', 'clock_in', 'Clock In', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1031, 1, 'hrm_dashboard', 'clocked_in', 'Clocked In', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1032, 1, 'hrm_dashboard', 'clock_out', 'Clock Out', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1033, 1, 'hrm_dashboard', 'clocked_out', 'Clocked Out', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1034, 1, 'hrm_dashboard', 'office_hours_expired', 'Times Up: Office Hours Expired. Ensure Timely Clock-In and Clock-Out to Keep Attendance Records Accurate.', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1035, 1, 'hrm_dashboard', 'pending_approvals', 'Pending Approvals', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1036, 1, 'hrm_dashboard', 'not_marked', 'Not Marked', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1037, 1, 'hrm_dashboard', 'present', 'Present', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1038, 1, 'hrm_dashboard', 'absent', 'Absent', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1039, 1, 'hrm_dashboard', 'today_is_holiday', 'Today is holiday...', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1040, 1, 'hrm_dashboard', 'you_are_on_leave', 'You are on leave so you cannot clock-in... Enjoy your leave.', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1041, 1, 'hrm_dashboard', 'self_clocking_is_disabled', 'Self clocking is disabled by admin', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1042, 1, 'hrm_dashboard', 'pending_leaves', 'Leaves', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1043, 1, 'hrm_dashboard', 'pending_expenses', 'Expenses', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1044, 1, 'hrm_dashboard', 'clock_in_out', 'Clock-In/Out', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1045, 1, 'hrm_dashboard', 'all_departments', 'All Departments', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1046, 1, 'hrm_dashboard', 'design', 'Design', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1047, 1, 'hrm_dashboard', 'today', 'Today', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1048, 1, 'hrm_dashboard', 'this_week', 'This Week', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1049, 1, 'hrm_dashboard', 'this_month', 'This Month', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1050, 1, 'hrm_dashboard', 'production', 'Production', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1051, 1, 'hrm_dashboard', 'view_all_attendance', 'View All Attendance', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1052, 1, 'hrm_dashboard', 'development', 'Development', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1053, 1, 'hrm_dashboard', 'view_all', 'View All', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1054, 1, 'hrm_dashboard', 'tomorrow', 'Tomorrow', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1055, 1, 'hrm_dashboard', 'no_of_employees_increased_by', 'No of Employees Increased by', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1056, 1, 'hrm_dashboard', 'birthdays', 'Birthdays', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1057, 1, 'hrm_dashboard', 'employees_by_department', 'Employees By Department', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1058, 1, 'hrm_dashboard', 'total_employee', 'Total Employees', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1059, 1, 'hrm_dashboard', 'view_all_employees', 'View All Employees', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1060, 1, 'hrm_dashboard', 'performance', 'Performance', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1061, 1, 'hrm_dashboard', 'send', 'Send', '2025-10-07 19:17:27', '2025-10-07 19:17:27'),
(1062, 1, 'hrm_dashboard', 'top_performer', 'Top Performer', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1063, 1, 'hrm_dashboard', 'your_operation_has_been_executed', 'Your operation has been executed', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1064, 1, 'hrm_dashboard', 'no_attendace_mark', 'No attendance mark yet', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1065, 1, 'hrm_dashboard', 'view_all_birthdays', 'View all birthdays', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1066, 1, 'hrm_dashboard', 'there_is_no_birthday', 'There is no birthdays', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1067, 1, 'hrm_dashboard', 'total_attendance', 'Total Attendance', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1068, 1, 'hrm_dashboard', 'number_of_employees', 'No of Employees', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1069, 1, 'hrm_dashboard', 'employee_status', 'Employee Status', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1070, 1, 'hrm_dashboard', 'phone_number', 'Phone Number', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1071, 1, 'hrm_dashboard', 'email', 'Email', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1072, 1, 'hrm_dashboard', 'address', 'Address', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1073, 1, 'hrm_dashboard', 'joining_date', 'Joining Date', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1074, 1, 'hrm_dashboard', 'yesterday', 'Yesterday', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1075, 1, 'hrm_dashboard', 'this_year', 'This Year', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1076, 1, 'hrm_dashboard', 'year', 'This Year', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1077, 1, 'hrm_dashboard', 'weekend_holiday', 'Weekend & Holidays', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1078, 1, 'hrm_dashboard', 'weekend', 'Weekend', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1079, 1, 'hrm_dashboard', 'holiday', 'Holiday', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1080, 1, 'hrm_dashboard', 'there_is_no_anniversary', 'There is no upcoming work anniversary', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1081, 1, 'hrm_dashboard', 'there_is_no_weekends', 'There is no weekends', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1082, 1, 'hrm_dashboard', 'there_is_no_holidays', 'There is no holidays', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1083, 1, 'hrm_dashboard', 'there_is_no_pending_leaves', 'There is no pending leaves', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1084, 1, 'hrm_dashboard', 'there_is_no_pending_expenses', 'There is no pending expenses', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1085, 1, 'hrm_dashboard', 'warnings', 'Warnings', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1086, 1, 'hrm_dashboard', 'expenses', 'Expenses', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1087, 1, 'hrm_dashboard', 'complaints', 'Complaints', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1088, 1, 'hrm_dashboard', 'appreciations', 'Appreciations', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1089, 1, 'hrm_dashboard', 'assets_lent', 'Assets Lent', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1090, 1, 'hrm_dashboard', 'assign_survey', 'Assign Survey', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1091, 1, 'hrm_dashboard', 'employee_work_status', 'Employee Work Status', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1092, 1, 'hrm_dashboard', 'attendance_details', 'Attendance Details', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1093, 1, 'hrm_dashboard', 'lastWeek', 'Last Week', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1094, 1, 'hrm_dashboard', 'lastMonth', 'Last Month', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1095, 1, 'hrm_dashboard', 'lastYear', 'Last Year', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1096, 1, 'hrm_dashboard', 'there_are_no_increment_promotions', 'There are no increment promotions', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1097, 1, 'hrm_dashboard', 'start', 'Start', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1098, 1, 'hrm_dashboard', 'pause', 'Pause', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1099, 1, 'hrm_dashboard', 'reason', 'Reason ', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1100, 1, 'hrm_dashboard', 'reason_required', 'Please enter reason', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1101, 1, 'hrm_dashboard', 'modal_title', 'Are you sure you want to clock out?', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1102, 1, 'hrm_dashboard', 'modal_content', 'Once you clock out, you can\'t make changes for today.', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1103, 1, 'hrm_dashboard', 'modal_text', 'Yes, Clock Out', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1104, 1, 'hrm_dashboard', 'add_reason', 'Add Reason', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1105, 1, 'increment_promotion', 'add', 'Add New Increment/Promotion', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1106, 1, 'increment_promotion', 'edit', 'Edit Increment/Promotion', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1107, 1, 'increment_promotion', 'created', 'Increment/Promotion Created Successfully', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1108, 1, 'increment_promotion', 'updated', 'Increment/Promotion Updated Successfully', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1109, 1, 'increment_promotion', 'deleted', 'Increment/Promotion Deleted Successfully', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1110, 1, 'increment_promotion', 'increment_and_promotion_details', 'Increment/Promotion Details', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1111, 1, 'increment_promotion', 'delete_message', 'Are you sure you want to delete this increment/promotion?', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1112, 1, 'increment_promotion', 'selected_delete_message', 'Are you sure you want to delete selected increment/promotion?', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1113, 1, 'increment_promotion', 'type', 'Type', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1114, 1, 'increment_promotion', 'date', 'Date', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1115, 1, 'increment_promotion', 'user_id', 'User', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1116, 1, 'increment_promotion', 'total_duration', 'Total Duration', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1117, 1, 'increment_promotion', 'description', 'Description', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1118, 1, 'increment_promotion', 'net_salary', 'Annual CTC', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1119, 1, 'increment_promotion', 'promoted_designation_id', 'Promoted Designation', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1120, 1, 'increment_promotion', 'current_designation_id', 'Current Designation', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1121, 1, 'increment_promotion', 'increment', 'Increment', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1122, 1, 'increment_promotion', 'promotion', 'Promotion', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1123, 1, 'increment_promotion', 'increment_promotion', 'Increment/Promotion', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1124, 1, 'increment_promotion', 'details', 'Details', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1125, 1, 'increment_promotion', 'update_basic_salary', 'Update Annual CTC', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1126, 1, 'increment_promotion', 'update_designation', 'Update Designation', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1127, 1, 'increment_promotion', 'decrement_demotion', 'Decrement/Demotion', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1128, 1, 'increment_promotion', 'decrement', 'Decrement', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1129, 1, 'increment_promotion', 'basic_details', 'Basic Details', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1130, 1, 'increment_promotion', 'letter_head_details', 'Letter Head Details', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1131, 1, 'increment_promotion', 'letterhead_template_id', 'Letter Head Template', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1132, 1, 'increment_promotion', 'current_designation', 'Current Designation', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1133, 1, 'increment_promotion', 'promoted_designation', 'Promoted Designation', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1134, 1, 'increment_promotion', 'no_increments_promotions', 'There is no increments/promotions', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1135, 1, 'holiday', 'add', 'Add New Holiday', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1136, 1, 'holiday', 'edit', 'Edit Holiday', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1137, 1, 'holiday', 'created', 'Holiday Created Successfully', '2025-10-07 19:17:28', '2025-10-07 19:17:28'),
(1138, 1, 'holiday', 'updated', 'Holiday Updated Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1139, 1, 'holiday', 'deleted', 'Holiday Deleted Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1140, 1, 'holiday', 'holiday_details', 'Holiday Details', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1141, 1, 'holiday', 'delete_message', 'Are you sure you want to delete this holiday?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1142, 1, 'holiday', 'selected_delete_message', 'Are you sure you want to delete selected holiday?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1143, 1, 'holiday', 'display_name', 'Display Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1144, 1, 'holiday', 'name', 'Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1145, 1, 'holiday', 'year', 'Year', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1146, 1, 'holiday', 'date', 'Date', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1147, 1, 'holiday', 'mark_weekend_holiday', 'Mark Weekend Holiday', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1148, 1, 'holiday', 'created_by', 'Created By', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1149, 1, 'holiday', 'mark_holiday', 'Marked Holiday', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1150, 1, 'holiday', 'from', 'From', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1151, 1, 'holiday', 'to', 'To', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1152, 1, 'holiday', 'month', 'Month', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1153, 1, 'holiday', 'ocassion_name', 'Ocassion Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1154, 1, 'holiday', 'holiday_calendar', 'Holiday Calendar', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1155, 1, 'holiday', 'import_holiday_from', 'Import Holiday From', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1156, 1, 'holiday', 'import_holiday_to', 'Import Holiday To', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1157, 1, 'holiday', 'the_name_field_is_required', 'The name field is required', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1158, 1, 'holiday', 'the_date_field_is_required', 'The date field is required', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1159, 1, 'holiday', 'view_all', 'View All', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1160, 1, 'holiday', 'next_holiday', 'Next Holiday', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1161, 1, 'holiday', 'is_half_day', 'Is Half Day', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1162, 1, 'holiday', 'half_holiday', 'Half Holiday', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1163, 1, 'holiday', 'award_id', 'Award', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1164, 1, 'holiday', 'no_weekends', 'There is no weekends', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1165, 1, 'holiday', 'no_holidays', 'There is no holidays', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1166, 1, 'holiday', 'no_holiday_types', 'There is no holiday types', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1167, 1, 'leave_type', 'add', 'Add New Leave Type', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1168, 1, 'leave_type', 'edit', 'Edit Leave Type', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1169, 1, 'leave_type', 'created', 'Leave Type Created Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1170, 1, 'leave_type', 'updated', 'Leave Type Updated Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1171, 1, 'leave_type', 'deleted', 'Leave Type Deleted Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1172, 1, 'leave_type', 'leave_type_details', 'LeaveType Details', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1173, 1, 'leave_type', 'delete_message', 'Are you sure you want to delete this leave type?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1174, 1, 'leave_type', 'selected_delete_message', 'Are you sure you want to delete selected leave type?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1175, 1, 'leave_type', 'display_name', 'Display Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1176, 1, 'leave_type', 'name', 'Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1177, 1, 'leave_type', 'is_paid', 'Is Paid', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1178, 1, 'leave_type', 'total_leaves', 'Total Leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1179, 1, 'leave_type', 'leave_interval_count', 'Leave Interval Count', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1180, 1, 'leave_type', 'max_leaves_per_month', 'Max Leaves Per Month', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1181, 1, 'leave_type', 'same_for_all', 'Same For All', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1182, 1, 'leave_type', 'employee_specific', 'Employee Specific', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1183, 1, 'leave_type', 'count_type', 'Count Type', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1184, 1, 'leave_type', 'employee_specific_leave_count', 'Employee Specific Leave Count', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1185, 1, 'leave_type', 'user_id', 'User', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1186, 1, 'leave_type', 'add_employees', 'Add Employees', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1187, 1, 'leave_type', 'edit_count', 'Edit Count', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1188, 1, 'leave_type', 'user_required', 'User is required', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1189, 1, 'leave_type', 'counts_required', 'Total Count is required', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1190, 1, 'leave_type', 'total_count', 'Total Count', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1191, 1, 'employee_work_status', 'add', 'Add New Employee Work Status', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1192, 1, 'employee_work_status', 'edit', 'Edit Employee Work Status', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1193, 1, 'employee_work_status', 'created', 'Employee Work Status Created Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1194, 1, 'employee_work_status', 'updated', 'Employee Work Status Updated Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1195, 1, 'employee_work_status', 'deleted', 'Employee Work Status Deleted Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1196, 1, 'employee_work_status', 'leave_type_details', 'LeaveType Details', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1197, 1, 'employee_work_status', 'delete_message', 'Are you sure you want to delete this employee work status?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1198, 1, 'employee_work_status', 'selected_delete_message', 'Are you sure you want to delete selected employee work status?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1199, 1, 'employee_work_status', 'name', 'Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1200, 1, 'employee_work_status', 'fulltime', 'Fulltime', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1201, 1, 'employee_work_status', 'contract', 'Contract', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1202, 1, 'employee_work_status', 'probation', 'Probation', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1203, 1, 'employee_work_status', 'work_from_home', 'Work From Home', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1204, 1, 'leave', 'add', 'Add New Leave', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1205, 1, 'leave', 'edit', 'Edit Leave', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1206, 1, 'leave', 'created', 'Leave Created Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1207, 1, 'leave', 'updated', 'Leave Updated Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1208, 1, 'leave', 'deleted', 'Leave Deleted Successfully', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1209, 1, 'leave', 'leave_details', 'Leave Details', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1210, 1, 'leave', 'delete_message', 'Are you sure you want to delete this leave?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1211, 1, 'leave', 'selected_delete_message', 'Are you sure you want to delete selected leave?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1212, 1, 'leave', 'display_name', 'Display Name', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1213, 1, 'leave', 'user_id', 'User', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1214, 1, 'leave', 'leave_type', 'Leave Type', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1215, 1, 'leave', 'is_paid', 'Is Paid', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1216, 1, 'leave', 'start_date', 'Start Date', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1217, 1, 'leave', 'end_date', 'End Date', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1218, 1, 'leave', 'is_half_day', 'Is Half Day', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1219, 1, 'leave', 'reason', 'Reason', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1220, 1, 'leave', 'file', 'File', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1221, 1, 'leave', 'status', 'Status', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1222, 1, 'leave', 'bill', 'Bill', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1223, 1, 'leave', 'date', 'Date', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1224, 1, 'leave', 'user', 'User', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1225, 1, 'leave', 'leave_status', 'Leave Status', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1226, 1, 'leave', 'department', 'Department', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1227, 1, 'leave', 'employees', 'Employees', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1228, 1, 'leave', 'mark_attendance', 'Mark Attendance By', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1229, 1, 'leave', 'clock_out', 'Clock Out', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1230, 1, 'leave', 'clock_in', 'Clock In', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1231, 1, 'leave', 'late', 'Late', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1232, 1, 'leave', 'half_day', 'Half Day', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1233, 1, 'leave', 'multiple_date', 'You can select multiple dates', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1234, 1, 'leave', 'clock_in_month', 'Clock in Month', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1235, 1, 'leave', 'attendance_overwrite', 'Attendance Overwrite', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1236, 1, 'leave', 'add_multiple', 'Add Multiple Attendance', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1237, 1, 'leave', 'approved_message', 'Are you sure you want to Approved this leave?', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1238, 1, 'leave', 'approve_reject', 'Approve/Reject', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1239, 1, 'leave', 'total_leaves', 'Total Leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1240, 1, 'leave', 'absent', 'Absent', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1241, 1, 'leave', 'worked_days', 'Worked Days', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1242, 1, 'leave', 'unpaid', 'Unpaid Leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1243, 1, 'leave', 'paid', 'Paid Leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1244, 1, 'leave', 'apply_new_leave', 'Apply New Leave', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1245, 1, 'leave', 'total_attendance', 'Total Attendance', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1246, 1, 'leave', 'present', 'Present', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1247, 1, 'leave', 'leaves', 'Leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1248, 1, 'leave', 'late_attendance', 'Late Attendance', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1249, 1, 'leave', 'half_leave', 'Half Leave For', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1250, 1, 'leave', 'total_days', 'Total Days', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1251, 1, 'leave', 'days', 'Day(s)', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1252, 1, 'leave', 'end_data_required', 'End date is required', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1253, 1, 'leave', 'no_leaves', 'There is no leaves yet.', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1254, 1, 'leave', 'no_pending_leaves', 'There is no pending leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1255, 1, 'leave', 'no_approved_leaves', 'There is no approved leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1256, 1, 'leave', 'no_rejected_leaves', 'There is no rejected leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1257, 1, 'leave', 'no_remaining_leaves', 'There is no remaining leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1258, 1, 'leave', 'remaining_leaves', 'Remaining Leaves', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1259, 1, 'leave', 'leave_month', 'Leave Month', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1260, 1, 'leave', 'counts', 'Counts', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1261, 1, 'award', 'add', 'Add New Award', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1262, 1, 'award', 'edit', 'Edit Award', '2025-10-07 19:17:29', '2025-10-07 19:17:29'),
(1263, 1, 'award', 'created', 'Award Created Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1264, 1, 'award', 'updated', 'Award Updated Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1265, 1, 'award', 'deleted', 'Award Deleted Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1266, 1, 'award', 'award_details', 'Award Details', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1267, 1, 'award', 'delete_message', 'Are you sure you want to delete this award?', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1268, 1, 'award', 'name', 'Name', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1269, 1, 'award', 'active', 'Active', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1270, 1, 'award', 'description', 'Description', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1271, 1, 'award', 'award_price', 'Award Price', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1272, 1, 'appreciation', 'add', 'Add New Appreciation', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1273, 1, 'appreciation', 'edit', 'Edit Appreciation', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1274, 1, 'appreciation', 'created', 'Appreciation Created Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1275, 1, 'appreciation', 'updated', 'Appreciation Updated Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1276, 1, 'appreciation', 'deleted', 'Appreciation Deleted Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1277, 1, 'appreciation', 'appreciation_details', 'Appreciation Details', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1278, 1, 'appreciation', 'delete_message', 'Are you sure you want to delete this appreciation?', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1279, 1, 'appreciation', 'date', 'Date', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1280, 1, 'appreciation', 'description', 'Description', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1281, 1, 'appreciation', 'user', 'User', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1282, 1, 'appreciation', 'award', 'Award', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1283, 1, 'appreciation', 'price_amount', 'Price Amount', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1284, 1, 'appreciation', 'price_given', 'Price Given', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1285, 1, 'appreciation', 'add_price_given', 'Add Price Given', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1286, 1, 'appreciation', 'price_given_placeholder', 'Holiday Vouchar, Movie Tickets etc...', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1287, 1, 'appreciation', 'profile_image', 'Profile Image', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1288, 1, 'appreciation', 'letterhead_template', 'Letterhead Template', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1289, 1, 'appreciation', 'generates', 'Generates', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1290, 1, 'appreciation', 'account_number', 'Account Number', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1291, 1, 'appreciation', 'letterhead_title', 'Letterhead Title', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1292, 1, 'appreciation', 'appreciations_count', 'Appreciation Count', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1293, 1, 'appreciation', 'no_appreciations', 'There is no appreciations yet.', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1294, 1, 'appreciation', 'appreciation_letter', 'Appreciation Letter', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1295, 1, 'payroll', 'add', 'Add New Payroll', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1296, 1, 'payroll', 'edit', 'Edit Payroll', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1297, 1, 'payroll', 'created', 'Payroll Created Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1298, 1, 'payroll', 'updated', 'Payroll Updated Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1299, 1, 'payroll', 'deleted', 'Payroll Deleted Successfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1300, 1, 'payroll', 'payroll_details', 'Payroll Details', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1301, 1, 'payroll', 'delete_message', 'Are you sure you want to delete this payroll?', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1302, 1, 'payroll', 'month', 'Month', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1303, 1, 'payroll', 'year', 'Year', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1304, 1, 'payroll', 'user_id', 'User', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1305, 1, 'payroll', 'net_salary', 'Net Salary', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1306, 1, 'payroll', 'status', 'Status', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1307, 1, 'payroll', 'payment_date', 'Payment Date', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1308, 1, 'payroll', 'generate', 'Generate', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1309, 1, 'payroll', 'generated', 'Generated', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1310, 1, 'payroll', 'regenerate', 'Regenerate', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1311, 1, 'payroll', 'regenerated', 'Regenerated', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1312, 1, 'payroll', 'bonus', 'Bonus', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1313, 1, 'payroll', 'earning', 'Earning', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1314, 1, 'payroll', 'amount', 'Amount', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1315, 1, 'payroll', 'add_earning', 'Add Earning', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1316, 1, 'payroll', 'deduction', 'Deduction', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1317, 1, 'payroll', 'total_days', 'Total Days', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1318, 1, 'payroll', 'working_days', 'Working Days', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1319, 1, 'payroll', 'present_days', 'Present Days', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1320, 1, 'payroll', 'total_office_time', 'Total Office Time', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1321, 1, 'payroll', 'total_worked_time', 'Total Worked Time', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1322, 1, 'payroll', 'half_days', 'Half Days', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1323, 1, 'payroll', 'late_days', 'Late Days', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1324, 1, 'payroll', 'paid_leaves', 'Paid Leaves', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1325, 1, 'payroll', 'unpaid_leaves', 'Unpaid Leaves', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1326, 1, 'payroll', 'holiday_count', 'Holiday Count', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1327, 1, 'payroll', 'leaves', 'Leaves', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1328, 1, 'payroll', 'holiday', 'Holiday', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1329, 1, 'payroll', 'summary', 'Summary', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1330, 1, 'payroll', 'pre_payment_deduction', 'Pre Payment Deduction', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1331, 1, 'payroll', 'salary_component', 'Salary Component', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1332, 1, 'payroll', 'expense_amount', 'Expense Amount', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1333, 1, 'payroll', 'basic_salary', 'Basic Salary', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1334, 1, 'payroll', 'salary_amount', 'Salary Amount', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1335, 1, 'payroll', 'paid', 'Paid', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1336, 1, 'payroll', 'payroll_status', 'Payroll Status', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1337, 1, 'payroll', 'date', 'Date', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1338, 1, 'payroll', 'status_generated', 'Status Updated Sucessfully', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1339, 1, 'payroll', 'setup_basic_salary_to_generate_payroll', 'If you want to generate payroll for an employee then first setup basic salary for that employee.', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1340, 1, 'payroll', 'basic_salary_setup', 'Basic Salary Setup', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1341, 1, 'payroll', 'account', 'Account', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1342, 1, 'payroll', 'selected_delete_message', 'Are you sure you want to delete selected Payroll?', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1343, 1, 'payroll', 'adds', 'Add', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1344, 1, 'payroll', 'add_additional_earnings', 'Add Additional Earnings', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1345, 1, 'payroll', 'special_allowances', 'Special Allowances', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1346, 1, 'payroll', 'earnings', 'Earnings', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1347, 1, 'payroll', 'deductions', 'Deductions', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1348, 1, 'payroll', 'monthly_value', 'Monthly Value', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1349, 1, 'payroll', 'additional_earnings', 'Additional Earnings', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1350, 1, 'payroll', 'pre_payment', 'Pre Payment', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1351, 1, 'payroll', 'leave_deductions', 'Leave Deduction', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1352, 1, 'payroll', 'gross_earnings', 'Gross Earnings', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1353, 1, 'payroll', 'total_deductions', 'Total Deductions', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1354, 1, 'payroll', 'reimbursements', 'Reimbursements', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1355, 1, 'payroll', 'expense_claim', 'Expense Claim', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1356, 1, 'payroll', 'payroll_slip', 'Payroll Slip', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1357, 1, 'payroll', 'name', 'Name', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1358, 1, 'payroll', 'department', 'Department', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1359, 1, 'payroll', 'designation', 'Designation', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1360, 1, 'payroll', 'employee_id', 'Employee Id', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1361, 1, 'payroll', 'salary_slip', 'Salary Slip', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1362, 1, 'payroll', 'joining_date', 'Joining Date', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1363, 1, 'payroll', 'pay_slip_for_duration', 'Pay Slip For Duration', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1364, 1, 'payroll', 'year_not_available', 'Year Not Available', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1365, 1, 'payroll', 'special_allowances_error', 'Special Allowances cannot be negative', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1366, 1, 'payroll', 'special_allowances_calculation', 'Your calculation is wrong. Special Allowances cannot be negative.', '2025-10-07 19:17:30', '2025-10-07 19:17:30'),
(1367, 1, 'payroll', 'view_salary', 'View Salary', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1368, 1, 'payroll', 'no_payrolls', 'There is no payrolls yet.', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1369, 1, 'pre_payment', 'add', 'Add New Pre Payment', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1370, 1, 'pre_payment', 'edit', 'Edit Pre Payment', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1371, 1, 'pre_payment', 'created', 'Pre Payment Created Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1372, 1, 'pre_payment', 'updated', 'Pre Payment Updated Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1373, 1, 'pre_payment', 'deleted', 'Pre Payment Deleted Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1374, 1, 'pre_payment', 'pre_payment_details', 'Pre Payment Details', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1375, 1, 'pre_payment', 'delete_message', 'Are you sure you want to delete this Pre Payment?', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1376, 1, 'pre_payment', 'selected_delete_message', 'Are you sure you want to delete selected Pre Payment?', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1377, 1, 'pre_payment', 'user_id', 'User', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1378, 1, 'pre_payment', 'date_time', 'Date', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1379, 1, 'pre_payment', 'amount', 'Amount', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1380, 1, 'pre_payment', 'notes', 'Notes', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1381, 1, 'pre_payment', 'month', 'Month', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1382, 1, 'pre_payment', 'deduct_from_payroll', 'Deduct From Payroll', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1383, 1, 'pre_payment', 'payroll_month', 'Payroll Month', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1384, 1, 'pre_payment', 'payroll_year', 'Payroll Year', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1385, 1, 'pre_payment', 'on_given_payment_month', 'On Given Payment Month', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1386, 1, 'pre_payment', 'another_month', 'Another Month', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1387, 1, 'pre_payment', 'deduct_month', 'Deduct Month', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1388, 1, 'pre_payment', 'deduct_account_number', 'Deduct Account Number', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1389, 1, 'pre_payment', 'no_pre_payments', 'There is no pre payments yet.', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1390, 1, 'pdf_font', 'add', 'Add New Pdf Font', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1391, 1, 'pdf_font', 'edit', 'Edit Pdf Font', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1392, 1, 'pdf_font', 'created', 'Pdf Font Created Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1393, 1, 'pdf_font', 'updated', 'Pdf Font Updated Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1394, 1, 'pdf_font', 'deleted', 'Pdf Font Deleted Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1395, 1, 'pdf_font', 'appreciation_details', 'Pdf Font Details', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1396, 1, 'pdf_font', 'delete_message', 'Are you sure you want to delete this pdf font?', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1397, 1, 'pdf_font', 'name', 'Name', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1398, 1, 'pdf_font', 'file', 'File (.ttf)', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1399, 1, 'pdf_font', 'user_kashida', 'User Kashida', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1400, 1, 'pdf_font', 'use_otl', 'Use Otl', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1401, 1, 'pdf_font', 'pdf_settings', 'Pdf Settings', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1402, 1, 'pdf_font', 'use_custom_font', 'Use Custom Font', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1403, 1, 'pdf_font', 'current', 'Current', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1404, 1, 'pdf_font', 'font_settings', 'Font Settings', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1405, 1, 'pdf_font', 'appearance', 'Appearance', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1406, 1, 'pdf_font', 'spacing', 'Spacing', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1407, 1, 'pdf_font', 'pdf_font_size', 'Pdf Font Size', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1408, 1, 'pdf_font', 'holiday_pdf', 'Holiday Pdf', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1409, 1, 'pdf_font', 'generate_pdf', 'Generate Pdf', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1410, 1, 'attendance', 'add', 'Add New Attendance', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1411, 1, 'attendance', 'edit', 'Edit Attendance', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1412, 1, 'attendance', 'created', 'Attendance Created Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1413, 1, 'attendance', 'updated', 'Attendance Updated Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1414, 1, 'attendance', 'deleted', 'Attendance Deleted Successfully', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1415, 1, 'attendance', 'delete_message', 'Are you sure you want to delete this attendance?', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1416, 1, 'attendance', 'selected_delete_message', 'Are you sure you want to delete selected attendance?', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1417, 1, 'attendance', 'month', 'Month', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1418, 1, 'attendance', 'year', 'Year', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1419, 1, 'attendance', 'user_id', 'Employees', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1420, 1, 'attendance', 'name', 'Name', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1421, 1, 'attendance', 'present', 'Present', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1422, 1, 'attendance', 'present_days', 'Present Days', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1423, 1, 'attendance', 'working_days', 'Working Days', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1424, 1, 'attendance', 'total_office_time', 'Total Office Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1425, 1, 'attendance', 'half_day', 'Half Days', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1426, 1, 'attendance', 'absent', 'Absent', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1427, 1, 'attendance', 'holiday', 'Holiday', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1428, 1, 'attendance', 'date', 'Date', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1429, 1, 'attendance', 'on_leave', 'On Leave', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1430, 1, 'attendance', 'status', 'Status', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1431, 1, 'attendance', 'clock_in', 'Clock In', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1432, 1, 'attendance', 'clock_out', 'Clock Out', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1433, 1, 'attendance', 'clocked_time', 'Clocked Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1434, 1, 'attendance', 'other_details', 'Other Details', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1435, 1, 'attendance', 'clock_in_ip', 'Clock-In IP', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1436, 1, 'attendance', 'clock_out_ip', 'Clock-Out IP', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1437, 1, 'attendance', 'hours', 'hrs', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1438, 1, 'attendance', 'minutes', 'mins', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1439, 1, 'attendance', 'late', 'Late', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1440, 1, 'attendance', 'not_marked', 'Not Marked', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1441, 1, 'attendance', 'total_worked_time', 'Total Worked Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1442, 1, 'attendance', 'present_working_days', 'Present / Working Days', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1443, 1, 'attendance', 'clock_in_date_time', 'Clock In Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1444, 1, 'attendance', 'clock_out_date_time', 'Clock Out Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1445, 1, 'attendance', 'clock_in_ip_address', 'Clock In IP Address', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1446, 1, 'attendance', 'clock_out_ip_address', 'Clock Out IP Address', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1447, 1, 'attendance', 'days', 'Days', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1448, 1, 'attendance', 'total_duration', 'Total Duration', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1449, 1, 'attendance', 'is_late', 'Is Late', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1450, 1, 'attendance', 'admin', 'Admin', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1451, 1, 'attendance', 'is_half_day', 'Is Half Day', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1452, 1, 'attendance', 'leave_type', 'Leave Type', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1453, 1, 'attendance', 'user', 'Users', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1454, 1, 'attendance', 'half_days', 'Half Days', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1455, 1, 'attendance', 'reason', 'Reason', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1456, 1, 'attendance', 'punch_in_at', 'Punch In at', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1457, 1, 'attendance', 'punch_out', 'Punch Out', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1458, 1, 'attendance', 'production', 'Production', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1459, 1, 'attendance', 'half_holiday', 'Half Holiday', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1460, 1, 'attendance', 'start_time', 'Start Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1461, 1, 'attendance', 'end_time', 'End Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1462, 1, 'attendance', 'duration', 'Duration', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1463, 1, 'attendance', 'notes', 'Notes', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1464, 1, 'attendance', 'paused', 'Paused', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1465, 1, 'attendance', 'started', 'Started', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1466, 1, 'attendance', 'break_history', 'Break History', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1467, 1, 'attendance', 'work_summary', 'Work Summary', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1468, 1, 'attendance', 'total_work_hours', 'Total Work Hours', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1469, 1, 'attendance', 'effective_work_time', 'Effective Work Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1470, 1, 'attendance', 'productivity_rate', 'Productivity Rate', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1471, 1, 'attendance', 'break_summary', 'Break Summary', '2025-10-07 19:17:31', '2025-10-07 19:17:31');
INSERT INTO `translations` (`id`, `lang_id`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1472, 1, 'attendance', 'total_break_time', 'Total Break Time', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1473, 1, 'attendance', 'total_breaks', 'Total Breaks', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1474, 1, 'attendance', 'avg_break_duration', 'Avg. Break Duration', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1475, 1, 'attendance', 'break_details', 'Break Details', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1476, 1, 'attendance', 'location_access_required', 'Location Access Required', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1477, 1, 'attendance', 'allow_location_access_to_mark_attendance', '\"Please allow location access to mark attendance.\"', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1478, 1, 'attendance', 'location', 'Location', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1479, 1, 'attendance', 'location_details', 'Location Details', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1480, 1, 'attendance', 'custom_map', 'Custom Map', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1481, 1, 'attendance', 'google_map', 'Google Map', '2025-10-07 19:17:31', '2025-10-07 19:17:31'),
(1482, 1, 'attendance', 'loading_map', 'Loading Map...', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1483, 1, 'attendance', 'location_not_available', 'Location not available.', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1484, 1, 'attendance', 'clock_in_location', 'Clock-in Location', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1485, 1, 'attendance', 'clock_out_location', 'Clock-out Location', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1486, 1, 'attendance', 'clock_in_details', 'Clock-in Details', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1487, 1, 'attendance', 'clock_out_details', 'Clock-out Details', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1488, 1, 'attendance', 'browser', 'Browser', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1489, 1, 'attendance', 'platform', 'Platform', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1490, 1, 'attendance', 'device_type', 'Device Type', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1491, 1, 'attendance', 'user_agent', 'User Agent', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1492, 1, 'attendance', 'latitude', 'latitude', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1493, 1, 'attendance', 'longitude', 'longitude', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1494, 1, 'attendance', 'upcoming', 'Upcoming', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1495, 1, 'attendance', 'no_attendance_records', 'No Attendance Records', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1496, 1, 'attendance', 'import_attendance', 'Import Attendance', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1497, 1, 'attendance', 'mark_attendance_by', 'Mark Attendance By', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1498, 1, 'attendance', 'mark_attendance', 'Mark Attendance', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1499, 1, 'attendance', 'bulk_attendance', 'Bulk Attendance', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1500, 1, 'attendance', 'employees', 'Employees', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1501, 1, 'attendance', 'geo_code_api_key_message', 'To view location name set Google Geocoding api key from Company Settings => Google Geocoding API', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1502, 1, 'attendance', 'location_address', 'Location Address', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1503, 1, 'attendance', 'attendance_statement', 'Attendance Statement', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1504, 1, 'attendance', 'employee_number', 'Employee Id', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1505, 1, 'attendance', 'clock_in_coordinates', 'Clock In Coordinates', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1506, 1, 'attendance', 'clock_out_coordinates', 'Clock Out Coordinates', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1507, 1, 'attendance', 'no_attendance_data', 'There is no attendance record found.', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1508, 1, 'basic_salary', 'add', 'Add New Basic Salary', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1509, 1, 'basic_salary', 'edit', 'Edit Basic Salary', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1510, 1, 'basic_salary', 'created', 'Basic Salary Created Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1511, 1, 'basic_salary', 'updated', 'Basic Salary Updated Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1512, 1, 'basic_salary', 'deleted', 'Basic Salary Deleted Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1513, 1, 'basic_salary', 'basic_salary_details', 'Basic Salary Details', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1514, 1, 'basic_salary', 'delete_message', 'Are you sure you want to delete this basic salary?', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1515, 1, 'basic_salary', 'user', 'User', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1516, 1, 'basic_salary', 'active', 'Active', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1517, 1, 'basic_salary', 'basic_salary', 'Basic Salary', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1518, 1, 'basic_salary', 'name', 'Name', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1519, 1, 'basic_salary', 'salary_group', 'Salary Group', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1520, 1, 'basic_salary', 'employee_salary_group', 'Employee Salary Group', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1521, 1, 'basic_salary', 'fixed', 'Fixed', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1522, 1, 'basic_salary', '%_of_ctc', '% of CTC', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1523, 1, 'basic_salary', 'annual_ctc', 'Annual CTC', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1524, 1, 'basic_salary', 'cost_to_company_value_for_this_year', '(Cost to company value for the whole year.)', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1525, 1, 'basic_salary', 'monthly_amount', 'Monthly Amount', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1526, 1, 'basic_salary', 'annual_amount', 'Annual Amount', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1527, 1, 'basic_salary', 'calculation_type', 'Calculation Type', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1528, 1, 'basic_salary', 'earnings', 'Earnings', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1529, 1, 'basic_salary', 'annual_CTC_sum_of_all_other_components', '(Annual CTC - Sum of all other components)', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1530, 1, 'basic_salary', 'special_allowances', 'Special Allowances', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1531, 1, 'basic_salary', 'cost_to_company', 'Cost To Company', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1532, 1, 'basic_salary', 'employee_salary', 'Employee Salary', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1533, 1, 'basic_salary', 'salary_component', 'Salary Component', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1534, 1, 'basic_salary', 'ctc', 'CTC', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1535, 1, 'basic_salary', 'ctc_value', 'CTC Value', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1536, 1, 'basic_salary', 'deductions', 'Deductions', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1537, 1, 'basic_salary', 'total_deductions', 'Total Deductions', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1538, 1, 'hrm_settings', 'updated', 'HRM Setting Updated Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1539, 1, 'hrm_settings', 'leave_start_month', 'Leave Start Month', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1540, 1, 'hrm_settings', 'late_mark_after', 'Late Mark After', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1541, 1, 'hrm_settings', 'clock_in_time', 'Clock In Time', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1542, 1, 'hrm_settings', 'clock_out_time', 'Clock Out Time', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1543, 1, 'hrm_settings', 'self_clocking', 'Self Clocking', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1544, 1, 'hrm_settings', 'allowed_ip_address', 'Allowed Ip Address', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1545, 1, 'hrm_settings', 'early_clock_in_time', 'Early Clock In Time', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1546, 1, 'hrm_settings', 'allow_clock_out_till', 'Allow Clock Out till', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1547, 1, 'hrm_settings', 'attendance_settings', 'Attendance Settings', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1548, 1, 'hrm_settings', 'front_job_details_settings', 'Front Job Details Settings', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1549, 1, 'hrm_settings', 'self_letter_head_settings', 'Self Letter Head Settings', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1550, 1, 'hrm_settings', 'letterhead_header_color', 'Letterhead Header Color', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1551, 1, 'hrm_settings', 'letterhead_show_company_name', 'Letterhead Show Company Name', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1552, 1, 'hrm_settings', 'letterhead_show_company_email', 'Letterhead Show Company Email', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1553, 1, 'hrm_settings', 'letterhead_show_company_phone', 'Letterhead Show Company Phone', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1554, 1, 'hrm_settings', 'letterhead_show_company_address', 'Letterhead Show Company Address', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1555, 1, 'hrm_settings', 'show_letterhead_footer', 'Show Letterhead Footer On', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1556, 1, 'hrm_settings', 'show_letterhead_footer_on_other_then_first_page', 'Show Letterhead Footer On Other Then First Page', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1557, 1, 'hrm_settings', 'show_letterhead_header', 'Show Letterhead Header On', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1558, 1, 'hrm_settings', 'all_pages', 'All Pages', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1559, 1, 'hrm_settings', 'first_page', 'First Page', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1560, 1, 'hrm_settings', 'none', 'None', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1561, 1, 'hrm_settings', 'employee_id_prefix_setting', 'Employee Id Prefix Setting', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1562, 1, 'hrm_settings', 'employee_id_prefix', 'Employee Id Prefix', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1563, 1, 'hrm_settings', 'employee_id_start', 'Employee Id Start', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1564, 1, 'hrm_settings', 'employee_id_digits', 'Employee Id Digits', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1565, 1, 'hrm_settings', 'letterhead_left_space', 'Letterhead Left Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1566, 1, 'hrm_settings', 'letterhead_right_space', 'Letterhead Right Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1567, 1, 'hrm_settings', 'letterhead_top_space', 'Letterhead Top Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1568, 1, 'hrm_settings', 'letterhead_bottom_space', 'Letterhead Bottom Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1569, 1, 'hrm_settings', 'holiday_pdf_font_size', 'Holiday Pdf Font Size', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1570, 1, 'hrm_settings', 'holiday_pdf_line_height', 'Holiday Pdf Line Height', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1571, 1, 'hrm_settings', 'generate_pdf_font_size', 'Generate Pdf Font Size', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1572, 1, 'hrm_settings', 'generate_pdf_line_height', 'Generate Pdf Line Height', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1573, 1, 'hrm_settings', 'letterhead_title_show_in_pdf', 'Letterhead Title Show In Pdf', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1574, 1, 'hrm_settings', 'capture_location', 'Capture Location', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1575, 1, 'hrm_settings', 'export_pdf', 'Export Pdf', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1576, 1, 'hrm_settings', 'export_pdf_font_size', 'Export Pdf Font Size', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1577, 1, 'hrm_settings', 'export_pdf_line_height', 'Export Pdf Line Height', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1578, 1, 'hrm_settings', 'letterhead_spacing', 'Letterhead Spacing', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1579, 1, 'hrm_settings', 'export_spacing', 'Export Pdf Spacing', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1580, 1, 'hrm_settings', 'export_pdf_right_space', 'Export Right Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1581, 1, 'hrm_settings', 'export_pdf_left_space', 'Export Left Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1582, 1, 'hrm_settings', 'export_pdf_top_space', 'Export Top Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1583, 1, 'hrm_settings', 'export_pdf_bottom_space', 'Export Bottom Space', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1584, 1, 'shift', 'add', 'Add New Shift', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1585, 1, 'shift', 'edit', 'Edit Shift', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1586, 1, 'shift', 'created', 'Shift Created Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1587, 1, 'shift', 'updated', 'Shift Updated Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1588, 1, 'shift', 'deleted', 'Shift Deleted Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1589, 1, 'shift', 'shift_details', 'Shift Details', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1590, 1, 'shift', 'delete_message', 'Are you sure you want to delete this shift?', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1591, 1, 'shift', 'selected_delete_message', 'Are you sure you want to delete selected shift?', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1592, 1, 'shift', 'display_name', 'Display Name', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1593, 1, 'shift', 'name', 'Name', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1594, 1, 'shift', 'clock_in_time', 'Clock In Time', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1595, 1, 'shift', 'clock_out_time', 'Clock Out Time', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1596, 1, 'shift', 'late_mark_after', 'Late Mark After', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1597, 1, 'shift', 'self_clocking', 'Self Clocking', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1598, 1, 'shift', 'allowed_ip_address', 'Allowed IP Address', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1599, 1, 'shift', 'add_new_ip_address', 'Add New IP Address', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1600, 1, 'shift', 'allow_clock_out_till', 'Allow Clock Out Till', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1601, 1, 'shift', 'early_clock_in_time', 'Early Clock In Time', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1602, 1, 'shift', 'total_employee', 'Total Employee', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1603, 1, 'shift', 'capture_location', 'Capture Location', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1604, 1, 'designation', 'add', 'Add New Designation', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1605, 1, 'designation', 'edit', 'Edit Designation', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1606, 1, 'designation', 'created', 'Designation Created Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1607, 1, 'designation', 'updated', 'Designation Updated Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1608, 1, 'designation', 'deleted', 'Designation Deleted Successfully', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1609, 1, 'designation', 'designation_details', 'Designation Details', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1610, 1, 'designation', 'delete_message', 'Are you sure you want to delete this designation?', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1611, 1, 'designation', 'selected_delete_message', 'Are you sure you want to delete selected designation?', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1612, 1, 'designation', 'display_name', 'Display Name', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1613, 1, 'designation', 'name', 'Name', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1614, 1, 'designation', 'total_employee', 'Total Employee', '2025-10-07 19:17:32', '2025-10-07 19:17:32'),
(1615, 1, 'department', 'add', 'Add New Department', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1616, 1, 'department', 'edit', 'Edit Department', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1617, 1, 'department', 'created', 'Department Created Successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1618, 1, 'department', 'updated', 'Department Updated Successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1619, 1, 'department', 'deleted', 'Department Deleted Successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1620, 1, 'department', 'department_details', 'Department Details', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1621, 1, 'department', 'delete_message', 'Are you sure you want to delete this department?', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1622, 1, 'department', 'selected_delete_message', 'Are you sure you want to delete selected department?', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1623, 1, 'department', 'display_name', 'Display Name', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1624, 1, 'department', 'name', 'Name', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1625, 1, 'department', 'user_id', 'User', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1626, 1, 'department', 'shift_id', 'Shift', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1627, 1, 'department', 'total_employee', 'Total Employee', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1628, 1, 'email_template', 'edit', 'Edit Email Template', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1629, 1, 'email_template', 'updated', 'Department Updated Successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1630, 1, 'email_template', 'name', 'Name', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1631, 1, 'email_template', 'subject', 'Email Subject', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1632, 1, 'email_template', 'body', 'Email Body', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1633, 1, 'email_template', 'type', 'Type', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1634, 1, 'email_template', 'available_variable', 'Available Variables', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1635, 1, 'mail_settings', 'updated', 'Mail Settings Updated', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1636, 1, 'mail_settings', 'mail_driver', 'Mail Driver', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1637, 1, 'mail_settings', 'none', 'None', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1638, 1, 'mail_settings', 'mail', 'Mail', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1639, 1, 'mail_settings', 'smtp', 'SMTP', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1640, 1, 'mail_settings', 'from_name', 'Mail From Name', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1641, 1, 'mail_settings', 'from_email', 'Mail From Email', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1642, 1, 'mail_settings', 'host', 'Host', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1643, 1, 'mail_settings', 'port', 'Port', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1644, 1, 'mail_settings', 'encryption', 'Encryption', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1645, 1, 'mail_settings', 'username', 'Username', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1646, 1, 'mail_settings', 'password', 'Password', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1647, 1, 'mail_settings', 'send_test_mail', 'Send Test Mail', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1648, 1, 'mail_settings', 'send_mail_setting_saved', 'Send mail setting saved', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1649, 1, 'mail_settings', 'enable_mail_queue', 'Enable Mail Queue', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1650, 1, 'mail_settings', 'send_mail_for', 'Send Mail To', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1651, 1, 'mail_settings', 'email', 'Email address for which you want to send test mail', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1652, 1, 'mail_settings', 'test_mail_sent_successfully', 'Test mail sent successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1653, 1, 'mail_settings', 'notificaiton_will_be_sent_to_company', 'Notification will be sent to company email', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1654, 1, 'mail_settings', 'notificaiton_will_be_sent_to_employee', 'Notification will be sent to employee email', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1655, 1, 'mail_settings', 'company', 'Company', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1656, 1, 'mail_settings', 'employee', 'Employee', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1657, 1, 'mail_settings', 'on_welcome_mail', 'Employee Welcome Mail', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1658, 1, 'mail_settings', 'on_asset_lent', 'On Asset Lent', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1659, 1, 'mail_settings', 'on_asset_return', 'On Asset Retrun', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1660, 1, 'mail_settings', 'on_appreciation', 'On Appreciation', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1661, 1, 'mail_settings', 'on_leave_approve', 'On Leave Approve', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1662, 1, 'mail_settings', 'on_leave_reject', 'On Leave Reject', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1663, 1, 'mail_settings', 'on_news_published', 'On News Published', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1664, 1, 'mail_settings', 'on_payroll_paid', 'On Payroll Paid', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1665, 1, 'mail_settings', 'on_warning', 'On Warning', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1666, 1, 'mail_settings', 'on_terminations', 'On Terminations', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1667, 1, 'mail_settings', 'on_resignation_approve', 'On Resignation Approve', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1668, 1, 'mail_settings', 'on_resignation_reject', 'On Resignation Reject', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1669, 1, 'mail_settings', 'on_complaint_approve', 'On Complaint Approve', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1670, 1, 'mail_settings', 'on_complaint_reject', 'On Complaint Reject', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1671, 1, 'mail_settings', 'on_expense_approve', 'On Expense Approve', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1672, 1, 'mail_settings', 'on_expense_reject', 'On Expense Reject', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1673, 1, 'mail_settings', 'on_survey_forms_assign', 'On Survey Forms Assign', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1674, 1, 'mail_settings', 'on_survey_forms_rating', 'On Survey Forms Rating', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1675, 1, 'mail_settings', 'on_employee_leave_apply', 'On Employee Leave Apply', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1676, 1, 'mail_settings', 'on_employee_clock_in', 'On Employee Clock In', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1677, 1, 'mail_settings', 'on_employee_clock_out', 'On Employee Clock Out', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1678, 1, 'mail_settings', 'on_employee_resignation_apply', 'On Employee Resignation Apply', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1679, 1, 'mail_settings', 'on_employee_complaint_apply', 'On Employee Complaint Apply', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1680, 1, 'mail_settings', 'on_employee_expense_apply', 'On Employee Expense Apply', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1681, 1, 'mail_settings', 'on_employee_survey_submit', 'On Employee Survey Submit', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1682, 1, 'database_backup', 'file', 'File', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1683, 1, 'database_backup', 'file_size', 'File Size', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1684, 1, 'database_backup', 'generate_backup', 'Generate Backup', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1685, 1, 'database_backup', 'delete_backup', 'Delete Backup', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1686, 1, 'database_backup', 'backup_generated_successfully', 'Backup Generated Successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1687, 1, 'database_backup', 'are_you_sure_generate_backup', 'Are you sure you want to generate database backup?', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1688, 1, 'database_backup', 'are_you_sure_delete_backup', 'Are you sure you want to delete this database backup?', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1689, 1, 'database_backup', 'backup_locaion_is', 'All generated database file will be stored in storage/app/public/backup folder. ', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1690, 1, 'database_backup', 'settings', 'Command Settings', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1691, 1, 'database_backup', 'backup_command_setting', 'Backup Command Settings', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1692, 1, 'database_backup', 'mysqldump_command_path', 'mysqldump command path', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1693, 1, 'database_backup', 'command_updated', 'Command updated successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1694, 1, 'database_backup', 'window_command_path', 'If you use XAMPP then it will be => C:\\xampp\\mysql\\bin\\mysqldump.exe', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1695, 1, 'database_backup', 'laragon_command_path', 'If you use Laragon then it will be => C:\\laragon\\bin\\mysql\\mysql-5.7.24-winx64\\bin\\mysqldump.exe', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1696, 1, 'database_backup', 'linux_command_path', 'If you are on ubuntu or mac then run following command and enter output here => which mysqldump', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1697, 1, 'database_backup', 'put_dump_path_command_on_env_file', 'Find your MySQL dump path from below and then add it to the DUMP_PATH inside .env file', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1698, 1, 'messages', 'somehing_went_wrong', 'Something went wrong. Please contact to administrator.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1699, 1, 'messages', 'verify_success', 'Successfully verified. Redirect to app...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1700, 1, 'messages', 'login_success', 'Successfully login. Redirect to app...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1701, 1, 'messages', 'login_success_dashboard', 'Successfully logged into app.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1702, 1, 'messages', 'click_here_to_find_purchase_code', 'Click here to find your purchase code', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1703, 1, 'messages', 'verification_successfull', 'Verification successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1704, 1, 'messages', 'other_domain_linked', 'Other domain linked', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1705, 1, 'messages', 'other_domain_linked_with_purchase_code', 'Other domain is already linked with your purchase code. Please enter your purchase code for more details...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1706, 1, 'messages', 'first_verify_module_message', 'To enable please \\n verify this module', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1707, 1, 'messages', 'are_you_sure_install_message', 'Are you sure you want to install?', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1708, 1, 'messages', 'downloading_started_message', 'Downloading started. Please wait ...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1709, 1, 'messages', 'file_extracting_message', 'File extracteding. Please wait ...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1710, 1, 'messages', 'installation_success', 'Installation successfully. Click here to reload page...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1711, 1, 'messages', 'are_you_sure_update_message', 'Are you sure you want to update? Please take backup before update.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1712, 1, 'messages', 'stmp_success_message', 'Your SMTP settings are correct..', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1713, 1, 'messages', 'stmp_error_message', 'Your SMTP settings are incorrect. Please update it to send mails', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1714, 1, 'messages', 'uploading_failed', 'Uploading failed', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1715, 1, 'messages', 'loading_app_message', 'Please wait... we are preparing something amazing for you', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1716, 1, 'messages', 'fetching_product_details', 'We are fetching product details. Please wait...', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1717, 1, 'messages', 'product_is_upto_date', 'You are on the latest version of app.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1718, 1, 'messages', 'new_app_version_avaialbe', 'New app version {0} is available. Please update to get latest version.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1719, 1, 'messages', 'not_able_to_edit_order', 'Only order status editable, other fields can not be editable becuase this order linked to some payments. Delete those payment(s) and try again.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1720, 1, 'messages', 'click_here_to_download_sample_file', 'Click here to download sample csv file', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1721, 1, 'messages', 'imported_successfully', 'Imported Successfully', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1722, 1, 'messages', 'company_admin_password_message', 'Admin will login using this password. (Leave blank to keep current password)', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1723, 1, 'messages', 'email_setting_not_configured', 'Email setting not configured', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1724, 1, 'messages', 'please_configure_email_settings', 'Please configure your email settings to send emails. Click Here to configure email settings.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1725, 1, 'popover', 'auto_detect_timezone', 'Allow auto detect timezone from browser for currently logged in user.', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1726, 1, 'popover', 'click_here_to_copy_credentials', 'Click here to copy {0} credentials', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1727, 1, 'topbar_add_button', 'add_staff_member', 'Add Employee', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1728, 1, 'topbar_add_button', 'add_currency', 'Add Currency', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1729, 1, 'topbar_add_button', 'add_language', 'Add Language', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1730, 1, 'topbar_add_button', 'add_role', 'Add Role', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1731, 1, 'topbar_add_button', 'add_departments', 'Add Departments', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1732, 1, 'topbar_add_button', 'add_designations', 'Add Designations', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1733, 1, 'topbar_add_button', 'add_shifts', 'Add Shipts', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1734, 1, 'topbar_add_button', 'add_assets', 'Add Assets', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1735, 1, 'topbar_add_button', 'add_holidays', 'Add Holidays', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1736, 1, 'topbar_add_button', 'add_appreciations', 'Add Appreciations', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1737, 1, 'topbar_add_button', 'add_awards', 'Add Awards', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1738, 1, 'topbar_add_button', 'add_leaves', 'Add Leaves', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1739, 1, 'topbar_add_button', 'add_attendance', 'Add Attendance', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1740, 1, 'topbar_add_button', 'add_news', 'Add News', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1741, 1, 'topbar_add_button', 'add_pre_payments', 'Add Pre Payments', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1742, 1, 'topbar_add_button', 'add_increment_promotions', 'Add Increment/Promotions', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1743, 1, 'topbar_add_button', 'add_company_policies', 'Add Company Policies', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1744, 1, 'topbar_add_button', 'add_job_questions', 'Add Job Questions', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1745, 1, 'topbar_add_button', 'add_openings', 'Add Openings', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1746, 1, 'topbar_add_button', 'add_applications', 'Add Applications', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1747, 1, 'topbar_add_button', 'add_warnings', 'Add Warnings', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1748, 1, 'topbar_add_button', 'add_resignations', 'Add Resignations', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1749, 1, 'topbar_add_button', 'add_terminations', 'Add Terminations', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1750, 1, 'topbar_add_button', 'add_complaints', 'Add Complaints', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1751, 1, 'topbar_add_button', 'add_templates', 'Add Templates', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1752, 1, 'topbar_add_button', 'add_generates', 'Add Generate', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1753, 1, 'topbar_add_button', 'add_indicators', 'Add Indicators', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1754, 1, 'topbar_add_button', 'add_feedbacks', 'Add Feedbacks', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1755, 1, 'topbar_add_button', 'add_accounts', 'Add Accounts', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1756, 1, 'topbar_add_button', 'add_payees', 'Add Payees', '2025-10-07 19:17:33', '2025-10-07 19:17:33'),
(1757, 1, 'topbar_add_button', 'add_payers', 'Add Payers', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1758, 1, 'topbar_add_button', 'add_deposits', 'Add Deposits', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1759, 1, 'topbar_add_button', 'add_deposit_categories', 'Add Deposit Categories', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1760, 1, 'topbar_add_button', 'add_expenses', 'Add Expenses', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1761, 1, 'topbar_add_button', 'add_expense_categories', 'Add Expense Categories', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1762, 1, 'topbar_add_button', 'add_forms', 'Add forms', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1763, 1, 'topbar_add_button', 'add_payrolls', 'Add Payroll', '2025-10-07 19:17:34', '2025-10-07 19:17:34'),
(1764, 1, 'menu', 'staff_onboarding', 'Staff Onboarding', '2025-10-16 18:09:14', '2025-10-16 18:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0,
  `user_type` varchar(191) NOT NULL DEFAULT 'staff_members',
  `login_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(191) NOT NULL,
  `employee_number` varchar(191) DEFAULT NULL,
  `gender` varchar(20) NOT NULL DEFAULT 'female',
  `dob` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `allow_login` tinyint(1) NOT NULL DEFAULT 1,
  `email` varchar(191) NOT NULL,
  `is_married` tinyint(1) DEFAULT 0,
  `marriage_date` date DEFAULT NULL,
  `personal_email` varchar(191) DEFAULT NULL,
  `personal_phone` varchar(191) DEFAULT NULL,
  `report_to` bigint(20) UNSIGNED DEFAULT NULL,
  `is_manager` varchar(191) DEFAULT '0',
  `visibility` varchar(20) NOT NULL DEFAULT 'individual',
  `password` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `probation_start_date` date DEFAULT NULL,
  `probation_end_date` date DEFAULT NULL,
  `notice_start_date` date DEFAULT NULL,
  `notice_end_date` date DEFAULT NULL,
  `profile_image` varchar(191) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `shipping_address` varchar(1000) DEFAULT NULL,
  `email_verification_code` varchar(50) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'active',
  `reset_code` varchar(191) DEFAULT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Kolkata',
  `date_format` varchar(20) NOT NULL DEFAULT 'd-m-Y',
  `date_picker_format` varchar(20) NOT NULL DEFAULT 'dd-mm-yyyy',
  `time_format` varchar(20) NOT NULL DEFAULT 'h:i a',
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `net_salary` varchar(191) DEFAULT NULL,
  `basic_salary` double DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `monthly_amount` double DEFAULT NULL,
  `annual_amount` double DEFAULT NULL,
  `annual_ctc` double DEFAULT NULL,
  `ctc_value` int(11) DEFAULT NULL,
  `special_allowances` double DEFAULT NULL,
  `calculation_type` enum('fixed','%_of_ctc') NOT NULL DEFAULT '%_of_ctc'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `role_id`, `is_superadmin`, `user_type`, `login_enabled`, `name`, `employee_number`, `gender`, `dob`, `joining_date`, `allow_login`, `email`, `is_married`, `marriage_date`, `personal_email`, `personal_phone`, `report_to`, `is_manager`, `visibility`, `password`, `phone`, `end_date`, `probation_start_date`, `probation_end_date`, `notice_start_date`, `notice_end_date`, `profile_image`, `address`, `shipping_address`, `email_verification_code`, `status`, `reset_code`, `timezone`, `date_format`, `date_picker_format`, `time_format`, `department_id`, `designation_id`, `shift_id`, `net_salary`, `basic_salary`, `location_id`, `salary_group_id`, `employee_status_id`, `created_at`, `updated_at`, `monthly_amount`, `annual_amount`, `annual_ctc`, `ctc_value`, `special_allowances`, `calculation_type`) VALUES
(1, 1, 1, 0, 'staff_members', 1, 'Admin', NULL, 'female', NULL, NULL, 1, 'admin@example.com', 0, NULL, NULL, NULL, NULL, '1', 'individual', '$2y$10$3epWpkFpVWpnrs8RL6pVJe0WU7YLdsjuQreCxpU09vAYlPJ9sPyFq', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 'Asia/Kolkata', 'd-m-Y', 'dd-mm-yyyy', 'h:i a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '%_of_ctc'),
(2, 1, NULL, 0, 'staff_members', 1, 'Onoyima Aneke', 'emp-0001', 'female', NULL, '2025-10-07', 1, 'Cj1900a@gmail.com', 0, NULL, NULL, NULL, NULL, '0', 'individual', '$2y$10$oKmYkGgpboujCZKqmubCZOeG7wCVfz4ewCVilDmhwq1MNvgRNyMwK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 'Asia/Kolkata', 'd-m-Y', 'dd-mm-yyyy', 'h:i a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-07 21:16:30', '2025-10-07 21:16:30', NULL, NULL, NULL, NULL, NULL, '%_of_ctc');

-- --------------------------------------------------------

--
-- Table structure for table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `field_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `values` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warnings`
--

CREATE TABLE `warnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `letterhead_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generates_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `warning_date` datetime DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_durations`
--

CREATE TABLE `work_durations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_company_id_foreign` (`company_id`);

--
-- Indexes for table `account_entries`
--
ALTER TABLE `account_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_entries_company_id_foreign` (`company_id`),
  ADD KEY `account_entries_account_id_foreign` (`account_id`),
  ADD KEY `account_entries_deposit_id_foreign` (`deposit_id`),
  ADD KEY `account_entries_expense_id_foreign` (`expense_id`),
  ADD KEY `account_entries_payroll_id_foreign` (`payroll_id`),
  ADD KEY `account_entries_pre_payment_id_foreign` (`pre_payment_id`),
  ADD KEY `account_entries_asset_id_foreign` (`asset_id`),
  ADD KEY `account_entries_appreciation_id_foreign` (`appreciation_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `appreciations`
--
ALTER TABLE `appreciations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appreciations_company_id_foreign` (`company_id`),
  ADD KEY `appreciations_award_id_foreign` (`award_id`),
  ADD KEY `appreciations_user_id_foreign` (`user_id`),
  ADD KEY `appreciations_letterhead_template_id_foreign` (`letterhead_template_id`),
  ADD KEY `appreciations_generates_id_foreign` (`generates_id`),
  ADD KEY `appreciations_account_id_foreign` (`account_id`),
  ADD KEY `appreciations_created_by_foreign` (`created_by`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assets_company_id_foreign` (`company_id`),
  ADD KEY `assets_asset_type_id_foreign` (`asset_type_id`),
  ADD KEY `assets_location_id_foreign` (`location_id`),
  ADD KEY `assets_user_id_foreign` (`user_id`),
  ADD KEY `assets_broken_by_foreign` (`broken_by`),
  ADD KEY `assets_account_id_foreign` (`account_id`),
  ADD KEY `assets_asset_user_id_foreign` (`asset_user_id`);

--
-- Indexes for table `asset_types`
--
ALTER TABLE `asset_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_types_company_id_foreign` (`company_id`);

--
-- Indexes for table `asset_users`
--
ALTER TABLE `asset_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_users_asset_id_foreign` (`asset_id`),
  ADD KEY `asset_users_lent_to_foreign` (`lent_to`),
  ADD KEY `asset_users_lent_by_foreign` (`lent_by`),
  ADD KEY `asset_users_return_by_foreign` (`return_by`),
  ADD KEY `asset_users_broken_user_id_foreign` (`broken_user_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_company_id_foreign` (`company_id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`),
  ADD KEY `attendances_leave_id_foreign` (`leave_id`),
  ADD KEY `attendances_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `attendances_holiday_id_foreign` (`holiday_id`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `awards_company_id_foreign` (`company_id`),
  ADD KEY `awards_created_by_foreign` (`created_by`);

--
-- Indexes for table `basic_salary_details`
--
ALTER TABLE `basic_salary_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basic_salary_details_company_id_foreign` (`company_id`),
  ADD KEY `basic_salary_details_user_id_foreign` (`user_id`),
  ADD KEY `basic_salary_details_salary_component_id_foreign` (`salary_component_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `companies_lang_id_foreign` (`lang_id`),
  ADD KEY `companies_subscription_plan_id_foreign` (`subscription_plan_id`),
  ADD KEY `companies_stripe_id_index` (`stripe_id`),
  ADD KEY `companies_pdf_font_id_foreign` (`pdf_font_id`);

--
-- Indexes for table `company_policies`
--
ALTER TABLE `company_policies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_policies_company_id_foreign` (`company_id`),
  ADD KEY `company_policies_location_id_foreign` (`location_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaints_company_id_foreign` (`company_id`),
  ADD KEY `complaints_from_user_id_foreign` (`from_user_id`),
  ADD KEY `complaints_to_user_id_foreign` (`to_user_id`),
  ADD KEY `complaints_letterhead_template_id_foreign` (`letterhead_template_id`),
  ADD KEY `complaints_generates_id_foreign` (`generates_id`),
  ADD KEY `complaints_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `currencies_company_id_foreign` (`company_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_company_id_foreign` (`company_id`),
  ADD KEY `departments_created_by_foreign` (`created_by`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposits_company_id_foreign` (`company_id`),
  ADD KEY `deposits_account_id_foreign` (`account_id`),
  ADD KEY `deposits_deposit_category_id_foreign` (`deposit_category_id`),
  ADD KEY `deposits_payer_id_foreign` (`payer_id`);

--
-- Indexes for table `deposit_categories`
--
ALTER TABLE `deposit_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposit_categories_company_id_foreign` (`company_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `designations_company_id_foreign` (`company_id`),
  ADD KEY `designations_created_by_foreign` (`created_by`);

--
-- Indexes for table `employee_fields`
--
ALTER TABLE `employee_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_fields_company_id_foreign` (`company_id`),
  ADD KEY `employee_fields_field_type_id_foreign` (`field_type_id`);

--
-- Indexes for table `employee_specific_leave_counts`
--
ALTER TABLE `employee_specific_leave_counts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_specific_leave_counts_company_id_foreign` (`company_id`),
  ADD KEY `employee_specific_leave_counts_user_id_foreign` (`user_id`),
  ADD KEY `employee_specific_leave_counts_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `employee_work_status`
--
ALTER TABLE `employee_work_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_work_status_company_id_foreign` (`company_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_company_id_foreign` (`company_id`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `expenses_payee_id_foreign` (`payee_id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `expenses_account_id_foreign` (`account_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_categories_company_id_foreign` (`company_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedbacks_company_id_foreign` (`company_id`),
  ADD KEY `feedbacks_form_id_foreign` (`form_id`);

--
-- Indexes for table `feedback_users`
--
ALTER TABLE `feedback_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_users_feedback_id_foreign` (`feedback_id`),
  ADD KEY `feedback_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `field_types`
--
ALTER TABLE `field_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field_types_company_id_foreign` (`company_id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forms_company_id_foreign` (`company_id`),
  ADD KEY `forms_created_by_foreign` (`created_by`),
  ADD KEY `forms_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `generates`
--
ALTER TABLE `generates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generates_company_id_foreign` (`company_id`),
  ADD KEY `generates_user_id_foreign` (`user_id`),
  ADD KEY `generates_letterhead_template_id_foreign` (`letterhead_template_id`),
  ADD KEY `generates_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `holidays_company_id_foreign` (`company_id`),
  ADD KEY `holidays_created_by_foreign` (`created_by`);

--
-- Indexes for table `increments_promotions`
--
ALTER TABLE `increments_promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `increments_promotions_company_id_foreign` (`company_id`),
  ADD KEY `increments_promotions_user_id_foreign` (`user_id`),
  ADD KEY `increments_promotions_promoted_designation_id_foreign` (`promoted_designation_id`),
  ADD KEY `increments_promotions_current_designation_id_foreign` (`current_designation_id`),
  ADD KEY `increments_promotions_generate_id_foreign` (`generate_id`),
  ADD KEY `increments_promotions_letterhead_template_id_foreign` (`letterhead_template_id`);

--
-- Indexes for table `indicators`
--
ALTER TABLE `indicators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicators_company_id_foreign` (`company_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `langs`
--
ALTER TABLE `langs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaves_company_id_foreign` (`company_id`),
  ADD KEY `leaves_user_id_foreign` (`user_id`),
  ADD KEY `leaves_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_types_company_id_foreign` (`company_id`),
  ADD KEY `leave_types_created_by_foreign` (`created_by`);

--
-- Indexes for table `letterhead_templates`
--
ALTER TABLE `letterhead_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `letterhead_templates_company_id_foreign` (`company_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locations_company_id_foreign` (`company_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_company_id_foreign` (`company_id`);

--
-- Indexes for table `news_users`
--
ALTER TABLE `news_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_users_news_id_foreign` (`news_id`),
  ADD KEY `news_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `offboardings`
--
ALTER TABLE `offboardings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offboardings_company_id_foreign` (`company_id`),
  ADD KEY `offboardings_user_id_foreign` (`user_id`),
  ADD KEY `offboardings_manager_id_foreign` (`manager_id`),
  ADD KEY `offboardings_letterhead_template_id_foreign` (`letterhead_template_id`),
  ADD KEY `offboardings_generates_id_foreign` (`generates_id`);

--
-- Indexes for table `payees`
--
ALTER TABLE `payees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payees_company_id_foreign` (`company_id`);

--
-- Indexes for table `payers`
--
ALTER TABLE `payers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payers_company_id_foreign` (`company_id`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payrolls_company_id_foreign` (`company_id`),
  ADD KEY `payrolls_user_id_foreign` (`user_id`),
  ADD KEY `payrolls_created_by_foreign` (`created_by`),
  ADD KEY `payrolls_updated_by_foreign` (`updated_by`),
  ADD KEY `payrolls_account_id_foreign` (`account_id`);

--
-- Indexes for table `payroll_components`
--
ALTER TABLE `payroll_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_components_company_id_foreign` (`company_id`),
  ADD KEY `payroll_components_user_id_foreign` (`user_id`),
  ADD KEY `payroll_components_payroll_id_foreign` (`payroll_id`),
  ADD KEY `payroll_components_pre_payment_id_foreign` (`pre_payment_id`),
  ADD KEY `payroll_components_expense_id_foreign` (`expense_id`),
  ADD KEY `payroll_components_salary_component_id_foreign` (`salary_component_id`);

--
-- Indexes for table `pdf_fonts`
--
ALTER TABLE `pdf_fonts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdf_fonts_company_id_foreign` (`company_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `pre_payments`
--
ALTER TABLE `pre_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pre_payments_company_id_foreign` (`company_id`),
  ADD KEY `pre_payments_user_id_foreign` (`user_id`),
  ADD KEY `pre_payments_account_id_foreign` (`account_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roles_company_id_foreign` (`company_id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_components_company_id_foreign` (`company_id`);

--
-- Indexes for table `salary_groups`
--
ALTER TABLE `salary_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_groups_company_id_foreign` (`company_id`);

--
-- Indexes for table `salary_group_components`
--
ALTER TABLE `salary_group_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_group_components_company_id_foreign` (`company_id`),
  ADD KEY `salary_group_components_salary_component_id_foreign` (`salary_component_id`),
  ADD KEY `salary_group_components_salary_group_id_foreign` (`salary_group_id`);

--
-- Indexes for table `salary_group_users`
--
ALTER TABLE `salary_group_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_group_users_company_id_foreign` (`company_id`),
  ADD KEY `salary_group_users_user_id_foreign` (`user_id`),
  ADD KEY `salary_group_users_salary_group_id_foreign` (`salary_group_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_company_id_foreign` (`company_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shifts_company_id_foreign` (`company_id`);

--
-- Indexes for table `staff_onboarding`
--
ALTER TABLE `staff_onboarding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_onboarding_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `staff_onboarding_email_index` (`email`),
  ADD KEY `staff_onboarding_status_index` (`status`),
  ADD KEY `staff_onboarding_created_at_index` (`created_at`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translations_lang_id_foreign` (`lang_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_company_id_foreign` (`company_id`),
  ADD KEY `users_report_to_foreign` (`report_to`),
  ADD KEY `users_location_id_foreign` (`location_id`),
  ADD KEY `users_salary_group_id_foreign` (`salary_group_id`),
  ADD KEY `users_employee_status_id_foreign` (`employee_status_id`),
  ADD KEY `users_department_id_foreign` (`department_id`),
  ADD KEY `users_designation_id_foreign` (`designation_id`),
  ADD KEY `users_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_documents_company_id_foreign` (`company_id`),
  ADD KEY `user_documents_user_id_foreign` (`user_id`),
  ADD KEY `user_documents_field_type_id_foreign` (`field_type_id`);

--
-- Indexes for table `warnings`
--
ALTER TABLE `warnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warnings_company_id_foreign` (`company_id`),
  ADD KEY `warnings_user_id_foreign` (`user_id`),
  ADD KEY `warnings_manager_id_foreign` (`manager_id`),
  ADD KEY `warnings_letterhead_template_id_foreign` (`letterhead_template_id`),
  ADD KEY `warnings_generates_id_foreign` (`generates_id`);

--
-- Indexes for table `work_durations`
--
ALTER TABLE `work_durations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_durations_company_id_foreign` (`company_id`),
  ADD KEY `work_durations_attendance_id_foreign` (`attendance_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_entries`
--
ALTER TABLE `account_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appreciations`
--
ALTER TABLE `appreciations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_types`
--
ALTER TABLE `asset_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_users`
--
ALTER TABLE `asset_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `basic_salary_details`
--
ALTER TABLE `basic_salary_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_policies`
--
ALTER TABLE `company_policies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposit_categories`
--
ALTER TABLE `deposit_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_fields`
--
ALTER TABLE `employee_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_specific_leave_counts`
--
ALTER TABLE `employee_specific_leave_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_work_status`
--
ALTER TABLE `employee_work_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_users`
--
ALTER TABLE `feedback_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `field_types`
--
ALTER TABLE `field_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `generates`
--
ALTER TABLE `generates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `increments_promotions`
--
ALTER TABLE `increments_promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indicators`
--
ALTER TABLE `indicators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `langs`
--
ALTER TABLE `langs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letterhead_templates`
--
ALTER TABLE `letterhead_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news_users`
--
ALTER TABLE `news_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offboardings`
--
ALTER TABLE `offboardings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payees`
--
ALTER TABLE `payees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payers`
--
ALTER TABLE `payers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_components`
--
ALTER TABLE `payroll_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pdf_fonts`
--
ALTER TABLE `pdf_fonts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `pre_payments`
--
ALTER TABLE `pre_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salary_components`
--
ALTER TABLE `salary_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_groups`
--
ALTER TABLE `salary_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_group_components`
--
ALTER TABLE `salary_group_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_group_users`
--
ALTER TABLE `salary_group_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_onboarding`
--
ALTER TABLE `staff_onboarding`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1765;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warnings`
--
ALTER TABLE `warnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_durations`
--
ALTER TABLE `work_durations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `account_entries`
--
ALTER TABLE `account_entries`
  ADD CONSTRAINT `account_entries_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_appreciation_id_foreign` FOREIGN KEY (`appreciation_id`) REFERENCES `appreciations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_deposit_id_foreign` FOREIGN KEY (`deposit_id`) REFERENCES `deposits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_entries_pre_payment_id_foreign` FOREIGN KEY (`pre_payment_id`) REFERENCES `pre_payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `appreciations`
--
ALTER TABLE `appreciations`
  ADD CONSTRAINT `appreciations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `appreciations_award_id_foreign` FOREIGN KEY (`award_id`) REFERENCES `awards` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `appreciations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appreciations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appreciations_generates_id_foreign` FOREIGN KEY (`generates_id`) REFERENCES `generates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `appreciations_letterhead_template_id_foreign` FOREIGN KEY (`letterhead_template_id`) REFERENCES `letterhead_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appreciations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_asset_type_id_foreign` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_asset_user_id_foreign` FOREIGN KEY (`asset_user_id`) REFERENCES `asset_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_broken_by_foreign` FOREIGN KEY (`broken_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `asset_types`
--
ALTER TABLE `asset_types`
  ADD CONSTRAINT `asset_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `asset_users`
--
ALTER TABLE `asset_users`
  ADD CONSTRAINT `asset_users_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_users_broken_user_id_foreign` FOREIGN KEY (`broken_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_users_lent_by_foreign` FOREIGN KEY (`lent_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_users_lent_to_foreign` FOREIGN KEY (`lent_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_users_return_by_foreign` FOREIGN KEY (`return_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendances_holiday_id_foreign` FOREIGN KEY (`holiday_id`) REFERENCES `holidays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendances_leave_id_foreign` FOREIGN KEY (`leave_id`) REFERENCES `leaves` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `awards`
--
ALTER TABLE `awards`
  ADD CONSTRAINT `awards_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `awards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `basic_salary_details`
--
ALTER TABLE `basic_salary_details`
  ADD CONSTRAINT `basic_salary_details_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `basic_salary_details_salary_component_id_foreign` FOREIGN KEY (`salary_component_id`) REFERENCES `salary_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `basic_salary_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_lang_id_foreign` FOREIGN KEY (`lang_id`) REFERENCES `langs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `companies_pdf_font_id_foreign` FOREIGN KEY (`pdf_font_id`) REFERENCES `pdf_fonts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `companies_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `company_policies`
--
ALTER TABLE `company_policies`
  ADD CONSTRAINT `company_policies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `company_policies_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaints_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaints_generates_id_foreign` FOREIGN KEY (`generates_id`) REFERENCES `generates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `complaints_letterhead_template_id_foreign` FOREIGN KEY (`letterhead_template_id`) REFERENCES `letterhead_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaints_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaints_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `currencies`
--
ALTER TABLE `currencies`
  ADD CONSTRAINT `currencies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `departments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `deposits_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deposits_deposit_category_id_foreign` FOREIGN KEY (`deposit_category_id`) REFERENCES `deposit_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deposits_payer_id_foreign` FOREIGN KEY (`payer_id`) REFERENCES `payers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deposit_categories`
--
ALTER TABLE `deposit_categories`
  ADD CONSTRAINT `deposit_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `designations`
--
ALTER TABLE `designations`
  ADD CONSTRAINT `designations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `designations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_fields`
--
ALTER TABLE `employee_fields`
  ADD CONSTRAINT `employee_fields_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_fields_field_type_id_foreign` FOREIGN KEY (`field_type_id`) REFERENCES `field_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_specific_leave_counts`
--
ALTER TABLE `employee_specific_leave_counts`
  ADD CONSTRAINT `employee_specific_leave_counts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_specific_leave_counts_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_specific_leave_counts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_work_status`
--
ALTER TABLE `employee_work_status`
  ADD CONSTRAINT `employee_work_status_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_payee_id_foreign` FOREIGN KEY (`payee_id`) REFERENCES `payees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedbacks_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback_users`
--
ALTER TABLE `feedback_users`
  ADD CONSTRAINT `feedback_users_feedback_id_foreign` FOREIGN KEY (`feedback_id`) REFERENCES `feedbacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedback_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `field_types`
--
ALTER TABLE `field_types`
  ADD CONSTRAINT `field_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `forms`
--
ALTER TABLE `forms`
  ADD CONSTRAINT `forms_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `forms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `forms_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `generates`
--
ALTER TABLE `generates`
  ADD CONSTRAINT `generates_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `generates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `generates_letterhead_template_id_foreign` FOREIGN KEY (`letterhead_template_id`) REFERENCES `letterhead_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `generates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `holidays_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `holidays_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `increments_promotions`
--
ALTER TABLE `increments_promotions`
  ADD CONSTRAINT `increments_promotions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `increments_promotions_current_designation_id_foreign` FOREIGN KEY (`current_designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `increments_promotions_generate_id_foreign` FOREIGN KEY (`generate_id`) REFERENCES `generates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `increments_promotions_letterhead_template_id_foreign` FOREIGN KEY (`letterhead_template_id`) REFERENCES `letterhead_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `increments_promotions_promoted_designation_id_foreign` FOREIGN KEY (`promoted_designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `increments_promotions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `indicators`
--
ALTER TABLE `indicators`
  ADD CONSTRAINT `indicators_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD CONSTRAINT `leave_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leave_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `letterhead_templates`
--
ALTER TABLE `letterhead_templates`
  ADD CONSTRAINT `letterhead_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news_users`
--
ALTER TABLE `news_users`
  ADD CONSTRAINT `news_users_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offboardings`
--
ALTER TABLE `offboardings`
  ADD CONSTRAINT `offboardings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offboardings_generates_id_foreign` FOREIGN KEY (`generates_id`) REFERENCES `generates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `offboardings_letterhead_template_id_foreign` FOREIGN KEY (`letterhead_template_id`) REFERENCES `letterhead_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offboardings_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `offboardings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payees`
--
ALTER TABLE `payees`
  ADD CONSTRAINT `payees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payers`
--
ALTER TABLE `payers`
  ADD CONSTRAINT `payers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `payrolls_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payrolls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payrolls_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payrolls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payroll_components`
--
ALTER TABLE `payroll_components`
  ADD CONSTRAINT `payroll_components_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_components_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_components_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_components_pre_payment_id_foreign` FOREIGN KEY (`pre_payment_id`) REFERENCES `pre_payments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_components_salary_component_id_foreign` FOREIGN KEY (`salary_component_id`) REFERENCES `salary_components` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_components_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pdf_fonts`
--
ALTER TABLE `pdf_fonts`
  ADD CONSTRAINT `pdf_fonts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pre_payments`
--
ALTER TABLE `pre_payments`
  ADD CONSTRAINT `pre_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pre_payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pre_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD CONSTRAINT `salary_components_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salary_groups`
--
ALTER TABLE `salary_groups`
  ADD CONSTRAINT `salary_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salary_group_components`
--
ALTER TABLE `salary_group_components`
  ADD CONSTRAINT `salary_group_components_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salary_group_components_salary_component_id_foreign` FOREIGN KEY (`salary_component_id`) REFERENCES `salary_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salary_group_components_salary_group_id_foreign` FOREIGN KEY (`salary_group_id`) REFERENCES `salary_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salary_group_users`
--
ALTER TABLE `salary_group_users`
  ADD CONSTRAINT `salary_group_users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salary_group_users_salary_group_id_foreign` FOREIGN KEY (`salary_group_id`) REFERENCES `salary_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salary_group_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff_onboarding`
--
ALTER TABLE `staff_onboarding`
  ADD CONSTRAINT `staff_onboarding_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `translations_lang_id_foreign` FOREIGN KEY (`lang_id`) REFERENCES `langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_employee_status_id_foreign` FOREIGN KEY (`employee_status_id`) REFERENCES `employee_work_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_report_to_foreign` FOREIGN KEY (`report_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_salary_group_id_foreign` FOREIGN KEY (`salary_group_id`) REFERENCES `salary_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD CONSTRAINT `user_documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_documents_field_type_id_foreign` FOREIGN KEY (`field_type_id`) REFERENCES `employee_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `warnings`
--
ALTER TABLE `warnings`
  ADD CONSTRAINT `warnings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `warnings_generates_id_foreign` FOREIGN KEY (`generates_id`) REFERENCES `generates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `warnings_letterhead_template_id_foreign` FOREIGN KEY (`letterhead_template_id`) REFERENCES `letterhead_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `warnings_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `warnings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `work_durations`
--
ALTER TABLE `work_durations`
  ADD CONSTRAINT `work_durations_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `work_durations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
