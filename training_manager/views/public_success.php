<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Status - <?php echo $training->subject; ?></title>
    <link href="<?php echo base_url('assets/css/style.min.css'); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; }
        .success-card { background: white; max-width: 500px; width: 90%; padding: 50px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
        .icon-box { width: 80px; height: 80px; background: #dcfce7; color: #166534; border-radius: 50%; margin: 0 auto 25px; display: flex; align-items: center; justify-content: center; font-size: 40px; }
        .btn-home { display: inline-block; margin-top: 25px; padding: 10px 25px; background: #2563eb; color: white; border-radius: 50px; text-decoration: none; font-weight: bold; transition:0.2s; }
        .btn-home:hover { background: #1d4ed8; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <div class="success-card">
        <?php if($status == 'waitlist'){ ?>
            <div class="icon-box" style="background:#fef3c7; color:#b45309;"><i class="fa fa-clock-o"></i></div>
            <h2 style="margin:0; color:#1e293b; font-weight:800;">Added to Waitlist</h2>
            <p style="color:#64748b; margin-top:15px; font-size:1.1rem;">
                The event <strong><?php echo $training->subject; ?></strong> is currently full. 
                You have been added to our priority waitlist and will be notified if a spot opens up.
            </p>
        <?php } else { ?>
            <div class="icon-box"><i class="fa fa-check"></i></div>
            <h2 style="margin:0; color:#1e293b; font-weight:800;">Registration Confirmed!</h2>
            <p style="color:#64748b; margin-top:15px; font-size:1.1rem;">
                You have successfully registered for:<br>
                <strong style="color:#2563eb;"><?php echo $training->subject; ?></strong>
            </p>
            <p style="color:#94a3b8; font-size:0.9rem;">A confirmation email has been sent to you.</p>
        <?php } ?>
        
        <a href="<?php echo site_url(); ?>" class="btn-home">Return to Home</a>
    </div>
</body>
</html>