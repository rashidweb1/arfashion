<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php 
    $CI =& get_instance();
    $CI->db->select('
        tblinvoicepaymentrecords.id as payment_id,
        tblinvoicepaymentrecords.date as payment_date,
        tblinvoicepaymentrecords.amount,
        tblinvoicepaymentrecords.invoiceid,
        tblpayment_modes.name as paymentmode,
        tblinvoicepaymentrecords.transactionid,
        tblinvoicepaymentrecords.note,
        tblclients.company as client_company,
        tblinvoices.clientid,
        CONCAT("#", tblinvoices.prefix, "", tblinvoices.number) as formatted_invoice_number
    ');
    $CI->db->from('tblinvoicepaymentrecords');
    $CI->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
    $CI->db->join('tblclients', 'tblclients.userid = tblinvoices.clientid');
    $CI->db->join('tblpayment_modes', 'tblpayment_modes.id = tblinvoicepaymentrecords.paymentmode', 'left');
    $CI->db->where('tblinvoicepaymentrecords.group_id', $group_id);
    $CI->db->order_by('tblinvoicepaymentrecords.date', 'DESC');
    $CI->db->order_by('tblinvoicepaymentrecords.id', 'DESC');
    $query = $CI->db->get();
    $records = $query->result_array();
?>

<div class="modal fade" id="income-payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title"><?php echo _l('Income Payment - Breakup of ').' '.$amount; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <table id="paymentsTable" border="1" width="100%" cellpadding="5" cellspacing="0" class="table customizable-table dataTable no-footer">    
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Note</th>
                            <th>Amount</th>
                            <th>Client</th>
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
                                    <td><?= _d($row['payment_date']); ?></td>
                                    <td><?= $row['note']; ?></td>
                                    <td><?= app_format_money($row['amount'], get_base_currency()); ?></td>
                                    <td>
                                        <a href="<?= admin_url('clients/client/' . $row['clientid']); ?>?group=statement" target="_blank">
                                            <?= $row['client_company']; ?>
                                        </a>
                                    </td>  
                                    <td>
                                        <a href="<?= admin_url('invoices/list_invoices/' . $row['invoiceid']); ?>" target="_blank">
                                            <?= $row['formatted_invoice_number']; ?>
                                        </a>
                                    </td> 
                                    <td><?= $row['transactionid']; ?></td>   
                                    <td><?= $row['paymentmode']; ?></td>                                  
                                    <!-- <td>
                                        <a href="<?= admin_url('payments/payment/' . $row['payment_id']); ?>" target="_blank">
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

