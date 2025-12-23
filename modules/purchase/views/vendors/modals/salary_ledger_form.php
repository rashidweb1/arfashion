<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Salary Setting Modal -->
<div class="modal fade" id="salary_setting_modal" tabindex="-1" role="dialog" aria-labelledby="paySalaryModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="paySalaryModalLabel">
                    <?php echo isset($ledger) ? _l('Edit Salary Ledger') : _l('Add Salary Ledger'); ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>          
            <div class="modal-body">
                <?php echo form_open(admin_url('purchase/salary_ledger_form/' . (isset($ledger) ? $ledger->id : ''))); ?>
                <div class="modal-body">
                    
                    <div class="form-group hide">
                        <input type="hidden" name="staff_id" class="form-control" value="<?php echo $staffid; ?>" required>
                        <input type="hidden" name="expense_id" class="form-control" value="<?php echo isset($ledger) ? $ledger->expense_id : null; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="entry_date"><?php echo _l('Entry Date'); ?></label>
                        <input type="date" name="entry_date" class="form-control" required value="<?php echo isset($ledger) ? $ledger->entry_date : date('Y-m-d'); ?>" required <?php echo ((int)$ledger->debit > 0 ? 'readonly' : ''); ?>>
                    </div>
                    
                    <?php if(!isset($ledger)): ?>
                    <div class="form-group">
                        <label for="credit"><?php echo _l('Amount'); ?></label>
                        <input type="number" name="credit" class="form-control" step="0.01" value="<?php echo isset($ledger) ? $ledger->credit : '0.00'; ?>" min="1" required>
                    </div> 
                    <div class="form-group hide">
                        <label for="debit"><?php echo _l('Amount'); ?></label>
                        <input type="number" name="debit" class="form-control" step="0.01" value="<?php echo isset($ledger) ? $ledger->debit : '0.00'; ?>">
                    </div>       
                    <?php else: ?>  
                        <?php if((int)$ledger->credit > 0): ?>
                        <div class="form-group">
                            <label for="credit"><?php echo _l('Amount'); ?></label>
                            <input type="number" name="credit" class="form-control" step="0.01" value="<?php echo isset($ledger) ? $ledger->credit : '0.00'; ?>" min="1" required>
                        </div>
                        <?php endif; ?>
                        <?php if((int)$ledger->debit > 0): ?>
                        <div class="form-group">
                            <label for="debit"><?php echo _l('Amount'); ?></label>
                            <input type="number" name="debit" class="form-control" step="0.01" value="<?php echo isset($ledger) ? $ledger->debit : '0.00'; ?>" min="1" required>
                        </div>       
                        <?php endif; ?>                                
                    <?php endif; ?>
                    
                    <div class="form-group <?php echo ((int)$ledger->debit > 0 ? 'hide' : ''); ?>">
                        <label for="payment_mode"><?php echo _l('Mode'); ?></label>
                        <!-- <input type="text" name="payment_mode" class="form-control" value="<?php echo isset($ledger) ? $ledger->payment_mode : ''; ?>"> -->
                        <select class="selectpicker form-control" name="payment_mode" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php foreach($payment_modes as $mode){ ?>
                            <option value="<?php echo $mode['id']; ?>"<?php if($ledger->payment_mode == $mode['id']){echo ' selected'; } ?>><?php echo $mode['name']; ?></option>
                            <?php } ?>
                        </select>                         
                    </div>   
                    
                    <div class="form-group hide">
                        <label for="reference_note"><?php echo _l('Reference Note'); ?></label>
                        <textarea name="reference_note" class="form-control"><?php echo isset($ledger) ? $ledger->reference_note : ''; ?></textarea>
                    </div>                    

                    <div class="form-group">
                        <label for="particular"><?php echo _l('Notes'); ?></label>
                        <input type="text" name="particular" class="form-control" value="<?php echo isset($ledger) ? $ledger->particular : ''; ?>">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script defer>
    $(function() {
        $('.selectpicker').selectpicker(); // Initialize on page load
    });
</script>