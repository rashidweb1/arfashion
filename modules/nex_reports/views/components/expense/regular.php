<?php
    $CI =& get_instance();

    // load models you need
    $CI->load->model('taxes_model');
    $CI->load->model('expenses_model');
    $CI->load->model('payment_modes_model');
    $CI->load->model('currencies_model');
    $CI->load->model('purchase/Purchase_model', 'purchase_model');

    // fetch data directly
    $taxes       = $CI->taxes_model->get();
    $categories  = $CI->expenses_model->get_category();
    $payment_modes = $CI->payment_modes_model->get('', ['invoices_only !=' => 1]);
    $currencies  = $CI->currencies_model->get();
    $vendors = $CI->purchase_model->get_vendor_select_dropdown('', "FIND_IN_SET(1, category) > 0 OR FIND_IN_SET(2, category) > 0");

?>

<?php echo form_open_multipart(admin_url('expenses/expense'), ['id' => 'expense-form', 'class' => 'dropzone dropzone-manual']) ; ?>
<div class="row">
    <div class="col-md-12">    
        <input type="hidden" name="expense_name" value="">
        <input type="hidden" name="clientid" value="">
        <input type="hidden" name="project_id" value="">
        <input type="hidden" name="tax" value="">
        <input type="hidden" name="tax2" value=""> 
        <input type="hidden" name="reference_no" value="">   
        <input type="hidden" name="repeat_every" value="">
        <input type="hidden" name="repeat_every_custom" value="1">
        <input type="hidden" name="repeat_type_custom" value="day">    
        <input type="hidden" name="currency" value="1">   
    </div>  

    <!-- Category (Required) -->
    <div class="col-md-4">
        <?php echo render_select('category', $categories, ['id','name'], 'Category', '', ['required' => true], [], '', '', true); ?>
    </div>    

    <!-- Date (Required) -->
    <div class="col-md-4">
        <?php echo render_date_input('date', 'Date', _d(date('Y-m-d')), ['required' => true]); ?>
    </div>

    <!-- Amount (Required) -->
    <div class="col-md-4">
        <?php echo render_input('amount', 'Amount', '', 'number', ['required' => true]); ?>
    </div>    
    
    <!-- <div class="col-md-12 text-right mb-3">
        <button type="button" id="show-advance" class="btn btn-link p-0" style="padding: 0; margin-bottom: 10px;">Advance</button>
    </div>     -->
</div>

<div class="row" id="advance-fields" class="row">
    <!-- Vendor (Optional) -->
    <!-- <div class="col-md-4">
        <?php hooks()->do_action('before_expense_form_name', null); ?>
    </div> -->
    <div class="col-md-4">
        <label for="vendor"><?php echo _l('Vendors'); ?></label>
        <select name="vendor" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="">
            <option value=""></option>
            <?php foreach($vendors as $s) { ?>
            <option data-content="<?php echo pur_html_entity_decode($s['company']); ?>" value="<?php echo pur_html_entity_decode($s['userid']); ?>"><?php echo pur_html_entity_decode($s['company']); ?></option>
                <?php } ?>
        </select> 
    </div>    

    <!-- Payment Mode (Optional) -->
    <div class="col-md-4">
        <?php echo render_select('paymentmode', $payment_modes, ['id','name'], 'Payment Mode', ''); ?>
    </div>

    <!-- Note (Optional) -->
    <div class="col-md-4">
        <?php echo render_input('note', 'Note'); ?>
    </div> 
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

        appValidateForm("#expense-form", {}, expenseSubmitHandler);

        function expenseSubmitHandler(form) {
            var $submitBtn = $(form).find('button[type="submit"]');

            // disable submit button
            $submitBtn.prop('disabled', true).text('Submitting...');

            $.post(form.action, $(form).serialize())
                .done(function(response) {
                    response = JSON.parse(response);
                    if (response.expenseid) {
                        // ✅ Success - reset the form
                        $(form).trigger("reset");

                        // reinit selectpickers and date
                        $(form).find('select').selectpicker('refresh');
                        $(form).find('input[name="date"]').val(moment().format('DD-MM-YYYY'));

                        $("#filter-date").click();
                        alert_float('success', 'Expense added successfully');
                    } else {
                        alert_float('danger', 'Something went wrong');
                    }
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
        
        // $("#show-advance").on("click", function() {
        //     $("#advance-fields").slideToggle();
        //     $(this).text($(this).text() === "Advance" ? "Hide Advance" : "Advance");
        // });        
    });
</script>