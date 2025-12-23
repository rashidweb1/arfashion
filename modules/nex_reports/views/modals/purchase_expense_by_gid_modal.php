<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php 
$CI =& get_instance();

$CI->db->select('
    tblpur_invoice_payment.id as payment_id,
    tblpur_invoice_payment.date as pay_date,
    tblpur_invoice_payment.amount,
    tblpur_invoices.invoice_number as pur_invoice_id,
    tblpayment_modes.name as paymentmode,
    tblpur_invoice_payment.transactionid,
    tblpur_invoice_payment.note,
    tblpur_vendor.company as vendor_company,
    tblpur_vendor.userid as vendor_id
');
$CI->db->from('tblpur_invoice_payment');
$CI->db->join('tblpur_invoices', 'tblpur_invoices.id = tblpur_invoice_payment.pur_invoice', 'left');
$CI->db->join('tblpur_vendor', 'tblpur_vendor.userid = tblpur_invoices.vendor', 'left');
$CI->db->join('tblpayment_modes', 'tblpayment_modes.id = tblpur_invoice_payment.paymentmode', 'left');
$CI->db->where('tblpur_invoice_payment.group_id', $group_id);
$CI->db->order_by('tblpur_invoice_payment.date', 'DESC');
$CI->db->order_by('tblpur_invoice_payment.id', 'DESC');

$query   = $CI->db->get();
$records = $query->result_array();
?>

<div class="modal fade" id="purchase-expense-payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title"><?php echo _l('Purchase Expense Payment - Breakup of ').' '.$amount; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <table id="purchaseExpensePaymentsTable" border="1" width="100%" cellpadding="5" cellspacing="0" class="table customizable-table dataTable no-footer">    
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Note</th>
                            <th>Amount</th>
                            <th>Vendor</th>
                            <th>Invoice</th>
                            <th>Trans. ID</th>
                            <th>Mode</th>
                            <!-- <th>Payment ID</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)) { ?>
                            <?php foreach ($records as $row) { ?>
                                <tr>
                                    <td><?= _d($row['pay_date']); ?></td>
                                    <td><?= $row['note']; ?></td>
                                    <td><?= app_format_money($row['amount'], get_base_currency()); ?></td>
                                    <td>
                                        <a href="<?= admin_url('purchase/vendor/' . $row['vendor_id']); ?>" target="_blank">
                                            <?= $row['vendor_company']; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= admin_url('purchase/purchase_invoice/' . $row['pur_invoice_id']); ?>" target="_blank">
                                            <?= $row['pur_invoice_id']; ?>
                                        </a>
                                    </td>     
                                    <td><?= $row['transactionid']; ?></td>
                                    <td><?= $row['paymentmode']; ?></td>                                                                   

                                    <!-- <td>
                                        <a href="<?= admin_url('purchase/payment_invoice/' . $row['payment_id']); ?>" target="_blank">
                                            <?= $row['payment_id']; ?>
                                        </a>
                                    </td> -->
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table> 
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default close_btn" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
// $(document).ready(function() {
//     $('#paymentsTable').DataTable({
//         "paging": false,       // 🔴 Disable pagination
//         "ordering": false,     // 🔴 Disable column sorting
//         "searching": true,     // ✅ Keep search box
//         "info": false,         // (optional) Hide "Showing X of Y entries"
//         "lengthChange": false  // (optional) Hide rows-per-page dropdown
//     });
// });
</script>

