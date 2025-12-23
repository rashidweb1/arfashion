
<?php 

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

   //getting from post
   $manufacturing_order_id = $manufacturing_order_id;
   
   //Vendors
   //$vendors = $this->db->where('active', 1)->like('category', 1)->order_by("company", 'asc')->get('tblpur_vendor')->result_array();
//    $this->db->where('active', 1);
//    $this->db->where("FIND_IN_SET(1, category) >", 0); 
//    $this->db->order_by("company", 'asc');
//    $vendors = $this->db->get('tblpur_vendor')->result_array(); 
   $this->load->model('purchase/Purchase_model', 'purchase_model');
   $vendors = $this->purchase_model->get_vendor_select_dropdown(); 

   //Insert all the existing raw material with proper quantity to use in selection
   $bom_inventory = $this->db->where('manufacturing_order_id', $manufacturing_order_id)->get('tblmrp_bom_inventory')->num_rows();
   if($bom_inventory == 0) {
        //Insert raw materials in tblmrp_mrp_bom_inventory

	    //Get finish product quantity to make
	    $manufacturing_order = $this->db->where('id', $manufacturing_order_id)->get('tblmrp_manufacturing_orders')->row_array();

	    $this->db->select('
			tblmrp_manufacturing_order_details.*, 
			tblitems.description, 
			tblitems.commodity_code
		');
        
		//Get raw product quantity details
		$this->db->from('tblmrp_manufacturing_order_details');
		$this->db->join('tblitems', 'tblitems.id = tblmrp_manufacturing_order_details.product_id', 'left');
		$this->db->where('tblmrp_manufacturing_order_details.manufacturing_order_id', $manufacturing_order['id']);
        $manufacturing_order_details = $this->db->get()->result_array();	   

	    foreach($manufacturing_order_details as $mo_detail):
			// Data to insert
			$data = [
				'manufacturing_order_id' => $manufacturing_order_id,
				'type' => 'existing_raw_material',
				'product_id' => $mo_detail['product_id'],
				'product_name' => $mo_detail['commodity_code'].'_'.$mo_detail['description'],
				'quantity_total' => $mo_detail['qty_to_consume'],
				'quantity_remaining' => $mo_detail['qty_to_consume'],
				'per_quantity_consumption' => $mo_detail['qty_to_consume'] / $manufacturing_order['product_qty'],
				'unit_id' => $mo_detail['unit_id'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];		

			// Insert into table
			$this->db->insert('tblmrp_bom_inventory', $data);
	    endforeach;
   }

	//get Items to display in table to assign 
	$this->db->select('b.*, u.unit_name');
	$this->db->from('tblmrp_bom_inventory b');
	$this->db->join('tblware_unit_type u', 'b.unit_id = u.unit_type_id', 'left');
	$this->db->where('b.manufacturing_order_id', $manufacturing_order_id);
	$bom_inventory = $this->db->get()->result_array();
	//var_dump($bom_inventory);   

?>

<div class="modal fade" id="commonModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign Inventory</h4>
			</div>
			<?php echo form_open_multipart(admin_url('manufacturing/create_production/'), array('id' => 'create_production')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row"> 
						<input type="hidden" value="<?php echo $manufacturing_order_id; ?>" name="manufacturing_order_id">

						<div class="col-md-6 form-group"> 
							<label for="vendor"><?php echo _l('Manufacturing vendors'); ?></label>
							<select name="vendor_id" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="" required>
								<option value="">--Select--</option>
								<?php foreach($vendors as $s) { ?>
								<option data-content="<?php echo pur_html_entity_decode($s['company']); ?>" value="<?php echo pur_html_entity_decode($s['userid']); ?>"><?php echo pur_html_entity_decode($s['company']); ?></option>
									<?php } ?>
							</select>                       
						</div>

						<div class="col-md-6 form-group"> 
							<label for="vendor"><?php echo _l('Product Name'); ?></label>
							<input type="text" class="form-control" name="product_name" value="" required>                       
						</div>	

						<div class="col-md-6 form-group"> 
							<label for="is_inventory"><?php echo _l('type'); ?></label>
							<select name="is_inventory" id="is_inventory" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="" required>
								<option value="">--Select--</option>
								<option value="1">Inventory</option>
								<option value="0">Finish Product</option>
							</select>                       
						</div>

						<div class="col-md-6 form-group"> 
							<label for="qty_assigned"><?php echo _l('Quantity Assign'); ?></label>
							<input type="number" class="form-control" name="qty_assigned" value="" min="1" required>                       
						</div>

						<div class="col-md-6 form-group"> 
							<label for="price"><?php echo _l('Receive - Per Piece Price'); ?></label>
							<input type="number" class="form-control" name="price" value="" step="0.01" required>                       
						</div>

						<div class="col-md-6 form-group"> 
							<label for="deduct_price"><?php echo _l('Lost / Waste - Per Piece Price'); ?></label>
							<input type="number" class="form-control" name="deduct_price" value="" step="0.01" required>                       
						</div>	

						<div class="col-md-12 form-group"> 
							<label for="comments"><?php echo _l('Note'); ?></label>
							<textarea class="form-control" name="comments" id="comments" rows="3" required>-</textarea>                    
						</div>	
						
						<div class="col-md-6 form-group"> 
							<label for="assign_date"><?php echo _l('Date of Assign'); ?></label>
							<input type="date" class="form-control" name="created_at" value="" required>                       
						</div>	
						
						<div class="col-md-6 form-group"> 
							<label for="deadline"><?php echo _l('Deadline Date'); ?></label>
							<input type="date" class="form-control" name="deadline" value="" required>                       
						</div>						
						
						<!--Table-->
						<div class="col-md-12 form-group"> 
						    <label for="vendor"><?php echo _l('Assign Items'); ?></label>
							<div>
						    	<button type="button" class="btn btn-sm btn-danger" id="bulk_delete_rows">Delete Selected</button>
							</div>

							<table id="assign-production-table" border="1" width="100%" cellpadding="5" cellspacing="0">
								<thead>
									<tr>
										<th><input type="checkbox" id="select_all_rows"></th>
										<th>Sr No.</th>
										<th>Name</th>
										<th>Per Quantity Consumption</th>
										<th>Remaining Quantity</th>
										<th style="padding-bottom: 20px;">Assign Quantity</th>
										<th>Options</th>
									</tr>
								</thead>
								<tbody>
									<?php $sr_no = 1; foreach ($bom_inventory as $item) : ?>
									<tr>
										<td>
											<?php if($item['quantity_remaining'] > 0): ?>
											<input type="checkbox" class="row_checkbox">
											<?php endif; ?>
										</td>
										<td class="text-center"><?php echo $sr_no++; ?></td>
										<td><?php echo $item['product_name']; ?></td>
										<td><?php echo $item['per_quantity_consumption'].' '.$item['unit_name']; ?></td>
										<td><?php echo $item['quantity_remaining'].' '.$item['unit_name']; ?></td>
										<td>
										    <div class="form-group">
												<?php if($item['quantity_remaining'] > 0): ?>
													<input name="details[bom_inventory_id][]" class="form-control" type="hidden" value="<?php echo $item['id']; ?>">
										        	<input name="details[qty_assigned][]" class="form-control" type="number" value="" step="0.01" min="0.01" max="<?php echo $item['quantity_remaining']; ?>" required>
											    <?php else: ?>
													<input name="" class="form-control input-sm" type="number" value="0" step="0.01" min="0.01" max="<?php echo $item['quantity_remaining']; ?>" readonly>
												<?php endif; ?>
											</div>
										</td>
										<td class="text-center">
											<?php if($item['quantity_remaining'] > 0): ?>
											<a href="javascript:void(0);" class="delete-production-item"><i class="fa fa-trash text-danger"></i></a>
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>

						</div>

						<div class="col-md-12 form-group">
							<input type="checkbox" id="confirm_checkbox" required>
							<label for="confirm_checkbox"><strong>Confirm to proceed, the assignment cannot be rolled back</strong></label>
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

