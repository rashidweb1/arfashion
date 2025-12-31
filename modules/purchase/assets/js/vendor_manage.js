(function($) {
	"use strict";  
	var table_vendor = $('.table-vendors');

    var VendorsServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          VendorsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       VendorsServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

	var tAPI = initDataTable('.table-vendors', admin_url+'purchase/table_vendor',[0], [0],VendorsServerParams,  [1, 'desc']);
	$('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });

	// Handle vendor delete with datatable refresh instead of page reload
	$('.table-vendors').off('click', '._delete').on('click', '._delete', function(e) {
		"use strict";
		e.preventDefault();
		e.stopImmediatePropagation();
		e.stopPropagation();
		
		var deleteLink = $(this);
		var deleteUrl = deleteLink.attr('href');
		
		if (confirm_delete()) {
			$.ajax({
				url: deleteUrl,
				type: 'GET',
				success: function(response) {
					// Show success message
					alert_float('success', 'Vendor deleted successfully');
					
					// Refresh the datatable
					if ($.fn.DataTable.isDataTable('.table-vendors')) {
						tAPI.ajax.reload(null, false);
					}
				},
				error: function(xhr) {
					// Try to parse response for error message
					var message = 'Problem deleting vendor';
					try {
						if (xhr.responseText) {
							// Check if response contains referenced error
							if (xhr.responseText.indexOf('is_referenced') !== -1 || xhr.responseText.indexOf('referenced') !== -1) {
								message = 'Cannot delete vendor: vendor is referenced in other records';
							}
						}
					} catch(e) {
						// Use default message
					}
					alert_float('warning', message);
					
					// Refresh the datatable even on error to ensure consistency
					if ($.fn.DataTable.isDataTable('.table-vendors')) {
						tAPI.ajax.reload(null, false);
					}
				}
			});
		}
		return false;
	});

})(jQuery);

function staff_bulk_actions(){
	"use strict";
	$('#table_vendors_list_bulk_actions').modal('show');
}

function purchase_delete_bulk_action(event) {
	"use strict";

	if (confirm_delete()) {
		var mass_delete = $('#mass_delete').prop('checked');

		if(mass_delete == true){
			var ids = [];
			var data = {};

			data.mass_delete = true;
			data.rel_type = 'vendors';

			var rows = $('.table-vendors').find('tbody tr');
			$.each(rows, function() {
				var checkbox = $($(this).find('td').eq(0)).find('input');
				if (checkbox.prop('checked') === true) {
					ids.push(checkbox.val());
				}
			});

			data.ids = ids;
			$(event).addClass('disabled');
			setTimeout(function() {
				$.post(admin_url + 'purchase/purchase_delete_bulk_action', data).done(function() {
					window.location.reload();
				}).fail(function(data) {
					$('#table_vendors_list_bulk_actions').modal('hide');
					alert_float('danger', data.responseText);
				});
			}, 200);
		}else{
			window.location.reload();
		}

	}
}

function filterVendorCategory(catId) {
  const selectedId = 'vendor_category_' + catId;

  // 1. Clear all filters at once (pass empty view)
  dt_custom_view('', '.table-vendors', '');

  // 2. Apply selected filter
  dt_custom_view(selectedId, '.table-vendors', selectedId);
}
