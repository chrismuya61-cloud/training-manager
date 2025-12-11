<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - <?php echo $training->subject; ?></title>
    <link href="<?php echo base_url('assets/css/style.min.css'); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .register-card { background: white; max-width: 750px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        .card-header { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); padding: 35px; color: white; text-align: center; }
        .card-body { padding: 40px; }
        .form-control { margin-bottom: 15px; background: #f8fafc; border: 1px solid #cbd5e1; height: 45px; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn-register { background: #2563eb; color: white; width: 100%; padding: 14px; border-radius: 8px; border: none; font-weight: bold; margin-top: 15px; transition: 0.2s; font-size: 16px; }
        .btn-register:hover { background: #1d4ed8; }
        .reg-type-selector { display: flex; background: #e2e8f0; padding: 5px; border-radius: 10px; margin-bottom: 25px; }
        .type-option { flex: 1; text-align: center; padding: 12px; cursor: pointer; border-radius: 8px; font-weight: 600; color: #64748b; transition: 0.2s; }
        .type-option.active { background: #fff; color: #2563eb; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .attendee-row { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 15px; position: relative; }
        .remove-row { position: absolute; top: 10px; right: 15px; color: #ef4444; cursor: pointer; font-size: 1.2rem; font-weight: bold; }
        .add-btn { background: #eff6ff; color: #2563eb; border: 2px dashed #bfdbfe; width: 100%; padding: 12px; border-radius: 10px; font-weight: 600; margin-top: 10px; transition: 0.2s; }
        .add-btn:hover { background: #dbeafe; border-color: #2563eb; }
    </style>
</head>
<body>
<div class="register-card">
    <div class="card-header">
        <?php echo get_company_logo(base_url('uploads/company/'), 'img-responsive', style:'max-height:40px; filter: brightness(0) invert(1); margin-bottom:15px;'); ?>
        <h2 style="margin:0; font-weight:800;"><?php echo $training->subject; ?></h2>
        <p style="opacity:0.9; margin-top:8px; font-size: 0.95rem;">
            <i class="fa fa-calendar-alt"></i> <?php echo _dt($training->start_date); ?> &nbsp;|&nbsp; <i class="fa fa-map-marker-alt"></i> <?php echo $training->venue; ?>
        </p>
    </div>
    <div class="card-body">
        <?php if(!empty($training->description)){ ?>
            <div style="background: #eff6ff; border-left: 4px solid #2563eb; padding: 15px 20px; margin-bottom: 30px; border-radius: 0 8px 8px 0; color: #334155; line-height: 1.6;">
                <?php echo nl2br($training->description); ?>
            </div>
        <?php } ?>

        <div class="reg-type-selector">
            <div class="type-option active" id="opt-ind" onclick="switchType('individual')"><i class="fa fa-user"></i> Individual Registration</div>
            <div class="type-option" id="opt-grp" onclick="switchType('group')"><i class="fa fa-users"></i> Group / Team Booking</div>
        </div>

        <?php echo form_open('training_manager/client/public_submit', ['id'=>'regForm']); ?>
            <input type="hidden" name="training_id" value="<?php echo $training->id; ?>">
            <input type="hidden" name="registration_type" id="registration_type" value="individual">

            <div id="individual-section">
                <div class="row">
                    <div class="col-md-6"><label>Full Name <span class="text-danger">*</span></label><input type="text" name="ind_name" class="form-control" placeholder="John Doe"></div>
                    <div class="col-md-6"><label>Phone Number <span class="text-danger">*</span></label><input type="text" name="ind_phone" class="form-control" placeholder="+254..."></div>
                </div>
                <label>Email Address <span class="text-danger">*</span></label><input type="email" name="ind_email" class="form-control" placeholder="john@example.com">
                <label>Company / Organization</label><input type="text" name="ind_company" class="form-control" placeholder="Acme Corp">
            </div>

            <div id="group-section" style="display:none;">
                <div style="background: #fff7ed; border:1px solid #fed7aa; padding:20px; border-radius:12px; margin-bottom:25px;">
                    <h5 style="margin-top:0; font-weight:700; color:#9a3412; margin-bottom:15px;">Billing Contact</h5>
                    <div class="row">
                        <div class="col-md-6"><label>Company Name <span class="text-danger">*</span></label><input type="text" name="group_company" class="form-control" placeholder="Company Name"></div>
                        <div class="col-md-6"><label>Contact Email <span class="text-danger">*</span></label><input type="email" name="group_email" class="form-control" placeholder="billing@company.com"></div>
                    </div>
                </div>
                <h5 style="font-weight:700; color:#1e293b; margin-bottom:15px;">Attendees List</h5>
                <div id="attendees-container">
                    <div class="attendee-row">
                        <div class="row">
                            <div class="col-md-4"><small class="text-muted">Name</small><input type="text" name="attendees[0][name]" class="form-control m-0" required></div>
                            <div class="col-md-4"><small class="text-muted">Email</small><input type="email" name="attendees[0][email]" class="form-control m-0" required></div>
                            <div class="col-md-4"><small class="text-muted">Phone</small><input type="text" name="attendees[0][phone]" class="form-control m-0"></div>
                        </div>
                    </div>
                </div>
                <button type="button" class="add-btn" onclick="addAttendee()"><i class="fa fa-plus-circle"></i> Add Another Attendee</button>
            </div>

            <?php if(!empty($training->venue) && !empty($training->meeting_url)){ ?>
                <div class="form-group" style="margin-top: 25px; background: #f0fdf4; padding: 15px; border: 1px solid #bbf7d0; border-radius: 8px;">
                    <label style="color:#166534; font-weight:bold;">How will you attend?</label>
                    <select name="attendance_mode" class="form-control m-0">
                        <option value="physical">🏢 Physically at <?php echo $training->venue; ?></option>
                        <option value="online">💻 Online via Zoom/Teams</option>
                    </select>
                </div>
            <?php } else { ?>
                <input type="hidden" name="attendance_mode" value="<?php echo (!empty($training->meeting_url) ? 'online' : 'physical'); ?>">
            <?php } ?>

            <?php if($training->price > 0){ ?>
                <div class="alert alert-info small mt-4" style="margin-top: 25px; background: #e0f2fe; border-color: #bae6fd; color: #0369a1;">
                    <i class="fa fa-tag"></i> <strong>Price per person:</strong> <?php echo app_format_money($training->price, $training->currency); ?>
                    <br><span id="total-note" style="opacity:0.8;">Total amount will be calculated at checkout based on the number of attendees.</span>
                </div>
            <?php } ?>

            <button type="submit" class="btn-register">Complete Registration <i class="fa fa-arrow-right pull-right"></i></button>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    function switchType(type) {
        document.getElementById('registration_type').value = type;
        if(type === 'individual') {
            document.getElementById('individual-section').style.display = 'block'; document.getElementById('group-section').style.display = 'none';
            document.getElementById('opt-ind').classList.add('active'); document.getElementById('opt-grp').classList.remove('active');
            setRequired('ind', true); setRequired('group', false);
        } else {
            document.getElementById('individual-section').style.display = 'none'; document.getElementById('group-section').style.display = 'block';
            document.getElementById('opt-ind').classList.remove('active'); document.getElementById('opt-grp').classList.add('active');
            setRequired('ind', false); setRequired('group', true);
        }
    }
    function setRequired(prefix, isRequired) { 
        document.querySelectorAll(`#${prefix}-section input`).forEach(el => { 
            if (el.name.includes('phone') || el.name.includes('company')) return;
            if(isRequired) el.setAttribute('required', 'required'); 
            else el.removeAttribute('required'); 
        }); 
    }
    let attendeeCount = 1;
    function addAttendee() {
        const div = document.createElement('div'); div.className = 'attendee-row';
        div.innerHTML = `<span class="remove-row" onclick="this.parentElement.remove()">×</span><div class="row"><div class="col-md-4"><small class="text-muted">Name</small><input type="text" name="attendees[${attendeeCount}][name]" class="form-control m-0" required></div><div class="col-md-4"><small class="text-muted">Email</small><input type="email" name="attendees[${attendeeCount}][email]" class="form-control m-0" required></div><div class="col-md-4"><small class="text-muted">Phone</small><input type="text" name="attendees[${attendeeCount}][phone]" class="form-control m-0"></div></div>`;
        document.getElementById('attendees-container').appendChild(div); attendeeCount++;
    }
    document.addEventListener('DOMContentLoaded', () => switchType('individual'));
</script>
</body>
</html>
