
<script>

	"use strict";

	var InvoiceServerParams={
		"products_filter": "[name='products_filter[]']",
		"routing_filter": "[name='routing_filter[]']",
		"status_filter": "[name='status_filter[]']",
	};
	var manufacturing_order_table = $('.table-manufacturing_order_table');
	initDataTable(manufacturing_order_table, admin_url+'manufacturing/manufacturing_order_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$.each(InvoiceServerParams, function(i, obj) {
		$('select' + obj).on('change', function() {  
			manufacturing_order_table.DataTable().ajax.reload()
			.columns.adjust()
			.responsive.recalc();
		});
	});

	var hidden_columns = [1];
	$('.table-manufacturing_order_table').DataTable().columns(hidden_columns).visible(false, false);

	function staff_bulk_actions(){
		"use strict";
		$('#manufacturing_order_table_bulk_actions').modal('show');
	}


	// Leads bulk action
	function mo_delete_bulk_action(event) {
		"use strict";

		if (confirm_delete()) {
			var mass_delete = $('#mass_delete').prop('checked');

			if(mass_delete == true){
				var ids = [];
				var data = {};

				data.mass_delete = true;
				data.rel_type = 'manufacturing_order';

				var rows = $('#table-manufacturing_order_table').find('tbody tr');
				$.each(rows, function() {
					var checkbox = $($(this).find('td').eq(0)).find('input');
					if (checkbox.prop('checked') === true) {
						ids.push(checkbox.val());
					}
				});

				data.ids = ids;
				$(event).addClass('disabled');
				setTimeout(function() {
					$.post(admin_url + 'manufacturing/mrp_product_delete_bulk_action', data).done(function() {
						window.location.reload();
					}).fail(function(data) {
						$('#manufacturing_order_table_bulk_actions').modal('hide');
						alert_float('danger', data.responseText);
					});
				}, 200);
			}else{
				window.location.reload();
			}

		}
	}

	// Handle manufacturing order delete with datatable refresh instead of page reload
	$('body').on('click', '.table-manufacturing_order_table ._delete', function(e) {
		"use strict";
		e.preventDefault();
		e.stopImmediatePropagation(); // Prevent the default _delete handler from running
		
		var deleteLink = $(this);
		var deleteUrl = deleteLink.attr('href');
		
		if (confirm_delete()) {
			$.ajax({
				url: deleteUrl,
				type: 'GET',
				success: function(response) {
					// Show success message
					alert_float('success', "<?php echo _l('mrp_deleted'); ?>");
					
					// Refresh the datatable
					if ($.fn.DataTable.isDataTable('.table-manufacturing_order_table')) {
						manufacturing_order_table.DataTable().ajax.reload(null, false);
					}
				},
				error: function(xhr) {
					// Try to parse response for error message
					var message = "<?php echo _l('problem_deleting'); ?>";
					try {
						if (xhr.responseText) {
							// Check if response contains referenced error
							if (xhr.responseText.indexOf('is_referenced') !== -1) {
								message = "<?php echo _l('problem_deleting'); ?>";
							}
						}
					} catch(e) {
						// Use default message
					}
					alert_float('warning', message);
					
					// Refresh the datatable even on error to ensure consistency
					if ($.fn.DataTable.isDataTable('.table-manufacturing_order_table')) {
						manufacturing_order_table.DataTable().ajax.reload(null, false);
					}
				}
			});
		}
		return false;
	});

</script>