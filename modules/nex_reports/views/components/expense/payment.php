<?php
    $CI =& get_instance();

    // load models you need
    $CI->load->model('purchase/Purchase_model', 'purchase_model');

    // fetch data directly
    $vendors = $CI->purchase_model->get_vendor_select_dropdown('', "FIND_IN_SET(1, category) > 0 OR FIND_IN_SET(2, category) > 0");
    $next_debit_note_number = get_option('next_debit_note_number');
    $__number = $next_debit_note_number;
    $_debit_note_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
?>

<?php echo form_open(admin_url('purchase/debit_note'), ['id'=>'expense-form']); ?>

<div class="row">

    <!-- Vendor (Display - Required) -->
    <div class="col-md-3">
        <label for="vendorid"><?php echo _l('Vendors'); ?></label>
        <select name="vendorid" id="vendorid" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Nothing selected" required>
            <option value=""></option>
            <?php foreach($vendors as $s) { ?>
            <option data-content="<?php echo pur_html_entity_decode($s['company']); ?>" value="<?php echo pur_html_entity_decode($s['userid']); ?>"><?php echo pur_html_entity_decode($s['company']); ?></option>
                <?php } ?>
        </select>
    </div>

    <!-- Date (Display - Required) -->
    <div class="col-md-3">
        <?php echo render_date_input('date', 'Date', _d(date('Y-m-d')), ['required' => true]); ?>
    </div>

    <!-- Date (Display - Required) -->
    <div class="col-md-3">
        <?php echo render_input('newitems[1][rate]', 'Amount', '', 'number', ['required' => true]); ?>
    </div>

    <!-- Admin Note (Dropdown - Required) -->
    <div class="col-md-3">
        <label for="adminnote">Note Type</label>
        <select name="adminnote" id="adminnote" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="" required>
            <option value=""></option>
            <option value="Advance Payment Note">Advance Payment Note</option>
            <option value="Debit Note">Debit Note</option>
        </select>
    </div>    

    <!-- Hidden Fields -->
    <input type="hidden" name="centralized" value="1">
    <input type="hidden" name="billing_street" value="">
    <input type="hidden" name="billing_city" value="">
    <input type="hidden" name="billing_state" value="">
    <input type="hidden" name="billing_zip" value="">
    <input type="hidden" name="shipping_street" value="">
    <input type="hidden" name="shipping_city" value="">
    <input type="hidden" name="shipping_state" value="">
    <input type="hidden" name="shipping_zip" value="">
    <input type="hidden" name="discount_type" value="">
    <input type="hidden" name="reference_no" value="">
    <input type="hidden" name="item_select" value="">
    <input type="hidden" name="description" value="">
    <input type="hidden" name="long_description" value="">
    <input type="hidden" name="unit" value="">
    <input type="hidden" name="vendornote" value="">
    <input type="hidden" name="terms" value="">
    <input type="hidden" name="rate" value="">

    <input type="hidden" name="subtotal" value="3000"> <!-- (Hidden - autofill from [rate]) -->
    <input type="hidden" name="quantity" value="1">
    <input type="hidden" name="total" value="3000"> <!-- (Hidden - autofill from [rate]) -->
    <input type="hidden" name="discount_percent" value="0">
    <input type="hidden" name="discount_total" value="0">
    <input type="hidden" name="adjustment" value="0">    
    <input type="hidden" name="currency" value="1">
    <input type="hidden" name="number" value="<?php echo $_debit_note_number; ?>"> <!-- (Hidden with data - autofetch) -->
    <input type="hidden" name="show_shipping_on_debit_note" value="on">


    <input type="hidden" name="newitems[1][order]" value="1">
    <input type="hidden" name="newitems[1][iid]" value="undefined">
    <input type="hidden" name="newitems[1][description]" class="form-control" value="-">
    <input type="hidden" name="newitems[1][long_description]" value="-">
    <input type="hidden" name="newitems[1][unit]" value="">    
    <input type="hidden" name="newitems[1][qty]" class="form-control" value="1">
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
    </div> 
</div>

<?php echo form_close(); ?>

<script>
    $(function() {
        init_selectpicker();
        init_datepicker();

        appValidateForm("#expense-form" , {}, expenseSubmitHandler);

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
        
        $(document).on("input", 'input[name="newitems[1][rate]"]', function() {
            let val = $(this).val() || 0;

            // Update hidden fields by name
            $('input[name="subtotal"]').val(val);
            $('input[name="total"]').val(val);
        });        
    });
</script>
