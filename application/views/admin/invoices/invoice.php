<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), ['id' => 'invoice-form', 'class' => '_transaction_form invoice-form']);
            if (isset($invoice)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e(isset($invoice) ? format_invoice_number($invoice) : _l('create_new_invoice')); ?>
                    </span>
                    <?php echo isset($invoice) ? format_invoice_status($invoice->status) : ''; ?>
                </h4>
                <?php $this->load->view('admin/invoices/invoice_template'); ?>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view('admin/invoice_items/item'); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    validate_invoice_form();
    // Init accountacy currency symbol
    init_currency();
    // Project ajax search
    init_ajax_project_search_by_customer_id();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
});
</script>

<!-- PO Module -->
<script>
$(document).ready(function () {
    const invoiceId = "<?php echo $this->uri->segment('4'); ?>";
    if (invoiceId) {
        $('input[name="custom_fields[invoice][4]"]').attr('readonly', true);
        $('input[name="custom_fields[invoice][5]"]').attr('readonly', true);
        return; // Exit the script if an ID value exists
    }	

    // Example values — replace with actual values passed from PHP (use PHP to output JS safely)
    var proposal_id = "<?php echo $this->input->get('proposal_id'); ?>";
    var manufacturing_order_code = "<?php echo $this->input->get('manufacturing_order_code'); ?>";
    var proposal_to = "<?php echo $this->input->get('proposal_to'); ?>";

    if (!proposal_id) {
        return; // Exit the script if an ID value exists
    }	

    // Set values
    $('input[name="custom_fields[invoice][4]"]').val(proposal_id).attr('readonly', true);
    $('input[name="custom_fields[invoice][5]"]').val(manufacturing_order_code).attr('readonly', true);
    setTimeout(function () { $('#clientid').parents('.bootstrap-select').find('button.dropdown-toggle').trigger('click'); }, 500);
    setTimeout(function () { var $searchBox = $('#clientid').parents('.bootstrap-select').find('input'); $searchBox.focus().val(proposal_to).trigger('keyup');  }, 1000);
    setTimeout(function () { $('.dropdown-menu.inner li a').first().trigger('click');  }, 3000);
    
});
</script>
<script>
// Store PHP value in JS variable
var allowedIIDs = "<?php echo $this->input->get('iid'); ?>".split(',').map(function(id) {
    return id.trim();
});

$(document).ready(function () {
    $('#item_select option').each(function () {
        var optionVal = $(this).val();
        if (optionVal === '' || allowedIIDs.includes(optionVal)) {
            $(this).show();
        } else {
            $(this).hide(); // Or use .hide() if preferred
        }
    });

    $('#item_select').selectpicker('refresh');
});
</script>
</body>

</html>