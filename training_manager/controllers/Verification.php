<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Verification extends ClientsController
{
    public function index($code)
    {
        $this->load->model('training_manager/training_model');
        $reg = $this->db->where('unique_ticket_code', $code)->get(db_prefix().'training_registrations')->row();
        $data = ['valid' => false, 'code' => $code];
        
        // Validation check: must exist, must be checked-in (status 1), and quiz must be passed
        if($reg && $reg->status == 1 && $reg->quiz_passed == 1) {
            $data['valid'] = true;
            $data['reg'] = $reg;
            $data['training'] = $this->db->where('id', $reg->training_id)->get(db_prefix().'trainings')->row();
        }
        $this->load->view('training_manager/verification', $data);
    }
}
