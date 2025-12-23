<?php 
    $vendor_id = $param2;

    $CI =& get_instance();
    $CI->load->model('purchase/Purchase_model', 'purchase_model');
    $pur_invoices = $CI->purchase_model->get_unpaid_pur_invoices($vendor_id);
?>


<!-- Add these new fields - END hide -->                    
<div class="">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><strong><?php echo _l('batch_payments_table_invoice_number_heading'); ?>
                            #</strong></th>
                    <th class="hide"><strong><?php echo _l('batch_payments_table_payment_date_heading'); ?></strong>
                    </th>
                    <th class="hide"><strong><?php echo _l('batch_payments_table_payment_mode_heading'); ?></strong>
                    </th>
                    <th class="hide"><strong><?php echo _l('batch_payments_table_transaction_id_heading'); ?></strong>
                    </th>
                    <th><strong><?php echo _l('batch_payments_table_amount_received_heading'); ?></strong>
                    </th>
                    <th class="hide"><strong><?php echo _l('TDS Amount'); //new field added ?></strong>
                    </th>
                    <th class="hide"><strong><?php echo _l('Total Amount'); //new field added ?></strong>
                    </th>
                    <th class="hide"><strong><?php echo _l('Notes'); //new field added ?></strong>
                    </th>
                    <th><strong><?php echo _l('batch_payments_table_invoice_balance_due'); ?></strong>
                    </th>
                </tr>
            </thead>
            <tbody id="batch-payment-body">
                <?php foreach (array_reverse($pur_invoices) as $index => $invoice) { ?>
                <tr class="batch_payment_item" data-clientid="<?php echo e($invoice->vendor); ?>"
                    data-invoiceId="<?php echo $invoice->id ?>">
                    <td>
                        <a href="<?php echo admin_url('purchase/purchase_invoice/' . $invoice->id); ?>"
                            target="_blank">
                            <?php echo $invoice->invoice_number ?>
                        </a><br>
                        <a class="text-dark"
                            href="<?php echo admin_url('purchase/vendor/' . $invoice->vendor); ?>"
                            target="_blank">
                            <?php echo isset($invoice->company) ? $invoice->company : 'company' ?>
                        </a>

                        <input type="hidden" name="pur_invoice[<?php echo $index ?>][pur_invoice]"
                            value="<?php echo $invoice->id?>">
                    </td>
                    <td class="tw-w-48 hide">
                        <?php echo render_date_input('pur_invoice[' . $index . '][date]', '', date(get_current_date_format(true))) ?>
                    </td>
                    <td class="tw-w-56 hide">
                        <div class="form-group">
                            <select class="selectpicker"
                                name="pur_invoice[<?php echo $index ?>][paymentmode]" data-width="100%"
                                data-none-selected-text="-">
                                <option></option>
                                <?php foreach ($invoice->allowed_payment_modes as $mode) { ?>
                                    <?php //if ($mode['id'] != 4) { ?>
                                        <option value="<?php echo e($mode['id']); ?>"><?php echo e($mode['name']); ?>
                                        </option>
                                    <?php //} ?>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td class="hide"><?php echo render_input('pur_invoice[' . $index . '][transactionid]') ?></td>
                    <td><?php echo render_input('pur_invoice[' . $index . '][amount]', '', '', 'number', ['readonly' => true]) ?>
                    </td>
                    <td class="hide">
                        <?php echo render_input('pur_invoice[' . $index . '][tds_amount]', '', '', 'number', []) //new field added ?>
                    </td>
                    <td class="hide">
                        <?php echo render_input('pur_invoice[' . $index . '][total_amount]', '', '', 'number', ['max' => $invoice->total_left_to_pay]) //new field added ?>
                    </td>
                    <td class="hide">
                        <?php echo render_input('pur_invoice[' . $index . '][note]', ''); ?>
                    </td>
                    <td><?php echo app_format_money($invoice->total_left_to_pay, $invoice->currency) ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="" id="vendor-expense-btn-wrapper" style="display:none; margin-bottom: 10px;">
        <a href="#" target="_blank" class="btn btn-success" id="vendor-expense-btn">
            Record Expense
        </a>
    </div>                         
    <div class="row1 hide">
        <div class="checkbox">
            <input type="checkbox" name="do_not_send_invoice_payment_recorded" value="1"
                id="do_not_send_invoice_payment_recorded" checked> 
            <label
                for="do_not_send_invoice_payment_recorded"><?php echo _l('batch_payments_send_invoice_payment_recorded'); ?></label>
        </div>
    </div>
</div>   
                
<div class="hide">
    <div class="alert alert-info">
        <strong>Note:</strong> Rows will be <strong>skipped</strong> during submission if any of the following fields are missing:
        <ul style="margin-bottom: 0;">
            <li>Invoice ID</li>
            <li>Amount (must be greater than 0)</li>
            <li>Payment Date</li>
            <li>Payment Mode</li>
        </ul>
    </div>
</div> 

<script>
$(function() {
    init_selectpicker();
    init_datepicker();
    appValidateForm("#expense-form", {}, expenseSubmitHandler);  

        function expenseSubmitHandler(form) {
            var $submitBtn = $(form).find('button[type="submit"]');

            // disable submit button
            $submitBtn.prop('disabled', true).text('Submitting...');

            $.post(form.action, $(form).serialize())
                .done(function(response) {
                    // response = JSON.parse(response);
                    // if (response.expenseid) {
                        // ✅ Success - reset the form
                        $(form).trigger("reset");

                        // reinit selectpickers and date
                        $(form).find('select').selectpicker('refresh');
                        $(form).find('input[name="date"]').val(moment().format('DD-MM-YYYY'));
                        
                        
                        $("#payment-table").html("");
                        $("#filter-date").click();
                        alert_float('success', 'Expense added successfully');
                    // } else {
                    //     alert_float('danger', 'Something went wrong');
                    // }
                })
                .fail(function() {
                    alert_float('danger', 'Server error, please try again.');
                })
                .always(function() {
                    // re-enable button
                    $submitBtn.prop('disabled', false).text('<?php echo _l('submit'); ?>');
                });

            return false;
        }     
});
</script>