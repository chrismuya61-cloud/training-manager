<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// ======================================================
// 1. TABLE: TRAININGS
// ======================================================
$table_trainings = db_prefix() . 'trainings';

if (!$CI->db->table_exists($table_trainings)) {
    $CI->db->query('CREATE TABLE `' . $table_trainings . '` (
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
} else {
    // UPGRADE: Add missing columns for v3.0.0
    $columns = [
        'meeting_url' => "ADD `meeting_url` TEXT NULL AFTER `venue`",
        'capacity' => "ADD `capacity` INT(11) DEFAULT 50 AFTER `assigned_staff_id`",
        'enable_waitlist' => "ADD `enable_waitlist` INT(1) DEFAULT 1 AFTER `capacity`",
        'validity_months' => "ADD `validity_months` INT(11) DEFAULT 0 AFTER `enable_waitlist`",
        'confirmation_email' => "ADD `confirmation_email` TEXT NULL AFTER `validity_months`",
        'require_quiz' => "ADD `require_quiz` INT(1) DEFAULT 0 AFTER `confirmation_email`",
        'require_feedback' => "ADD `require_feedback` INT(1) DEFAULT 0 AFTER `require_quiz`"
    ];
    
    foreach ($columns as $col => $sql) {
        if (!$CI->db->field_exists($col, $table_trainings)) {
            $CI->db->query("ALTER TABLE `" . $table_trainings . "` " . $sql . ";");
        }
    }
}

// ======================================================
// 2. TABLE: REGISTRATIONS
// ======================================================
$table_regs = db_prefix() . 'training_registrations';

if (!$CI->db->table_exists($table_regs)) {
    $CI->db->query('CREATE TABLE `' . $table_regs . '` (
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
} else {
    // UPGRADE: Add missing columns
    $columns = [
        'attendance_mode' => "ADD `attendance_mode` VARCHAR(20) DEFAULT 'physical' AFTER `status`",
        'is_waitlist' => "ADD `is_waitlist` INT(1) DEFAULT 0 AFTER `attendance_mode`",
        'payment_status' => "ADD `payment_status` VARCHAR(20) DEFAULT 'unpaid' AFTER `is_waitlist`",
        'invoice_id' => "ADD `invoice_id` INT(11) DEFAULT 0 AFTER `payment_status`",
        'unique_ticket_code' => "ADD `unique_ticket_code` VARCHAR(50) NOT NULL DEFAULT 'LEGACY' AFTER `registration_source`",
        'referral_code_generated' => "ADD `referral_code_generated` VARCHAR(50) NULL AFTER `unique_ticket_code`",
        'quiz_passed' => "ADD `quiz_passed` INT(1) DEFAULT 0 AFTER `invoice_id`",
        'feedback_submitted' => "ADD `feedback_submitted` INT(1) DEFAULT 0 AFTER `quiz_passed`",
        'certificate_sent' => "ADD `certificate_sent` INT(1) DEFAULT 0 AFTER `feedback_submitted`",
        'attendance_date' => "ADD `attendance_date` DATETIME NULL AFTER `certificate_sent`"
    ];

    foreach ($columns as $col => $sql) {
        if (!$CI->db->field_exists($col, $table_regs)) {
            $CI->db->query("ALTER TABLE `" . $table_regs . "` " . $sql . ";");
        }
    }
}

// ======================================================
// 3. TABLE: EXPENSES (The source of your error)
// ======================================================
$table_expenses = db_prefix() . 'training_expenses';

if (!$CI->db->table_exists($table_expenses)) {
    $CI->db->query('CREATE TABLE `' . $table_expenses . '` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `training_id` INT(11) NOT NULL,
        `expense_name` VARCHAR(200) NOT NULL,
        `amount` DECIMAL(15,2) NOT NULL,
        `date_added` DATE NOT NULL,
        `note` TEXT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    // FIX: Add missing 'date_added' if table exists but column is missing
    if (!$CI->db->field_exists('date_added', $table_expenses)) {
        $CI->db->query("ALTER TABLE `" . $table_expenses . "` ADD `date_added` DATE NOT NULL AFTER `amount`;");
    }
    if (!$CI->db->field_exists('note', $table_expenses)) {
        $CI->db->query("ALTER TABLE `" . $table_expenses . "` ADD `note` TEXT NULL AFTER `date_added`;");
    }
}

// ======================================================
// 4. TABLE: FEEDBACK
// ======================================================
$table_feedback = db_prefix() . 'training_feedback';

if (!$CI->db->table_exists($table_feedback)) {
    $CI->db->query('CREATE TABLE `' . $table_feedback . '` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `registration_id` INT(11) NOT NULL,
        `rating` INT(1) NOT NULL,
        `comment` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    if (!$CI->db->field_exists('created_at', $table_feedback)) {
        $CI->db->query("ALTER TABLE `" . $table_feedback . "` ADD `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;");
    }
}

// ======================================================
// 5. TABLE: MEDIA
// ======================================================
$table_media = db_prefix() . 'training_media';

if (!$CI->db->table_exists($table_media)) {
    $CI->db->query('CREATE TABLE `' . $table_media . '` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `training_id` INT(11) NOT NULL,
        `file_name` VARCHAR(255) NOT NULL,
        `file_type` VARCHAR(50) NOT NULL,
        `share_token` VARCHAR(100) NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    if (!$CI->db->field_exists('share_token', $table_media)) {
        $CI->db->query("ALTER TABLE `" . $table_media . "` ADD `share_token` VARCHAR(100) NULL AFTER `file_type`;");
    }
}

// ======================================================
// 6. TABLE: QUIZ QUESTIONS
// ======================================================
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