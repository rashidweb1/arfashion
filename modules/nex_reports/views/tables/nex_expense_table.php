<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();

$start_date = $CI->input->get('start_date') ?? date("Y-m-d");
$end_date = $CI->input->get('end_date') ?? date("Y-m-d");

$aColumns = [
    'tblexpenses.id as payment_id',
    'tblexpenses.date as expense_date',
    'tblexpenses.amount as amount',
    'tblexpenses_categories.name as category',
    'tblpayment_modes.name as paymentmode',
    'tblexpenses.note as note',
    'tblpur_vendor.company as vendor_company',
    'tblpur_vendor.userid as userid'
];

$sIndexColumn = 'tblexpenses.id';
$sTable       = db_prefix() . 'expenses';

$join = [
    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
    'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'expenses.vendor'
];

$where = [];

if ($start_date) {
    $where[] = 'AND tblexpenses.date >= "' . $start_date . '"';
}
if ($end_date) {
    $where[] = 'AND tblexpenses.date <= "' . $end_date . '"';
}

$additionalSelect = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

// Calculate total expense
$CI->db->select_sum('tblexpenses.amount', 'total_amount');
$CI->db->from(db_prefix() . 'expenses');
$CI->db->join(db_prefix() . 'expenses_categories', db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category', 'left');
$CI->db->join(db_prefix() . 'payment_modes', db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode', 'left');
$CI->db->join(db_prefix() . 'pur_vendor', db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'expenses.vendor', 'left');

if ($start_date) {
    $CI->db->where('tblexpenses.date >=', $start_date);
}
if ($end_date) {
    $CI->db->where('tblexpenses.date <=', $end_date);
}

$query = $CI->db->get();
$totalExpense = $query->row()->total_amount ?? 0;
$output['total_expense'] = app_format_money($totalExpense, get_base_currency());

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<a href="' . admin_url('expenses/#' . $aRow['payment_id']) . '" target="_blank">' . $aRow['payment_id'] . '</a>';
    $row[] = '<span data-order="' . strtotime($aRow['expense_date']) . '">' . _d($aRow['expense_date']) . '</span>';
    $row[] = app_format_money($aRow['amount'] ?? 0, get_base_currency());
    $row[] = $aRow['category'] ?? '';
    $row[] = $aRow['paymentmode'] ?? '';
    $row[] = $aRow['note'] ?? '';
    if (!empty($aRow['vendor_company']) && !empty($aRow['userid'])) {
        $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['userid']) . '" target="_blank">' . $aRow['vendor_company'] . '</a>';
        $row[] = $aRow['userid'];
    } else {
        $row[] = '';
        $row[] = '';
    }

    $output['aaData'][] = $row;
}

echo json_encode($output);exit;
