<table class="table table-small dt-table scroll-responsive" data-order-type="desc">
    <thead>
        <tr>
            <th>Sr No.</th>
            <th>Refrence</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Type</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            
            //get Items to display in table to assign 
            $this->db->select('b.*, v.company, mo.manufacturing_order_code');
            $this->db->from('tblmrp_bom_production_inventory b');
            $this->db->join('tblpur_vendor v', 'b.vendor_id = v.userid', 'left'); // Left join to include all records from production inventory
            $this->db->join('tblmrp_manufacturing_orders mo', 'b.manufacturing_order_id = mo.id', 'both');
            $this->db->where('b.vendor_id', $client->userid);
            $this->db->order_by('b.manufacturing_order_id', 'desc');
            //$this->db->where('b.manufacturing_order_id', $manufacturing_order->id);
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
            <td>
                <a target="_blank" href="<?php echo admin_url('manufacturing/view_manufacturing_order/' . $item['manufacturing_order_id']); ?>">
                    <?php echo $item['manufacturing_order_code']; ?>
                </a>                
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

                $class = $statusClasses[$item['status']] ?? 'secondary'; // Default to 'secondary' if status is unknown
            ?>
                <span class="label label-<?php echo $class ?>" ><?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?></span>
            </td>	
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<style>
.table-small {
    font-size: 12px !important;
}    
</style>