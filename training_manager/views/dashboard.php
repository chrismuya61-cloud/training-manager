<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>.metric-help{cursor:pointer;color:#64748b;font-size:14px;}.formula-box{background:#f8fafc;padding:10px;border-left:3px solid #2563eb;margin-bottom:10px;font-family:monospace;font-size:0.9em;}</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="bold no-margin pull-left">Training Dashboard</h4>
                <button onclick="$('#metricsModal').modal('show')" class="btn btn-default btn-xs pull-right mtop5">
                    <i class="fa fa-question-circle"></i> Metrics Guide
                </button>
                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3"><div class="panel_s"><div class="panel-body"><h3 class="text-success bold"><?php echo app_format_money($total_revenue, $currency); ?></h3><span class="text-muted">Total Revenue</span></div></div></div>
            <div class="col-lg-3"><div class="panel_s"><div class="panel-body"><h3 class="text-danger bold"><?php echo app_format_money($total_expenses, $currency); ?></h3><span class="text-muted">Total Expenses</span></div></div></div>
            <div class="col-lg-3"><div class="panel_s"><div class="panel-body"><h3 class="text-warning bold"><?php echo $roi; ?>%</h3><span class="text-muted">ROI</span></div></div></div>
            <div class="col-lg-3"><div class="panel_s"><div class="panel-body"><h3 class="text-info bold"><?php echo $total_attendees; ?></h3><span class="text-muted">Total Attendees</span></div></div></div>
        </div>

        <div class="panel_s">
            <div class="panel-body">
                <a href="<?php echo admin_url('training_manager/export_dashboard_report'); ?>" class="btn btn-success pull-right">Export Report</a>
                <h4 class="bold">Financial Report</h4>
                <hr>
                <table class="table dt-table">
                    <thead><th>Event</th><th>Date</th><th>Revenue</th><th>Expense</th><th>Profit</th></thead>
                    <tbody>
                        <?php foreach($events_report as $ev){ ?>
                            <tr>
                                <td><?php echo $ev['subject']; ?></td>
                                <td><?php echo _d($ev['start_date']); ?></td>
                                <td><?php echo app_format_money($ev['revenue'], $currency); ?></td>
                                <td><?php echo app_format_money($ev['expense'], $currency); ?></td>
                                <td><?php echo app_format_money($ev['revenue']-$ev['expense'], $currency); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- METRICS GUIDE MODAL -->
<div class="modal fade" id="metricsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title bold">Metrics & Calculation Guide</h4>
            </div>
            <div class="modal-body">
                <p class="text-muted mbot20">This guide explains the formulas used for financial metrics.</p>
                
                <h5 class="bold">1. Total Revenue</h5>
                <div class="formula-box">SUM( Event Price × Total Attendees )</div>

                <h5 class="bold mtop20">2. Net Profit</h5>
                <div class="formula-box">Total Revenue - Total Expenses</div>

                <h5 class="bold mtop20">3. ROI (Return on Investment)</h5>
                <div class="formula-box">( Net Profit / Total Expenses ) × 100</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
