<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="batch-payment-modal">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php
                  echo _l('add_batch_payments') ?></h4>
            </div>
            <?php
          echo form_open('admin/purchase/add_batch_payment', ['id' => 'batch-payment-form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group select-placeholder">
                            <select id="batch-payment-filter" class="selectpicker" data-live-search="true" name="client_filter"
                                data-width="100%"
                                data-none-selected-text="<?php echo _l('batch payment filter by vendor') ?>">
                                <option value=""></option>
                                <?php foreach ($vendors as $vendor) { ?>
                                <option data-content="<?php echo pur_html_entity_decode($vendor['company']); ?>" value="<?php echo e($vendor['userid']); ?>"><?php echo $vendor['company']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                   <!-- Add these new fields - Start -->
                    <div class="col-sm-2">
                        <div class="form-group">
                            <?php echo render_date_input('global_payment_date', '', '', ['placeholder' => 'Payment Date']) //date(get_current_date_format(true)) ?>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <?php
                                $unique_payment_modes = [];
                                foreach ($invoices as $invoice) {
                                    foreach ($invoice->allowed_payment_modes as $mode) {
                                        if (!isset($unique_payment_modes[$mode->id])) {
                                            $unique_payment_modes[$mode->id] = $mode;
                                        }
                                    }
                                }
                            ?>
                            <select id="global-payment-mode" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('payment_mode') ?>">
                                <option value=""></option>
                                <?php foreach ($unique_payment_modes as $mode) { ?>
                                <option value="<?php echo e($mode->id); ?>"><?php echo e($mode->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <?php echo render_input('global_transaction_id', '', '', 'text', ['placeholder' => 'Transaction ID']) ?>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <?php 
                                echo render_input('global_amount', '', '', 'number', [
                                    'max1' => null,
                                    'placeholder' => 'Total Amount Received',
                                    'data-total-due' => null
                                ]) 
                            ?>
                            <?php 
                                echo render_input('remaining_amount', '', '', 'hidden', []) 
                            ?>
                        </div>
                    </div>    
                    <div class="col-sm-1">
                        <div class="form-group">
                            <?php 
                                echo render_input('global_tds_percentage', '', '', 'number', [
                                    'max' => 30,
                                    'placeholder' => 'TDS Percentage %',
                                ]) 
                            ?>
                        </div>
                    </div>  
                    <div class="col-sm-2">
                        <div class="form-group">
                            <?php 
                                echo render_input('global_note', '', '', 'text', [
                                    'placeholder' => 'Global Note',
                                ]) 
                            ?>
                        </div>
                    </div>                                                     
                    <!-- Add these new fields - END -->                    
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong><?php echo _l('batch_payments_table_invoice_number_heading'); ?>
                                                #</strong></th>
                                        <th><strong><?php echo _l('batch_payments_table_payment_date_heading'); ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('batch_payments_table_payment_mode_heading'); ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('batch_payments_table_transaction_id_heading'); ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('batch_payments_table_amount_received_heading'); ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('TDS Amount'); //new field added ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('Total Amount'); //new field added ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('Notes'); //new field added ?></strong>
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
                                        <td class="tw-w-48">
                                            <?php echo render_date_input('pur_invoice[' . $index . '][date]', '', date(get_current_date_format(true))) ?>
                                        </td>
                                        <td class="tw-w-56">
                                            <div class="form-group">
                                                <select class="selectpicker"
                                                    name="pur_invoice[<?php echo $index ?>][paymentmode]" data-width="100%"
                                                    data-none-selected-text="-">
                                                    <option></option>
                                                    <?php foreach ($invoice->allowed_payment_modes as $mode) { ?>
                                                        <?php if ($mode['id'] != 4) { ?>
                                                            <option value="<?php echo e($mode['id']); ?>"><?php echo e($mode['name']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td><?php echo render_input('pur_invoice[' . $index . '][transactionid]') ?></td>
                                        <td><?php echo render_input('pur_invoice[' . $index . '][amount]', '', '', 'number', []) ?>
                                        </td>
                                        <td>
                                            <?php echo render_input('pur_invoice[' . $index . '][tds_amount]', '', '', 'number', []) //new field added ?>
                                        </td>
                                        <td>
                                            <?php echo render_input('pur_invoice[' . $index . '][total_amount]', '', '', 'number', ['max' => $invoice->total_left_to_pay]) //new field added ?>
                                        </td>
                                        <td>
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
                        <div class="col-sm-12 row">
                            <div class="checkbox">
                                <input type="checkbox" name="do_not_send_invoice_payment_recorded" value="1"
                                    id="do_not_send_invoice_payment_recorded">
                                <label
                                    for="do_not_send_invoice_payment_recorded"><?php echo _l('batch_payments_send_invoice_payment_recorded'); ?></label>
                            </div>
                        </div>
                    </div>                   
                    <div class="col-sm-12">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php
                      echo _l('close'); ?></button>
                    <button onclick="return confirm('Are you sure you want to apply this action?');" type="submit" class="btn btn-primary"><?php
                      echo _l('apply'); ?></button>
                </div>
                <?php
              echo form_close(); ?>
            <?php    
                // echo '<pre>';
                // print_r($invoices);
                // echo '</pre>';

                // echo '<pre>';
                // print_r($pur_invoices);
                // echo '</pre>';                    
            ?>              
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Calculate total due amount
    var totalDue = parseFloat($('#global-amount').data('total-due'));
    
    // Enable/disable fields based on filter
    function toggleFields(enable) {
        $('.batch_payment_item input, .batch_payment_item').prop('readonly', !enable);
        if (enable) {
            $('.batch_payment_item .selectpicker').selectpicker('refresh');
        }
    }  
    
    // Reset all fields
    function resetFields() {
        $('#global_payment_date').val('').trigger('change');
        $('#global-payment-mode').val('').selectpicker('refresh');
        $('#global_transaction_id').val('');
        $('#global_amount').val('');
        $('#global_tds_percentage').val('');
        $('#global_note').val('');
        $('#remaining_amount').val('')
        
        $('.batch_payment_item input:not([type="hidden"])').val('');
        $('.batch_payment_item select').val('').selectpicker('refresh');
        $('.batch_payment_item input[name^="pur_invoice"][name$="[total_amount]"]').attr('readonly', 'readonly');
    }

    $('#batch-payment-filter').change(function() {
        var clientId = $(this).val();      
        
        if (clientId) {
            $('.batch_payment_item').hide();
            var totalDueForClient = 0;

            $('.batch_payment_item[data-clientid="' + clientId + '"]').each(function() {
                $(this).show();
                var dueText = $(this).find('td:eq(8)').text().replace(/[^0-9.-]+/g,"");
                totalDueForClient += parseFloat(dueText) || 0;
            });

            // Update max attribute and data-total-due on input
            //$('#global_amount').attr('max', totalDueForClient.toFixed(2));
            $('#global_amount').attr('data-total-due', totalDueForClient.toFixed(2));

            // Show and update vendor expense button
            $('#vendor-expense-btn').attr('href', admin_url + 'expenses/expense?vendor=' + clientId);
            $('#vendor-expense-btn-wrapper').show();            
            
            toggleFields(true);
        } else {
            $('.batch_payment_item').hide();
            //$('#global_amount').removeAttr('max')
            $('#global_amount').removeAttr('data-total-due');

            // Hide vendor expense button
            $('#vendor-expense-btn-wrapper').hide();

            toggleFields(false);
        }

        resetFields();
        updateVendorExpenseButton();
    }).trigger('change');
    
    // Global payment date change - fills all visible date fields
    $('#global_payment_date').on('change', function() {
        var date = $(this).val();
        $('.batch_payment_item:visible').each(function() {
            $(this).find('input[name*="[date]"]').val(date).trigger('change');
        });
    });
    
    // Global payment mode change - fills all visible mode selects
    $('#global-payment-mode').on('change', function() {
        var mode = $(this).val();
        $('.batch_payment_item:visible').each(function() {
            var $select = $(this).find('select[name*="[paymentmode]"]');
            if ($select.find('option[value="' + mode + '"]').length) {
                $select.val(mode).selectpicker('refresh');
            }
        });
    });
    
    // Global transaction ID change - fills all visible transaction ID fields
    $('#global_transaction_id').on('input', function() {
        var transId = $(this).val();
        $('.batch_payment_item:visible').each(function() {
            $(this).find('input[name*="[transactionid]"]').val(transId);
        });
    });
    
    $('#global_amount, #global_tds_percentage').on('input', function() {
        var totalAmount = parseFloat($('#global_amount').val()) || 0;
        var tdsPercent = parseFloat($('#global_tds_percentage').val()) || 0;
        var visibleInvoices = $('.batch_payment_item:visible');
        var invoiceCount = visibleInvoices.length;

        if (invoiceCount > 0) {
            // Reset all fields
            visibleInvoices.find('input[name*="[total_amount]"]').val('0.00');
            visibleInvoices.find('input[name*="[amount]"]').val('0.00');
            visibleInvoices.find('input[name*="[tds_amount]"]').val('0.00');

            // Prepare invoice array
            var invoices = [];
            visibleInvoices.each(function() {
                var maxAmount = parseFloat($(this).find('td:eq(8)').text().replace(/[^0-9.-]+/g, "")) || 0;
                invoices.push({
                    element: $(this),
                    maxAmount: maxAmount,
                    amountToApply: 0
                });
            });

            // Distribute totalAmount into total_amount
            var remainingAmount = totalAmount;
            for (var i = 0; i < invoices.length && remainingAmount > 0; i++) {
                var invoice = invoices[i];
                var amountToApply = Math.min(invoice.maxAmount, remainingAmount);

                // Set total_amount field
                invoice.element.find('input[name*="[total_amount]"]').val(amountToApply.toFixed(2));

                // Calculate and set TDS and amount after TDS
                var tdsAmount = (tdsPercent > 0) ? (amountToApply * tdsPercent / 100) : 0;
                var netAmount = amountToApply - tdsAmount;

                invoice.element.find('input[name*="[tds_amount]"]').val(tdsAmount.toFixed(2));
                invoice.element.find('input[name*="[amount]"]').val(netAmount.toFixed(2));

                remainingAmount -= amountToApply;
            }

            
            $('#remaining_amount').attr('value', remainingAmount);
            updateVendorExpenseButton();            
        }
    });

    // Global Note change - fills all visible note fields
    $('#global_note').on('input', function() {
        var note = $(this).val();
        $('.batch_payment_item:visible').each(function() {
            $(this).find('input[name*="[note]"]').val(note);
        });
    });
    
    $('#vendor-expense-btn-wrapper').on('mouseenter', function () {
        updateVendorExpenseButton();
    }); 
    
    $('#batch-payment-body').sortable({
        items: '> tr:visible',       // Only allow visible rows to be sorted
        handle: 'td',                // You can drag from any table cell
        cursor: 'move',              // Cursor style
        helper: 'clone',             // Ghost row while dragging
        update: function (event, ui) {
            // Trigger recalculation after sorting
            $('#global_amount, #global_tds_percentage').trigger('input');
        }
    });    
    
    // Initialize with fields disabled
    toggleFields(false);
    updateVendorExpenseButton();
});

function updateVendorExpenseButton() {
    var vendorId = $('#batch-payment-filter').val();
    var global_note = $('#global_note').val() || '';
    var remainingAmount = parseFloat($('#remaining_amount').val()) || 0;
    var date = $('#global_payment_date').val() || '';

    if (vendorId) {
        var encodedNote = encodeURIComponent(global_note);
        var encodedDate = encodeURIComponent(date);

        var newHref = admin_url + 'expenses/expense?vendor=' + vendorId +
                      '&amount=' + remainingAmount.toFixed(2) +
                      '&note=' + encodedNote +
                      '&date=' + encodedDate;

        $('#vendor-expense-btn').attr('href', newHref);
        $('#vendor-expense-btn').html('Record Expense (' + remainingAmount.toFixed(2) + ')');
    }
}

    $(document).on('input', 'input[name$="[amount]"], input[name$="[tds_amount]"]', function() {
        // Get the current row
        var $row = $(this).closest('.batch_payment_item');

        // Get values of amount and tds_amount
        var amount = parseFloat($row.find('input[name$="[amount]"]').val()) || 0;
        var tdsAmount = parseFloat($row.find('input[name$="[tds_amount]"]').val()) || 0;

        // Calculate total amount
        var totalAmount = amount + tdsAmount;

        // Set total_amount field
        $row.find('input[name$="[total_amount]"]').val(totalAmount.toFixed(2));
    });
</script>