<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();
// var_dump($CI->input->post('order'));exit;
$start_date = $CI->input->get('start_date') ?? date("Y-m-d");
$end_date = $CI->input->get('end_date') ?? date("Y-m-d");

$aColumns = [
    'tblpur_invoice_payment.id as payment_id',
    'tblpur_invoice_payment.date as pay_date',
    'tblpur_invoice_payment.amount as amount',
    'tblpur_invoices.invoice_number as pur_invoice_id',
    'tblpayment_modes.name as paymentmode',
    'tblpur_invoice_payment.transactionid as transactionid',
    'tblpur_invoice_payment.note as note',
    'tblpur_vendor.company as vendor_company',
    'tblpur_vendor.userid as vendor_id',
];

$sIndexColumn = 'tblpur_invoice_payment.id';
$sTable       = db_prefix() . 'pur_invoice_payment';

$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_invoices ON ' . db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'pur_invoice_payment.pur_invoice',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_invoices.vendor',
    'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'pur_invoice_payment.paymentmode'
];

$where = [];
if ($start_date) {
    $where[] = 'AND tblpur_invoice_payment.date >= "' . $start_date . '"';
}
if ($end_date) {
    $where[] = 'AND tblpur_invoice_payment.date <= "' . $end_date . '"';
}

$additionalSelect = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

// Total Purchase Expense
$CI->db->select_sum('amount', 'total_amount');
$CI->db->from(db_prefix() . 'pur_invoice_payment');
if ($start_date) {
    $CI->db->where('date >=', $start_date);
}
if ($end_date) {
    $CI->db->where('date <=', $end_date);
}
$query = $CI->db->get();
$totalExpense = $query->row()->total_amount ?? 0;
$output['total_expense'] = app_format_money($totalExpense, get_base_currency());

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="' . admin_url('purchase/payment_invoice/' . $aRow['payment_id']) . '" target="_blank">' . $aRow['payment_id'] . '</a>';
    $row[] = '<span data-order="' . strtotime($aRow['pay_date']) . '">' . _d($aRow['pay_date']) . '</span>';
    $row[] = app_format_money($aRow['amount'] ?? 0, get_base_currency());
    $row[] = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['pur_invoice_id']) . '" target="_blank">' . $aRow['pur_invoice_id'] . '</a>';
    $row[] = $aRow['paymentmode'] ?? '';
    $row[] = $aRow['transactionid'] ?? '';
    $row[] = $aRow['note'];
    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_company'] . '</a>';
    $row[] = $aRow['vendor_id'];

    $output['aaData'][] = $row;
}

echo json_encode($output); exit;