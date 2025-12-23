<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table id="list-production-table" border="1" width="100%" cellpadding="5" cellspacing="0" class="table customizable-table dataTable no-footer">
								<thead>
									<tr>
										<th>Sr No.</th>
										<th>Vendor</th>
										<th>Name</th>
										<th>Quantity</th>
										<th>Description</th>
										<th>Type</th>
										<th>Status</th>
										<th>Options</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$finishProductFinalQty = null;
										$sr_no = 1; 
										foreach ($production_inventory as $item) : 
											if($item['is_inventory'] == 0 && $item['status'] == 'completed'){
												$finishProductFinalQty += $item['qty_received'];
											}
									?>
									<tr>
										<td><?php echo $sr_no++; ?></td>
										<td><?php echo $item['company']; ?></td>
										<td><?php echo $item['product_name']; ?></td>
										<td><?php echo $item['qty_received'].'/'.$item['qty_assigned']; ?></td>
										<td><?php echo $item['comments']; ?></td>
										<td><?php echo $item['is_inventory'] ? "Inventory" : "Finish Product"; ?></td>
										<td>
											<?php 
												$statusClasses = [
													'pending'     => 'danger',
													'in_progress' => 'primary',
													'completed'   => 'success',
													'cancelled'   => 'warning'
												];

												$class = $statusClasses[$item['status']] ?? 'secondary';
											?>
											<span class="label label-<?php echo $class ?>"><?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?></span>
										</td>
										<td>
											<a target="_blank" href="<?php echo admin_url('manufacturing/receipt_production/' . $item['id']); ?>?sr_no=<?php echo $sr_no - 1 ?>">
												<?php echo _l('Receipt'); ?>
											</a>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="row mtop20">
					<div class="col-md-12">
						<a href="<?php echo site_url('purchase/vendors_portal/production'); ?>" class="btn btn-default">
							<?php echo _l('back'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>

<style>
.table-small {
	font-size: 12px !important;
}
.customizable-table {
	border-collapse: collapse;
}
.customizable-table th,
.customizable-table td {
	border: 1px solid #ddd;
	padding: 8px;
}
</style>

<script>
jQuery(function () {
	$('#list-production-table').DataTable({
		paging: true,
		pageLength: 25,
		order: [[0, 'asc']],
		responsive: true
	});
});
</script>

