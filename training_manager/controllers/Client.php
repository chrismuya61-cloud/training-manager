<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Client extends ClientsController
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('url'); 
        $this->load->model('training_manager/training_model');
    }

    // --- 1. PUBLIC REGISTRATION & SUBMISSION ---
    public function register($id) {
        $data['training'] = $this->db->where('id', $id)->get(db_prefix().'trainings')->row();
        if(!$data['training']) show_404();
        if($data['training']->is_active == 0) { show_error('This training event is currently closed for registration.', 403, 'Registration Closed'); return; }
        $this->load->view('training_manager/public_register', $data);
    }

    public function public_submit() {
        $data = $this->input->post();
        $t = $this->db->where('id', $data['training_id'])->get(db_prefix().'trainings')->row();
        
        if(!$t || $t->is_active == 0) {
            set_alert('danger', 'Registration is closed for this event.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $reg_type = $data['registration_type'];
        $attendees = [];
        $billing_info = '';

        if($reg_type == 'individual') {
            if(!empty(trim($data['ind_name'])) && !empty(trim($data['ind_email']))) {
                $attendees[] = ['name' => trim($data['ind_name']), 'email' => trim($data['ind_email']), 'phone' => $data['ind_phone'], 'company' => $data['ind_company']??''];
                $billing_info = $data['ind_name'] . ' (' . $data['ind_email'] . ')';
            }
        } else {
            if(isset($data['attendees']) && is_array($data['attendees'])){
                foreach($data['attendees'] as $a){
                    if(!empty(trim($a['name'])) && !empty(trim($a['email']))) {
                        $attendees[] = ['name' => trim($a['name']), 'email' => trim($a['email']), 'phone' => $a['phone']??'', 'company' => $data['group_company']];
                    }
                }
            }
            $billing_info = $data['group_company'] . ' - Contact: ' . $data['group_email'];
        }

        if(empty($attendees)) { set_alert('danger','Please provide valid attendee details (Name and Email are required).'); redirect($_SERVER['HTTP_REFERER']); }

        $curr = $this->db->where('training_id', $t->id)->count_all_results(db_prefix().'training_registrations');
        $is_waitlist = ($t->enable_waitlist && ($curr + count($attendees)) > $t->capacity) ? 1 : 0;

        $created_reg_ids = [];
        $duplicates = 0;

        foreach($attendees as $person) {
            $insert_data = [
                'training_id' => $t->id, 'name' => $person['name'], 'email' => $person['email'], 
                'phonenumber' => $person['phone'], 'company' => $person['company'], 'status' => 0, 
                'attendance_mode' => $data['attendance_mode'], 'is_waitlist' => $is_waitlist
            ];
            
            // FIX: Check return value
            $reg_id = $this->training_model->add_walkin($insert_data);
            if($reg_id) {
                $created_reg_ids[] = $reg_id;
            } else {
                $duplicates++;
            }
        }

        if(empty($created_reg_ids) && $duplicates > 0) {
            set_alert('warning', 'All attendees were already registered for this event.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        if($is_waitlist) {
            $this->load->view('training_manager/public_success', ['training'=>$t, 'status'=>'waitlist']);
            return;
        }

        if($t->price > 0 && !empty($created_reg_ids)) {
            $this->load->model('invoices_model');
            $qty = count($created_reg_ids);
            $total_cost = $t->price * $qty;
            
            $new_invoice_data = [
                'clientid' => 0, 'number' => get_option('next_invoice_number'), 'date' => date('Y-m-d'), 'duedate' => date('Y-m-d'),
                'currency' => $t->currency, 'subtotal' => $total_cost, 'total' => $total_cost,
                'billing_street' => $billing_info,
                'show_quantity_as' => 1,
                'newitems' => [[
                    'description' => 'Training: '.$t->subject.' ('.$qty.' Attendees)',
                    'long_description' => 'Attendees: ' . implode(', ', array_column($attendees, 'name')),
                    'qty' => $qty, 'rate' => $t->price, 'unit' => 'Ticket', 'order' => 1
                ]]
            ];

            $invoice_id = $this->invoices_model->add($new_invoice_data);

            if($invoice_id) {
                $this->db->where_in('id', $created_reg_ids)->update(db_prefix().'training_registrations', ['invoice_id' => $invoice_id]);
                $inv = $this->invoices_model->get($invoice_id);
                redirect(site_url('invoice/' . $invoice_id . '/' . $inv->hash));
                return;
            }
        }

        $this->load->view('training_manager/public_success', ['training'=>$t, 'status'=>'confirmed']);
    }

    // --- 2. LMS PORTAL & QUIZ/FEEDBACK ---
    public function portal($ticket_code) {
        $reg = $this->db->where('unique_ticket_code', $ticket_code)->get(db_prefix().'training_registrations')->row();
        if(!$reg) show_404();
        $data['reg'] = $reg;
        $data['training'] = $this->db->where('id', $reg->training_id)->get(db_prefix().'trainings')->row();
        $data['questions'] = $this->db->where('training_id', $reg->training_id)->get(db_prefix().'training_quiz_questions')->result();
        $data['media'] = $this->db->where('training_id', $reg->training_id)->get(db_prefix().'training_media')->result();
        $this->load->view('training_manager/client_portal', $data);
    }

    public function submit_quiz() {
        $reg_id = $this->input->post('registration_id'); $ans = $this->input->post('answers'); 
        if(!$ans) redirect($_SERVER['HTTP_REFERER']);
        $corr = 0; $total = count($ans);
        foreach($ans as $k=>$v){ if($this->db->where('id',$k)->get(db_prefix().'training_quiz_questions')->row()->correct_option == $v) $corr++; }
        $s = ($corr/$total)*100;
        if($s>=80){ $this->db->where('id',$reg_id)->update(db_prefix().'training_registrations',['quiz_passed'=>1]); set_alert('success','Passed!'); }
        else set_alert('danger','Score: '.round($s).'% (Failed)');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function submit_feedback() {
        $this->db->insert(db_prefix().'training_feedback', ['registration_id'=>$this->input->post('registration_id'), 'rating'=>$this->input->post('rating'), 'comment'=>$this->input->post('comment')]);
        $this->db->where('id', $this->input->post('registration_id'))->update(db_prefix().'training_registrations', ['feedback_submitted'=>1]);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function download_certificate($code) {
        $reg = $this->db->where('unique_ticket_code', $code)->get(db_prefix().'training_registrations')->row();
        if(!$reg) show_404();
        
        // FIX: Enforce Attendance Status Check
        if($reg->status != 1) {
            show_error('Certificate Access Denied. You must be marked as "Attended" by the administrator.', 403, 'Access Denied');
            return;
        }

        $t = $this->db->where('id', $reg->training_id)->get(db_prefix().'trainings')->row();
        if(($t->require_quiz && !$reg->quiz_passed) || ($t->require_feedback && !$reg->feedback_submitted)) die('Access Denied: Prerequisites not met.');
        
        if (!class_exists('TCPDF')) $this->load->library('pdf');
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false); $pdf->SetPrintFooter(false); $pdf->SetAutoPageBreak(false); $pdf->AddPage();
        $path = module_dir_path('training_manager', 'assets/');
        if(file_exists($path.'cert_bg.jpg') && @getimagesize($path.'cert_bg.jpg')) $pdf->Image($path.'cert_bg.jpg', 0, 0, 297, 210); else $pdf->Rect(10, 10, 277, 190);
        if(file_exists($path.'logo.png') && @getimagesize($path.'logo.png')) { list($w, $h) = getimagesize($path.'logo.png'); $r = $h/$w; $cw = 60; $pdf->Image($path.'logo.png', (297-$cw)/2, 20, $cw); $pdf->SetY(20+($cw*$r)+5); } else { $pdf->SetY(25); $pdf->SetFont('times', 'B', 24); $pdf->Cell(0, 15, strtoupper(get_option('companyname')), 0, 1, 'C'); }
        $pdf->Ln(5); $pdf->SetFont('times', 'B', 36); $pdf->Cell(0, 15, 'CERTIFICATE OF PARTICIPATION', 0, 1, 'C');
        $pdf->Ln(2); $pdf->SetFont('helvetica', '', 14); $pdf->Cell(0, 10, 'This Acknowledges That', 0, 1, 'C');
        $pdf->Ln(2); $pdf->SetFont('times', 'B', 32); $pdf->SetTextColor(0,51,102); $pdf->Cell(0, 15, strtoupper($reg->name), 0, 1, 'C');
        $pdf->SetLineStyle(['width'=>0.5]); $pdf->Line(60, $pdf->GetY()-2, 237, $pdf->GetY()-2);
        $pdf->Ln(5); $pdf->SetFont('helvetica', '', 11); $pdf->SetTextColor(100); $pdf->Cell(0, 10, 'HAS SUCCESSFULLY PARTICIPATED IN', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 16); $pdf->SetTextColor(0); $pdf->MultiCell(200, 10, strtoupper($t->subject), 0, 'C', 0, 1, 48);
        $pdf->Ln(3); $pdf->SetFont('helvetica', 'I', 13); $pdf->Cell(0, 10, 'Held at '.$t->venue.' on '._d($t->start_date), 0, 1, 'C');
        if($t->validity_months > 0){ $exp = date('Y-m-d', strtotime($t->end_date." + ".$t->validity_months." months")); $pdf->Ln(6); $pdf->SetFont('helvetica','B',11); $pdf->SetTextColor(200,0,0); $pdf->Cell(0,10,'Valid Until: '._d($exp),0,1,'C'); $pdf->SetTextColor(0); }
        $pdf->SetY(-55); if(file_exists($path.'signature.png') && @getimagesize($path.'signature.png')) $pdf->Image($path.'signature.png', 128, $pdf->GetY()-15, 40);
        $pdf->Ln(5); $pdf->SetFont('times', 'B', 14); $pdf->Cell(0, 6, 'Muya Kamamia', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10); $pdf->Cell(0, 5, 'Chairman', 0, 1, 'C');
        $pdf->SetY(-20); $pdf->SetFont('helvetica', '', 9); $pdf->SetX(15); $pdf->Cell(50, 0, 'Serial: '.$reg->unique_ticket_code, 0, 0, 'L');
        $pdf->write2DBarcode(site_url('training_manager/verification/index/'.$reg->unique_ticket_code), 'QRCODE,H', 260, 185, 20, 20);
        $pdf->Output('Certificate.pdf', 'D');
    }
}