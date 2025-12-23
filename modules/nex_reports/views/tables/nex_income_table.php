<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();

$start_date = $CI->input->get('start_date') ?? date("Y-m-d");
$end_date = $CI->input->get('end_date') ?? date("Y-m-d");

//Table Records
$aColumns = [
    'tblinvoicepaymentrecords.id as payment_id',
    'tblinvoicepaymentrecords.date as payment_date',
    'tblinvoicepaymentrecords.amount as amount',
    'tblinvoicepaymentrecords.invoiceid as invoiceid',
    'tblpayment_modes.name as paymentmode',
    'tblinvoicepaymentrecords.transactionid as transactionid',
    'tblinvoicepaymentrecords.note as note',
    'tblclients.company as client_company',
    'tblinvoices.clientid as clientid',
    "CONCAT('#', tblinvoices.prefix, '', tblinvoices.number) as formatted_invoice_number"
];
//'tblinvoicepaymentrecords.id as payment_id',

$sIndexColumn = 'tblinvoicepaymentrecords.id';
$sTable       = db_prefix() . 'invoicepaymentrecords';

$join = [
    'JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
    'JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
    'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode'
];

$additionalSelect = [];
$where = [];

if ($start_date) {
    $where[] = 'AND tblinvoicepaymentrecords.date >= "' . $start_date . '"';
}
if ($end_date) {
    $where[] = 'AND tblinvoicepaymentrecords.date <= "' . $end_date . '"';
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

// Calculate total income

// Calculate total income
$CI->db->select_sum('tblinvoicepaymentrecords.amount', 'total_amount');
$CI->db->from(db_prefix() . 'invoicepaymentrecords');
$CI->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
$CI->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid');

if ($start_date) {
    $CI->db->where('tblinvoicepaymentrecords.date >=', $start_date);
}
if ($end_date) {
    $CI->db->where('tblinvoicepaymentrecords.date <=', $end_date);
}

// Apply same filters if needed
$query = $CI->db->get();
$totalIncome = $query->row()->total_amount ?? 0;
$output['total_income'] = app_format_money($totalIncome, get_base_currency());


foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<a href="' . admin_url('payments/payment/' . $aRow['payment_id']) . '" target="_blank">' . $aRow['payment_id'] . '</a>';
    $row[] = '<span data-order="' . strtotime($aRow['payment_date']) . '">' . _d($aRow['payment_date']) . '</span>';
    $row[] = app_format_money($aRow['amount'] ?? 0, get_base_currency());
    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">#' . format_invoice_number($aRow['invoiceid']) . '</a>';
    $row[] = $aRow['paymentmode'];
    $row[] = $aRow['transactionid'];
    $row[] = $aRow['note'];
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['client_company'] . '</a>';
    $row[] = $aRow['clientid']; 
    $row[] = $aRow['formatted_invoice_number'];     

    $output['aaData'][] = $row;
}

echo json_encode($output);exit;