<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div id="calendar-loader" style="text-align:center; padding:20px; color:#64748b;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading Events...
                </div>
                <div id="calendar" style="display:none;"></div>
                
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
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('calendar');
        var loader = document.getElementById('calendar-loader');
        
        var c = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
            events: {
                url: '<?php echo admin_url('training_manager/get_calendar_data'); ?>',
                failure: function() {
                    alert('Error fetching events!');
                }
            },
            height: 'auto',
            loading: function(isLoading) {
                if(isLoading) {
                    loader.style.display = 'block';
                    el.style.display = 'none';
                } else {
                    loader.style.display = 'none';
                    el.style.display = 'block';
                }
            },
            eventDidMount: function(info) {
                // Add Tooltip
                var tooltipTitle = info.event.title;
                if(info.event.extendedProps.venue) {
                    tooltipTitle += "\n📍 " + info.event.extendedProps.venue;
                }
                info.el.setAttribute('title', tooltipTitle);
            }
        }); 
        c.render(); 
    });
</script>