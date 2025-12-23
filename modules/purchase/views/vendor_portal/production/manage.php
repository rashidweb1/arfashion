<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				<table class="table dt-table-custom table-small scroll-responsive" data-order-type="desc">
					<thead>
						<tr>
							<th>Sr No.</th>
							<th>Reference</th>
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
							$sr_no = 1; 
							$finishProductFinalQty = null;
							foreach ($production_inventory as $item) : 
								if($item['is_inventory'] == 0 && $item['status'] == 'completed'){
									$finishProductFinalQty += $item['qty_received'];
								}
						?>
						<tr>
							<td><?php echo $sr_no++; ?></td>
							<td>
								<?php echo $item['manufacturing_order_code']; ?>
							</td>
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
								<a href="<?php echo site_url('purchase/vendors_portal/production_detail/' . $item['manufacturing_order_id']); ?>" class="btn btn-info btn-xs">
									<?php echo _l('Detail'); ?>
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>

<style>
.table-small {
	font-size: 12px !important;
}
</style>

<script>
jQuery(function () {
	$('.dt-table-custom').DataTable({
		paging: true,
		pageLength: 25,
		order: [[0, 'asc']]
	});
});
</script>

