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
          echo form_open('admin/payments/add_batch_payment', ['id' => 'batch-payment-form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group select-placeholder">
                            <select id="batch-payment-filter" class="selectpicker" data-live-search="true" name="client_filter"
                                data-width="100%"
                                data-none-selected-text="<?php echo _l('batch_payment_filter_by_customer') ?>">
                                <option value=""></option>
                                <?php foreach ($customers as $customer) { ?>
                                <option value="<?php echo e($customer->userid); ?>"><?php echo e($customer->company); ?>
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
                    <div class="col-sm-2">
                        <div class="form-group">
                            <?php 
                                echo render_input('global_amount', '', '', 'number', [
                                    'max' => null,
                                    'placeholder' => 'Total Amount Received',
                                    'data-total-due' => null
                                ]) 
                            ?>
                        </div>
                    </div>    
                    <div class="col-sm-2">
                        <div class="form-group">
                            <?php 
                                echo render_input('global_tds_percentage', '', '', 'number', [
                                    'max' => 30,
                                    'placeholder' => 'TDS Percentage %',
                                ]) 
                            ?>
                        </div>
                    </div>  
                    <!-- Global Note Field -->
                    <div class="col-sm-12 mtop10">
                        <div class="form-group">
                            <?php echo render_input('global_note', '', '', 'text', ['placeholder' => 'Enter a note to apply to all payments']) ?>
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
                                        <th><strong><?php echo _l('Note'); ?></strong>
                                        </th>
                                        <th><strong><?php echo _l('batch_payments_table_invoice_balance_due'); ?></strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="batch-payment-body">
                                    <?php foreach (array_reverse($invoices) as $index => $invoice) { ?>
                                    <tr class="batch_payment_item" data-clientid="<?php echo e($invoice->clientid); ?>"
                                        data-invoiceId="<?php echo $invoice->id ?>">
                                        <td>
                                            <a href="<?php echo admin_url('invoices/list_invoices/' . $invoice->id); ?>"
                                                target="_blank">
                                                <?php echo format_invoice_number($invoice->id) ?>
                                            </a><br>
                                            <a class="text-dark"
                                                href="<?php echo admin_url('clients/client/' . $invoice->clientid); ?>"
                                                target="_blank">
                                                <?php echo $invoice->company ?>
                                            </a>

                                            <input type="hidden" name="invoice[<?php echo $index ?>][invoiceid]"
                                                value="<?php echo $invoice->id?>">
                                        </td>
                                        <td class="tw-w-48">
                                            <?php echo render_date_input('invoice[' . $index . '][date]', '', date(get_current_date_format(true))) ?>
                                        </td>
                                        <td class="tw-w-56">
                                            <div class="form-group">
                                                <select class="selectpicker"
                                                    name="invoice[<?php echo $index ?>][paymentmode]" data-width="100%"
                                                    data-none-selected-text="-">
                                                    <option></option>
                                                    <?php foreach ($invoice->allowed_payment_modes as $mode) { ?>
                                                    <option value="<?php echo e($mode->id); ?>"><?php echo e($mode->name); ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td><?php echo render_input('invoice[' . $index . '][transactionid]') ?></td>
                                        <td><?php echo render_input('invoice[' . $index . '][amount]', '', '', 'number', ['max' => $invoice->total_left_to_pay, 'readonly' => 'readonly']) ?>
                                        </td>
                                        <td>
                                            <?php echo render_input('invoice[' . $index . '][tds_amount]', '', '', 'number', []) //new field added ?>
                                        </td>
                                        <td>
                                            <?php echo render_input('invoice[' . $index . '][total_amount]', '', '', 'number', ['max' => $invoice->total_left_to_pay]) //new field added ?>
                                        </td>
                                        <td>
                                            <?php echo render_input('invoice[' . $index . '][note]', '', '', 'text', ['placeholder' => 'Payment note']) ?>
                                        </td>                                        
                                        <td><?php echo app_format_money($invoice->total_left_to_pay, $invoice->currency) ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
        //$('.batch_payment_item input, .batch_payment_item select').prop('disabled', !enable);
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
        
        $('.batch_payment_item input:not([type="hidden"])').val('');
        $('.batch_payment_item select').val('').selectpicker('refresh');
        $('.batch_payment_item input[name^="invoice"][name$="[total_amount]"]').attr('readonly', 'readonly');
    }
    
    // Filter by customer
    // $('#batch-payment-filter').change(function() {
    //     var clientId = $(this).val();      
        
    //     if (clientId) {
    //         $('.batch_payment_item').hide();
    //         $('.batch_payment_item[data-clientid="' + clientId + '"]').show();
    //         toggleFields(true);
    //     } else {
    //         $('.batch_payment_item').hide();
    //         toggleFields(false);
    //     }
        
    //     resetFields();
    // }).trigger('change');

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
            $('#global_amount').attr('max', totalDueForClient.toFixed(2));
            $('#global_amount').attr('data-total-due', totalDueForClient.toFixed(2));
            
            toggleFields(true);
        } else {
            $('.batch_payment_item').hide();
            $('#global_amount').removeAttr('max').removeAttr('data-total-due');
            toggleFields(false);
        }

        resetFields();
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

    // Global note change - fills all visible note fields
    $('#global_note').on('input', function() {
        var note = $(this).val();
        $('.batch_payment_item:visible').each(function() {
            $(this).find('input[name*="[note]"]').val(note);
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
        }
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

    // Bind event to amount and tds_amount fields
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
});
</script>