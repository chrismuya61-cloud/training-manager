<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Training_model extends App_Model
{
    public function __construct() { parent::__construct(); }

    public function get($id='') { if(is_numeric($id)){ $this->db->where('id',$id); return $this->db->get(db_prefix().'trainings')->row(); } return $this->get_all(); }
    public function get_all() { $this->db->order_by('start_date','desc'); return $this->db->get(db_prefix().'trainings')->result_array(); }
    public function get_attendees($training_id) { $this->db->where('training_id',$training_id); return $this->db->get(db_prefix().'training_registrations')->result_array(); }

    public function add($data) {
        $data['require_quiz'] = isset($data['require_quiz']) ? 1 : 0;
        $data['require_feedback'] = isset($data['require_feedback']) ? 1 : 0;
        $data['enable_waitlist'] = isset($data['enable_waitlist']) ? 1 : 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        if(isset($data['description'])) $data['description'] = trim($data['description']);
        $this->db->insert(db_prefix().'trainings', $data);
        return $this->db->insert_id();
    }

    public function update($data, $id) {
        $data['require_quiz'] = isset($data['require_quiz']) ? 1 : 0;
        $data['require_feedback'] = isset($data['require_feedback']) ? 1 : 0;
        $data['enable_waitlist'] = isset($data['enable_waitlist']) ? 1 : 0;
        $this->db->where('id', $id);
        return $this->db->update(db_prefix().'trainings', $data);
    }

    public function delete($id) {
        // CRITICAL FIX: Cascading Delete Logic
        $this->db->where('id', $id)->delete(db_prefix().'trainings');
        
        $reg_ids = $this->db->select('id')->where('training_id', $id)->get(db_prefix().'training_registrations')->result_array();
        $this->db->where('training_id', $id)->delete(db_prefix().'training_registrations');
        
        if(!empty($reg_ids)) {
            // Delete Feedback linked by registration ID
            $this->db->where_in('registration_id', array_column($reg_ids, 'id'))->delete(db_prefix() . 'training_feedback');
        }

        // Delete remaining linked records
        $this->db->where('training_id', $id)->delete(db_prefix().'training_expenses');
        $this->db->where('training_id', $id)->delete(db_prefix().'training_quiz_questions');
        $this->db->where('training_id', $id)->delete(db_prefix().'training_media');
        
        return true;
    }

    public function add_walkin($data) {
        $data['unique_ticket_code'] = 'TKT-'.strtoupper(substr(md5(uniqid(rand(),true)),0,8));
        $data['referral_code_generated'] = substr(md5($data['email'].uniqid()),0,8);
        $data['attendance_date'] = date('Y-m-d H:i:s');
        
        if(!isset($data['status'])) $data['status'] = 1;
        if(!isset($data['attendance_mode'])) $data['attendance_mode'] = 'physical';
        if(!isset($data['payment_status'])) $data['payment_status'] = 'unpaid';
        if(!isset($data['is_waitlist'])) $data['is_waitlist'] = 0;
        
        $this->db->insert(db_prefix().'training_registrations', $data);
        return $this->db->insert_id();
    }

    public function add_expense($data) {
        if(empty($data['date_added'])) $data['date_added'] = date('Y-m-d');
        $this->db->insert(db_prefix().'training_expenses', $data);
        return $this->db->insert_id();
    }
}
