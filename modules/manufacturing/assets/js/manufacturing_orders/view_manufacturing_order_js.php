<script>
	var product_tabs;
	var data_color = <?php echo json_encode($data_color); ?>;

	(function($) {
		"use strict";  

		<?php if(isset($product_tab_details)){ ?>
			var dataObject_pu = <?php echo json_encode($product_tab_details) ; ?>;
		<?php }else{?>
			var dataObject_pu = [];
		<?php } ?>

		var hotElement1 = document.getElementById('product_tab_hs');

		product_tabs = new Handsontable(hotElement1, {
			licenseKey: 'non-commercial-and-evaluation',

			contextMenu: true,
			manualRowMove: true,
			manualColumnMove: true,
			stretchH: 'all',
			autoWrapRow: true,
			rowHeights: 30,
			defaultRowHeight: 100,
			minRows: 10,
			maxRows: 40,
			width: '100%',

			rowHeaders: true,
			cells: function(row, col, prop) {
				var cellProperties = {};
				if (col > 2) {
					cellProperties.renderer = firstRowRenderer; 
				}
				return cellProperties;
			},
			colHeaders: true,
			autoColumnSize: {
				samplingRatio: 23
			},

			filters: true,
			manualRowResize: true,
			manualColumnResize: true,
			allowInsertRow: true,
			allowRemoveRow: true,
			columnHeaderHeight: 40,

			rowHeights: 30,
			rowHeaderWidth: [44],
			minSpareRows: 1,
			hiddenColumns: {
				columns: [0],
				indicators: true
			},

			columns: [
			{
				type: 'text',
				data: 'id',
			},
			{
				type: 'text',
				data: 'product_id',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($product_for_hansometable); ?>
				},
			},
			{
				type: 'text',
				data: 'unit_id',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($unit_for_hansometable); ?>
				},
			},
			
			{
				data: 'qty_to_consume',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},
			{
				data: 'qty_to_consume_uc',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
				readOnly: true,
				renderer: function(instance, td, row, col, prop, value, cellProperties) {
					Handsontable.renderers.NumericRenderer.apply(this, arguments);
					td.style.fontSize = 'smaller';
					td.style.color = '#666';
				}
			},
			{
				data: 'qty_reserved',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},
			{
				data: 'qty_reserved_uc',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
				readOnly: true,
				renderer: function(instance, td, row, col, prop, value, cellProperties) {
					Handsontable.renderers.NumericRenderer.apply(this, arguments);
					td.style.fontSize = 'smaller';
					td.style.color = '#666';
				}
			},
			{
				data: 'qty_done',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},
			{
				data: 'qty_done_uc',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
				readOnly: true,
				renderer: function(instance, td, row, col, prop, value, cellProperties) {
					Handsontable.renderers.NumericRenderer.apply(this, arguments);
					td.style.fontSize = 'smaller';
					td.style.color = '#666';
				}
			},

			
			],

			colHeaders: [
				'<?php echo _l('id'); ?>',
				'<?php echo _l('product_label'); ?>',
				'<?php echo _l('unit_id'); ?>',
				'<?php echo _l('qty_to_consume'); ?>',
				'<?php echo _l('_'); ?>',
				'<?php echo _l('qty_reserved'); ?>',
				'<?php echo _l('_'); ?>',
				'<?php echo _l('qty_done'); ?>',
				'<?php echo _l('_'); ?>',
			],

			data: dataObject_pu,
		});


	})(jQuery);

	function firstRowRenderer(instance, td, row, col, prop, value, cellProperties) {
		
		"use strict";
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		td.style.background = '#fff';
		if(data_color[row] != undefined){
			td.style.color = data_color[row];
			td.className = 'htRight';

		}
	}

	function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		var selectedId;
		var optionsList = cellProperties.chosenOptions.data;

		if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
			Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
			return td;
		}

		var values = (value + "").split("|");
		value = [];
		for (var index = 0; index < optionsList.length; index++) {

			if (values.indexOf(optionsList[index].id + "") > -1) {
				selectedId = optionsList[index].id;
				value.push(optionsList[index].label);
			}
		}
		value = value.join(", ");

		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}

	
	$('.mark_as_todo').on('click', function() {
		"use strict";

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_todo/' + id+'/mark_as_todo', function (response) {
			if(response.status == 'warning'){
				alert_float(response.status, response.message, 5000);
				setTimeout(function(){ location.reload(); }, 5000);
			}else{
				alert_float(response.status, response.message);
				location.reload();
			}
		}, 'json');

	});

	$('.mark_check_availability').on('click', function() {
		"use strict";

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_todo/' + id+'/check_availability', function (response) {
			if(response.status == 'warning'){
				alert_float(response.status, response.message, 5000);
				setTimeout(function(){ location.reload(); }, 5000);
			}else{
				alert_float(response.status, response.message);
				location.reload();
			}
		}, 'json');

	});

	$('.mark_as_done').on('click', function() {

		"use strict";

		$('#show_detail').modal('show');

	});


	// $('.btn_mark_as_done').on('click', function() { //old code
	// 	"use strict";


	// 	var id = $("input[name='id']").val();
	// 	var quantity = $("input[name='change_product_qty']").val();

	// 	if(quantity != undefined && quantity != ''){

	// 		$('.mark_as_done').attr( "disabled", "disabled" );
	// 		$('.btn_mark_as_done').attr( "disabled", "disabled" );
	// 		$.get(admin_url + 'manufacturing/mo_mark_as_done/' + id + '/' + quantity, function (response) {
	// 			alert_float(response.status, response.message);

	// 			location.reload();
	// 		}, 'json');
	// 	}else{
	// 		alert_float('warning', '<?php echo _l('please_enter_quantity_produced'); ?>');
	// 	}

	// });

	$('.btn_mark_as_done').on('click', function () { //new code (chunk invetory add of manufacturing product)
		"use strict";

		var id = $("input[name='id']").val();
		var quantity = $("input[name='change_product_qty']").val();
		var maxQty = parseFloat($("input[name='change_product_qty']").attr('max'));
		var isFinal = $('#final_produced_checkbox').is(':checked') ? 1 : 0;
		var remaining_qty_produced = $('#remaining_qty_produced').val();

		// Convert quantity to number
		quantity = parseFloat(quantity);

		// Basic validation
		if (!isNaN(quantity) && quantity > 0 && quantity <= maxQty) {

			// Confirm before proceeding
			if (confirm('Are you sure you want to mark as done with quantity: ' + quantity + '?')) {

				$('.mark_as_done, .btn_mark_as_done').attr("disabled", "disabled");

				$.get(admin_url + 'manufacturing/mo_mark_as_done/' + id + '/' + quantity + '/' + isFinal + '/' + remaining_qty_produced, function (response) {
					alert_float(response.status, response.message);
					location.reload();
				}, 'json');
			}

		} else {
			alert_float('warning', 'Please enter a valid quantity (1 to ' + maxQty + ')');
		}
	});	

	$('.mark_as_planned').on('click', function() {
		"use strict";
		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_planned/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});

	$('.mark_as_unreserved').on('click', function() {
		"use strict";

		$('.mark_as_unreserved').attr( "disabled", "disabled" );

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_unreserved/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});
	
	$('.mark_as_cancel').on('click', function() {
		"use strict";

		if (!confirm("Are you sure you want to cancel this?")) {
			return false; // stop execution if user clicks Cancel
		}		

		$('.mark_as_cancel').attr( "disabled", "disabled" );

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_cancel/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});
	
	$('.mo_create_purchase_request').on('click', function() {
		"use strict";

		$('.mo_create_purchase_request').attr( "disabled", "disabled" );

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_create_purchase_request/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});

	var BomChangeLogParams={
		"manufacturing_order_id": "[name='manufacturing_order_id']",
	};
	var bom_change_log_table = $('.table-bom_change_log_table');
	initDataTable(bom_change_log_table, admin_url+'manufacturing/bom_change_log_table',[0],[0], BomChangeLogParams, [0 ,'desc']);
	//hide first column
	var hidden_columns = [0];
	$('.table-bom_change_log_table').DataTable().columns(hidden_columns).visible(false, false);
	

</script>

<script>
	function assign_production_modal(manufacturing_order_id) {
		"use strict";
		//alert(manufacturing_order_id);

		$("#modal_wrapper").load("<?php echo admin_url('manufacturing/assign_production_modal'); ?>", {
			manufacturing_order_id: manufacturing_order_id,
		}, function() {
			$("body").find('#commonModal').modal({ show: true, backdrop: 'static' });
			init_selectpicker();
			$("#vendor").selectpicker('refresh');			
		});
	}	

    $("body").on('click', '.delete-production-item', function() {
        if (confirm("Are you sure you want to delete this item?")) {
            $(this).closest("tr").remove();
        }
    });

    // Select/Deselect All
	$("body").on('click', '#select_all_rows', function() {
        $('.row_checkbox').prop('checked', $(this).prop('checked'));
    });

    // Delete Selected Rows
	$("body").on('click', '#bulk_delete_rows', function() {	
        var selected = $('.row_checkbox:checked');
        if (selected.length === 0) {
            alert('Please select at least one row to delete.');
            return;
        }

        if (confirm("Are you sure you want to delete selected items?")) {
            selected.each(function() {
                $(this).closest('tr').remove();
            });
        }
    });	
    
	$("body").on("input", "input[name='qty_assigned']", function() {
		let qtyAssigned = parseFloat($(this).val()) || 0; // Get assigned quantity, default to 0 if empty

		$("#assign-production-table tbody tr").each(function(index) {
			let perConsumptionText = $(this).find("td:eq(3)").text().trim(); // Get text and remove spaces
			let perConsumption = parseFloat(perConsumptionText.split(" ")[0]) || 0; // Extract numeric part only

			let assignedQty = perConsumption * qtyAssigned; // Calculate Assigned Quantity

			// Set value in Assign Quantity input field
			$(this).find("input[name='details[qty_assigned][]']").val(assignedQty);
		});

		console.log("Calculation completed.");
	});
     
	$("body").on("submit", "#create_production", function(e) { 
		e.preventDefault();
		var form = this;
		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).done(function (response) {
			//response = JSON.parse(response);
			if (response.success == true) {
				alert_float("success", response.message);
				$('#commonModal').modal('hide');
				setTimeout(function () {
		    		window.location.reload();
				}, 2000);				
			} else {
		    	alert_float("warning", response.message);
			}
		});	
	});

	//
	function receive_production_modal(bom_production_inventory_id) {
		"use strict";

		$("#modal_wrapper").load("<?php echo admin_url('manufacturing/receive_production_modal'); ?>", {
			bom_production_inventory_id: bom_production_inventory_id,
		}, function() {
			$("body").find('#commonModal').modal({ show: true, backdrop: 'static' });
		});

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');
	}


	$(document).on('input', '#receive_production input[name="qty_received"], #receive_production input[name="qty_lost"]', function () {
		let form = $(this).closest('#receive_production'); // Get the closest form

		let qtyAssigned = parseFloat(form.find('input[name="qty_assigned"]').val()) || 0;
		let qtyPendingOriginal = parseFloat(form.find('input[name="qty_pending"]').data('qty-pending')) || 0;
		let qtyReceived = parseFloat(form.find('input[name="qty_received"]').val()) || 0;
		let qtyLost = parseFloat(form.find('input[name="qty_lost"]').val()) || 0;

		// Calculate pending quantity
		//let qtyPendingNew = qtyAssigned - (qtyReceived + qtyLost);
		let qtyPendingNew = qtyPendingOriginal - (qtyReceived + qtyLost);
		//qtyPendingNew = qtyPendingNew < 0 ? 0 : qtyPendingNew; // Ensure it doesn't go negative

		// Update the pending quantity field
		form.find('input[name="qty_pending"]').val(qtyPendingNew);
	});

	$("body").on("submit", "#receive_production", function(e) { 
		e.preventDefault();
		var form = this;
		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).done(function (response) {
			//response = JSON.parse(response);
			if (response.success == true) {
			alert_float("success", response.message);
               if(response.is_inventory == 1) {
					setTimeout(function () {
						window.location.reload();
					}, 1000);
			   }else{
				    $('#change_product_qty').val(response.qty_received);
					$('#commonModal').modal('hide');
					$('#show_detail').modal('show');
			   }


			} else {
			alert_float("warning", response.message);
			}
		});	
	});	

	function edit_production_modal(id) {
		"use strict";
		//alert(manufacturing_order_id);

		$("#modal_wrapper").load("<?php echo admin_url('manufacturing/edit_production_modal'); ?>", {
			id: id,
		}, function() {
			$("body").find('#commonModal').modal({ show: true, backdrop: 'static' });
			init_selectpicker();
			$("#vendor").selectpicker('refresh');			
		});
	}

	$("body").on("submit", "#update_production", function(e) { 
		e.preventDefault();
		var form = this;
		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).done(function (response) {
			//response = JSON.parse(response);
			if (response.success == true) {
    			alert_float("success", response.message);
			} else {
    			alert_float("warning", response.message);
			}
			setTimeout(function () {
			window.location.reload();
			}, 1000);
		});	
	});	

	$("body").on("click", ".delete_production", function(e) {
		e.preventDefault();
		var target = $(this);
		var id = target.data("id");
		var url = target.data("url");

		if (confirm("Are you sure you want to delete this production record?")) {
			$.post(url, { id: id }).done(function (response) {
				if (response.success == true) {
					alert_float("success", response.message);
				} else {
					alert_float("warning", response.message);
				}

				setTimeout(function () {
					//window.location.reload();
				}, 1000);
			});
		}
	});	
</script>