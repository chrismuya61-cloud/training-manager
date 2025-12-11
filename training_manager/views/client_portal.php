<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $training->subject; ?> - Training Portal</title>
    <link href="<?php echo base_url('assets/css/style.min.css'); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --secondary: #475569; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; }
        body { background: #f8fafc; font-family: 'Inter', 'Segoe UI', sans-serif; color: #334155; }
        .hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; padding: 40px 0 80px; clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%); }
        .hero h1 { font-weight: 800; letter-spacing: -1px; margin-bottom: 5px; }
        .hero-meta { opacity: 0.8; font-size: 0.95rem; }
        .portal-logo img { max-height: 50px; margin-bottom: 15px; filter: brightness(0) invert(1); opacity: 0.9; }
        .main-container { max-width: 1100px; margin: -50px auto 50px; padding: 0 20px; position: relative; z-index: 10; }
        .content-card { background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 30px; height: 100%; border: 1px solid #e2e8f0; }
        .section-header { display: flex; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; }
        .section-header i { margin-right: 10px; font-size: 1.2rem; }
        .section-header h4 { margin: 0; font-weight: 700; color: #1e293b; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .modern-textarea { width: 100%; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 15px; font-size: 0.95rem; color: #334155; transition: all 0.2s; resize: vertical; min-height: 100px; }
        .btn-modern { border: none; padding: 12px 25px; border-radius: 50px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.85rem; transition: all 0.2s; cursor: pointer; width: 100%; display: block; }
        .btn-submit { background: #2563eb; color: white; }
        .btn-download { background: #10b981; color: white; }
        .quiz-question-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .answer-card { display: flex; align-items: center; padding: 12px 15px; margin-bottom: 10px; border: 2px solid #e2e8f0; border-radius: 10px; background: #fff; cursor: pointer; width: 100%; }
        .answer-card input { position: absolute; opacity: 0; }
        .option-badge { background: #e2e8f0; color: #64748b; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 12px; }
        .answer-card input:checked ~ .option-badge { background: var(--primary); color: #fff; }
        .answer-card:has(input:checked) { border-color: var(--primary); background: #eff6ff; }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 8px; margin-bottom: 15px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 2.2rem; color: #cbd5e1; cursor: pointer; transition: color 0.2s; }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #f59e0b; }
    </style>
</head>
<body>
<div class="hero">
    <div class="main-container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="portal-logo"><a href="<?php echo site_url(); ?>"><?php echo get_company_logo(base_url('uploads/company/'), 'img-responsive', style:'filter: brightness(0) invert(1);'); ?></a></div>
                <p class="hero-meta text-white-50 mb-1 mt-2">TRAINING PORTAL</p>
                <h1><?php echo $training->subject; ?></h1>
                <p class="hero-meta">
                    <i class="fa fa-user-circle"></i> <?php echo $reg->name; ?>  |  
                    <i class="fa fa-calendar-alt"></i> <?php echo _dt($training->start_date); ?>  | 
                    <span class="badge" style="background: rgba(255,255,255,0.2); border:1px solid #fff;"><?php echo strtoupper($reg->attendance_mode); ?></span>
                </p>

                <?php if(!empty($training->description)){ ?>
                    <div style="background: rgba(255,255,255,0.1); padding: 12px 15px; border-radius: 8px; margin-top: 20px; max-width: 650px; border-left: 3px solid #2563eb;">
                        <p style="margin:0; font-size: 0.95rem; opacity: 0.9;">
                            <i class="fa fa-info-circle"></i> 
                            <?php echo strip_tags(substr($training->description, 0, 120)) . '...'; ?>
                            <a href="#" style="color:#fff; text-decoration:underline; font-weight:bold; margin-left:10px;" onclick="$('#descModal').modal('show'); return false;">View Topics & Details</a>
                        </p>
                    </div>
                <?php } ?>

                <?php if($reg->attendance_mode == 'online' && !empty($training->meeting_url)){ ?>
                    <a href="<?php echo $training->meeting_url; ?>" target="_blank" class="btn btn-warning btn-lg bold mtop20" style="border-radius:50px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);"><i class="fa fa-video-camera"></i> JOIN LIVE SESSION</a>
                <?php } elseif(!empty($training->venue)) { ?>
                    <a href="http://maps.google.com/maps?q=<?php echo urlencode($training->venue); ?>" target="_blank" class="btn btn-info btn-lg bold mtop20" style="border-radius:50px; background: rgba(255,255,255,0.2); border: 1px solid #fff;"><i class="fa fa-map-marker-alt"></i> GET DIRECTIONS</a>
                <?php } ?>

            </div>
            <div class="col-md-4 text-right">
                <?php 
                    $steps = 1; $done = 1;
                    if($training->require_quiz) { $steps++; if($reg->quiz_passed) $done++; }
                    if($training->require_feedback) { $steps++; if($reg->feedback_submitted) $done++; }
                    $pct = ($done / $steps) * 100;
                ?>
                <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2); border-radius: 4px; margin-top: 20px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $pct; ?>%;"></div>
                </div>
                <small class="text-white-50"><?php echo round($pct); ?>% Complete</small>
            </div>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="row">
        
        <div class="col-lg-8 mb-4">
            <!-- LEARNING JOURNEY GUIDE -->
            <div class="content-card mb-4" style="border-left: 4px solid #2563eb;">
                <h5 class="font-weight-bold mb-3" style="color:#1e293b;">Your Learning Journey</h5>
                <div style="display:flex; justify-content:space-between; text-align:center; font-size:0.85rem;">
                    
                    <div style="opacity:<?php echo ($reg->status==1)?'1':'0.5'; ?>">
                        <i class="fa fa-map-marker-alt fa-2x mb-2 <?php echo ($reg->status==1)?'text-success':'text-muted'; ?>"></i>
                        <div style="font-weight:bold;">1. Check In</div>
                        <span class="text-muted">Attend Event</span>
                    </div>
                    
                    <div style="border-top:2px dashed #ccc; flex-grow:1; margin: 15px 10px 0;"></div>

                    <div style="opacity:<?php echo ($reg->quiz_passed==1)?'1':'0.5'; ?>">
                        <i class="fa fa-brain fa-2x mb-2 <?php echo ($reg->quiz_passed==1)?'text-success':'text-muted'; ?>"></i>
                        <div style="font-weight:bold;">2. Assessment</div>
                        <span class="text-muted">Pass Quiz (>80%)</span>
                    </div>

                    <div style="border-top:2px dashed #ccc; flex-grow:1; margin: 15px 10px 0;"></div>

                    <div style="opacity:<?php echo ($reg->feedback_submitted==1)?'1':'0.5'; ?>">
                        <i class="fa fa-star fa-2x mb-2 <?php echo ($reg->feedback_submitted==1)?'text-success':'text-muted'; ?>"></i>
                        <div style="font-weight:bold;">3. Feedback</div>
                        <span class="text-muted">Rate Course</span>
                    </div>

                    <div style="border-top:2px dashed #ccc; flex-grow:1; margin: 15px 10px 0;"></div>

                    <div style="opacity:<?php echo ($reg->quiz_passed && $reg->feedback_submitted)?'1':'0.5'; ?>">
                        <i class="fa fa-certificate fa-2x mb-2 <?php echo ($reg->quiz_passed && $reg->feedback_submitted)?'text-warning':'text-muted'; ?>"></i>
                        <div style="font-weight:bold;">4. Certified</div>
                        <span class="text-muted">Download PDF</span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="section-header">
                    <i class="fa fa-images text-primary"></i>
                    <h4>Resources & Media</h4>
                </div>
                <!-- Gallery and Docs logic... -->
                <?php if(empty($media)){ echo '<div class="text-center p-5 text-muted bg-light rounded">No content available.</div>'; } ?>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- FEEDBACK WIDGET -->
            <?php if($training->require_feedback){ ?>
                <div class="content-card mb-4">
                    <div class="section-header"><i class="fa fa-star text-warning"></i><h4>Feedback</h4></div>
                    <?php if($reg->feedback_submitted){ ?>
                        <div class="alert alert-success text-center m-0">Feedback Submitted</div>
                    <?php } else { ?>
                        <?php echo form_open('training_manager/client/submit_feedback'); ?>
                            <input type="hidden" name="registration_id" value="<?php echo $reg->id; ?>">
                            <div class="text-center mb-3">
                                <div class="star-rating">
                                    <input type="radio" id="s5" name="rating" value="5" required><label for="s5">★</label>
                                    <input type="radio" id="s4" name="rating" value="4"><label for="s4">★</label>
                                    <input type="radio" id="s3" name="rating" value="3"><label for="s3">★</label>
                                    <input type="radio" id="s2" name="rating" value="2"><label for="s2">★</label>
                                    <input type="radio" id="s1" name="rating" value="1"><label for="s1">★</label>
                                </div>
                            </div>
                            <textarea name="comment" class="modern-textarea mb-3" placeholder="Share your experience..."></textarea>
                            <button class="btn-modern btn-submit">Submit Feedback</button>
                        <?php echo form_close(); ?>
                    <?php } ?>
                </div>
            <?php } ?>

            <!-- QUIZ WIDGET -->
            <?php if($training->require_quiz){ ?>
                <div class="content-card mb-4">
                    <div class="section-header"><i class="fa fa-brain text-danger"></i><h4>Assessment</h4></div>
                    <?php if($reg->quiz_passed){ ?>
                        <div class="text-center p-3"><i class="fa fa-medal fa-3x text-warning mb-2"></i><h5 class="text-success font-weight-bold">Passed!</h5></div>
                    <?php } else { ?>
                        <div id="quiz-start-view" class="text-center"><button class="btn-modern btn-submit" onclick="document.getElementById('quiz-start-view').style.display='none'; document.getElementById('quiz-form-view').style.display='block';">Start Quiz</button></div>
                        <div id="quiz-form-view" style="display:none;">
                            <?php echo form_open('training_manager/client/submit_quiz'); ?>
                                <input type="hidden" name="registration_id" value="<?php echo $reg->id; ?>">
                                <?php foreach($questions as $index => $q){ ?>
                                    <div class="quiz-question-box">
                                        <div class="quiz-question-title text-dark"><span class="text-muted mr-2">Q<?php echo $index+1; ?>.</span> <?php echo $q->question; ?></div>
                                        <label class="answer-card"><input type="radio" name="answers[<?php echo $q->id; ?>]" value="A"> <div class="option-badge">A</div> <span><?php echo $q->option_a; ?></span></label>
                                        <label class="answer-card"><input type="radio" name="answers[<?php echo $q->id; ?>]" value="B"> <div class="option-badge">B</div> <span><?php echo $q->option_b; ?></span></label>
                                        <label class="answer-card"><input type="radio" name="answers[<?php echo $q->id; ?>]" value="C"> <div class="option-badge">C</div> <span><?php echo $q->option_c; ?></span></label>
                                    </div>
                                <?php } ?>
                                <button class="btn-modern btn-submit mt-3">Submit Answers</button>
                            <?php echo form_close(); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <!-- CERTIFICATE WIDGET -->
            <?php $locked = ($training->require_quiz && !$reg->quiz_passed) || ($training->require_feedback && !$reg->feedback_submitted); ?>
            <div class="content-card text-center" style="<?php echo $locked ? 'opacity:0.8' : 'border:2px solid #10b981; background:#f0fdf4;'; ?>">
                <div class="section-header justify-content-center" style="border:none;"><i class="fa fa-certificate text-success"></i><h4 style="color:#166534">Certificate</h4></div>
                <?php if($locked){ ?>
                    <i class="fa fa-lock fa-3x text-muted mb-3"></i><p class="text-muted">Locked: Complete all steps above.</p>
                <?php } else { ?>
                    <i class="fa fa-award fa-3x text-warning mb-3"></i><p class="text-success bold">Ready for Download</p>
                    <a href="<?php echo site_url('training_manager/client/download_certificate/'.$reg->unique_ticket_code); ?>" class="btn-modern btn-download"><i class="fa fa-download"></i> Download PDF</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: DESCRIPTION -->
<div class="modal fade text-dark" id="descModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h4 class="modal-title font-weight-bold" style="color:#1e293b;">Training Overview & Topics</h4></div>
            <div class="modal-body" style="padding: 30px; font-size: 1.1rem; line-height: 1.7; color: #334155;"><?php echo $training->description; ?></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
</body>
</html>
