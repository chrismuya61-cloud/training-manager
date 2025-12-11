<?php
/**
 * Module Name: Training Manager Enterprise
 * Description: Complete Training Management System with Group Booking, LMS, and Financials.
 * Version: 1.1.0
 * Requires at least: 2.3.*
 */
defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('admin_init', 'training_manager_menu');
hooks()->add_action('admin_init', 'training_manager_permissions');
hooks()->add_action('before_cron_run', 'training_manager_reminders');
hooks()->add_action('after_payment_added', 'training_manager_payment_recorded'); // CRITICAL: Payment Webhook

function training_manager_menu() {
    $CI = &get_instance();
    if (has_permission('training_manager', '', 'view') || has_permission('training_manager', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('training_manager', ['name' => 'Training Manager', 'icon' => 'fa fa-graduation-cap', 'position' => 30,]);
        $CI->app_menu->add_sidebar_menu_item('training_manager_events', ['name' => 'Events & Classes', 'icon' => 'fa fa-calendar', 'href' => admin_url('training_manager'), 'parent' => 'training_manager', 'position' => 1,]);
        $CI->app_menu->add_sidebar_menu_item('training_manager_dashboard', ['name' => 'Dashboard & Reports', 'icon' => 'fa fa-bar-chart', 'href' => admin_url('training_manager/dashboard'), 'parent' => 'training_manager', 'position' => 2,]);
        $CI->app_menu->add_sidebar_menu_item('training_manager_calendar', ['name' => 'Calendar', 'icon' => 'fa fa-calendar-alt', 'href' => admin_url('training_manager/calendar'), 'parent' => 'training_manager', 'position' => 3,]);
    }
}

function training_manager_permissions() {
    register_staff_capabilities('training_manager', ['capabilities' => [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'view_own' => _l('permission_view_own'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete')
    ]], 'Training Manager');
}

function training_manager_reminders() {
    $CI = &get_instance();
    $CI->load->library('email');
    $trainings = $CI->db->where('start_date', date('Y-m-d', strtotime('+1 day')))->where('is_active', 1)->get(db_prefix().'trainings')->result();
    foreach($trainings as $t) {
        $attendees = $CI->db->where('training_id', $t->id)->get(db_prefix().'training_registrations')->result();
        foreach($attendees as $att) {
            $CI->email->clear();
            $CI->email->from(get_option('smtp_email'), get_option('companyname'));
            $CI->email->to($att->email);
            $CI->email->subject('Reminder: Training Tomorrow - ' . $t->subject);
            $loc = !empty($t->meeting_url) ? "Online Link: " . $t->meeting_url : "Venue: " . $t->venue;
            $msg = "Hello " . $att->name . ",<br><br>This is a reminder that <strong>" . $t->subject . "</strong> starts tomorrow.<br><br>" . $loc . "<br><br>We look forward to seeing you!<br><br>Regards,<br>" . get_option('companyname');
            $CI->email->message($msg);
            $CI->email->send();
        }
    }
}

function training_manager_payment_recorded($payment_id) {
    $CI = &get_instance();
    $CI->load->model('payments_model');
    $payment = $CI->payments_model->get($payment_id);
    if(!$payment) return;
    
    // CRITICAL FIX: Marks ALL registrations linked to the paid invoice as "Paid"
    $CI->db->where('invoice_id', $payment->invoiceid)->update(db_prefix().'training_registrations', ['payment_status' => 'paid']);
}
