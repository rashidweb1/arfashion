<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Salary Setting Modal -->
<div class="modal fade" id="salary_setting_modal" tabindex="-1" role="dialog" aria-labelledby="salarySettingModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="salarySettingModalLabel">
                    <?php echo isset($setting) ? _l('Salary Setting') : _l('Salary Setting'); ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>    
            <?php //var_dump($setting); ?>        
            <?php echo form_open(admin_url('purchase/salary_modal_form/' . (isset($setting) ? $setting->id : '')), ['id' => 'salary-setting-form']); ?>
            <div class="modal-body">
                <input type="hidden" name="id" value="<?php echo isset($setting) ? $setting->id : ''; ?>">
                
                <div class="form-group">
                    <input type="hidden" name="staff_id" class="form-control" value="<?php echo $staffid ?>" required>
                </div>

                <div class="form-group">
                    <label for="salary_amount"><?php echo _l('Salary Amount'); ?></label>
                    <input type="number" name="salary_amount" class="form-control" required min="1" step="0.01"
                        value="<?php echo isset($setting) ? $setting->salary_amount : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="effective_from"><?php echo _l('Effective From'); ?></label>
                    <input type="date" name="effective_from" class="form-control" required
                        value="<?php echo isset($setting) ? $setting->effective_from : date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="pause" name="pause" value="1"
                            <?php echo (!empty($setting) && isset($setting->pause) && $setting->pause == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="pause"><?php echo _l('Pause'); ?></label>
                    </div>
                </div>               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
$(function() {
    // Initialize datepicker if you're using one
    if(typeof init_datepicker === 'function') {
        init_datepicker();
    }
    
    // Initialize selectpicker if you're using it
    if($.fn.selectpicker) {
        $('.selectpicker').selectpicker();
    }
});
</script>