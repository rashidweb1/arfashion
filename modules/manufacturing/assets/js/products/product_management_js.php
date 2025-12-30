<script>
	$(function(){
		'use strict';
		var ProposalServerParams = {
			"item_filter": "[name='item_filter[]']",
			"product_type_filter": "[name='product_type_filter[]']",
			"product_category_filter": "[name='product_category_filter[]']",
			"can_be_value_filter": "[name='can_be_value_filter[]']",
			
		};

		var product_table = $('table.table-product_table');
		var _table_api = initDataTable(product_table, admin_url+'manufacturing/product_table', [0], [0], ProposalServerParams,  [1, 'desc']);
		$.each(ProposalServerParams, function(i, obj) {
			$('select' + obj).on('change', function() {  
				product_table.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
		});
	});

	function staff_bulk_actions(){
		"use strict";
		$('#product_table_bulk_actions').modal('show');
	}


	// Leads bulk action
	function warehouse_delete_bulk_action(event) {
		"use strict";

		if (confirm_delete()) {
			var mass_delete = $('#mass_delete').prop('checked');

			if(mass_delete == true){
				var ids = [];
				var data = {};

				data.mass_delete = true;
				data.rel_type = 'commodity_list';

				var rows = $('#table-product_table').find('tbody tr');
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
						$('#product_table_bulk_actions').modal('hide');
						alert_float('danger', data.responseText);
					});
				}, 200);
			}else{
				window.location.reload();
			}

		}
	}

	function print_barcode_option(invoker) {
		"use strict";
		var data={};
		data.profit_rate_by_purchase_price_sale = invoker.value;

		if(invoker.value == 1){
			$('.display-select-item').removeClass('hide');
		}else if(invoker.value == 0){
			$('.display-select-item').addClass('hide');
		}
	}


	/*print barcode*/
	function print_barcode_bulk_actions(){
		"use strict";
		$('.display-select-item').addClass('hide');
		$("#y_opt_1_").prop("checked", true);

		$("#table_commodity_list_print_barcode option:selected").prop("selected", false).change()
		$("table_commodity_list_print_barcode select[id='item_select_print_barcode']").selectpicker('refresh');

		$('#table_commodity_list_print_barcode').modal('show');
	}

	// Handle product delete with datatable refresh instead of page reload
	$('body').on('click', '.product-delete', function(e) {
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
					if ($.fn.DataTable.isDataTable('.table-product_table')) {
						$('.table-product_table').DataTable().ajax.reload(null, false);
					}
				},
				error: function(xhr) {
					// Try to parse response for error message
					var message = "<?php echo _l('problem_deleting'); ?>";
					try {
						if (xhr.responseText) {
							// Check if response contains referenced error
							if (xhr.responseText.indexOf('is_referenced') !== -1) {
								message = "<?php echo _l('is_referenced', _l('commodity')); ?>";
							}
						}
					} catch(e) {
						// Use default message
					}
					alert_float('warning', message);
					
					// Refresh the datatable even on error to ensure consistency
					if ($.fn.DataTable.isDataTable('.table-product_table')) {
						$('.table-product_table').DataTable().ajax.reload(null, false);
					}
				}
			});
		}
		return false;
	});

</script>