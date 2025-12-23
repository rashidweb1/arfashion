<?php 

	error_reporting(E_ALL);
	ini_set('display_errors', 1);  

    $bom_production_inventory_id = $bom_production_inventory_id;

    $production_inventory = $this->db->select('*')->from('tblmrp_bom_production_inventory')->where('id', $bom_production_inventory_id)->get()->row_array();

    $production_inventory_logs = $this->db->select('*')->from('tblmrp_bom_production_inventory_logs')->where('bom_production_inventory_id', $bom_production_inventory_id)->order_by('id', 'desc')->get()->result_array();

    //var_dump($production_inventory_logs);

?>

<div class="modal fade" id="commonModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Receive Inventory</h4>
			</div>
			
			<?php echo form_open_multipart(admin_url('manufacturing/receive_production/'), array('id' => 'receive_production')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<?php /*
				    <div class="row">
						<?php if (!empty($production_inventory_logs)): ?>
							<div class="col-md-12">
							    <label for="qty_assigned"><?php echo _l('Logs'); ?></label>
							    <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;font-size: 12px;"> 
									<?php foreach ($production_inventory_logs as $log): ?>
										<p style="margin-bottom:3px"><strong>Date :</strong> <?php echo htmlspecialchars($log['created_at']); ?></p>
										<p style="margin-bottom:3px"><strong>Quantity Received :</strong> <?php echo htmlspecialchars($log['qty_received']); ?></p>
										<p style="margin-bottom:3px"><strong>Quantity Lost :</strong> <?php echo htmlspecialchars($log['qty_lost']); ?></p>
										<p style="margin-bottom:3px"><strong>Quantity Pending :</strong> <?php echo htmlspecialchars($log['qty_pending']); ?></p>
										<p style="margin-bottom:3px"><strong>Comment :</strong> <?php echo htmlspecialchars($log['comments']); ?></p>
										<p style="margin-bottom:3px"><strong>Status :</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $log['status']))); ?></p>
										<hr style="margin: 10px 0px;"> <!-- Separator for multiple log entries -->
									<?php endforeach; ?>
								</div>
							</div>
						<?php else: ?>
							<div class="col-md-12">
								<p>No logs available.</p>
							</div>
						<?php endif; ?>
						<div class="col-md-12">
					    	<hr> <!-- Separator for multiple log entries -->
						</div>
                    </div>
					*/ ?>

					<div class="row">
						<div class="col-md-12">
							<label for="qty_assigned"><?php echo _l('Logs'); ?></label>
							<?php if (!empty($production_inventory_logs)): ?>
								<div>
									<table class="table table-sm mb-0 customizable-table dataTable" style="font-size: 12px;margin-top: 10px;">
										<thead class="thead-light">
											<tr>
												<th width="5%">#</th>
												<th width="18%">Date</th>
												<th width="8%">Received</th>
												<th width="8%">Lost</th>
												<th width="8%">Pending</th>
												<th width="12%">Status</th>
												<th width="31%">Comment</th>
												
											</tr>
										</thead>
										<tbody>
											<?php $x = 0; foreach ($production_inventory_logs as $log): ?>
												<tr>
												    <td><?php echo htmlspecialchars(++$x); ?></td>
													<td><?php echo htmlspecialchars(date("d M Y H:iA", strtotime($log['created_at']))); ?></td>
													<td><?php echo htmlspecialchars($log['qty_received']); ?></td>
													<td><?php echo htmlspecialchars($log['qty_lost']); ?></td>
													<td><?php echo htmlspecialchars($log['qty_pending']); ?></td>
													<td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $log['status']))); ?></td>
													<td><?php echo htmlspecialchars($log['comments']); ?></td>
													
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php else: ?>
								<p class="text-muted">No logs available.</p>
							<?php endif; ?>
						</div>
					</div>					

					<div class="row"> 
						<input type="hidden" value="<?php echo $bom_production_inventory_id; ?>" name="bom_production_inventory_id">
						<input type="hidden" value="<?php echo $production_inventory['is_inventory']; ?>" name="is_inventory">
						<input type="hidden" value="<?php echo $production_inventory['manufacturing_order_id']; ?>" name="manufacturing_order_id">
						<input type="hidden" value="<?php echo $production_inventory['product_name']; ?>" name="product_name">
						<input type="hidden" value="<?php echo $production_inventory['comments']; ?>" name="comments">
						<input type="hidden" value="<?php echo ($production_inventory['manufacturing_order_id'] > 147) ? 'new' : 'old'; ?>" name="inventory_system">

						<div class="col-md-3 form-group"> 
							<label for="qty_assigned"><?php echo _l('Quantity Assinged'); ?></label>
							<input type="number" class="form-control" name="qty_assigned" value="<?php echo $production_inventory['qty_assigned']; ?>" required readonly>                       
						</div>

						<div class="col-md-3 form-group"> 
							<label for="deduct_price"><?php echo _l('Quantity Pending'); ?></label>
							<input type="number" class="form-control" name="qty_pending" data-qty-pending="<?php echo $production_inventory['qty_pending']; ?>" value="<?php echo $production_inventory['qty_pending']; ?>" min="1" required readonly>                       
						</div>	                        

						<div class="col-md-3 form-group"> 
							<label for="price"><?php echo _l('Quantity Received'); ?></label>
							<input type="number" class="form-control" name="qty_received" value="0" required>                       
						</div>

						<div class="col-md-3 form-group"> 
							<label for="deduct_price"><?php echo _l('Quantity Lost'); ?></label>
							<input type="number" class="form-control" name="qty_lost" value="0" required>                       
						</div>	                        

						<div class="col-md-12 form-group"> 
							<label for="comments"><?php echo _l('Comments'); ?></label>
							<textarea class="form-control" name="comments" id="comments" rows="3" required></textarea>                    
						</div>						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>