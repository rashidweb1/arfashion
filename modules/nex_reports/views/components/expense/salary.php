<?php
    $CI =& get_instance();

    // load models you need
    $CI->load->model('taxes_model');
    $CI->load->model('expenses_model');
    $CI->load->model('payment_modes_model');
    $CI->load->model('currencies_model');
    $CI->load->model('purchase/Purchase_model', 'purchase_model');

    // fetch data directly
    $payment_modes = $CI->payment_modes_model->get('', ['invoices_only !=' => 1]);
    $vendors = $CI->purchase_model->get_vendor_select_dropdown('', "FIND_IN_SET(3, category) > 0");

?>

<?php echo form_open_multipart(admin_url('purchase/salary_ledger_form'), ['id' => 'expense-form', 'class' => 'dropzone dropzone-manual']) ; ?>
<div class="row">

    <div class="col-md-12">
        <input type="hidden" name="expense_id" value="">
        <input type="hidden" name="debit" value="0">
        <input type="hidden" name="reference_note" value="0">
        <input type="hidden" name="centralized" value="1">
    </div>

    <!-- Staff (Required) -->
    <div class="col-md-4">
        <label for="staff_id"><?php echo _l('Staff'); ?></label>
        <select name="staff_id" id="staff_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Nothing selected" required>
            <option value=""></option>
            <?php foreach($vendors as $s) { ?>
            <option data-content="<?php echo pur_html_entity_decode($s['company']); ?>" value="<?php echo pur_html_entity_decode($s['userid']); ?>"><?php echo pur_html_entity_decode($s['company']); ?></option>
                <?php } ?>
        </select> 
    </div>

    <!-- Date (Required) -->
    <div class="col-md-4">
        <?php echo render_date_input('entry_date', 'Date', _d(date('Y-m-d')), ['required' => true]); ?>
    </div>

    <!-- Amount (Required) -->
    <div class="col-md-4">
        <?php echo render_input('credit', 'Amount', '', 'number', ['required' => true]); ?>
    </div>    

    <!-- <div class="col-md-12 text-right mb-3">
        <button type="button" id="show-advance" class="btn btn-link p-0" style="padding: 0; margin-bottom: 10px;">Advance</button>
    </div>     -->
</div>  

<div class="row" id="advance-fields">
    <!-- Mode -->
    <div class="col-md-4">
        <label for="payment_mode"><?php echo _l('Payment Mode'); ?></label>
        <select class="selectpicker form-control" name="payment_mode" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <option value=""></option>
            <?php foreach($payment_modes as $mode){ ?>
            <option value="<?php echo $mode['id']; ?>"><?php echo $mode['name']; ?></option>
            <?php } ?>
        </select> 
    </div>      

    <!-- Notes -->
    <div class="col-md-4">
        <label for="particular"><?php echo _l('Notes'); ?></label>
        <input type="text" name="particular" class="form-control" value=""> 
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
        
        // $("#show-advance").on("click", function() {
        //     $("#advance-fields").slideToggle();
        //     $(this).text($(this).text() === "Advance" ? "Hide Advance" : "Advance");
        // });        
    });
</script>