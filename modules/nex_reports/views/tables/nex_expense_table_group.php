<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();

$start_date = $CI->input->get('start_date') ?? date("Y-m-d");
$end_date = $CI->input->get('end_date') ?? date("Y-m-d");

// Get logged-in staff ID
$staff_id = get_staff_user_id();

// Staff filter: hide rows with paymentmode = 3, null, or empty for staff ID = 1
$staff_condition = '';
if ($staff_id == 1) {
    $staff_condition = ' AND tblexpenses.paymentmode IS NOT NULL AND tblexpenses.paymentmode != "" AND tblexpenses.paymentmode != 3';
}

$aColumns = [
    'tblexpenses.date as expense_date',
    'tblexpenses.amount as amount',
    'tblpur_vendor.company as vendor_company',
    'tblexpenses.note as note',
    'tblpayment_modes.name as paymentmode',
    'tblexpenses_categories.name as category',
    'tblexpenses.id as payment_id',
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

// Append staff condition
if ($staff_condition) {
    $where[] = $staff_condition;
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

if ($staff_id == 1) {
    $CI->db->where('tblexpenses.paymentmode IS NOT NULL');
    $CI->db->where('tblexpenses.paymentmode != ""');
    $CI->db->where('tblexpenses.paymentmode != 3');
}

$query = $CI->db->get();
$totalExpense = $query->row()->total_amount ?? 0;
$output['total_expense'] = app_format_money($totalExpense, get_base_currency());

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<span data-order="' . strtotime($aRow['expense_date']) . '">' . _d($aRow['expense_date']) . '</span>';
    $row[] = app_format_money($aRow['amount'] ?? 0, get_base_currency());
    $row[] = $aRow['vendor_company'] ? '<a href="' . admin_url('purchase/vendor/' . $aRow['userid']) . '" target="_blank">' . $aRow['vendor_company'] . '</a>' : null;
    $row[] = $aRow['note'] ?? '';
    $row[] = $aRow['paymentmode'] ?? '';
    $row[] = $aRow['category'] ?? '';
    $row[] = '<a href="' . admin_url('expenses/#' . $aRow['payment_id']) . '" target="_blank">' . $aRow['payment_id'] . '</a>';
    $row[] = $aRow['userid'];
    $output['aaData'][] = $row;
}

echo json_encode($output);exit;
