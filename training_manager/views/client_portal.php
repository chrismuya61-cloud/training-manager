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
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; color: #334155; line-height: 1.6; }
        
        /* Layout Wrapper */
        .main-wrapper { display: flex; justify-content: center; padding: 40px 20px; min-height: 100vh; }
        .portal-card { background: white; max-width: 1200px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.08); display: flex; flex-direction: column; }
        
        /* Header Section */
        .card-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px 20px; color: white; text-align: center; border-bottom: 4px solid var(--primary); }
        .portal-logo img { max-height: 55px; margin-bottom: 15px; filter: brightness(0) invert(1); }
        .hero-title { font-weight: 800; letter-spacing: -0.5px; margin: 0 0 10px; font-size: 2.2rem; }
        .hero-meta { opacity: 0.9; font-size: 1rem; display: flex; justify-content: center; align-items: center; gap: 15px; flex-wrap: wrap; }
        .badge-mode { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); padding: 4px 12px; border-radius: 50px; font-size: 0.8rem; letter-spacing: 0.5px; text-transform: uppercase; }

        /* Body Content */
        .card-body { padding: 40px; }
        
        /* Action Bar (Buttons + Progress) */
        .action-bar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 35px; background: #f8fafc; padding: 20px 25px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-join { background: #f59e0b; color: white; padding: 10px 25px; border-radius: 50px; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2); }
        .btn-join:hover { background: #d97706; color: white; text-decoration: none; transform: translateY(-1px); }
        
        /* Progress Bar */
        .progress-wrapper { min-width: 250px; flex-grow: 1; max-width: 400px; }
        .progress-label { display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 6px; }
        .progress { height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); }
        .progress-bar { height: 100%; background: linear-gradient(90deg, #10b981, #059669); transition: width 0.6s ease; }

        /* Section Cards */
        .section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .section-header { border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px; display: flex; align-items: center; }
        .section-header h4 { margin: 0; font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 0.95rem; letter-spacing: 0.5px; }
        .section-header i { margin-right: 12px; font-size: 1.2rem; color: var(--primary); }

        /* Description Content Fix */
        .description-content { color: #475569; font-size: 1.05rem; line-height: 1.7; text-align: left; }
        .description-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
        .description-content ul { padding-left: 20px; margin-bottom: 15px; }
        .description-content p { margin-bottom: 15px; }

        /* Journey Timeline */
        .journey-wrapper { display: flex; justify-content: space-between; position: relative; margin-top: 10px; }
        .journey-step { text-align: center; flex: 1; position: relative; z-index: 2; padding: 0 5px; }
        .journey-icon { width: 45px; height: 45px; background: white; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 1.1rem; color: #cbd5e1; transition: 0.3s; }
        .journey-step.active .journey-icon { border-color: var(--success); color: var(--success); background: #ecfdf5; transform: scale(1.1); box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
        .journey-line { position: absolute; top: 22px; left: 0; width: 100%; height: 2px; background: #cbd5e1; z-index: 1; }
        .journey-title { font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 2px; }
        .journey-desc { font-size: 0.75rem; color: #64748b; }

        /* Buttons & Forms */
        .btn-modern { width: 100%; padding: 12px; border-radius: 8px; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; transition: 0.2s; border: none; cursor: pointer; display: block; text-align: center; text-decoration: none; }
        .btn-action { background: var(--primary); color: white; }
        .btn-action:hover { background: #1d4ed8; color: white; transform: translateY(-2px); }
        .btn-download { background: #10b981; color: white; }
        .btn-download:hover { background: #059669; color: white; }
        
        .modern-textarea { width: 100%; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 15px; font-size: 0.95rem; color: #334155; transition: 0.2s; resize: vertical; min-height: 100px; }
        .modern-textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }

        /* Star Rating */
        .rating-modern { display: flex; flex-direction: row-reverse; justify-content: center; gap: 8px; margin-bottom: 15px; }
        .rating-modern input { display: none; }
        .rating-modern label { font-size: 2.2rem; color: #e2e8f0; cursor: pointer; transition: 0.2s; }
        .rating-modern input:checked ~ label, .rating-modern label:hover, .rating-modern label:hover ~ label { color: #f59e0b; transform: scale(1.1); }

        /* Quiz */
        .quiz-question-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .answer-card { display: flex; align-items: center; padding: 12px 15px; margin-bottom: 10px; border: 2px solid #e2e8f0; border-radius: 8px; background: #fff; cursor: pointer; transition: 0.2s; position: relative; }
        .answer-card:hover { border-color: #93c5fd; }
        .answer-card input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
        .option-badge { background: #f1f5f9; color: #64748b; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 12px; font-size: 0.8rem; }
        .answer-card input:checked ~ .option-badge { background: var(--primary); color: #fff; }
        .answer-card:has(input:checked) { border-color: var(--primary); background: #eff6ff; }

        /* Media Grid & Lightbox */
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
        .media-item { border: 1px solid #e2e8f0; border-radius: 8px; background: white; transition: 0.2s; overflow: hidden; display: flex; flex-direction: column; height: 100%; }
        .media-item:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .media-thumb { height: 110px; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden; cursor: pointer; border-bottom: 1px solid #f1f5f9; }
        .media-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .media-thumb:hover img { transform: scale(1.05); }
        .media-body { padding: 12px; text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .media-name { font-size: 0.8rem; font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 10px; }

        /* Lightbox */
        .lightbox-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.95); z-index: 9999; align-items: center; justify-content: center; }
        .lightbox-img { max-width: 90%; max-height: 85vh; border-radius: 4px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .lightbox-close { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; cursor: pointer; opacity: 0.8; transition: 0.2s; }
        .lightbox-close:hover { opacity: 1; transform: scale(1.1); }
        .lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); color: white; font-size: 24px; cursor: pointer; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 50%; transition: 0.2s; backdrop-filter: blur(5px); }
        .lightbox-nav:hover { background: rgba(255,255,255,0.25); transform: translateY(-50%) scale(1.1); }
        .nav-prev { left: 20px; } .nav-next { right: 20px; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="portal-card">
        
        <div class="card-header">
            <div class="portal-logo">
                <a href="<?php echo site_url(); ?>">
                    <?php echo get_company_logo(base_url('uploads/company/'), 'img-responsive'); ?>
                </a>
            </div>
            <h1 class="hero-title"><?php echo $training->subject; ?></h1>
            <div class="hero-meta">
                <span><i class="fa fa-user-circle"></i> <?php echo $reg->name; ?></span>
                <span><i class="fa fa-calendar-alt"></i> <?php echo _dt($training->start_date); ?></span>
                <span class="badge-mode"><?php echo strtoupper($reg->attendance_mode); ?></span>
            </div>
        </div>

        <div class="card-body">
            
            <div class="action-bar">
                <div class="action-buttons">
                    <?php if($reg->attendance_mode == 'online' && !empty($training->meeting_url)){ ?>
                        <a href="<?php echo $training->meeting_url; ?>" target="_blank" class="btn-join"><i class="fa fa-video-camera"></i> Join Live Session</a>
                    <?php } elseif(!empty($training->venue)) { ?>
                        <a href="http://maps.google.com/maps?q=<?php echo urlencode($training->venue); ?>" target="_blank" class="btn-join" style="background:#0ea5e9;"><i class="fa fa-map-marker-alt"></i> Get Directions</a>
                    <?php } ?>
                </div>

                <div class="progress-wrapper">
                    <?php 
                        $steps = 1; $done = 1;
                        if($training->require_quiz) { $steps++; if($reg->quiz_passed) $done++; }
                        if($training->require_feedback) { $steps++; if($reg->feedback_submitted) $done++; }
                        $pct = ($done / $steps) * 100;
                    ?>
                    <div class="progress-label"><span>Course Progress</span><span><?php echo round($pct); ?>%</span></div>
                    <div class="progress"><div class="progress-bar" style="width: <?php echo $pct; ?>%;"></div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    
                    <div class="section-box">
                        <div class="section-header"><i class="fa fa-info-circle"></i><h4>Course Overview</h4></div>
                        <div class="description-content">
                            <?php echo $training->description; ?>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-header"><i class="fa fa-road"></i><h4>Your Journey</h4></div>
                        <div class="journey-wrapper">
                            <div class="journey-line"></div>
                            <div class="journey-step <?php echo ($reg->status==1)?'active':''; ?>">
                                <div class="journey-icon"><i class="fa fa-check"></i></div>
                                <div class="journey-title">Check In</div>
                                <div class="journey-desc">Attend</div>
                            </div>
                            <div class="journey-step <?php echo ($reg->quiz_passed==1)?'active':''; ?>">
                                <div class="journey-icon"><i class="fa fa-brain"></i></div>
                                <div class="journey-title">Quiz</div>
                                <div class="journey-desc">Pass > 80%</div>
                            </div>
                            <div class="journey-step <?php echo ($reg->feedback_submitted==1)?'active':''; ?>">
                                <div class="journey-icon"><i class="fa fa-star"></i></div>
                                <div class="journey-title">Feedback</div>
                                <div class="journey-desc">Rate Us</div>
                            </div>
                            <div class="journey-step <?php echo ($reg->quiz_passed && $reg->feedback_submitted && $reg->status==1)?'active':''; ?>">
                                <div class="journey-icon"><i class="fa fa-award"></i></div>
                                <div class="journey-title">Certified</div>
                                <div class="journey-desc">Download</div>
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-header"><i class="fa fa-folder-open"></i><h4>Course Materials</h4></div>
                        <?php if(empty($media)){ ?>
                            <div class="text-center p-4 text-muted bg-light rounded small">No materials uploaded yet.</div>
                        <?php } else { ?>
                            <div class="media-grid">
                                <?php $img_index = 0; foreach($media as $m){ 
                                    $url = base_url('modules/training_manager/uploads/'.$training->id.'/'.$m->file_name);
                                    $is_img = (strpos($m->file_type, 'image') !== false);
                                ?>
                                    <div class="media-item">
                                        <div class="media-thumb" <?php if($is_img){ echo 'onclick="openLightbox('.$img_index.')"'; $img_index++; } ?>>
                                            <?php if($is_img){ ?>
                                                <img src="<?php echo $url; ?>" class="gallery-img" data-src="<?php echo $url; ?>">
                                            <?php } else { ?>
                                                <i class="fa fa-file-pdf-o fa-3x text-muted"></i>
                                            <?php } ?>
                                        </div>
                                        <div class="media-body">
                                            <div class="media-name" title="<?php echo $m->file_name; ?>"><?php echo $m->file_name; ?></div>
                                            <a href="<?php echo $url; ?>" download class="btn btn-xs btn-default btn-block mt-auto">Download</a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    
                    <?php if($training->require_quiz){ ?>
                        <div class="section-box">
                            <div class="section-header"><i class="fa fa-question-circle text-danger"></i><h4>Assessment</h4></div>
                            <?php if($reg->quiz_passed){ ?>
                                <div class="text-center p-4 bg-light rounded border border-success">
                                    <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                                    <h4 class="text-success bold m-0">PASSED!</h4>
                                    <p class="text-muted small">Well done!</p>
                                </div>
                            <?php } else { ?>
                                <div id="quiz-start">
                                    <p class="text-muted small mb-3">Score 80% or higher to pass.</p>
                                    <button class="btn-modern btn-action" onclick="$('#quiz-start').hide();$('#quiz-form').show();">Start Quiz</button>
                                </div>
                                <div id="quiz-form" style="display:none;">
                                    <?php echo form_open('training_manager/client/submit_quiz'); ?>
                                        <input type="hidden" name="registration_id" value="<?php echo $reg->id; ?>">
                                        <?php foreach($questions as $idx => $q){ ?>
                                            <div class="quiz-question-box">
                                                <p class="font-weight-bold mb-2 small text-dark"><?php echo ($idx+1).'. '.$q->question; ?></p>
                                                <label class="answer-card"><input type="radio" name="answers[<?php echo $q->id; ?>]" value="A"> <span class="option-badge">A</span> <?php echo $q->option_a; ?></label>
                                                <label class="answer-card"><input type="radio" name="answers[<?php echo $q->id; ?>]" value="B"> <span class="option-badge">B</span> <?php echo $q->option_b; ?></label>
                                                <label class="answer-card"><input type="radio" name="answers[<?php echo $q->id; ?>]" value="C"> <span class="option-badge">C</span> <?php echo $q->option_c; ?></label>
                                            </div>
                                        <?php } ?>
                                        <button class="btn-modern btn-action mt-3">Submit Answers</button>
                                    <?php echo form_close(); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if($training->require_feedback){ ?>
                        <div class="section-box">
                            <div class="section-header"><i class="fa fa-comment text-warning"></i><h4>Feedback</h4></div>
                            <?php if($reg->feedback_submitted){ ?>
                                <div class="alert alert-success text-center m-0"><i class="fa fa-check"></i> Feedback Submitted</div>
                            <?php } else { ?>
                                <?php echo form_open('training_manager/client/submit_feedback'); ?>
                                    <input type="hidden" name="registration_id" value="<?php echo $reg->id; ?>">
                                    <div class="star-rating">
                                        <input type="radio" id="s5" name="rating" value="5" required><label for="s5">★</label>
                                        <input type="radio" id="s4" name="rating" value="4"><label for="s4">★</label>
                                        <input type="radio" id="s3" name="rating" value="3"><label for="s3">★</label>
                                        <input type="radio" id="s2" name="rating" value="2"><label for="s2">★</label>
                                        <input type="radio" id="s1" name="rating" value="1"><label for="s1">★</label>
                                    </div>
                                    <textarea name="comment" class="modern-textarea mb-3" placeholder="Any comments?" rows="3"></textarea>
                                    <button class="btn-modern btn-action">Submit Feedback</button>
                                <?php echo form_close(); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php 
                        $is_attended = ($reg->status == 1);
                        $quiz_ok = (!$training->require_quiz || $reg->quiz_passed);
                        $feed_ok = (!$training->require_feedback || $reg->feedback_submitted);
                        $locked = !$is_attended || !$quiz_ok || !$feed_ok;
                    ?>
                    <div class="section-box text-center" style="<?php echo $locked ? 'background:#f8fafc;' : 'background:#f0fdf4; border-color:#bbf7d0;'; ?>">
                        <div class="section-header justify-content-center" style="border:none;"><i class="fa fa-certificate text-success"></i><h4>Certificate</h4></div>
                        <?php if($locked){ ?>
                            <i class="fa fa-lock fa-3x text-muted mb-3" style="opacity:0.3"></i>
                            <div class="text-left small bg-white p-3 rounded border">
                                <div class="<?php echo $is_attended?'text-success':'text-danger'; ?>"><i class="fa <?php echo $is_attended?'fa-check':'fa-times'; ?>"></i> Attendance Confirmed</div>
                                <div class="<?php echo $quiz_ok?'text-success':'text-danger'; ?>"><i class="fa <?php echo $quiz_ok?'fa-check':'fa-times'; ?>"></i> Quiz Passed</div>
                                <div class="<?php echo $feed_ok?'text-success':'text-danger'; ?>"><i class="fa <?php echo $feed_ok?'fa-check':'fa-times'; ?>"></i> Feedback Submitted</div>
                            </div>
                        <?php } else { ?>
                            <i class="fa fa-award fa-3x text-warning mb-3"></i>
                            <p class="text-success font-weight-bold">Ready for Download</p>
                            <a href="<?php echo site_url('training_manager/client/download_certificate/'.$reg->unique_ticket_code); ?>" class="btn-modern btn-download"><i class="fa fa-download"></i> Download PDF</a>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="lightbox" class="lightbox-overlay">
    <span class="lightbox-close" onclick="closeLightbox()">×</span>
    <div class="lightbox-nav nav-prev" onclick="changeImg(-1)">❮</div>
    <img id="lightbox-img" class="lightbox-img">
    <div class="lightbox-nav nav-next" onclick="changeImg(1)">❯</div>
</div>

<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script>
    // Lightbox Logic
    let currentImg = 0;
    const images = [];
    document.querySelectorAll('.gallery-img').forEach(img => images.push(img.dataset.src));

    function openLightbox(index) {
        if(images.length === 0) return;
        currentImg = index;
        document.getElementById('lightbox-img').src = images[currentImg];
        document.getElementById('lightbox').style.display = 'flex';
    }
    function closeLightbox() { document.getElementById('lightbox').style.display = 'none'; }
    function changeImg(n) {
        currentImg += n;
        if(currentImg >= images.length) currentImg = 0;
        if(currentImg < 0) currentImg = images.length - 1;
        document.getElementById('lightbox-img').src = images[currentImg];
    }
    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if(document.getElementById('lightbox').style.display === 'flex') {
            if(e.key === 'ArrowLeft') changeImg(-1);
            if(e.key === 'ArrowRight') changeImg(1);
            if(e.key === 'Escape') closeLightbox();
        }
    });
</script>
</body>
</html>