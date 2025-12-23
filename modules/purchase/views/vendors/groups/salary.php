<a href="#" class="btn btn-primary" onclick="open_salary_modal('<?php echo $client->userid; ?>'); return false;">
    <?php echo _l('Set Salary'); ?>
</a>
<a href="#" class="btn btn-success" onclick="salary_ledger_modal(''); return false;">
    <?php echo _l('Pay Salary'); ?>
</a>

<div id="salary_modal_area"></div>

<div class="">
  <div class="">
    <hr class="hr-panel-heading" />
    <h4 class="tw-font-semibold tw-mb-4">Salary Statement For <?php echo $client->company; ?></h4>
        <?php /*echo form_open(admin_url() . 'purchase/vendor/' . $client->userid, ['method' => 'get']); ?>
        <div class="row mb-2">
            <div class="col-md-3">
                <input type="text" name="from_date" class="form-control datepicker" placeholder="From Date" autocomplete="off"
                    value="<?php echo set_value('from_date', $from_date ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="to_date" class="form-control datepicker" placeholder="To Date" autocomplete="off"
                    value="<?php echo set_value('to_date', $to_date ?? ''); ?>">
            </div>
            <div class="col-md-3 mt-4">
                <input type="hidden" name="group" value="<?php echo $group; ?>">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?php echo admin_url() . 'purchase/vendor/' . $client->userid.'?group='.$group; ?>" class="btn btn-secondary">Reset</a>
            </div>
        </div>
        <?php echo form_close();*/ ?>

<?php 
// Get current date for dynamic options
$current_date = date('d-m-Y');
$first_day_month = date('01-m-Y');
$last_day_month = date('t-m-Y');
$first_day_last_month = date('01-m-Y', strtotime('first day of last month'));
$last_day_last_month = date('t-m-Y', strtotime('last day of last month'));
$first_day_year = date('01-01-Y');
$last_day_year = date('31-12-Y');
$first_day_last_year = date('01-01-'.(date('Y')-1));
$last_day_last_year = date('31-12-'.(date('Y')-1));

// Calculate week dates
$monday = date('d-m-Y', strtotime('monday this week'));
$sunday = date('d-m-Y', strtotime('sunday this week'));

// Check if range is selected from dropdown
$range_selected = isset($_GET['range']) ? $_GET['range'] : '';
$show_period_fields = ($range_selected === 'period');
?>
<?php echo form_open(admin_url() . 'purchase/vendor/' . $client->userid, ['method' => 'get', 'id' => 'vendor_statement_form']); ?>
    <div class="row mb-2">
        <div class="col-md-3">
            <select class="form-control selectpicker" name="range" id="range" data-width="100%" onchange="" tabindex="-98">
                <option value=""></option>
                <option value='["<?php echo $current_date; ?>","<?php echo $current_date; ?>"]' <?php echo ($range_selected === '["'.$current_date.'","'.$current_date.'"]') ? 'selected' : ''; ?>>Today</option>
                <option value='["<?php echo $monday; ?>","<?php echo $sunday; ?>"]' <?php echo ($range_selected === '["'.$monday.'","'.$sunday.'"]') ? 'selected' : ''; ?>>This Week</option>
                <option value='["<?php echo $first_day_month; ?>","<?php echo $last_day_month; ?>"]' <?php echo ($range_selected === '["'.$first_day_month.'","'.$last_day_month.'"]') ? 'selected' : ''; ?>>This Month</option>
                <option value='["<?php echo $first_day_last_month; ?>","<?php echo $last_day_last_month; ?>"]' <?php echo ($range_selected === '["'.$first_day_last_month.'","'.$last_day_last_month.'"]') ? 'selected' : ''; ?>>Last Month</option>
                <option value='["<?php echo $first_day_year; ?>","<?php echo $last_day_year; ?>"]' <?php echo ($range_selected === '["'.$first_day_year.'","'.$last_day_year.'"]') ? 'selected' : ''; ?>>This Year</option>
                <option value='["<?php echo $first_day_last_year; ?>","<?php echo $last_day_last_year; ?>"]' <?php echo ($range_selected === '["'.$first_day_last_year.'","'.$last_day_last_year.'"]') ? 'selected' : ''; ?>>Last Year</option>
                <option value="period" <?php echo ($range_selected === 'period') ? 'selected' : ''; ?>>Period</option>
            </select>
        </div>
        
        <div class="col-md-3 period-fields" style="<?php echo !$show_period_fields ? 'display:none;' : ''; ?>">
            <input type="text" name="from_date" class="form-control datepicker" placeholder="From Date" autocomplete="off"
                value="<?php echo set_value('from_date', $from_date ?? ''); ?>">
        </div>
        <div class="col-md-3 period-fields" style="<?php echo !$show_period_fields ? 'display:none;' : ''; ?>">
            <input type="text" name="to_date" class="form-control datepicker" placeholder="To Date" autocomplete="off"
                value="<?php echo set_value('to_date', $to_date ?? ''); ?>">
        </div>
        
        <div class="col-md-3 mt-1 hide">
            <input type="hidden" name="group" value="<?php echo $group; ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?php echo admin_url() . 'purchase/vendor/' . $client->userid.'?group='.$group; ?>" class="btn btn-secondary">Reset</a>
        </div>
    </div>
<?php echo form_close(); ?>        

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><?php echo _l('Date'); ?></th>
                <th><?php echo _l('Details'); ?></th>
                <th><?php echo _l('Salary'); ?></th>
                <th><?php echo _l('Payment'); ?></th>
                <th><?php echo _l('Balance'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ledger)) {
                foreach ($ledger as $row) { ?>
                    <tr>
                        <td>
                            <?php if ($row['particular'] != 'Opening Balance') { ?>
                                <?php echo _d($row['entry_date']); ?>
                                <div class="row-options">
                                    <a href="javascript:void(0);"
                                    class="text-info"
                                    onclick="salary_ledger_modal('<?php echo $row['id']; ?>'); return false;">
                                        Edit
                                    </a>
                                    <?php if ((int)$row['salary_due'] == 0): ?>
                                        |
                                        <a href="<?php echo admin_url('purchase/delete_salary_ledger/' . $row['id'] . '?expense_id=' . $row['expense_id']); ?>"
                                        class="text-danger _delete"
                                        onclick="return confirm('<?php echo _l('confirm_action_prompt'); ?>');">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?php echo html_escape($row['particular']); ?></td>
                        <td><?php echo app_format_money($row['salary_due'], ''); ?></td>
                        <td><?php echo app_format_money($row['payment_done'], ''); ?></td>
                        <td><?php echo app_format_money($row['running_balance'], ''); ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="5" class="text-center"><?php echo _l('No records found'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

  </div>
</div>