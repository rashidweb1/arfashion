
<?php 

	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);

   //getting from post
   $id = $id;
   
   //Vendors
   
   $this->db->where('active', 1);
   $this->db->where("FIND_IN_SET(1, category) >", 0); 
   $this->db->order_by("company", 'asc');
   $vendors = $this->db->get('tblpur_vendor')->result_array();    

   $bom_inventory = $this->db->where('id', $id)->get('tblmrp_bom_production_inventory')->row_array();

?>

<div class="modal fade" id="commonModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign Inventory</h4>
			</div>
			<?php echo form_open_multipart(admin_url('manufacturing/update_production/'), array('id' => 'update_production')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row"> 
						<input type="hidden" value="<?php echo $id; ?>" name="id">

						<div class="col-md-6 form-group"> 
							<label for="vendor"><?php echo _l('Manufacturing vendors'); ?></label>
							<select name="vendor_id" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="" required>
								<option value="">--Select--</option>
								<?php foreach($vendors as $s) { ?>
								<option value="<?php echo pur_html_entity_decode($s['userid']); ?>" <?php if($bom_inventory['vendor_id'] == $s['userid']){ echo 'selected'; } ?>><?php echo pur_html_entity_decode($s['company']); ?></option>
									<?php } ?>
							</select>                       
						</div>

						<div class="col-md-6 form-group"> 
							<label for="vendor"><?php echo _l('Product Name'); ?></label>
							<input type="text" class="form-control" name="product_name" value="<?= $bom_inventory['product_name'] ?>" required>                       
						</div>	

						<div class="col-md-6 form-group"> 
							<label for="price"><?php echo _l('Receive - Per Piece Price'); ?></label>
							<input type="number" class="form-control" name="price" value="<?= $bom_inventory['price'] ?>" step="0.01" required>                       
						</div>

						<div class="col-md-6 form-group"> 
							<label for="deduct_price"><?php echo _l('Lost / Waste - Per Piece Price'); ?></label>
							<input type="number" class="form-control" name="deduct_price" value="<?= $bom_inventory['deduct_price'] ?>" step="0.01" required>                       
						</div>						

						<div class="col-md-12 form-group"> 
							<label for="comments"><?php echo _l('Note'); ?></label>
							<textarea class="form-control" name="comments" id="comments" rows="3" required><?= $bom_inventory['comments'] ?></textarea>                    
						</div>	
						
						<div class="col-md-6 form-group"> 
							<label for="assign_date"><?php echo _l('Date of Assign'); ?></label>
							<input type="date" class="form-control" name="created_at" value="<?= date("Y-m-d", strtotime($bom_inventory['created_at'])) ?>" required>                       
						</div>		
						
						<div class="col-md-6 form-group"> 
							<label for="deadline"><?php echo _l('Deadline Date'); ?></label>
							<input type="date" class="form-control" name="deadline" value="<?= date("Y-m-d", strtotime($bom_inventory['deadline'])) ?>" required>                       
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