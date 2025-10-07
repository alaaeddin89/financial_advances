-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2025 at 08:47 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `financial_advances`
--

-- --------------------------------------------------------

--
-- Table structure for table `advance_invoice_closures`
--

CREATE TABLE `advance_invoice_closures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `advance_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `closed_amount` decimal(10,2) NOT NULL COMMENT 'قيمة المبلغ المقفل من العهدة بهذه الفاتورة',
  `closure_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `accountant_approved_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accountant_approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'اعتماد المحاسب النهائي',
  `accountant_approval_date` date DEFAULT NULL COMMENT 'تاريخ اعتماد المحاسب.',
  `is_rejected` tinyint(4) NOT NULL DEFAULT 0,
  `rejection_reason` varchar(250) DEFAULT NULL,
  `rejected_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advance_invoice_closures`
--

INSERT INTO `advance_invoice_closures` (`id`, `advance_id`, `invoice_id`, `closed_amount`, `closure_date`, `closed_by_user_id`, `accountant_approved_by_user_id`, `accountant_approved`, `accountant_approval_date`, `is_rejected`, `rejection_reason`, `rejected_by_id`, `rejected_at`, `deleted_at`, `created_at`, `updated_at`) VALUES
(18, 10, 22, 250.00, '2025-10-04 12:38:33', 6, NULL, 1, '2025-10-04', 0, NULL, NULL, NULL, NULL, '2025-10-04 12:38:33', '2025-10-04 12:45:00'),
(19, 10, 23, 150.00, '2025-10-04 12:38:33', 6, NULL, 0, NULL, 1, 'المبلغ غير مطابق', 7, '2025-10-04 12:45:20', '2025-10-04 12:45:20', '2025-10-04 12:38:33', '2025-10-04 12:45:20'),
(20, 10, 23, 170.00, '2025-10-04 12:49:03', 6, NULL, 1, '2025-10-04', 0, NULL, NULL, NULL, NULL, '2025-10-04 12:49:03', '2025-10-04 12:49:32');

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name_ar`, `name_en`, `created_at`, `updated_at`) VALUES
(1, 'الرياض', NULL, NULL, NULL),
(3, 'المدينة المنورة', NULL, '2025-10-04 14:20:12', '2025-10-04 14:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `expense_invoices`
--

CREATE TABLE `expense_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_type` enum('Tax_Invoice','Invoice_with_Attachments','Invoice_without_Attachments') NOT NULL,
  `invoice_no` int(11) NOT NULL COMMENT 'رقم الفاتورة',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` int(10) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `used_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'المبلغ المستخدم للتقفيل',
  `status` enum('Pending Review','Approved','Rejected') NOT NULL DEFAULT 'Pending Review',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'مسار حفظ صورة/ملف الفاتورة',
  `invoice_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `is_general_expense` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_invoices`
--

INSERT INTO `expense_invoices` (`id`, `invoice_type`, `invoice_no`, `user_id`, `supplier_id`, `amount`, `used_amount`, `status`, `file_path`, `invoice_date`, `description`, `is_general_expense`, `created_at`, `updated_at`) VALUES
(22, 'Tax_Invoice', 1000, 6, 1, 250.00, 250.00, 'Approved', 'invoices/1yn7X128NSnXfE4enqOsncuxuiwsZA6zdj4BSVp9.pdf', '2025-10-01', NULL, 1, '2025-10-04 12:30:59', '2025-10-04 12:38:33'),
(23, 'Invoice_without_Attachments', 1, 6, NULL, 170.00, 170.00, 'Approved', NULL, '2025-10-02', 'بدل مصاريف ضيافة', 0, '2025-10-04 12:37:16', '2025-10-04 12:49:03'),
(24, 'Invoice_with_Attachments', 1500, 6, NULL, 20.00, 0.00, 'Approved', 'invoices/WGSkxpiQcIdSTBUXn8VnnVNSNUWO0tw9qFe3YHII.jpg', '2025-09-25', NULL, 1, '2025-10-04 15:06:43', '2025-10-04 15:06:43'),
(25, 'Invoice_without_Attachments', 2, 6, NULL, 158.00, 0.00, 'Approved', NULL, '2025-09-20', 'فاتورة  من دون رقم فاتورة', 1, '2025-10-04 15:15:52', '2025-10-04 15:15:52'),
(26, 'Tax_Invoice', 32458, 6, 10, 500.00, 0.00, 'Approved', 'invoices/xYh1O03xguORkOZFVo3uC98PrQY3Q12liqS4j9gE.jpg', '2025-08-01', NULL, 1, '2025-10-04 15:19:35', '2025-10-04 15:19:35');

-- --------------------------------------------------------

--
-- Table structure for table `expense_invoice_branch`
--

CREATE TABLE `expense_invoice_branch` (
  `expense_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_invoice_branch`
--

INSERT INTO `expense_invoice_branch` (`expense_invoice_id`, `branch_id`) VALUES
(23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_advances`
--

CREATE TABLE `financial_advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `issued_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `issue_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Confirmed','Partially Closed','Closed') NOT NULL DEFAULT 'Pending',
  `confirmation_date` timestamp NULL DEFAULT NULL COMMENT 'تاريخ تأكيد الموظف',
  `closed_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي المبلغ المقفل بالفواتير',
  `remaining_balance` decimal(10,2) DEFAULT NULL COMMENT 'المبلغ المتبقي المستحق على الموظف',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_advances`
--

INSERT INTO `financial_advances` (`id`, `user_id`, `issued_by_user_id`, `amount`, `description`, `issue_date`, `status`, `confirmation_date`, `closed_amount`, `remaining_balance`, `created_at`, `updated_at`) VALUES
(10, 6, 7, 1000.00, 'عهدة لشهر 10-2025', '2025-10-04 12:23:24', 'Partially Closed', '2025-10-04 12:25:59', 420.00, 580.00, '2025-10-04 12:23:24', '2025-10-04 12:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `b_enabled` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `b_enabled`, `created_at`, `updated_at`) VALUES
(1, 'مدير النظام', 1, NULL, NULL),
(2, 'محاسب', 1, '2025-03-20 11:53:47', '2025-03-20 11:53:47'),
(3, 'كاشير', 1, '2025-03-20 11:53:56', '2025-03-20 11:53:56'),
(7, 'التدقيق', 1, '2025-10-04 12:56:39', '2025-10-04 12:56:39');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '1 is readed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(8, '2010_10_12_100000_create_users_table', 1),
(9, '2014_10_12_100000_create_password_resets_table', 1),
(10, '2019_08_19_000000_create_failed_jobs_table', 1),
(11, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(15, '2025_03_09_191218_create_messages_table', 2),
(16, '2025_03_09_191247_create_attachments_table', 2),
(17, '2025_03_20_120808_create_tools_table', 3),
(18, '2025_03_20_121034_create_groups_table', 3),
(19, '2025_03_20_121133_create_user_groups_table', 3),
(20, '2025_03_20_121217_create_tool_groups_table', 3),
(52, '2025_09_29_162133_create_financial_advances_table', 4),
(53, '2025_09_29_162311_create_expense_invoices_table', 5),
(54, '2025_09_29_162409_create_advance_invoice_closures_table', 5),
(55, '2025_10_02_154847_create_notifications_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('3ce232c2-e651-43db-87f5-6d76626b4655', 'App\\Notifications\\ClosureRejected', 'App\\Models\\User', 6, '{\"type\":\"closure_rejected\",\"invoice_id\":23,\"advance_id\":10,\"reason\":\"\\u0627\\u0644\\u0645\\u0628\\u0644\\u063a \\u063a\\u064a\\u0631 \\u0645\\u0637\\u0627\\u0628\\u0642\",\"message\":\"\\u062a\\u0645 \\u0631\\u0641\\u0636 \\u062a\\u0642\\u0641\\u064a\\u0644 \\u0627\\u0644\\u0641\\u0627\\u062a\\u0648\\u0631\\u0629 \\u0631\\u0642\\u0645 23 (\\u0627\\u0644\\u0639\\u0647\\u062f\\u0629 \\u0631\\u0642\\u0645 10) \\u0628\\u0633\\u0628\\u0628: \\u0627\\u0644\\u0645\\u0628\\u0644\\u063a \\u063a\\u064a\\u0631 \\u0645\\u0637\\u0627\\u0628\\u0642\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/invoices\\/23\"}', '2025-10-04 14:48:09', '2025-10-04 12:45:20', '2025-10-04 14:48:09'),
('78ffff66-c7bd-40a7-800f-004f2718fc4e', 'App\\Notifications\\NewAdvancePendingConfirmation', 'App\\Models\\User', 6, '{\"advance_id\":10,\"amount\":\"1000.00\",\"issuer_name\":\"\\u0639\\u0644\\u064a \\u0645\\u062d\\u0645\\u062f \\u0635\\u064a\\u0627\\u0645\",\"message\":\"\\u0644\\u062f\\u064a\\u0643 \\u0639\\u0647\\u062f\\u0629 \\u0645\\u0627\\u0644\\u064a\\u0629 \\u062c\\u062f\\u064a\\u062f\\u0629 \\u0628\\u0631\\u0642\\u0645 **10** \\u0628\\u0642\\u064a\\u0645\\u0629 1,000.00 \\u0641\\u064a \\u0627\\u0646\\u062a\\u0638\\u0627\\u0631 \\u0627\\u0644\\u0642\\u0628\\u0648\\u0644.\",\"link\":\"http:\\/\\/127.0.0.1:8000\\/advances\\/10\"}', '2025-10-04 14:48:09', '2025-10-04 12:23:24', '2025-10-04 14:48:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `text`) VALUES
(1, 'POST /salary/changeStatus HTTP/1.1\nAccept:             */*\nAccept-Encoding:    gzip, deflate, br, zstd\nAccept-Language:    en-US,en;q=0.9\nConnection:         keep-alive\nContent-Length:     152\nContent-Type:       application/x-www-form-urlencoded; charset=UTF-8\nCookie:             XSRF-TOKEN=eyJpdiI6IlJKdUJBRTVYc1loSXBqUkVWQ2pLT2c9PSIsInZhbHVlIjoiYkg3L2RINjBIWmszUnAwMThNNlVsbTVxYVU3UUVhVSsxb0lZK0owS0xabklOU3VzV2gxR0t3ZjEzTldiaTRMYnRYN0ZYOUNJa2Z6MGR2eVVFQWVNdkhpMWs5YS9PbFdlYlNtRHM5eFh2ZWdaMjBCMHBLdjV3U3phQU1mU0g0QWoiLCJtYWMiOiIxYTQzMGJhNGVmOTg3ZTMxNGE3NTM1NjYzMWI5OTg2YTI2ZjEwMGQ2MzFmNjdmOGYzZTYwNmZiZTY2NGQ2NTc5IiwidGFnIjoiIn0%3D; _session=eyJpdiI6Ii80cnA4S0Z2WWRuVnYyMEh1V0EyY2c9PSIsInZhbHVlIjoiR2dMOXhEL1dkU2RCTmNueDBhWHMrWmR3YXA1YlBadWZWQkdtdXNnU0pDekdQNEZRQjlDYjhjMVl0TitaZHZQa1plaCt1czJJUEprTXAxMUNDMFBXWnZaQWZnWERoN0JQYXRJS0V4WnNGd2taZ3NVaDZ2VURlUnFIQXdDa3BmZ2UiLCJtYWMiOiIyMGI4YmNkOGM1MDQyMTE4NjU5MTcwNjFkMzJlZjViNTdmNzRmNDk4MjBkNzMwMjk1NTNmZmNkYTYxMWFjZjM3IiwidGFnIjoiIn0%3D\nHost:               127.0.0.1:8000\nOrigin:             http://127.0.0.1:8000\nReferer:            http://127.0.0.1:8000/salaries\nSec-Ch-Ua:          \"Google Chrome\";v=\"131\", \"Chromium\";v=\"131\", \"Not_A Brand\";v=\"24\"\nSec-Ch-Ua-Mobile:   ?0\nSec-Ch-Ua-Platform: \"Windows\"\nSec-Fetch-Dest:     empty\nSec-Fetch-Mode:     cors\nSec-Fetch-Site:     same-origin\nUser-Agent:         Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36\nX-Requested-With:   XMLHttpRequest\nCookie: XSRF-TOKEN=UV34zwlfKZ0ZrlONxICB46xoFe5EtAeODGOyRDGd; _session=u2hgM1hNmYngvArokVskiswuoJBlZPaF96NXdj21\n\n_token=UV34zwlfKZ0ZrlONxICB46xoFe5EtAeODGOyRDGd&job_id_no=&emp_name=&month=&file_number=&year=&return_name=&return_amount=&return_date=&id=&return_note='),
(2, 'id:status:');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `tax_id_no` varchar(30) NOT NULL COMMENT 'الرقم الضريبي',
  `commercial_register_no` varchar(100) NOT NULL COMMENT 'السجل التجاري',
  `phone` varchar(20) DEFAULT NULL,
  `national_address` varchar(255) NOT NULL COMMENT 'العنوان الوطني (الشارع والمدينة)',
  `building_number` varchar(10) DEFAULT NULL COMMENT 'رقم المبنى',
  `sub_number` varchar(10) DEFAULT NULL COMMENT 'الرقم الفرعي',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'مصفوفة بمسارات المرفقات والمستندات' CHECK (json_valid(`attachments`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `user_id`, `name_ar`, `name_en`, `tax_id_no`, `commercial_register_no`, `phone`, `national_address`, `building_number`, `sub_number`, `attachments`, `created_at`, `updated_at`) VALUES
(1, 6, 'شركة الضيافة', NULL, '1111111111111', '125489', '0599680096', 'الرياض - شارع السفارة', '125', '4', '[\"\\/storage\\/uploads\\/suppliers\\/6\\/PJUwVaeigi2tHTzk7DXvTZY7aUfCLRfBZ0gwHS66.pdf\"]', '2025-10-03 07:39:16', '2025-10-03 07:39:16'),
(2, 6, 'شركة السقا للأجهزة الكهربائية', 'Saqaa Company', '1111111111113', '124587', '1222222222', 'الرياض - شارع السفارة', '125', '5', NULL, '2025-10-02 18:36:49', '2025-10-02 18:36:49'),
(10, 6, 'شركة الأصيل الذهبي', 'ِِAseil Dahaby', '1111111111114', '125489', '1222222222', 'الرياض - شارع السفارة', '125', '7', '\"[]\"', '2025-10-03 05:20:58', '2025-10-03 05:20:58'),
(11, 6, 'شركة النهار', 'Al Nahar Com', '369369369369', '54879', '0593258258', 'الرياض - شارع السفارة', '125', '7', '\"[]\"', '2025-10-03 20:22:05', '2025-10-03 20:22:05');

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE `tools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `b_public` int(11) NOT NULL,
  `i_type` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `i_parent_id` int(11) NOT NULL,
  `i_order` int(11) NOT NULL,
  `i_show_menu` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tools`
--

INSERT INTO `tools` (`id`, `name`, `url`, `b_public`, `i_type`, `icon`, `i_parent_id`, `i_order`, `i_show_menu`, `created_at`, `updated_at`) VALUES
(1, 'نظام الصلاحيات', '1', 0, 1, 'fa-check-square-o', 0, 1, 0, NULL, NULL),
(2, 'كشف مجموعات الصلاحيات', 'group', 0, 2, '', 1, 1, 0, NULL, NULL),
(3, 'إدارة المستخدمين', '2', 0, 1, 'fa-users', 0, 0, 0, NULL, NULL),
(4, 'أرشيف الملفات', '10', 0, 1, '', 0, 10, 0, NULL, NULL),
(5, 'المراسلات', '11', 0, 1, '', 0, 11, 0, NULL, NULL),
(6, 'البحث', 'files.index', 0, 2, '', 4, 2, 1, NULL, NULL),
(7, 'رقع ملفات', 'files.create', 0, 2, '', 4, 1, 1, NULL, NULL),
(8, 'مراسلة جديدة', 'messages.create', 0, 2, '', 5, 1, 0, NULL, NULL),
(9, 'كشف المراسلات', 'messages', 0, 2, '', 5, 2, 0, NULL, NULL),
(10, 'الرد على المراسلات', '16', 0, 2, '', 5, 3, 0, NULL, NULL),
(11, 'مجموعة صلاحيات جديدة', '17', 0, 2, '', 1, 2, 0, NULL, NULL),
(12, 'اضافة صلاحيات الى مجموعة', 'pergroup', 0, 2, '', 1, 3, 0, NULL, NULL),
(13, 'كشف المستخدمين', 'users', 0, 2, '', 3, 1, 0, NULL, NULL),
(14, 'اضافة مستخدم جديد', 'users.create', 0, 2, '', 3, 2, 0, NULL, NULL),
(15, 'تعديل بيانات مستخدم', 'users.edit', 0, 2, '', 3, 3, 0, NULL, NULL),
(16, 'حذف مستخدم', '18', 0, 2, '', 3, 4, 0, NULL, NULL),
(17, 'إدارة العهد', 'advances', 0, 1, 'fa-users', 0, 1, 1, NULL, NULL),
(18, 'تسجيل العهد', 'advances.create', 0, 1, 'fa-users', 17, 2, 1, NULL, NULL),
(19, 'كشف العهد', 'advances.index', 0, 1, 'fa-users', 17, 1, 1, NULL, NULL),
(20, 'إدارة الفواتير', 'invoices', 0, 1, 'fa-users', 0, 2, 1, NULL, NULL),
(21, 'كشف الفواتير', 'invoices.index', 0, 1, 'fa-users', 20, 1, 1, NULL, NULL),
(22, 'تسجيل الفواتير', 'invoices.create', 0, 1, 'fa-users', 20, 2, 1, NULL, NULL),
(23, 'اغلاق العهد', 'closures.form', 0, 1, 'fa-users', 17, 3, 1, NULL, NULL),
(24, 'إعتماد اغلاق العهد', 'closures.review', 0, 1, 'fa-users', 17, 4, 1, NULL, NULL),
(25, 'إدارة الموردين', 'suppliers', 0, 1, 'fa-users', 0, 3, 1, NULL, NULL),
(26, 'كشف الموردين', 'suppliers.index', 0, 1, 'fa-users', 25, 1, 1, NULL, NULL),
(27, 'تقرير العهد - كاشير', 'reports.advance.employee.view', 0, 1, 'fa-users', 17, 4, 1, NULL, NULL),
(28, 'تقرير العهد - الإجمالي', 'reports.advance.all.view', 0, 1, 'fa-users', 17, 5, 1, NULL, NULL),
(29, 'كشف المصروفات/التقفيل المعتمد', 'advances.closure_report', 0, 1, 'fa-users', 17, 6, 1, NULL, NULL),
(30, 'ادارة الفروع', 'Branches', 0, 1, 'fa-users', 0, 3, 0, NULL, NULL),
(31, 'كشف الفروع', 'branches.index', 0, 1, 'fa-users', 30, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tool_groups`
--

CREATE TABLE `tool_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` int(11) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tool_groups`
--

INSERT INTO `tool_groups` (`id`, `group_id`, `tool_id`, `created_at`, `updated_at`) VALUES
(6222, 17, 16, '2025-09-29 18:37:59', '2025-09-29 18:37:59'),
(6254, 3, 19, '2025-10-02 18:20:23', '2025-10-02 18:20:23'),
(6255, 3, 23, '2025-10-02 18:20:23', '2025-10-02 18:20:23'),
(6256, 3, 21, '2025-10-02 18:20:23', '2025-10-02 18:20:23'),
(6257, 3, 22, '2025-10-02 18:20:23', '2025-10-02 18:20:23'),
(6258, 3, 26, '2025-10-02 18:20:23', '2025-10-02 18:20:23'),
(6302, 2, 13, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6303, 2, 14, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6304, 2, 15, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6305, 2, 16, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6306, 2, 8, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6307, 2, 9, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6308, 2, 10, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6309, 2, 19, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6310, 2, 18, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6311, 2, 24, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6312, 2, 27, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6313, 2, 28, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6314, 2, 29, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6315, 2, 21, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6316, 2, 26, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(6317, 1, 2, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6318, 1, 11, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6319, 1, 12, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6320, 1, 13, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6321, 1, 14, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6322, 1, 15, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6323, 1, 16, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6324, 1, 7, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6325, 1, 6, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6326, 1, 8, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6327, 1, 9, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6328, 1, 10, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6329, 1, 19, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6330, 1, 18, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6331, 1, 27, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6332, 1, 29, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6333, 1, 21, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6334, 1, 22, '2025-10-04 14:14:08', '2025-10-04 14:14:08'),
(6335, 1, 31, '2025-10-04 14:14:08', '2025-10-04 14:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `full_name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'omar', 'عمر بشير', 'omar@gmail.com', NULL, '$2y$10$5tb3JvUkJNkPolSzkxB9.OrRdRsKUnkC6MBgqolchCuG8JMIe/Ruu', NULL, 'admin', NULL, '2025-01-21 14:31:11', '2025-03-21 22:48:13'),
(6, 'alaaeddin', 'علاء بشير', 'abashear.1989@gmail.com', NULL, '$2y$10$VYpIQrmBtlI8zKBzHBTKXuXGpCq6WuEJc6Qg5lZ1BvrzQTZGSRBtW', NULL, 'cashier', NULL, '2025-03-20 23:16:48', '2025-09-30 09:08:44'),
(7, 'ali', 'علي محمد صيام', 'a@a.ps', NULL, '$2y$10$U/MN106TpTsYAHa4b5oRXOLQ5KvRwVShHFNGn5i3xvTAz0xTNA.ae', NULL, 'accountant', NULL, '2025-10-01 20:03:41', '2025-10-01 20:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `user_id`, `group_id`, `created_at`, `updated_at`) VALUES
(109, 6, 3, '2025-10-02 18:20:23', '2025-10-02 18:20:23'),
(114, 7, 2, '2025-10-04 10:40:43', '2025-10-04 10:40:43'),
(115, 1, 1, '2025-10-04 14:14:08', '2025-10-04 14:14:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advance_invoice_closures`
--
ALTER TABLE `advance_invoice_closures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `advance_invoice_closures_advance_id_foreign` (`advance_id`),
  ADD KEY `advance_invoice_closures_invoice_id_foreign` (`invoice_id`),
  ADD KEY `advance_invoice_closures_closed_by_user_id_foreign` (`closed_by_user_id`),
  ADD KEY `accountant_approved_by_user_id_fk` (`accountant_approved_by_user_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_message_id_foreign` (`message_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_invoices`
--
ALTER TABLE `expense_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`,`supplier_id`),
  ADD KEY `expense_invoices_user_id_foreign` (`user_id`);

--
-- Indexes for table `expense_invoice_branch`
--
ALTER TABLE `expense_invoice_branch`
  ADD PRIMARY KEY (`expense_invoice_id`,`branch_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `financial_advances`
--
ALTER TABLE `financial_advances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_advances_user_id_foreign` (`user_id`),
  ADD KEY `financial_advances_issued_by_user_id_foreign` (`issued_by_user_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`),
  ADD KEY `messages_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

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
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_id_no` (`tax_id_no`),
  ADD KEY `supp;ier_user_id_fk` (`user_id`);

--
-- Indexes for table `tools`
--
ALTER TABLE `tools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tool_groups`
--
ALTER TABLE `tool_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advance_invoice_closures`
--
ALTER TABLE `advance_invoice_closures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expense_invoices`
--
ALTER TABLE `expense_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_advances`
--
ALTER TABLE `financial_advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `tool_groups`
--
ALTER TABLE `tool_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6336;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advance_invoice_closures`
--
ALTER TABLE `advance_invoice_closures`
  ADD CONSTRAINT `accountant_approved_by_user_id_fk` FOREIGN KEY (`accountant_approved_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `advance_invoice_closures_advance_id_foreign` FOREIGN KEY (`advance_id`) REFERENCES `financial_advances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `advance_invoice_closures_closed_by_user_id_foreign` FOREIGN KEY (`closed_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `advance_invoice_closures_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `expense_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_invoices`
--
ALTER TABLE `expense_invoices`
  ADD CONSTRAINT `expense_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `expense_invoice_branch`
--
ALTER TABLE `expense_invoice_branch`
  ADD CONSTRAINT `expense_invoice_branch_ibfk_1` FOREIGN KEY (`expense_invoice_id`) REFERENCES `expense_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_invoice_branch_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_advances`
--
ALTER TABLE `financial_advances`
  ADD CONSTRAINT `financial_advances_issued_by_user_id_foreign` FOREIGN KEY (`issued_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `financial_advances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `supp;ier_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
