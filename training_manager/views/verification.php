<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Certificate Verification</title>
    <link href="<?php echo base_url('assets/css/style.min.css'); ?>" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; }
        .verify-card { background: white; max-width: 450px; width: 90%; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-box { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 40px; }
        .valid { background: #dcfce7; color: #166534; }
        .invalid { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="verify-card">
        <?php if($valid){ ?>
            <div class="icon-box valid">✔</div>
            <h2 class="text-success bold">Valid Certificate</h2>
            <p class="text-muted">Issued to:</p>
            <h3 class="bold"><?php echo $reg->name; ?></h3>
            <hr>
            <p><strong>Course:</strong> <?php echo $training->subject; ?></p>
            <p><strong>Date:</strong> <?php echo _d($training->start_date); ?></p>
            <p><strong>Serial:</strong> <?php echo $code; ?></p>
        <?php } else { ?>
            <div class="icon-box invalid">✘</div>
            <h2 class="text-danger bold">Invalid Certificate</h2>
            <p class="text-muted">Code <strong><?php echo $code; ?></strong> not found.</p>
        <?php } ?>
        <div class="mtop20 text-center">
            <?php echo get_company_logo(base_url('uploads/company/'), 'img-responsive', style:'max-height:30px;'); ?>
        </div>
    </div>
</body>
</html>
