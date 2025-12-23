<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				<?php /*
				<a href="<?php echo site_url('purchase/vendors_portal/add_update_invoice'); ?>" class="btn btn-info"><?php echo _l('add_new'); ?></a>
				<br><br>
                */ ?>
				<table class="table dt-table-custom">
			       <thead>
			       	<th><?php echo _l('invoice_code'); ?></th>
			       	<th><?php echo _l('invoice_no'); ?></th>
						<?php /*
						<th><?php echo _l('contract'); ?></th>
						<th><?php echo _l('pur_order'); ?></th>
						*/ ?>
			          <th><?php echo _l('invoice_date'); ?></th>
			          <th><?php echo _l('invoice_amount'); ?></th>
			          <!--<th><?php echo _l('tax_value'); ?></th>-->
			          <th><?php echo _l('Paid Amount'); ?></th>
			          <th><?php echo _l('Remaining Amount'); ?></th>
			          <!--<th><?php echo _l('total_included_tax'); ?></th>-->
			          <th><?php echo _l('payment_status'); ?></th>
					  <?php /*
			          <th><?php echo _l('options'); ?></th>
					  */ ?>
			       </thead>
			      <tbody>
			         <?php foreach($invoices as $inv) { ?>
			         	<?php 
			         		$base_currency = get_base_currency_pur(); 
			         		if($inv['currency'] != 0){
			         			$base_currency = pur_get_currency_by_id($inv['currency']);
			         		}
			         	?>
			         <tr class="inv_tr">
			         	<td class="inv_tr"><a href="<?php echo site_url('purchase/vendors_portal/invoice/'.$inv['id']); ?>"><?php echo pur_html_entity_decode($inv['invoice_number']); ?></a></td>
			         	<td class="inv_tr"><?php 
			         		$vendor_invoice_number = ($inv['vendor_invoice_number'] != '' ? $inv['vendor_invoice_number'] : $inv['invoice_number']);
			         		echo pur_html_entity_decode($vendor_invoice_number); 
			         	?></td>
						<?php /*
			         	<td class="inv_tr"><?php echo get_pur_contract_number($inv['contract'],''); ?></td>
			         	<td class="inv_tr"><?php echo get_pur_order_subject($inv['pur_order']); ?></td>
						*/ ?>
			         	<td class="inv_tr"><?php echo '<span class="label label-info">'._d($inv['invoice_date']).'</span>'; ?></td>
			         	<td class="inv_tr"><?php echo app_format_money($inv['subtotal'],$base_currency->symbol); ?></td>
			         	<!--<td class="inv_tr"><?php echo app_format_money($inv['tax'],$base_currency->symbol); ?></td>-->
			         	<td class="inv_tr">
							<?php 
								$paid_amount = $inv['total'] - purinvoice_left_to_pay($inv['id']);
								echo app_format_money($paid_amount, $base_currency->symbol);
							?>
						</td>
						<td class="inv_tr">
							<?php 
								$remaining_amount = purinvoice_left_to_pay($inv['id']);
								echo app_format_money($remaining_amount, $base_currency->symbol);
							?>
						</td>
			         	<!--<td class="inv_tr"><?php echo app_format_money($inv['total'],$base_currency->symbol); ?></td>-->
			         	<td class="inv_tr"><?php 
			         	$class = '';
			            if($inv['payment_status'] == 'unpaid'){
			                $class = 'danger';
			            }elseif($inv['payment_status'] == 'paid'){
			                $class = 'success';
			            }elseif ($inv['payment_status'] == 'partially_paid') {
			                $class = 'warning';
			            }

			            echo  '<span class="label label-'.$class.' s-status invoice-status-3">'._l($inv['payment_status']).'</span>';

			         	?>
			         	</td>
						<?php /*
			         	<td>
			         		<a href="<?php echo site_url('purchase/vendors_portal/add_update_invoice/'.$inv['id']); ?>" class="btn btn-warning btn-icon"><i class="fa fa-pencil"></i></a>
			         		<a href="<?php echo site_url('purchase/vendors_portal/delete_invoice/'.$inv['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
			         	</td>
						*/ ?>
			         </tr>
			         <?php } ?>
			      </tbody>
					<tfoot>
						<tr>
							<th colspan="3" class="text-right"><?php echo _l('total'); ?></th>
							<th id="total_invoice_amount"></th>
							<!--<th id="total_tax_value"></th>-->
							<th id="total_paid_amount"></th>
							<th id="total_remaining_amount"></th>
							<!--<th id="total_total_included_tax"></th>-->
							<th></th>
						</tr>
					</tfoot>				  
			   </table>	
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>

<script>
jQuery(function () {
    let table = $('.dt-table-custom').DataTable({
        paging: false // Disable pagination
    });

    table.on('draw', function () {
        let invoiceAmountTotal = 0;
        // let taxValueTotal = 0;
        let paidAmountTotal = 0;
        let remainingAmountTotal = 0;
        // let totalIncludedTaxTotal = 0;

        table.rows({ page: 'current' }).every(function () {
            let row = $(this.node());

            let amount = row.find('td:eq(3)').text().replace(/[^0-9.-]+/g, '');
            // let tax = row.find('td:eq(4)').text().replace(/[^0-9.-]+/g, '');
            let paid = parseFloat(row.find('td:eq(4)').text().replace(/[^0-9.-]+/g, '')) || 0;
            let remaining = parseFloat(row.find('td:eq(5)').text().replace(/[^0-9.-]+/g, '')) || 0;
            // let total = row.find('td:eq(6)').text().replace(/[^0-9.-]+/g, '');

            invoiceAmountTotal += parseFloat(amount) || 0;
            // taxValueTotal += parseFloat(tax) || 0;
            paidAmountTotal += paid;
            remainingAmountTotal += remaining;
            // totalIncludedTaxTotal += parseFloat(total) || 0;
        });

        const formatMoney = (val) => {
            return Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(val);
        };

        $('#total_invoice_amount').text(formatMoney(invoiceAmountTotal));
        // $('#total_tax_value').text(formatMoney(taxValueTotal));
        $('#total_paid_amount').text(formatMoney(paidAmountTotal));
        $('#total_remaining_amount').text(formatMoney(remainingAmountTotal));
        // $('#total_total_included_tax').text(formatMoney(totalIncludedTaxTotal));
    });

    // Trigger initial draw
    table.draw();
});
</script>