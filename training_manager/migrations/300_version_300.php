<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Migration for Version 3.0.0
 * Adds Hybrid Events, Waitlist, Payment Status, and LMS Certification fields.
 */

$CI = &get_instance();

// 1. UPDATE 'tbltrainings' TABLE
$table_trainings = db_prefix() . 'trainings';

if ($CI->db->table_exists($table_trainings)) {
    
    // Hybrid Event Link
    if (!$CI->db->field_exists('meeting_url', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `meeting_url` TEXT NULL AFTER `venue`;");
    }

    // Capacity & Waitlist
    if (!$CI->db->field_exists('capacity', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `capacity` INT(11) DEFAULT 50 AFTER `assigned_staff_id`;");
    }
    if (!$CI->db->field_exists('enable_waitlist', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `enable_waitlist` INT(1) DEFAULT 1 AFTER `capacity`;");
    }

    // Certificate Validity
    if (!$CI->db->field_exists('validity_months', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `validity_months` INT(11) DEFAULT 0 AFTER `enable_waitlist`;");
    }

    // Custom Confirmation Email
    if (!$CI->db->field_exists('confirmation_email', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `confirmation_email` TEXT NULL AFTER `validity_months`;");
    }

    // LMS Requirements
    if (!$CI->db->field_exists('require_quiz', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `require_quiz` INT(1) DEFAULT 0 AFTER `confirmation_email`;");
    }
    if (!$CI->db->field_exists('require_feedback', $table_trainings)) {
        $CI->db->query("ALTER TABLE `" . $table_trainings . "` ADD `require_feedback` INT(1) DEFAULT 0 AFTER `require_quiz`;");
    }
}

// 2. UPDATE 'tbltraining_registrations' TABLE
$table_regs = db_prefix() . 'training_registrations';

if ($CI->db->table_exists($table_regs)) {

    // Attendance Mode (Physical/Online)
    if (!$CI->db->field_exists('attendance_mode', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `attendance_mode` VARCHAR(20) DEFAULT 'physical' AFTER `status`;");
    }

    // Waitlist Status
    if (!$CI->db->field_exists('is_waitlist', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `is_waitlist` INT(1) DEFAULT 0 AFTER `attendance_mode`;");
    }

    // Payment Tracking
    if (!$CI->db->field_exists('payment_status', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `payment_status` VARCHAR(20) DEFAULT 'unpaid' AFTER `is_waitlist`;");
    }
    if (!$CI->db->field_exists('invoice_id', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `invoice_id` INT(11) DEFAULT 0 AFTER `payment_status`;");
    }

    // Unique Ticket Codes (Safety check: Generate if empty for existing rows?)
    if (!$CI->db->field_exists('unique_ticket_code', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `unique_ticket_code` VARCHAR(50) NOT NULL AFTER `registration_source`;");
    }
    if (!$CI->db->field_exists('referral_code_generated', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `referral_code_generated` VARCHAR(50) NULL AFTER `unique_ticket_code`;");
    }

    // Certification Progress
    if (!$CI->db->field_exists('quiz_passed', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `quiz_passed` INT(1) DEFAULT 0 AFTER `invoice_id`;");
    }
    if (!$CI->db->field_exists('feedback_submitted', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `feedback_submitted` INT(1) DEFAULT 0 AFTER `quiz_passed`;");
    }
    if (!$CI->db->field_exists('certificate_sent', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `certificate_sent` INT(1) DEFAULT 0 AFTER `feedback_submitted`;");
    }
    
    // Attendance Date Timestamp
    if (!$CI->db->field_exists('attendance_date', $table_regs)) {
        $CI->db->query("ALTER TABLE `" . $table_regs . "` ADD `attendance_date` DATETIME NULL AFTER `certificate_sent`;");
    }
}

// 3. CREATE NEW TABLES IF MISSING (For older versions that might lack them entirely)

// Quiz Questions
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

// Feedback
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

// Expenses
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

// Media
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