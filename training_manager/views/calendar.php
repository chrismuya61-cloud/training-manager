<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div id="calendar"></div>
                
                <!-- CALENDAR LEGEND -->
                <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                    <span class="bold mright15">Legend:</span>
                    <span class="label label-primary mright5" style="background:#2563eb;">&nbsp;&nbsp;</span> Active / Upcoming
                    <span class="label label-default mleft15 mright5" style="background:#94a3b8;">&nbsp;&nbsp;</span> Completed / Closed
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){ 
        var el = document.getElementById('calendar'); 
        var c = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
            events: '<?php echo admin_url('training_manager/get_calendar_data'); ?>',
            height: 'auto'
        }); 
        c.render(); 
    });
</script>
