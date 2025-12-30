<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div class="panel-table-full">
                    <?php $this->load->view('admin/payments/table_html'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    var paymentsTable = initDataTable('.table-payments', admin_url + 'payments/table', undefined, undefined, 'undefined',
        <?php echo hooks()->apply_filters('payments_table_default_order', json_encode([0, 'desc'])); ?>);

    // Handle payment delete with datatable refresh instead of page reload
    // Attach handler directly to table element (not body) so it runs before global handler
    // This ensures our handler processes the event before it bubbles to body
    var $paymentsTable = $('.table-payments');
    $paymentsTable.off('click', '._delete').on('click', '._delete', function(e) {
        // Stop all event propagation immediately to prevent global handler on body
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();
        
        var deleteLink = $(this);
        var deleteUrl = deleteLink.attr('href');
        
        // Show confirmation dialog (only once, since we stopped propagation)
        if (confirm_delete()) {
            $.ajax({
                url: deleteUrl,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Handle both JSON and HTML responses
                    var jsonResponse = response;
                    if (typeof response === 'string') {
                        try {
                            jsonResponse = JSON.parse(response);
                        } catch(e) {
                            // If response is HTML, assume success and refresh table
                            alert_float('success', '<?php echo _l('deleted', _l('payment')); ?>');
                            if ($.fn.DataTable.isDataTable('.table-payments')) {
                                $('.table-payments').DataTable().ajax.reload(null, false);
                            }
                            return;
                        }
                    }
                    
                    // Show success/error message
                    if (jsonResponse && jsonResponse.success) {
                        alert_float('success', jsonResponse.message || '<?php echo _l('deleted', _l('payment')); ?>');
                    } else {
                        alert_float('warning', (jsonResponse && jsonResponse.message) ? jsonResponse.message : '<?php echo _l('problem_deleting', _l('payment_lowercase')); ?>');
                    }
                    
                    // Refresh the datatable
                    if ($.fn.DataTable.isDataTable('.table-payments')) {
                        $('.table-payments').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    // Try to parse JSON response, fallback to text
                    var message = 'Failed to delete payment';
                    try {
                        if (xhr.responseText) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                message = response.message;
                            }
                        }
                    } catch(e) {
                        // If not JSON, use default message
                    }
                    alert_float('danger', message);
                    
                    // Refresh the datatable even on error to ensure consistency
                    if ($.fn.DataTable.isDataTable('.table-payments')) {
                        $('.table-payments').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
        return false;
    });
});
</script>
</body>

</html>