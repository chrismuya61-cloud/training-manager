<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// 1. TRAININGS TABLE
if (!$CI->db->table_exists(db_prefix() . 'trainings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'trainings` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `subject` VARCHAR(200) NOT NULL,
        `description` TEXT NULL,
        `venue` VARCHAR(200) NULL,
        `meeting_url` TEXT NULL,
        `start_date` DATETIME NOT NULL,
        `end_date` DATETIME NOT NULL,
        `price` DECIMAL(15,2) DEFAULT 0.00,
        `currency` INT(11) DEFAULT 1,
        `assigned_staff_id` INT(11) NOT NULL,
        `capacity` INT(11) DEFAULT 50,
        `enable_waitlist` INT(1) DEFAULT 1,
        `validity_months` INT(11) DEFAULT 0,
        `confirmation_email` TEXT NULL,
        `require_quiz` INT(1) DEFAULT 0,
        `require_feedback` INT(1) DEFAULT 0,
        `is_active` INT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 2. REGISTRATIONS TABLE
if (!$CI->db->table_exists(db_prefix() . 'training_registrations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'training_registrations` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `training_id` INT(11) NOT NULL,
        `name` VARCHAR(150) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phonenumber` VARCHAR(50) NULL,
        `company` VARCHAR(150) NULL,
        `status` INT(1) DEFAULT 0 COMMENT "0=Registered, 1=Attended",
        `attendance_mode` VARCHAR(20) DEFAULT "physical",
        `is_waitlist` INT(1) DEFAULT 0,
        `payment_status` VARCHAR(20) DEFAULT "unpaid",
        `registration_source` VARCHAR(50) DEFAULT "manual",
        `unique_ticket_code` VARCHAR(50) NOT NULL,
        `referral_code_generated` VARCHAR(50) NULL,
        `invoice_id` INT(11) DEFAULT 0,
        `quiz_passed` INT(1) DEFAULT 0,
        `feedback_submitted` INT(1) DEFAULT 0,
        `certificate_sent` INT(1) DEFAULT 0,
        `attendance_date` DATETIME NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 3. QUIZ QUESTIONS
if (!$CI->db->table_exists(db_prefix() . 'training_quiz_questions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'training_quiz_questions` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `training_id` INT(11) NOT NULL,
        `question` TEXT NOT NULL,
        `option_a` VARCHAR(255) NOT NULL,
        `option_b` VARCHAR(255) NOT NULL,
        `option_c` VARCHAR(255) NOT NULL,
        `correct_option` CHAR(1) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 4. FEEDBACK
if (!$CI->db->table_exists(db_prefix() . 'training_feedback')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'training_feedback` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `registration_id` INT(11) NOT NULL,
        `rating` INT(1) NOT NULL,
        `comment` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 5. EXPENSES
if (!$CI->db->table_exists(db_prefix() . 'training_expenses')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'training_expenses` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `training_id` INT(11) NOT NULL,
        `expense_name` VARCHAR(200) NOT NULL,
        `amount` DECIMAL(15,2) NOT NULL,
        `date_added` DATE NOT NULL,
        `note` TEXT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 6. MEDIA
if (!$CI->db->table_exists(db_prefix() . 'training_media')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'training_media` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `training_id` INT(11) NOT NULL,
        `file_name` VARCHAR(255) NOT NULL,
        `file_type` VARCHAR(50) NOT NULL,
        `share_token` VARCHAR(100) NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
