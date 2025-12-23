
<?php 
    $CI =& get_instance();
    $CI->load->model('invoices_model');
    $CI->load->model('purchase/Purchase_model', 'purchase_model');

    $invoices     = $CI->invoices_model->get_unpaid_invoices();  
    $vendors      = $CI->purchase_model->get_vendor_select_dropdown('', "FIND_IN_SET(1, category) > 0 OR FIND_IN_SET(2, category) > 0");
    //$pur_invoices = $CI->purchase_model->get_unpaid_pur_invoices();    
?>

<?php echo form_open('admin/purchase/add_batch_payment?centralized=1', ['id' => 'expense-form']); ?>
<div class="">

    <div class="row">

        <div class="col-sm-4">
            <div class="form-group select-placeholder">
                <label for="vendor"><?php echo _l('Vendors'); ?></label>
                <select id="batch-payment-filter" class="selectpicker" data-live-search="true" name="client_filter"
                    data-width="100%"
                    data-none-selected-text="Nothing selected" required>
                    <option value=""></option>
                    <?php foreach ($vendors as $vendor) { ?>
                    <option data-content="<?php echo pur_html_entity_decode($vendor['company']); ?>" value="<?php echo e($vendor['userid']); ?>"><?php echo $vendor['company']; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- Add these new fields - Start -->
        <div class="col-sm-4">
            <div class="form-group">
                <?php echo render_date_input('global_payment_date', 'Date', '', ['placeholder' => 'Payment Date', 'required' => true]) //date(get_current_date_format(true)) ?>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <?php 
                    echo render_input('global_amount', 'Amount', '', 'number', [
                        'max1' => null,
                        'placeholder' => 'Total Amount Received',
                        'data-total-due' => null,
                        'required' => true
                    ]) 
                ?>
                <?php 
                    echo render_input('remaining_amount', '', '', 'hidden', []) 
                ?>
            </div>
        </div>

        <!-- <div class="col-md-12 text-right mb-3">
            <button type="button" id="show-advance" class="btn btn-link p-0" style="padding: 0; margin-bottom: 10px;">Advance</button>
        </div> -->
    </div>

    <div class="row" id="advance-fields" class="row">

        <div class="col-sm-4">
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
                <label for="global-payment-mode"><?php echo _l('Payment Mode'); ?></label>
                <select id="global-payment-mode" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected">
                    <option value=""></option>
                    <?php foreach ($unique_payment_modes as $mode) { ?>
                        <?php if ($mode->id != 3) { ?>
                            <option value="<?php echo e($mode->id); ?>"><?php echo e($mode->name); ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <?php echo render_input('global_transaction_id', 'Transaction ID', '', 'text', ['placeholder' => 'Transaction ID']) ?>
            </div>
        </div>    
        <div class="col-sm-4 hide">
            <div class="form-group">
                <?php 
                    echo render_input('global_tds_percentage', '', '', 'number', [
                        'max' => 30,
                        'placeholder' => 'TDS Percentage %',
                    ]) 
                ?>
            </div>
        </div>  
        <div class="col-sm-4">
            <div class="form-group">
                <?php 
                    echo render_input('global_note', 'Note', '', 'text', [
                        'placeholder' => 'Global Note',
                    ]) 
                ?>
            </div>
        </div>

    </div>
 
    <div class="row"> 
        <div class="col-sm-12 text-right">
            <button onclick="return confirm('Are you sure you want to apply this action?');" type="submit" class="btn btn-primary"><?php echo _l('apply'); ?></button>
        </div>

        <div class="col-sm-12">
            <div class="" id="payment-table" style="visibility: hidden;"></div>
        </div>                    
    </div>
    
    <?php echo form_close(); ?>


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

    // $('#batch-payment-filter').change(function() {
    //     var clientId = $(this).val();      
        
    //     if (clientId) {
    //         $('.batch_payment_item').hide();
    //         var totalDueForClient = 0;

    //         $('.batch_payment_item[data-clientid="' + clientId + '"]').each(function() {
    //             $(this).show();
    //             var dueText = $(this).find('td:eq(8)').text().replace(/[^0-9.-]+/g,"");
    //             totalDueForClient += parseFloat(dueText) || 0;
    //         });

    //         // Update max attribute and data-total-due on input
    //         //$('#global_amount').attr('max', totalDueForClient.toFixed(2));
    //         $('#global_amount').attr('data-total-due', totalDueForClient.toFixed(2));

    //         // Show and update vendor expense button
    //         $('#vendor-expense-btn').attr('href', admin_url + 'expenses/expense?vendor=' + clientId);
    //         $('#vendor-expense-btn-wrapper').show();            
            
    //         toggleFields(true);
    //     } else {
    //         $('.batch_payment_item').hide();
    //         //$('#global_amount').removeAttr('max')
    //         $('#global_amount').removeAttr('data-total-due');

    //         // Hide vendor expense button
    //         $('#vendor-expense-btn-wrapper').hide();

    //         toggleFields(false);
    //     }

    //     resetFields();
    //     updateVendorExpenseButton();
    // }).trigger('change');
    
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
    
    $('body').off('change', '#batch-payment-filter').on('change', '#batch-payment-filter', function () {
        let vendorId = $(this).val();
        if(vendorId == "") return;
        var $container = $("#payment-table");

        $container.html('<p>Loading...</p>'); // optional loader

        $.ajax({
            url: admin_url + "nex_reports/centralize_expense/components/" + vendorId,   // single handler file
            type: "POST",
            data: { type: '_purchase'},             // pass selected as data
            success: function(response){
                $container.html(response);

                setTimeout(function() {
                    var totalDueForClient = 0;

                    $('.batch_payment_item[data-clientid="' + vendorId + '"]').each(function() {
                        $(this).show();
                        var dueText = $(this).find('td:eq(8)').text().replace(/[^0-9.-]+/g, "");
                        totalDueForClient += parseFloat(dueText) || 0;
                    });
                    // Update max attribute and data-total-due on input
                    $('#global_amount').attr('data-total-due', totalDueForClient.toFixed(2)).attr('max', totalDueForClient.toFixed(2)).attr('min', 1);

                    resetFields();
                }, 100); // 1000ms = 1 second

            },
            error: function(xhr, status, error){
                $container.html('<p style="color:red;">Error loading section: ' + error + '</p>');
            }
        });
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

    $(function() {
        init_selectpicker();
        init_datepicker();

        appValidateForm("#expense-form");         
    });      
    
    // $("#show-advance").on("click", function() {
    //     $("#advance-fields").slideToggle();
    //     $(this).text($(this).text() === "Advance" ? "Hide Advance" : "Advance");
    // });    
</script>