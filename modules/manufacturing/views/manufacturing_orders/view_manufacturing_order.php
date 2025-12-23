<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
			$id = '';
			$title = '';
			$title .= _l('view_manufacturing_order_lable');

			?>

			<?php
			    //inventory produced logs
				$this->db->select('gr.id as goods_receipt_id, gr.goods_receipt_code, gtd.quantity, gtd.date_add');
				$this->db->from(db_prefix().'goods_transaction_detail as gtd');
				$this->db->join(db_prefix().'goods_receipt as gr', 'gr.id = gtd.goods_receipt_id', 'left');
				$this->db->where('gr.manufacturing_order_id', $manufacturing_order->id);
				$this->db->where('gtd.commodity_id', $manufacturing_order->product_id);
				$this->db->order_by('gr.id', 'desc');
				$transactions = $this->db->get()->result();

				$quantity_produced = 0;
			?>			

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<!-- action related work order -->
						<div class="row">
							<div class="col-md-12">
								<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit') ){ ?>
									<?php 
									$check_availability_status = true;
									 ?>
									<?php if($check_availability && $manufacturing_order->status != 'draft'){ ?>
										<button type="button" class="label-planned btn btn-success pull-left mark_check_availability mright5"><?php echo _l('mark_as_check_availability'); ?></button>
										<?php 
										$check_availability_status = false;
										 ?>
									<?php } ?>

									<?php if($manufacturing_order->status == 'draft'){ ?>
										<button type="button" class="label-confirmed  btn btn-info pull-left mark_as_todo mright5"><?php echo _l('mark_as_todo'); ?></button>
									<?php } ?>
										
									<?php if($manufacturing_order->status == 'confirmed' && $check_planned){ ?>
										<button type="button" class="label-planned btn btn-success pull-left mark_as_planned mright5"><?php echo _l('mark_as_planned'); ?></button>
									<?php } ?>

									<?php if($manufacturing_order->status == 'confirmed'){ ?>
										<button type="button" class="label-warning btn btn-success pull-left mark_as_unreserved mright5"><?php echo _l('mark_as_unreserved'); ?></button>
									<?php } ?>
										
									<?php if($check_mark_done && $manufacturing_order->status == 'in_progress' && $check_availability_status ){ ?>
										<button type="button" class="btn btn-success pull-left mark_as_done mright5"><?php echo _l('mark_as_done'); ?></button>
									<?php } ?>

									<?php if(($check_create_purchase_request && $manufacturing_order->status != 'draft') || (!$pur_order_exist) ){ ?>
										<button type="button" class="btn btn-success pull-left mo_create_purchase_request mright5" data-toggle="tooltip" title="" data-original-title="<?php echo _l('create_purchase_request_title'); ?>"><?php echo _l('mo_create_purchase_request'); ?> <i class="fa fa-question-circle i_tooltip" ></i></button>
									<?php } ?>
									
									<?php if($manufacturing_order->status != 'cancelled' && $manufacturing_order->status != 'done'){ ?>
										<button type="button" class="btn btn-default pull-left mark_as_cancel mright5"><?php echo _l('mrp_cancel'); ?></button>
									<?php } ?>

									<?php if($manufacturing_order->status == 'planned' || $manufacturing_order->status == 'in_progress' || $manufacturing_order->status == 'done' ){ ?>
										
										<a href="<?php echo admin_url('manufacturing/mo_work_order_manage/'.$manufacturing_order->id); ?>" class="btn btn-warning pull-right display-block mright5"><i class="fa fa-play-circle-o"></i> <?php echo _l('mrp_work_orders'); ?></a>

									<?php } ?>


									<?php } ?>
							</div>
						</div>
						<br>
						<!-- action related work order -->

						<div class="row mb-5">
							<div class="col-md-5">
								<h4 class="no-margin"><?php echo new_html_entity_decode($manufacturing_order->manufacturing_order_code); ?> 
							</div>
						</div>
						<hr class="hr-color no-margin">

						<!-- start tab -->
						<div class="modal-body">
							<div class="tab-content">
								<!-- start general infor -->
								<?php 

								$id = isset($manufacturing_order) ? $manufacturing_order->id : '';
								$product_id = isset($manufacturing_order) ? $manufacturing_order->product_id : '';
								$product_qty = isset($manufacturing_order) ? $manufacturing_order->product_qty : 1;
								$unit_id = isset($manufacturing_order) ? $manufacturing_order->unit_id : '';
								$manufacturing_order_code = isset($manufacturing_order) ? $manufacturing_order->manufacturing_order_code : '';
								$staff_id = isset($manufacturing_order) ? $manufacturing_order->staff_id : '';
								$bom_id = isset($manufacturing_order) ? $manufacturing_order->bom_id : '';
								$routing_id = isset($manufacturing_order) ? $manufacturing_order->routing_id : '';
								$components_warehouse_id = isset($manufacturing_order) ? $manufacturing_order->components_warehouse_id : '';
								$finished_products_warehouse_id = isset($manufacturing_order) ? $manufacturing_order->finished_products_warehouse_id : '';
								$date_deadline = isset($manufacturing_order) ? _dt($manufacturing_order->date_deadline) : '';
								$date_plan_from = isset($manufacturing_order) ? _dt($manufacturing_order->date_plan_from) : '';
								$routing_id_view = isset($manufacturing_order) ? mrp_get_routing_name($manufacturing_order->routing_id) : '';
								$routing_id = isset($manufacturing_order) ? ($manufacturing_order->routing_id) : '';
								$status = isset($manufacturing_order) ? ($manufacturing_order->status) : '';
								$reference_purchase_request = isset($manufacturing_order) ? ($manufacturing_order->purchase_request_id) : '';
								$proposal_id = $manufacturing_order->proposal_id; //PO Module
								$proposal_to = $this->db->select('proposal_to')->from(db_prefix() . 'proposals')->where(['id' => $proposal_id])->get()->row('proposal_to'); //PO Module
								$iids = $this->db->select('GROUP_CONCAT(iid) as iids')->from(db_prefix() . 'itemable')->where(['rel_type' => 'proposal', 'rel_id' => $proposal_id])->get()->row('iids'); //PO Module

								$components_warehouse_name='';
								$finished_products_warehouse_name= mrp_get_warehouse_name($finished_products_warehouse_id);
								if($components_warehouse_id != ''){
									$components_warehouse_name .= mrp_get_warehouse_name($components_warehouse_id);
								}else{
									$components_warehouse_name .= _l('mrp_all');
								}

								$date_planned_start = '';
								if(isset($manufacturing_order) && $manufacturing_order->date_planned_start != null && $manufacturing_order->date_planned_start != ''){

									$date_planned_start = _dt($manufacturing_order->date_planned_start).' '._l('mrp_to').' '. _dt($manufacturing_order->date_planned_finished);
								};
								?>
								<div class="row">
									<div class="col-md-6 panel-padding" >
										<input type="hidden" name="id" value="<?php echo new_html_entity_decode($id) ?>">

										<table class="table border table-striped table-margintop" >
											<tbody>
												<?php if ($proposal_id): ?> <!-- PO Module -->
													<tr>
													    <td class="bold td-width"><?php echo _l('PO Number'); ?></td>
														<td>
															<a href="<?php echo admin_url('proposals/list_proposals/' . $proposal_id); ?>" target="_blank">
																<?php echo get_proposal_po_format($proposal_id); ?>
															</a>
														</td>													
													</tr>
												<?php endif; ?>
												<tr class="project-overview">
													<td class="bold td-width"><?php echo _l('product_label'); ?></td>
													<td><?php echo mrp_get_product_name($product_id) ; ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('unit_of_measure'); ?></td>
													<td><?php echo mrp_get_unit_name($unit_id) ; ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('product_qty'); ?></td>
													<td><?php echo new_html_entity_decode($product_qty)  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('bill_of_material_label'); ?></td>
													<td><?php echo mrp_get_product_name(mrp_get_bill_of_material($bom_id))  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('routing_label'); ?></td>
													<td><?php echo mrp_get_routing_name($routing_id)  ?></td>
												</tr>
												

											</tbody>
										</table>
									</div>

									<div class="col-md-6 panel-padding" >
										<table class="table table-striped table-margintop">
											<tbody>
												<tr class="project-overview">
													<td class="bold" width="40%"><?php echo _l('date_deadline'); ?></td>
													<td><?php echo new_html_entity_decode($date_deadline)  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('date_plan_from'); ?></td>
													<td><?php echo new_html_entity_decode($date_plan_from)  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('planned_date'); ?></td>
													<td><?php echo new_html_entity_decode($date_planned_start)  ?></td>
												</tr>
												

												<tr class="project-overview">
													<td class="bold"><?php echo _l('responsible'); ?></td>
													<td><?php echo new_html_entity_decode(get_staff_full_name($staff_id))  ?></td>
												</tr>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('status'); ?></td>
													<td><span class="label label-<?php echo  new_html_entity_decode($status) ?>" ><?php echo _l($status); ?></span></td>
												</tr>

												<?php if($reference_purchase_request != ''){ ?>
													<tr class="project-overview">
														<td class="bold"><?php echo _l('reference_purchase_request'); ?></td>
														<td><a href="<?php echo admin_url('purchase/view_pur_request/'.$reference_purchase_request) ?>" ><?php echo mrp_purchase_request_code($reference_purchase_request) ?></a></td>
													</tr>
												<?php } ?>
												 
												<?php if($status == 'done'): //PO Module ?>
												<tr class="project-overview">
													<td class="bold"><?php echo _l('Make Sale Invoice'); ?></td>
													<td>
												    	<a target="_blank" href="<?php echo admin_url('invoices/invoice?proposal_id='.$proposal_id.'&iid='.$iids.'&manufacturing_order_code='.$manufacturing_order_code.'&proposal_to='.$proposal_to) ?>" >Create Invoice</a>
												    	<!-- <a target="_blank" href="<?php echo admin_url('invoices/invoice?proposal_id='.$proposal_id.'&iid='.$product_id) ?>" >Create Invoice</a> -->
													</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>

								</div>


								<div class="row">
									<h5 class="h5-color"><?php echo _l('work_center_info'); ?></h5>
									<hr class="hr-color">
								</div>

								<div class="row">
									<div class="horizontal-scrollable-tabs preview-tabs-top">
										<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
										<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
										<div class="horizontal-tabs">
											<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
												<li role="presentation" class="active">
													<a href="#component_tab" aria-controls="component_tab" role="tab" data-toggle="tab">
														<span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('tab_component_tab'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#finished_product_tab" aria-controls="finished_product_tab" role="tab" data-toggle="tab">
														<span class="fa fa-cogs menu-icon"></span>&nbsp;<?php echo _l('finished_product_tab'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#miscellaneous_tab" aria-controls="miscellaneous_tab" role="tab" data-toggle="tab">
														<span class="fa fa-balance-scale menu-icon"></span>&nbsp;<?php echo _l('miscellaneous_tab'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#costing" aria-controls="costing" role="tab" data-toggle="tab">
														<span class="fa-solid fa-coins"></span>&nbsp;<?php echo _l('costing'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#bom_changes_logs_tab" aria-controls="bom_changes_logs_tab" role="tab" data-toggle="tab">
														<span class="fa-regular fa-clock"></span>&nbsp;<?php echo _l('mrp_bom_changes_logs'); ?>
													</a>
												</li>
												<li role="presentation" class="">
													<a href="#inventory_produced_logs_tab" aria-controls="inventory_produced_logs_tab" role="tab" data-toggle="tab">
														<span class="fa-regular fa-clock"></span>&nbsp;<?php echo _l('Inventory Produced Logs'); ?>
													</a>
												</li>		
												<?php if(in_array($status, ['in_progress', 'done'])): ?>										
												<li role="presentation" class="">
													<a href="#inventory_production_tab" aria-controls="inventory_production_tab" role="tab" data-toggle="tab">
														<span class="fa-solid fa-industry"></span>&nbsp;<?php echo _l('Production'); ?>
													</a>
												</li>
												<?php endif; ?>
											</ul>
										</div>
									</div>
									<br>


									<div class="tab-content active">
										<div role="tabpanel" class="tab-pane active" id="component_tab">
											<div class="form"> 
												<div id="product_tab_hs" class="product_tab handsontable htColumnHeaders">
												</div>
												<?php echo form_hidden('product_tab_hs'); ?>
											</div>

										</div>
										<div role="tabpanel" class="tab-pane" id="finished_product_tab">
											<?php echo _l('Use_the_Produce_button_or_process_the_work_orders_to_create_some_finished_products'); ?>
										</div>
										<div role="tabpanel" class="tab-pane " id="miscellaneous_tab">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('components_warehouse'); ?></td>
																<td><?php echo new_html_entity_decode($components_warehouse_name)  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('finished_products_warehouse'); ?></td>
																<td><?php echo new_html_entity_decode($finished_products_warehouse_name)  ?></td>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="costing">
											<div class="row">
												<div class="col-md-6 panel-padding" >
													<table class="table table-striped table-margintop">
														<tbody>
															<tr class="project-overview">
																<td class="bold" width="40%"><?php echo _l('total_material_cost'); ?></td>
																<td><?php echo app_format_money($manufacturing_order_costing['total_material_cost'], $currency->name)  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="bold"><?php echo _l('total_labour_cost'); ?></td>
																<td>
																	<?php echo app_format_money($manufacturing_order_costing['total_labour_cost'], $currency->name)  ?>
																	<br>
																</td>
															</tr>
															<tr class="project-overview">
																<td class="" width="40%">    +   <?php echo _l('total_work_center_cost'); ?></td>
																<td><?php echo app_format_money($manufacturing_order_costing['total_work_center_cost'], $currency->name)  ?></td>
															</tr>
															<tr class="project-overview">
																<td class="" width="40%">    +   <?php echo _l('total_employee_working_cost'); ?></td>
																<td><?php echo app_format_money($manufacturing_order_costing['total_employee_working_cost'], $currency->name)  ?></td>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
										</div>

										<div role="tabpanel" class="tab-pane" id="bom_changes_logs_tab">
											<?php if(has_permission('manufacturing', '', 'create')){ ?>
												<div class="_buttons hide">
													<a href="#" onclick="add_component(<?php echo new_html_entity_decode($manufacturing_order->id) ?>,0, 'add'); return false;" class="btn btn-info mbot10 pull-right"><?php echo _l('mrp_add_change_log_manual'); ?></a>
												</div>
											<?php } ?>

											<?php render_datatable(array(

												_l('id'),
												_l('component'),
												// _l('mrp_parent'),
												_l('mrp_change_type'),
												_l('mrp_change_quantity'),
												_l('mrp_date_and_time'),
												_l('mrp_user'),
												_l('description'),
												_l('mrp_related'),
											),'bom_change_log_table',
											array('customizable-table'),
											array(
												'id'=>'table-bom_change_log_table',
												'data-last-order-identifier'=>'bom_change_log_table',
												'data-default-order'=>get_table_last_order('bom_change_log_table'),
											)); ?>

										</div>

										<div role="tabpanel" class="tab-pane" id="inventory_produced_logs_tab">
											
											<table id="list-inventory-produced-logs-table" border="1" width="100%" cellpadding="5" cellspacing="0" class="table customizable-table dataTable no-footer">
												<thead>
													<tr>
														<th>Sr No.</th>
														<th>Goods Receipt</th>
														<th>Quantity</th>
														<th>Date & Time</th>
													</tr>
												</thead>
												<tbody>
													<?php if (!empty($transactions)) : ?>
														<?php $sr = 1; ?>
														<?php foreach ($transactions as $txn) : ?>
															<tr>
																<td><?php echo $sr++; ?></td>
																<td>
																	<a href="<?php echo admin_url('warehouse/manage_purchase#' . $txn->goods_receipt_id); ?>" target="_blank">
																		<?php echo $txn->goods_receipt_code; ?>
																	</a>
																</td>
																<td><?php echo number_format($txn->quantity, 2); ?></td>
																<td><?php echo _dt($txn->date_add); ?></td>
															</tr>
															<?php $quantity_produced += $txn->quantity; ?>
														<?php endforeach; ?>

														<tr class="bg-light">
															<td class="text-right font-bold" colspan="2">Total</td>
															<td class="font-bold"><?php echo number_format($quantity_produced, 2); ?></td>
															<td></td>
														</tr>

													<?php else : ?>
														<tr>
															<td colspan="4">No goods receipt records found for this manufacturing order.</td>
														</tr>
													<?php endif; ?>
												</tbody>
											</table>


										</div>

										<div role="tabpanel" class="tab-pane" id="inventory_production_tab">
										    <div id="modal_wrapper"></div>
											<div class="text-right">
												<?php if($status != 'cancelled'){ ?>
                                                    <a href="#" onclick="assign_production_modal(<?php echo $manufacturing_order->id ?>); return false;" class="btn btn-info mbot10 float-right"><?php echo _l('Assign Inventory'); ?></a>
											    <?php } ?>
											</div>

                                            <div>
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
														    
															//get Items to display in table to assign 
															$this->db->select('b.*, v.company');
															$this->db->from('tblmrp_bom_production_inventory b');
															$this->db->join('tblpur_vendor v', 'b.vendor_id = v.userid', 'left'); // Left join to include all records from production inventory
															$this->db->where('b.manufacturing_order_id', $manufacturing_order->id);
															$production_inventory = $this->db->get()->result_array();

															//var_dump($production_inventory);		
                                                            $finishProductFinalQty = null;
													    	$sr_no = 1; foreach ($production_inventory as $item) : 
															
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

																$class = $statusClasses[$item['status']] ?? 'secondary'; // Default to 'secondary' if status is unknown
															?>
																<span class="label label-<?php echo $class ?>" ><?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?></span>
															</td>	
															<td>
																<a target="_blank" href="<?php echo admin_url('manufacturing/receipt_production/' . $item['id']); ?>?sr_no=<?php echo $sr_no ?>">
																	<?php echo _l('Receipt'); ?>
																</a> 
																<?php if($status != 'cancelled'){ ?>
																|
																<a href="#" onclick="receive_production_modal(<?php echo $item['id'] ?>); return false;">
																	<?php echo _l('Receive'); ?>
																</a> 
															    <?php //if($item['status'] == 'completed'): ?>
															    <?php if($item['status'] == 'completed' || $item['status'] == 'in_progress'): ?>
																|
																<?php
                                                                    $invoice_id = $this->db->where('value', $item['id'])->where('fieldid', 3)->where('fieldto', 'pur_invoice')->get('tblcustomfieldsvalues')->row()->relid;
																	$params = [
																		'bom_production_inventory_id' => $item['id'],
																		'vendor' => $item['vendor_id'],
																		'manufacturing_order_code' => $manufacturing_order->manufacturing_order_code,
																		'item_name' => $item['product_name'],
																		'description' => $item['comments'],
																		'qty_assigned' => $item['qty_assigned'],
																		'qty_received' => $item['qty_received'],
																		'qty_lost' => $item['qty_lost'],
																		'qty_pending' => $item['qty_pending'],
																		'price' => $item['price'],
																		'deduct_price' => $item['deduct_price']
																	];
																	$url = admin_url('purchase/pur_invoice?' . http_build_query($params));
																?>
																    <?php if(!$invoice_id): ?>
																    	<a target="_blank" href="<?php echo $url; ?>">Make Invoice</a>
																	<?php else: ?> 
																		<!-- <a target="_blank" href="<?php echo admin_url('purchase/purchase_invoice/'.$invoice_id); ?>">View Invoice</a> -->
																		<a target="_blank" href="<?php echo admin_url('purchase/pur_invoice/'.$invoice_id.'?'.http_build_query($params)); ?>">Edit Invoice</a>
																	<?php endif; ?> 
																<?php endif; ?> 
																<?php } ?>
															    <?php if($item['status'] != 'completed' && $item['qty_received'] <= 0): ?>
																	|
																	<a href="#" onclick="edit_production_modal(<?php echo $item['id']; ?>); return false;">
																		<?php echo _l('Edit'); ?>
																	</a>
																	|
																	<a href="javascript:void(0)" class="text-danger delete_production" data-id="<?php echo $item['id']; ?>" data-url="<?php echo admin_url('manufacturing/delete_production/'); ?>">
																		<?php echo _l('Delete'); ?>
																	</a>
																<?php endif; ?>
														    </td>
														</tr>
														<?php endforeach; ?>
													</tbody>
												</table>												
										    </div>
                                            

										</div>


									</div>
								</div>

							</div>

							<div class="modal-footer">
								<a href="<?php echo admin_url('manufacturing/manufacturing_order_manage'); ?>"  class="btn btn-default mr-2 "><?php echo _l('close'); ?></a>

									<?php if(has_permission('manufacturing', '', 'create') ){ ?>
										<a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order'); ?>" class="btn btn-info pull-right display-block mright5"><?php echo _l('add_manufacturing_order'); ?></a>
									<?php } ?>

									<?php if( has_permission('manufacturing', '', 'edit')){ ?>
										<a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order/'.$manufacturing_order->id); ?>" class="btn btn-primary pull-right display-block mright5"><?php echo _l('edit_manufacturing'); ?></a>
									<?php } ?>

							</div>

						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="modal fade" id="show_detail" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">
							<span class="add-title"><?php echo _l('quantity_produced'); ?></span>
						</h4>
					</div>
					<div class="modal-body">
						<div class="row">

							<div class="col-md-12">
								<h5>Inventory In Logs (Goods Receipt):</h5>
								<table class="table table-bordered table-striped table-condensed">
									<thead>
										<tr>
											<th>Sr No.</th>
											<th>Goods Receipt</th>
											<th>Quantity</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($transactions)) : ?>
											<?php $sr = 1; ?>
											<?php foreach ($transactions as $txn) : ?>
												<tr>
													<td><?php echo $sr++; ?></td>
													<td>
														<a href="<?php echo admin_url('warehouse/manage_purchase#' . $txn->goods_receipt_id); ?>" target="_blank">
															<?php echo $txn->goods_receipt_code; ?>
														</a>
													</td>
													<td><?php echo number_format($txn->quantity, 2); ?></td>
													<td><?php echo _dt($txn->date_add); ?></td>
												</tr>
												<?php //$quantity_produced += $txn->quantity; ?>
											<?php endforeach; ?>

											<tr class="bg-light">
												<td class="text-right font-bold" colspan="2">Total</td>
												<td class="font-bold"><?php echo number_format($quantity_produced, 2); ?></td>
												<td></td>
											</tr>

										<?php else : ?>
											<tr>
												<td colspan="4">No goods receipt records found for this manufacturing order.</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>	
							
							<div class="col-md-12">
								<label class="text-danger">
									<?php echo _l('If_the_actual_quantity_of_products_produced_note'); ?>
									<br>
									If you are entering the final pending produced quantity, then check the checkbox below to confirm.
								</label>
							</div>							

							<div class="col-md-12">
								<?php echo render_input('change_product_qty', 'Quantity Produced (' . number_format($quantity_produced, 2) . ' out of ' . $product_qty . ')', ($product_qty ? ($product_qty - $quantity_produced) : $product_qty), 'number', ['min' => 0, 'max' => ($product_qty - $quantity_produced),  'step' => '1']); ?>
							</div>

							<div class="col-md-12">
								<div class="checkbox checkbox-primary">
									<input type="hidden" id="remaining_qty_produced" name="remaining_qty_produced" value="<?php echo ($product_qty - $quantity_produced) ?>">
									<input type="checkbox" id="final_produced_checkbox" name="final_produced_checkbox" value="1">
									<label for="final_produced_checkbox">
										Final Produced Quantity <br><small class="text-muted">If you check this, the manufacturing order will be marked as done.</small>
									</label>
								</div>
							</div>

						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary btn_mark_as_done" ><?php echo _l('mark_as_done'); ?></button>
					</div>
				</div>
			</div>
		</div>

		<?php echo form_hidden('manufacturing_order_id',$manufacturing_order->id); ?>
		<?php init_tail(); ?>
		<?php 
		require('modules/manufacturing/assets/js/manufacturing_orders/view_manufacturing_order_js.php');
		?>
	</body>
	</html>
