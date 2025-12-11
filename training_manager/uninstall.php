<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'trainings`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'training_registrations`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'training_quiz_questions`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'training_feedback`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'training_expenses`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'training_media`');
