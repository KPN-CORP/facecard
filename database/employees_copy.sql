/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : hcispanel_hc

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 27/05/2025 15:54:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for employees (Core Employee Data for Face Card)
-- ----------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `fullname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('Male','Female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `group_company` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `designation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `designation_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `job_level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `office_area` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `date_of_birth` date NULL DEFAULT NULL,
  `nationality` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `religion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `marital_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `permanent_city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `date_of_joining` date NULL DEFAULT NULL,
  -- Removed unused columns like manager_l1_id, manager_l2_id, employee_type, etc.
  -- Added back users_id and access_menu as they were in your previous INSERT, making them nullable
  `users_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `access_menu` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `employees_employee_id_unique`(`employee_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of employees
-- ----------------------------
INSERT INTO `employees` (`id`, `employee_id`, `fullname`, `gender`, `email`, `group_company`, `designation`, `designation_name`, `job_level`, `company_name`, `office_area`, `unit`, `date_of_birth`, `nationality`, `religion`, `marital_status`, `permanent_city`, `date_of_joining`, `users_id`, `access_menu`, `created_at`, `updated_at`) VALUES
(1, 'DBOX', 'Wilia Mahnawangkartika', 'Female', 'wilia.m@kpndomain.com', 'KPN Corporation', 'Officer', 'Officer', '5A', 'PT Karya Panca Sakti Nugraha', 'Head Office', 'Human Capital Information System', '2000-01-01', 'Indonesia', 'N.A', 'Single', 'Palembang', '2025-01-01', '12595', NULL, NOW(), NOW());

-- ----------------------------
-- Table structure for formal_educations
-- ----------------------------
DROP TABLE IF EXISTS `formal_educations`;
CREATE TABLE `formal_educations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_start_year` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `period_end_year` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `degree` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `institution` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `major` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `gpa` decimal(3,2) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `fk_formal_education_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of formal_educations
-- ----------------------------
INSERT INTO `formal_educations` (`id`, `employee_id`, `period_start_year`, `period_end_year`, `degree`, `institution`, `major`, `gpa`, `created_at`, `updated_at`) VALUES (1, 'DBOX', '2014', '2018', 'Master Degree', 'Harvard University', 'Informatics', 3.67, NOW(), NOW());
INSERT INTO `formal_educations` (`id`, `employee_id`, `period_start_year`, `period_end_year`, `degree`, `institution`, `major`, `gpa`, `created_at`, `updated_at`) VALUES (2, 'DBOX', '2019', '2021', 'Bachelor Degree', 'University of Oxford', 'Management', 3.50, NOW(), NOW());

-- ----------------------------
-- Table structure for work_experiences
-- ----------------------------
DROP TABLE IF EXISTS `work_experiences`;
CREATE TABLE `work_experiences` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `join_date` date NULL DEFAULT NULL,
  `resign_date` date NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `fk_work_experience_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of work_experiences
-- ----------------------------
INSERT INTO `work_experiences` (`id`, `employee_id`, `company_name`, `position`, `department`, `join_date`, `resign_date`, `created_at`, `updated_at`) VALUES (1, 'DBOX', 'PT Wilmar Internasional', 'Relationship Manager', 'Information Technology', '2021-05-20', '2024-05-20', NOW(), NOW());
INSERT INTO `work_experiences` (`id`, `employee_id`, `company_name`, `position`, `department`, `join_date`, `resign_date`, `created_at`, `updated_at`) VALUES (2, 'DBOX', 'Orang Tua Group', 'Officer', 'Industrial Relational', '2024-06-01', '2025-05-01', NOW(), NOW());

-- ----------------------------
-- Table structure for trainings_certifications
-- ----------------------------
DROP TABLE IF EXISTS `trainings_certifications`;
CREATE TABLE `trainings_certifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `training_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `organizer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `start_date` date NULL DEFAULT NULL,
  `end_date` date NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `fk_training_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of trainings_certifications
-- ----------------------------
INSERT INTO `trainings_certifications` (`id`, `employee_id`, `training_name`, `organizer`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES (1, 'DBOX', 'Chatbot WhatsApp API and Node JS Send and receive messages', 'Maxy Academy', '2021-05-20', '2021-05-20', NOW(), NOW());
INSERT INTO `trainings_certifications` (`id`, `employee_id`, `training_name`, `organizer`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES (2, 'DBOX', 'Chatbot WhatsApp API and Node JS Send and receive messages', 'Coursera', '2021-05-20', '2021-05-20', NOW(), NOW());

-- ----------------------------
-- Table structure for performance_appraisals
-- ----------------------------
DROP TABLE IF EXISTS `performance_appraisals`;
CREATE TABLE `performance_appraisals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `appraisal_year` year(4) NOT NULL,
  `grade` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `employee_id_year_unique` (`employee_id` ASC, `appraisal_year` ASC) USING BTREE,
  CONSTRAINT `fk_performance_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of performance_appraisals
-- ----------------------------
INSERT INTO `performance_appraisals` (`id`, `employee_id`, `appraisal_year`, `grade`, `created_at`, `updated_at`) VALUES (1, 'DBOX', 2024, 'A', NOW(), NOW());
INSERT INTO `performance_appraisals` (`id`, `employee_id`, `appraisal_year`, `grade`, `created_at`, `updated_at`) VALUES (2, 'DBOX', 2023, 'A', NOW(), NOW());
INSERT INTO `performance_appraisals` (`id`, `employee_id`, `appraisal_year`, `grade`, `created_at`, `updated_at`) VALUES (3, 'DBOX', 2022, 'A', NOW(), NOW());
INSERT INTO `performance_appraisals` (`id`, `employee_id`, `appraisal_year`, `grade`, `created_at`, `updated_at`) VALUES (4, 'DBOX', 2021, 'B', NOW(), NOW());

-- ----------------------------
-- Table structure for result_summaries
-- ----------------------------
DROP TABLE IF EXISTS `result_summaries`;
CREATE TABLE `result_summaries` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proposed_grade` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `talent_box` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `talent_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `employee_id_unique_result_summary` (`employee_id` ASC) USING BTREE,
  CONSTRAINT `fk_result_summary_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of result_summaries
-- ----------------------------
INSERT INTO `result_summaries` (`id`, `employee_id`, `proposed_grade`, `talent_box`, `talent_status`, `created_at`, `updated_at`) VALUES (1, 'DBOX', '6A', 'High Potentials', 'Non Talent', NOW(), NOW());

-- ----------------------------
-- Table structure for competency_assessments
-- ----------------------------
DROP TABLE IF EXISTS `competency_assessments`;
CREATE TABLE `competency_assessments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_date` date NOT NULL,
  `matrix_grade` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `synergized_team_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `integrity_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `growth_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `adaptive_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `passion_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `manage_planning_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `decision_making_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `relationship_building_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `developing_others_score` tinyint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  CONSTRAINT `fk_comp_assessment_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


-- ----------------------------
-- Table structure for matrix_grades_configs
-- ----------------------------
DROP TABLE IF EXISTS `matrix_grades_configs`;
CREATE TABLE `matrix_grades_configs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `grade_level` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `synergized_team_min` tinyint UNSIGNED NOT NULL,
  `integrity_min` tinyint UNSIGNED NOT NULL,
  `growth_min` tinyint UNSIGNED NOT NULL,
  `adaptive_min` tinyint UNSIGNED NOT NULL,
  `passion_min` tinyint UNSIGNED NOT NULL,
  `manage_planning_min` tinyint UNSIGNED NOT NULL,
  `decision_making_min` tinyint UNSIGNED NOT NULL,
  `relationship_building_min` tinyint UNSIGNED NOT NULL,
  `developing_others_min` tinyint UNSIGNED NOT NULL,
  `overall_status_min` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of matrix_grades_configs
-- ----------------------------
INSERT INTO `matrix_grades_configs` (`period`, `grade_level`, `synergized_team_min`, `integrity_min`, `growth_min`, `adaptive_min`, `passion_min`, `manage_planning_min`, `decision_making_min`, `relationship_building_min`, `developing_others_min`, `overall_status_min`, `created_at`, `updated_at`) VALUES
(2025, '2A', 1, 1, 1, 1, 1, 0, 0, 0, 0, 'Competent', NOW(), NOW()),
(2025, '2B', 1, 1, 1, 1, 1, 1, 0, 0, 0, 'Competent', NOW(), NOW()),
(2025, '2C', 1, 1, 1, 1, 0, 0, 0, 0, 0, 'Competent', NOW(), NOW()),
(2025, '2D', 1, 1, 1, 1, 1, 1, 0, 0, 0, 'Competent', NOW(), NOW()),
(2025, '3A', 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Proficient', NOW(), NOW()),
(2025, '3B', 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Proficient', NOW(), NOW()),
(2025, '4A', 2, 2, 2, 2, 2, 2, 2, 2, 2, 'Proficient', NOW(), NOW()),
(2025, '4B', 2, 2, 2, 2, 2, 2, 2, 2, 2, 'Proficient', NOW(), NOW()),
(2025, '5A', 3, 3, 3, 3, 3, 2, 2, 2, 2, 'Excel', NOW(), NOW()),
(2025, '5B', 3, 3, 3, 3, 3, 2, 2, 2, 2, 'Excel', NOW(), NOW()),
(2025, '6A', 3, 3, 3, 3, 3, 3, 3, 3, 3, 'Excel', NOW(), NOW()),
(2025, '6B', 3, 3, 3, 3, 3, 3, 3, 3, 3, 'Excel', NOW(), NOW()),
(2025, '7A', 3, 3, 3, 3, 3, 3, 3, 3, 3, 'Excel', NOW(), NOW()),
(2025, '7B', 3, 3, 3, 3, 3, 3, 3, 3, 3, 'Excel', NOW(), NOW()),
(2025, '8A', 4, 4, 4, 4, 4, 4, 4, 4, 4, 'Excel', NOW(), NOW()),
(2025, '8B', 4, 4, 4, 4, 4, 4, 4, 4, 4, 'Excel', NOW(), NOW()),
(2025, '9B', 4, 4, 4, 4, 4, 4, 4, 4, 4, 'Excel', NOW(), NOW());