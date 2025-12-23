<?php

    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    $proposal = $this->db->where("id", $proposal_id)->get(db_prefix() . 'proposals')->row();
    $proposal_items = $this->db->where("rel_id", $proposal_id)->where("rel_type", "proposal")->get(db_prefix() . 'itemable')->result();

    function get_manufacturing_info($proposal_id, $product_id) {
        $CI =& get_instance();
        $mo = $CI->db->where("proposal_id", $proposal_id)->where("product_id", $product_id)->get(db_prefix() . 'mrp_manufacturing_orders')->row();
        $receipt_goods = $CI->db->where("manufacturing_order_id", $mo->id)->get(db_prefix() . 'goods_receipt_detail')->row();

        return (object)[
            'manufacturing_order_id' => $mo->id ?? '',
            'manufacturing_order_code' => $mo->manufacturing_order_code ?? '',
            'status' => $mo->status ?? '',
            'qty_making' => number_format($mo->product_qty ?? 0, 2),
            'qty_maked' => number_format($receipt_goods->quantities ?? 0, 2),
        ];
    }

    // function get_invoice_info($proposal_id, $product_id) {
    //     $CI =& get_instance();

    //     $custom_field = $CI->db->where("fieldid", 4)->where("fieldto", 'invoice')->where("value", $proposal_id)->get(db_prefix() . 'customfieldsvalues')->row();
    //     $invoice_id = $custom_field->relid;
    //     $invoice = $CI->db->where("id", $invoice_id)->get(db_prefix() . 'invoices')->row();
    //     //$invoice_items = $this->db->where("rel_id", $invoice_id)->where("iid", $product_id)->get(db_prefix() . 'itemable')->result();

    //     return (object)[
    //         'invoice_id' => $invoice_id,
    //         'prefix' => $invoice->prefix,
    //     ];
    // }  
    
    function get_invoice_info($proposal_id, $product_id) {
        $CI =& get_instance();
    
        // Get all custom field matches
        $custom_fields = $CI->db->where("fieldid", 4)
                                ->where("fieldto", 'invoice')
                                ->where("value", $proposal_id)
                                ->get(db_prefix() . 'customfieldsvalues')
                                ->result();
    
        $invoices = [];
    
        foreach ($custom_fields as $field) {
            $invoice_id = $field->relid;
    
            $invoice = $CI->db->where("id", $invoice_id)
                              ->get(db_prefix() . 'invoices')
                              ->row();
    
            if ($invoice) {
                $qty_sum = $CI->db->select_sum('qty')->where('rel_id', $invoice_id)->where('iid', $product_id)->where('rel_type', 'invoice')->get(db_prefix() . 'itemable')->row()->qty ?? 0;
                $delivery_vaucher_status = $CI->db->where('invoice_id', $invoice_id)->get(db_prefix() . 'goods_delivery')->row();
                $delivery_vaucher = $CI->db->where('invoice_id', $invoice_id)->get(db_prefix() . 'goods_delivery')->row();
    
                if($qty_sum){
                    $invoices[] = (object)[
                        'invoice_id' => $invoice_id,
                        'prefix'     => $invoice->prefix.$invoice->number,
                        'qty'        => number_format($qty_sum, 2),
                        'status'     => $invoice->status,
                        'delivery_vaucher' => $delivery_vaucher->delivery_status,
                        'goods_delivery_code' => $delivery_vaucher->goods_delivery_code,
                        'goods_delivery_id' => $delivery_vaucher->id,
                    ];
                }
            }
        }
    
        return $invoices;
    }    

    function status_label($status) {
        $statusClasses = [
            'pending'     => 'danger',
            'in_progress' => 'primary',
            'done'   => 'success',
            'cancelled'   => 'warning',
            'confirmed'   => 'primary',
            'planned'   => 'primary',
            'unpaid'   => 'danger',
            'draft'   => 'default',
        ];

        $class = $statusClasses[$status] ?? 'default';    
        return $class;    
    }
?>


<h4 class="bold">
    <span id="proposal-number"><?php echo get_proposal_po_format($proposal->id) ?></span>
</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Serial</th>
            <th>Product</th>
            <th>Manufacturing Info</th>
            <th>Invoice Info</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($proposal_items as $index => $item): 
            $mo = get_manufacturing_info($proposal->id, $item->iid);
            $ii = get_invoice_info($proposal->id, $item->iid);
        ?>
        <tr>
            <td><?php echo ++$index ?></td>
            <td>
                <a href="<?php echo admin_url('warehouse/view_commodity_detail/' . $item->iid); ?>" target="_blank">
                    <?php echo $item->description; ?>
                </a>
            </td>
            <td>
                <?php if (!empty($mo->manufacturing_order_id)): ?>
                    <a href="<?php echo admin_url('manufacturing/view_manufacturing_order/' . $mo->manufacturing_order_id); ?>" target="_blank">
                        <?php echo $mo->manufacturing_order_code; ?>
                    </a>
                    - <span>(<?php echo $mo->qty_maked . ' / ' . $mo->qty_making ?>)</span>
                    <span class="label label-<?php echo status_label($mo->status); ?>">
                        <?php echo ucfirst(_l($mo->status)); ?>
                    </span>
                <?php else: ?>
                    <?= _l("Not started") ?> -
                    <a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order/?proposal_id=' . $proposal->id . '&iid=' . $item->iid.'&product_qty='.$item->qty); ?>" target="_blank">
                        Start
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <?php if (empty($ii)): ?>
                    <?= _l("Not Available") ?>
                <?php else: ?>
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>SR</th>
                                <th>Number</th>
                                <th>Status</th>
                                <th>Qty</th>
                                <th>Delivery Voucher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ii as $index => $i): ?>
                            <tr>
                                <td><?= ++$index ?></td>
                                <td>
                                    <a href="<?php echo admin_url('invoices/#' . $i->invoice_id); ?>" target="_blank">
                                        <?= $i->prefix ?>
                                    </a>
                                    <?= format_invoice_status($i->status) ?>
                                </td>
                                <td><?= $i->status ?></td>
                                <td><?= $i->qty ?></td>
                                <td>
                                    <?php if($i->delivery_vaucher): ?>
                                    <a href="<?php echo admin_url('warehouse/manage_delivery/' . $i->goods_delivery_id); ?>" target="_blank">
                                        <?php echo $i->goods_delivery_code; ?>
                                    </a>
                                    <span class="label label-default"><?php echo str_replace('_', ' ', ucfirst($i->delivery_vaucher)) ?></span>
                                    <?php else: ?>
                                        <a href="<?php echo admin_url('warehouse/goods_delivery?invoice_id='.$i->invoice_id); ?>" target="_blank">
                                            Create 
                                        </a>                                        
                                        <span class="label label-default"><?php echo _l("Not Initiated") ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<div style="display:none;">
<h4 class="bold">
    <span id="proposal-number"><?php echo get_proposal_po_format($proposal->id) ?></span>
</h4>

    <?php 
        foreach ($proposal_items as $index => $item): 
        $mo = get_manufacturing_info($proposal->id, $item->iid);
        $ii = get_invoice_info($proposal->id, $item->iid);
        //var_dump($ii);
    ?>
    <p><b>Serial Number : </b><?php echo ++$index ?></p>

    <!-- Products Information -->
    <p><b>Product: </b>
        <a href="<?php echo admin_url('warehouse/view_commodity_detail/' . $item->iid); ?>" target="_blank">
            <?php echo $item->description; ?>
        </a>
    </p>

    <!-- Manufacturing Information -->
    <?php if (!empty($mo->manufacturing_order_id)): ?>
        <p>
            <b><?= _l("Manufacturing") ?> :</b>
            <a href="<?php echo admin_url('manufacturing/view_manufacturing_order/' . $mo->manufacturing_order_id); ?>" target="_blank">
                <?php echo $mo->manufacturing_order_code; ?>
            </a>
            -
            <span> (<?php echo $mo->qty_maked.' / '.$mo->qty_making ?>)</span>
            
            <span class="label label-<?php echo status_label($mo->status); ?>">
                <?php echo ucfirst(_l($mo->status)); ?>
            </span>

        </p>
    <?php else: ?>
        <p><b><?= _l("Manufacturing") ?> :</b> 
        <?= _l("Not started") ?>
        - 
        <a href="<?php echo admin_url('manufacturing/add_edit_manufacturing_order/?proposal_id='.$proposal->id.'&iid='.$item->iid.'&qty='.$item->qty); ?>" target="_blank">
           Start
        </a>
    </p>
    <?php endif; ?>  
    
    <!-- Invoice Information -->
    <p><b><?= _l("Invoice Information") ?> : </b> <?= empty($ii) ? _l("Not Available") : ''; ?></p> 
    <div>
        <?php foreach ($ii as $index => $i): ?>
            <p style="margin:0"> * SR. : <?= ++$index ?></p>
            <p style="margin:0"> * Number :
                <a href="<?php echo admin_url('invoices/#' . $i->invoice_id); ?>" target="_blank">
                    <?= $i->prefix ?> 
                </a> 
                <?= format_invoice_status($i->status) ?>
            </p>
            <p> * Quantity : <?= $i->qty ?></p>
            <p> * Delivery Vaucher : 
                <a href="<?php echo admin_url('warehouse/manage_delivery/' . $i->goods_delivery_id); ?>" target="_blank">
                    <?php echo $i->goods_delivery_code; ?>
                </a>                
                <?php //echo $i->delivery_vaucher ?>
            </p>
        <?php endforeach; ?>
    </div>     
    <hr>

<?php
    // echo '<pre>';
    // var_dump($manufacturing_info);
    // echo '</pre>';
?>   
<?php endforeach; ?>

<?php
    // echo '<pre>';
    // var_dump($proposal_items);
    // echo '</pre>';
?>
</div>

