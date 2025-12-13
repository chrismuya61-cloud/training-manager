<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Training_manager extends AdminController
{
    public function __construct() {
        parent::__construct();
        if (!$this->db->table_exists(db_prefix() . 'trainings')) require_once(module_dir_path('training_manager', 'install.php'));
        $this->load->model('training_manager/training_model');
        $this->load->model('staff_model');
        $this->load->model('invoices_model');
    }

    // --- 1. MAIN LIST & LEADERBOARD ---
    public function index() {
        $has_global = has_permission('training_manager', '', 'view');
        $has_own = has_permission('training_manager', '', 'view_own');
        if (!$has_global && !$has_own) access_denied('Training Manager');

        $all_staff = $this->staff_model->get('', ['active'=>1]); 
        $leaderboard = [];

        foreach($all_staff as $staff) {
            $s_events = $this->db->where('assigned_staff_id', $staff['staffid'])->get(db_prefix().'trainings')->result_array();
            if(!empty($s_events)){
                $e_ids = array_column($s_events, 'id');
                $atts = $this->db->where_in('training_id', $e_ids)->where('status', 1)->count_all_results(db_prefix().'training_registrations');
                $query = "SELECT AVG(rating) as r FROM ".db_prefix()."training_feedback JOIN ".db_prefix()."training_registrations ON ".db_prefix()."training_registrations.id=".db_prefix()."training_feedback.registration_id WHERE training_id IN (".implode(',',$e_ids).")";
                $rating = $this->db->query($query)->row()->r ?? 0;
                $score = $atts + ($rating*10); 
                if($score > 0) $leaderboard[] = ['name' => $staff['firstname'], 'image' => staff_profile_image($staff['staffid'], ['staff-profile-image-small']), 'attendees' => $atts, 'rating' => round($rating,1), 'badge' => ($score > 100 ? 'gold' : ($score > 50 ? 'silver' : 'bronze'))];
            }
        }
        usort($leaderboard, function($a,$b){ return $b['attendees'] <=> $a['attendees']; });
        $data['leaderboard'] = $leaderboard;

        if($has_global) $data['trainings'] = $this->training_model->get_all();
        else $data['trainings'] = $this->db->where('assigned_staff_id', get_staff_user_id())->get(db_prefix().'trainings')->result_array();
        
        $data['title'] = 'Training Events';
        $this->load->view('manage', $data);
    }

    // --- 2. DASHBOARD ---
    public function dashboard() {
        if (!has_permission('training_manager', '', 'view')) access_denied();
        $trainings = $this->training_model->get_all();
        $total_rev = 0; $total_exp = 0; $total_att = 0; $report = [];
        foreach($trainings as $t) {
            $att_count = $this->db->where('training_id', $t['id'])->count_all_results(db_prefix().'training_registrations');
            $rev = $t['price'] * $att_count;
            $exp = $this->db->select_sum('amount')->where('training_id', $t['id'])->get(db_prefix().'training_expenses')->row()->amount ?? 0;
            $total_rev += $rev; $total_exp += $exp; $total_att += $att_count;
            $report[] = ['id'=>$t['id'], 'subject'=>$t['subject'], 'start_date'=>$t['start_date'], 'attendee_count'=>$att_count, 'revenue'=>$rev, 'expense'=>$exp];
        }
        $roi = ($total_exp > 0) ? round((($total_rev - $total_exp) / $total_exp) * 100) : 0;
        $data = ['total_revenue'=>$total_rev, 'total_expenses'=>$total_exp, 'roi'=>$roi, 'total_events'=>count($trainings), 'total_attendees'=>$total_att, 'events_report'=>$report, 'currency'=>get_base_currency()];
        $this->load->view('dashboard', $data);
    }

    public function export_dashboard_report() {
        $trainings = $this->training_model->get_all();
        header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="Training_Report.csv"');
        $fp = fopen('php://output', 'w'); fputcsv($fp, ['Event', 'Date', 'Attendees', 'Revenue', 'Expenses', 'Profit']);
        foreach($trainings as $t) {
            $att = $this->db->where('training_id', $t['id'])->count_all_results(db_prefix().'training_registrations');
            $rev = $t['price'] * $att;
            $exp = $this->db->select_sum('amount')->where('training_id', $t['id'])->get(db_prefix().'training_expenses')->row()->amount ?? 0;
            fputcsv($fp, [$t['subject'], _d($t['start_date']), $att, $rev, $exp, $rev-$exp]);
        }
        fclose($fp); exit;
    }

    // --- 3. CRUD ---
    public function event($id = '') {
        if ($this->input->post()) {
            if ($id == '') { 
                if(!has_permission('training_manager','','create')) access_denied(); 
                $id = $this->training_model->add($this->input->post()); 
                set_alert('success','Created Successfully'); 
            } else { 
                if(!has_permission('training_manager','','edit')) access_denied(); 
                $this->training_model->update($this->input->post(), $id); 
                set_alert('success','Updated Successfully'); 
            }
            redirect(admin_url('training_manager/event/' . $id));
        }
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        if ($id) {
            $data['event'] = $this->training_model->get($id);
            $data['attendees'] = $this->training_model->get_attendees($id);
            $data['expenses'] = $this->db->where('training_id', $id)->get(db_prefix().'training_expenses')->result();
            $data['questions'] = $this->db->where('training_id', $id)->get(db_prefix().'training_quiz_questions')->result();
            $data['media'] = $this->db->where('training_id', $id)->get(db_prefix().'training_media')->result();
            $data['title'] = $data['event']->subject;
        } else { $data['title'] = 'Create New Training'; }
        $this->load->view('event', $data);
    }

    // --- 4. ACTIONS & STATUS ---
    public function mark_status($id, $status) {
        if(!has_permission('training_manager','','edit')) access_denied();
        $this->db->where('id', $id)->update(db_prefix().'trainings', ['is_active' => $status]);
        set_alert('success', 'Status Updated'); 
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function sync_expenses($id) {
        if(!has_permission('expenses','','create')) access_denied();
        $expenses = $this->db->where('training_id', $id)->get(db_prefix().'training_expenses')->result();
        
        $cat = $this->db->where('name', 'Training')->get(db_prefix().'expenses_categories')->row();
        if(!$cat) {
            $this->db->insert(db_prefix().'expenses_categories', ['name'=>'Training', 'description'=>'Imported from Training Manager']);
            $cat_id = $this->db->insert_id();
        } else {
            $cat_id = $cat->id;
        }

        $count = 0;
        $this->load->model('expenses_model');
        foreach($expenses as $exp) {
            $exists = $this->db->where('expense_name', $exp->expense_name)->where('date', $exp->date_added)->where('amount', $exp->amount)->get(db_prefix().'expenses')->row();
            if(!$exists) {
                $this->expenses_model->add([
                    'category' => $cat_id, 'amount' => $exp->amount, 'date' => $exp->date_added,
                    'expense_name' => $exp->expense_name, 'note' => $exp->note ?? 'Synced from Training Manager',
                    'currency' => get_base_currency()->id
                ]);
                $count++;
            }
        }
        set_alert('success', "Synced $count Expenses to Core Module"); redirect($_SERVER['HTTP_REFERER']);
    }

    public function sync_to_leads($id) {
        if(!has_permission('leads','','create')) access_denied();
        $attendees = $this->training_model->get_attendees($id); $this->load->model('leads_model');
        $this->db->where('name', 'Training Event'); $src = $this->db->get(db_prefix().'leads_sources')->row(); $sid = $src ? $src->id : 1;
        $count = 0;
        foreach($attendees as $att){
            if(!$this->db->where('email', $att['email'])->get(db_prefix().'leads')->row()){
                $this->leads_model->add([
                    'name'=>$att['name'], 'email'=>$att['email'], 'phonenumber'=>$att['phonenumber'], 
                    'company'=>$att['company'], 'source'=>$sid, 'status'=>1, 
                    'description'=>'Imported from Training Manager', 'address'=>'', 'assigned'=>get_staff_user_id(),
                    'dateadded'=>date('Y-m-d H:i:s'), 'addedfrom'=>get_staff_user_id()
                ]);
                $count++;
            }
        }
        set_alert('success', "Synced $count Leads"); redirect(admin_url('training_manager/event/'.$id));
    }

    // --- 5. ATTENDEE & UTILS ---
    public function add_walkin() {
        if(!has_permission('training_manager','','edit')) access_denied();
        $data = $this->input->post(); $data['status'] = 1; 
        
        $reg_id = $this->training_model->add_walkin($data);
        if($reg_id === false) {
            set_alert('warning', 'This user is already registered for this event.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $t = $this->training_model->get($data['training_id']);
        if($t->price > 0 && !empty($data['email'])) {
            $this->invoices_model->add([
                'clientid'=>0, 'number'=>get_option('next_invoice_number'), 'date'=>date('Y-m-d'), 'duedate'=>date('Y-m-d'), 
                'currency'=>$t->currency, 'subtotal'=>$t->price, 'total'=>$t->price, 'billing_street'=>$data['name'], 
                'newitems'=>[['description'=>'Training Ticket', 'long_description'=>'Registration', 'qty'=>1, 'rate'=>$t->price, 'unit'=>'Ticket', 'order'=>1]]
            ]);
        }
        set_alert('success', 'Walk-in Added'); redirect($_SERVER['HTTP_REFERER']);
    }

    // FIX: NEW FUNCTION TO EDIT ATTENDEE
    public function update_attendee() {
        if(!has_permission('training_manager','','edit')) access_denied();
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']); // Remove ID from update data
        
        $this->db->where('id', $id)->update(db_prefix().'training_registrations', $data);
        set_alert('success', 'Attendee Updated Successfully');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function check_in($id) {
        if(!has_permission('training_manager','','edit')) access_denied();
        $this->db->where('id', $id)->update(db_prefix().'training_registrations', ['status' => 1]);
        set_alert('success', 'Checked In'); redirect($_SERVER['HTTP_REFERER']);
    }

    public function reschedule_attendee($reg_id, $new_id) {
        if(!has_permission('training_manager','','edit')) access_denied();
        if(!$this->training_model->get($new_id)){ set_alert('warning', 'Invalid ID'); redirect($_SERVER['HTTP_REFERER']); }
        $this->db->where('id', $reg_id)->update(db_prefix().'training_registrations', ['training_id'=>$new_id, 'status'=>0, 'quiz_passed'=>0, 'feedback_submitted'=>0, 'certificate_sent'=>0]);
        set_alert('success', 'Rescheduled'); redirect($_SERVER['HTTP_REFERER']);
    }

    public function import_attendees($id) { 
        if(isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''){ 
            $file = $_FILES['file_csv']['tmp_name']; $handle = fopen($file, "r"); $row = 0; $success=0; $dup=0;
            while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){ 
                if($row == 0 && strtolower($data[0]) == 'name') { $row++; continue; }
                if(!empty($data[0]) && !empty($data[1])) {
                    $res = $this->training_model->add_walkin(['training_id'=>$id, 'name'=>$data[0], 'email'=>$data[1], 'phonenumber'=>$data[2]??'', 'company'=>$data[3]??'', 'status'=>0, 'registration_source'=>'import']);
                    if($res) $success++; else $dup++;
                }
                $row++;
            } 
            fclose($handle); set_alert('success', "Imported $success attendees. Skipped $dup duplicates.");
        } 
        redirect(admin_url('training_manager/event/'.$id)); 
    }

    public function download_sample_csv() { header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="sample_attendees.csv"'); $f=fopen('php://output','w'); fputcsv($f,['Name','Email','Phone','Company']); fputcsv($f,['John Doe','john@example.com','+1234567890','Acme Corp']); fclose($f); exit; }
    
    public function upload_media($id) { 
        if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != ''){ 
            $path = module_dir_path('training_manager','uploads/'.$id.'/'); if(!is_dir($path)) mkdir($path, 0755, true); 
            $config['upload_path'] = $path; $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx';
            $this->load->library('upload', $config);
            if($this->upload->do_upload('file')) { $f = $this->upload->data(); $this->db->insert(db_prefix().'training_media', ['training_id'=>$id, 'file_name'=>$f['file_name'], 'file_type'=>$f['file_type']]); }
        } 
    }

    public function delete_media($id) { $m = $this->db->where('id',$id)->get(db_prefix().'training_media')->row(); if($m){ unlink(module_dir_path('training_manager','uploads/'.$m->training_id.'/'.$m->file_name)); $this->db->where('id',$id)->delete(db_prefix().'training_media'); } redirect($_SERVER['HTTP_REFERER']); }
    
    public function add_question($id) { 
        $data = $this->input->post(); $data['training_id'] = $id; 
        $this->db->insert(db_prefix().'training_quiz_questions', $data); redirect($_SERVER['HTTP_REFERER']); 
    }
    
    public function delete_question($id) { $this->db->where('id',$id)->delete(db_prefix().'training_quiz_questions'); redirect($_SERVER['HTTP_REFERER']); }
    public function add_expense() { $this->training_model->add_expense($this->input->post()); redirect($_SERVER['HTTP_REFERER']); }
    public function delete_event($id) { $this->training_model->delete($id); redirect(admin_url('training_manager')); }
    public function calendar() { $data['title']='Training Calendar'; $this->load->view('calendar', $data); }
    public function get_calendar_data() { 
        $ts=$this->training_model->get_all(); $ev=[]; 
        foreach($ts as $t){ 
            $ev[]=[
                'title'=>$t['subject'], 
                'start'=>date('c', strtotime($t['start_date'])), 
                'end'=>date('c', strtotime($t['end_date'])), 
                'url'=>admin_url('training_manager/event/'.$t['id']), 
                'color'=>($t['is_active']?'#2563eb':'#94a3b8')
            ]; 
        } 
        header('Content-Type: application/json'); echo json_encode($ev); 
        die();
    }

    public function bulk_email_certificates($id) {
        if (!has_permission('training_manager', '', 'view')) access_denied();
        $t = $this->training_model->get($id); $atts = $this->training_model->get_attendees($id);
        $this->load->library('email'); $s = 0;
        foreach($atts as $a){
            if($a['status']==1 && $a['quiz_passed'] && $a['feedback_submitted']){
                $pdf = $this->generate_certificate_pdf((object)$a, $t);
                $this->email->clear(true); $this->email->from(get_option('smtp_email')); $this->email->to($a['email']);
                $this->email->subject('Certificate: '.$t->subject); $this->email->message('Attached.');
                $this->email->attach($pdf->Output('', 'S'), 'attachment', 'Cert.pdf', 'application/pdf');
                if($this->email->send()) { $this->db->where('id', $a['id'])->update(db_prefix().'training_registrations', ['certificate_sent'=>1]); $s++; }
            }
        }
        set_alert('success', "Sent $s certs"); redirect(admin_url('training_manager/event/'.$id));
    }

    public function print_badges($id) {
        if (!class_exists('TCPDF')) $this->load->library('pdf');
        $atts = $this->training_model->get_attendees($id);
        $pdf = new TCPDF('P', 'mm', 'A4'); $pdf->SetPrintHeader(false); $pdf->SetPrintFooter(false); $pdf->AddPage();
        $bg = module_dir_path('training_manager', 'assets/badge_bg.jpg'); $col = 0; $row = 0;
        foreach($atts as $a){
            $x=15+($col*91); $y=15+($row*59);
            if(file_exists($bg) && filesize($bg)>0 && @getimagesize($bg)) $pdf->Image($bg, $x, $y, 86, 54); else $pdf->Rect($x, $y, 86, 54);
            $pdf->SetXY($x, $y+20); $pdf->SetFont('helvetica', 'B', 13); $pdf->Cell(86, 0, $a['name'], 0, 1, 'C');
            $pdf->write2DBarcode($a['unique_ticket_code'], 'QRCODE,H', $x+65, $y+29, 15, 15);
            $col++; if($col>1){$col=0; $row++;} if($row>4){$pdf->AddPage(); $col=0; $row=0;}
        }
        $pdf->Output('Badges.pdf', 'I');
    }

    private function generate_certificate_pdf($att, $training) {
        if (!class_exists('TCPDF')) { $this->load->library('pdf'); }
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false); $pdf->SetPrintFooter(false); $pdf->SetAutoPageBreak(false); $pdf->AddPage();
        $path = module_dir_path('training_manager', 'assets/');
        if(file_exists($path.'cert_bg.jpg') && @getimagesize($path.'cert_bg.jpg')) $pdf->Image($path.'cert_bg.jpg', 0, 0, 297, 210); else $pdf->Rect(10, 10, 277, 190);
        if(file_exists($path.'logo.png') && @getimagesize($path.'logo.png')) { list($w, $h) = getimagesize($path.'logo.png'); $r = $h/$w; $cw = 70; $pdf->Image($path.'logo.png', (297-$cw)/2, 20, $cw); $pdf->SetY(20+($cw*$r)+5); } else { $pdf->SetY(25); $pdf->SetFont('times', 'B', 24); $pdf->Cell(0, 15, strtoupper(get_option('companyname')), 0, 1, 'C'); }
        $pdf->Ln(5); $pdf->SetFont('times', 'B', 36); $pdf->Cell(0, 15, 'CERTIFICATE OF PARTICIPATION', 0, 1, 'C');
        $pdf->Ln(2); $pdf->SetFont('helvetica', '', 14); $pdf->Cell(0, 10, 'This Acknowledges That', 0, 1, 'C');
        $pdf->Ln(2); $pdf->SetFont('times', 'B', 32); $pdf->SetTextColor(0,51,102); $pdf->Cell(0, 15, strtoupper($att->name), 0, 1, 'C');
        $pdf->SetLineStyle(['width'=>0.5]); $pdf->Line(60, $pdf->GetY()-2, 237, $pdf->GetY()-2);
        $pdf->Ln(5); $pdf->SetFont('helvetica', '', 11); $pdf->SetTextColor(100); $pdf->Cell(0, 10, 'HAS SUCCESSFULLY PARTICIPATED IN', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 15); $pdf->SetTextColor(0); $pdf->MultiCell(200, 10, strtoupper($training->subject), 0, 'C', 0, 1, 48);
        $pdf->Ln(3); $pdf->SetFont('helvetica', 'I', 13); $pdf->Cell(0, 10, 'Held at '.$training->venue.' on '._d($training->start_date), 0, 1, 'C');
        if($training->validity_months > 0){ $exp = date('Y-m-d', strtotime($training->end_date." + ".$training->validity_months." months")); $pdf->Ln(6); $pdf->SetFont('helvetica','B',11); $pdf->SetTextColor(200,0,0); $pdf->Cell(0,10,'Valid Until: '._d($exp),0,1,'C'); $pdf->SetTextColor(0); }
        $pdf->SetY(-55); if(file_exists($path.'signature.png') && @getimagesize($path.'signature.png')) $pdf->Image($path.'signature.png', 128, $pdf->GetY()-15, 40);
        $pdf->Ln(5); $pdf->SetFont('times', 'B', 14); $pdf->Cell(0, 6, 'Muya Kamamia', 0, 1, 'C');
		$pdf->SetFont('helvetica', '', 12); $pdf->Cell(0, 5, 'Licensed Land Surveyor, Kenya', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12); $pdf->Cell(0, 5, 'Chairman, Measurement Systems Ltd', 0, 1, 'C');
        $pdf->SetY(-39); $pdf->SetFont('helvetica', '', 9); $pdf->SetX(38); $pdf->Cell(55, 0, 'Serial: '.$att->unique_ticket_code, 0, 0, 'L');
        $pdf->write2DBarcode(site_url('training_manager/verification/index/'.$att->unique_ticket_code), 'QRCODE,H', 240, 160, 26, 26);
        return $pdf;
    }

    public function download_certificate($reg_id) {
        if(!has_permission('training_manager','','view')) access_denied();
        $att = $this->db->where('id', $reg_id)->get(db_prefix().'training_registrations')->row();
        $pdf = $this->generate_certificate_pdf($att, $this->training_model->get($att->training_id));
        $pdf->Output('Cert.pdf', 'D');
    }
}