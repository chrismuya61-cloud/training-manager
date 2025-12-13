<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6"><h4 class="bold no-margin"><?php echo $title; ?></h4></div>
                            <div class="col-md-6 text-right">
                                <?php if(isset($event)){ ?>
                                    <div class="btn-group">
                                        <?php if($event->is_active == 1){ ?>
                                            <a href="<?php echo admin_url('training_manager/mark_status/'.$event->id.'/0'); ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Close Event</a>
                                        <?php } else { ?>
                                            <a href="<?php echo admin_url('training_manager/mark_status/'.$event->id.'/1'); ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Re-Open Event</a>
                                        <?php } ?>
                                        <a href="<?php echo admin_url('training_manager/sync_expenses/'.$event->id); ?>" class="btn btn-warning btn-sm" onclick="return confirm('Sync all local training expenses to the main Expense module?');"><i class="fa fa-refresh"></i> Sync Expenses</a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <hr>

                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a></li>
                                    <?php if(isset($event)){ ?>
                                        <li role="presentation"><a href="#attendees" aria-controls="attendees" role="tab" data-toggle="tab">Attendees</a></li>
                                        <li role="presentation"><a href="#quiz" aria-controls="quiz" role="tab" data-toggle="tab">Quiz</a></li>
                                        <li role="presentation"><a href="#media" aria-controls="media" role="tab" data-toggle="tab">Media</a></li>
                                        <li role="presentation"><a href="#expenses" aria-controls="expenses" role="tab" data-toggle="tab">Expenses</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content mtop15">
                            <div role="tabpanel" class="tab-pane active" id="details">
                                <?php echo form_open($this->uri->uri_string()); ?>
                                <div class="row">
                                    <div class="col-md-6"><?php echo render_input('subject', 'Subject', $event->subject ?? ''); ?></div>
                                    <div class="col-md-6">
                                         <div class="form-group"><label>Staff in Charge <i class="fa fa-question-circle" data-toggle="tooltip" title="Manager"></i></label>
                                            <select name="assigned_staff_id" class="form-control selectpicker" data-live-search="true"><option value=""></option><?php foreach($staff_members as $staff){ ?><option value="<?php echo $staff['staffid']; ?>" <?php if(isset($event) && $event->assigned_staff_id == $staff['staffid']){ echo 'selected'; } ?>><?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?></option><?php } ?></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3"><?php echo render_datetime_input('start_date', 'Start', _dt($event->start_date ?? '')); ?></div>
                                    <div class="col-md-3"><?php echo render_datetime_input('end_date', 'End', _dt($event->end_date ?? '')); ?></div>
                                    <div class="col-md-6"><div class="row"><div class="col-md-6"><?php echo render_input('venue', 'Venue (Physical)', $event->venue ?? ''); ?></div><div class="col-md-6"><?php echo render_input('meeting_url', 'Online Link <i class="fa fa-question-circle" data-toggle="tooltip" title="Zoom/Teams URL"></i>', $event->meeting_url ?? '', 'url'); ?></div></div></div>
                                    <div class="col-md-3"><?php echo render_input('price', 'Price', $event->price ?? ''); ?></div>
                                    <div class="col-md-3"><?php echo render_input('capacity', 'Capacity <i class="fa fa-question-circle" data-toggle="tooltip" title="Triggers Waitlist"></i>', $event->capacity ?? '50', 'number'); ?></div>
                                    <div class="col-md-3"><?php echo render_input('validity_months', 'Validity (Months) <i class="fa fa-question-circle" data-toggle="tooltip" title="0 = No Expiry"></i>', $event->validity_months ?? '0', 'number'); ?></div>
                                    <div class="col-md-12"><p class="bold mtop15">Description</p><?php echo render_textarea('description', '', $event->description ?? '', [], [], '', 'tinymce'); ?></div>
                                    <div class="col-md-12"><p class="bold mtop15">Confirmation Email Msg</p><?php echo render_textarea('confirmation_email', '', $event->confirmation_email ?? ''); ?></div>
                                    
                                    <div class="col-md-12 mtop15" style="background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 4px;">
                                        <h5 class="bold text-info"><i class="fa fa-cogs"></i> Event Settings & Status</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label clearfix">Status</label>
                                                    <div class="radio radio-primary radio-inline">
                                                        <input type="radio" id="status_active" name="is_active" value="1" <?php if(!isset($event) || (isset($event) && $event->is_active == 1)) echo 'checked'; ?>>
                                                        <label for="status_active">Active (Open)</label>
                                                    </div>
                                                    <div class="radio radio-danger radio-inline">
                                                        <input type="radio" id="status_closed" name="is_active" value="0" <?php if(isset($event) && $event->is_active == 0) echo 'checked'; ?>>
                                                        <label for="status_closed">Closed</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="checkbox checkbox-primary mtop10">
                                                    <input type="checkbox" name="require_quiz" value="1" <?php if(isset($event)&&$event->require_quiz==1){echo 'checked';} ?>>
                                                    <label>Require Quiz?</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="checkbox checkbox-primary mtop10">
                                                    <input type="checkbox" name="require_feedback" value="1" <?php if(isset($event)&&$event->require_feedback==1){echo 'checked';} ?>>
                                                    <label>Require Feedback?</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="checkbox checkbox-primary mtop10">
                                                    <input type="checkbox" name="enable_waitlist" value="1" <?php if(isset($event)&&$event->enable_waitlist==1){echo 'checked';} ?>>
                                                    <label>Enable Waitlist?</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr><button type="submit" class="btn btn-primary pull-right">Save Changes</button><?php echo form_close(); ?>
                            </div>

                            <?php if(isset($event)){ ?>
                            <div role="tabpanel" class="tab-pane" id="attendees">
                                <div class="row"><div class="col-md-6"><div class="input-group"><span class="input-group-addon bg-info text-white">Registration Link</span><input type="text" class="form-control" value="<?php echo site_url('training_manager/client/register/'.$event->id); ?>" id="regLink" readonly><span class="input-group-btn"><button class="btn btn-default" onclick="copyLink()">Copy</button></span></div></div><div class="col-md-6 text-right"><a href="<?php echo admin_url('training_manager/sync_to_leads/'.$event->id); ?>" class="btn btn-primary btn-sm">Sync Leads</a> <a href="<?php echo admin_url('training_manager/bulk_email_certificates/'.$event->id); ?>" class="btn btn-warning btn-sm" onclick="return confirm('Send to all qualified attendees?');">Email Certs</a> <a href="<?php echo admin_url('training_manager/print_badges/'.$event->id); ?>" target="_blank" class="btn btn-default btn-sm">Badges</a> <a href="#" onclick="$('#import_form').toggle();" class="btn btn-info btn-sm">Import</a></div></div>
                                <div id="import_form" style="display:none;margin-top:10px;padding:10px;border:1px dashed #ccc;"><?php echo form_open_multipart(admin_url('training_manager/import_attendees/'.$event->id)); ?><input type="file" name="file_csv" required><button class="btn btn-success btn-sm mtop5">Upload</button><?php echo form_close(); ?></div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table dt-table">
                                        <thead><th>Name</th><th>Email</th><th>Status</th><th>Ticket / Portal</th><th>Actions</th></thead>
                                        <tbody>
                                        <?php foreach($attendees as $a){ 
                                            $portal_link = site_url('training_manager/client/portal/'.$a['unique_ticket_code']);
                                        ?>
                                            <tr>
                                                <td><?php echo $a['name']; ?></td>
                                                <td><?php echo $a['email']; ?></td>
                                                <td>
                                                    <?php 
                                                        if($a['status']==1) echo '<span class="label label-success">ATTENDED</span>'; 
                                                        elseif($a['is_waitlist']==1) echo '<span class="label label-warning">WAITLIST</span>';
                                                        else echo '<span class="label label-info">REGISTERED</span>'; 
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-xs" style="width:150px;">
                                                        <input type="text" class="form-control" value="<?php echo $portal_link; ?>" id="p_<?php echo $a['id']; ?>" readonly>
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default" onclick="copyPortal('p_<?php echo $a['id']; ?>')" title="Copy Student Portal Link"><i class="fa fa-copy"></i></button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-right">
                                                    <a href="#" onclick="edit_attendee(<?php echo $a['id']; ?>, '<?php echo addslashes($a['name']); ?>', '<?php echo $a['email']; ?>', '<?php echo $a['phonenumber']; ?>')" class="btn btn-default btn-xs mright5" title="Edit Details"><i class="fa fa-pencil"></i></a>

                                                    <?php if($a['status'] == 0){ ?>
                                                        <a href="<?php echo admin_url('training_manager/check_in/'.$a['id']); ?>" class="btn btn-success btn-xs mright5">Check In</a>
                                                        <a href="#" onclick="reschedule(<?php echo $a['id']; ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="Reschedule"><i class="fa fa-calendar"></i></a>
                                                    <?php } ?>
                                                    
                                                    <?php if($a['status'] == 1){ ?>
                                                        <a href="<?php echo admin_url('training_manager/download_certificate/'.$a['id']); ?>" class="btn btn-info btn-xs"><i class="fa fa-download"></i> Certificate</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr><h4>Add Walk-in</h4><?php echo form_open(admin_url('training_manager/add_walkin')); ?><input type="hidden" name="training_id" value="<?php echo $event->id; ?>"><div class="row"><div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div><div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div><div class="col-md-3"><input type="text" name="phonenumber" class="form-control" placeholder="Phone"></div><div class="col-md-3"><button class="btn btn-primary btn-block">Add</button></div></div><?php echo form_close(); ?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="quiz"><div class="row"><div class="col-md-4"><div class="panel_s" style="border:1px solid #e2e8f0; padding:15px;"><h4 class="bold">Add Question</h4><?php echo form_open(admin_url('training_manager/add_question/'.$event->id)); ?><?php echo render_textarea('question', 'Question', '', ['rows'=>2,'required'=>'true']); ?><input type="text" name="option_a" class="form-control mbot10" placeholder="A" required><input type="text" name="option_b" class="form-control mbot10" placeholder="B" required><input type="text" name="option_c" class="form-control mbot10" placeholder="C" required><select name="correct_option" class="form-control mbot15"><option value="A">A</option><option value="B">B</option><option value="C">C</option></select><button class="btn btn-primary btn-block">Save</button><?php echo form_close(); ?></div></div><div class="col-md-8"><table class="table dt-table"><thead><th>Q</th><th>Ans</th><th>Del</th></thead><tbody><?php foreach($questions as $q){ ?><tr><td><?php echo $q->question; ?></td><td><span class="label label-success"><?php echo $q->correct_option; ?></span></td><td><a href="<?php echo admin_url('training_manager/delete_question/'.$q->id); ?>" class="text-danger _delete">X</a></td></tr><?php } ?></tbody></table></div></div></div>

                            <div role="tabpanel" class="tab-pane" id="media">
                                <h4 class="bold">Files</h4>
                                <?php echo form_open_multipart(admin_url('training_manager/upload_media/'.$event->id), ['class'=>'dropzone', 'id'=>'media-upload']); echo form_close(); ?>
                                <div class="row mtop15">
                                    <?php foreach($media as $m){ 
                                        $url = base_url('modules/training_manager/uploads/'.$event->id.'/'.$m->file_name);
                                        $is_img = (strpos($m->file_type, 'image') !== false);
                                    ?>
                                        <div class="col-md-3 text-center mbot15">
                                            <div style="border:1px solid #eee; padding:10px; border-radius:4px; height:180px; position:relative;">
                                                <div style="height:100px; display:flex; align-items:center; justify-content:center; overflow:hidden; margin-bottom:10px;">
                                                    <?php if($is_img){ ?>
                                                        <a href="<?php echo $url; ?>" target="_blank">
                                                            <img src="<?php echo $url; ?>" class="img-responsive" style="max-height:100px;">
                                                        </a>
                                                    <?php } else { ?>
                                                        <i class="fa fa-file-text-o fa-4x text-muted"></i>
                                                    <?php } ?>
                                                </div>
                                                <p class="text-muted small" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo $m->file_name; ?>"><?php echo $m->file_name; ?></p>
                                                <a href="<?php echo admin_url('training_manager/delete_media/'.$m->id); ?>" class="text-danger _delete">Delete</a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="expenses"><?php echo form_open(admin_url('training_manager/add_expense')); ?><input type="hidden" name="training_id" value="<?php echo $event->id; ?>"><div class="row"><div class="col-md-4"><?php echo render_input('expense_name', 'Name'); ?></div><div class="col-md-3"><?php echo render_input('amount', 'Amount', '', 'number'); ?></div><div class="col-md-3"><?php echo render_date_input('date_added', 'Date'); ?></div><div class="col-md-2"><button class="btn btn-primary mtop25">Add</button></div></div><?php echo form_close(); ?><hr><table class="table dt-table"><thead><th>Name</th><th>Date</th><th>Amount</th></thead><tbody><?php foreach($expenses as $ex){ ?><tr><td><?php echo $ex->expense_name; ?></td><td><?php echo _d($ex->date_added); ?></td><td><?php echo app_format_money($ex->amount, $event->currency); ?></td></tr><?php } ?></tbody></table></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editAttendeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Edit Attendee Details</h4>
            </div>
            <?php echo form_open(admin_url('training_manager/update_attendee')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label class="control-label">Full Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Email Address</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Phone Number</label>
                    <input type="text" name="phonenumber" id="edit_phone" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    function copyLink() { var c = document.getElementById("regLink"); c.select(); document.execCommand("copy"); alert("Registration Link Copied!"); }
    function copyPortal(id) { var c = document.getElementById(id); c.select(); document.execCommand("copy"); alert("Student Portal Link Copied!"); }
    function reschedule(id) { var n = prompt("New Event ID:"); if(n) window.location.href = "<?php echo admin_url('training_manager/reschedule_attendee/'); ?>"+id+"/"+n; }
    
    // Populate Modal
    function edit_attendee(id, name, email, phone) {
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#editAttendeeModal').modal('show');
    }
</script>