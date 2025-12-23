<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();
$start_date = $CI->input->get('start_date') ?? date("Y-m-d");
$end_date = $CI->input->get('end_date') ?? date("Y-m-d");

$aColumns = [
    'tblpur_debit_notes.id as debit_id',
    'tblpur_debit_notes.date as debit_date',
    'tblpur_debit_notes.total as amount',
    'tblpur_debit_notes.number as debit_number',
    'tblpur_debit_notes.status as status',
    'tblpur_debit_notes.reference_no as reference_no',
    '(SELECT tblpur_debit_notes.total - (
      (SELECT COALESCE(SUM(amount),0) FROM tblpur_debits WHERE tblpur_debits.debit_id=tblpur_debit_notes.id)
      +
      (SELECT COALESCE(SUM(amount),0) FROM tblpur_debits_refunds WHERE tblpur_debits_refunds.debit_note_id=tblpur_debit_notes.id)
    )) as remaining_amount',
    'tblpur_vendor.company as vendor_company',
    'tblpur_vendor.userid as vendor_id',
    'tblcurrencies.name as currency_name'
];

$sIndexColumn = 'tblpur_debit_notes.id';
$sTable       = db_prefix() . 'pur_debit_notes';

$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_debit_notes.vendorid',
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'pur_debit_notes.currency'
];

$where = [];
if ($start_date) {
    $where[] = 'AND tblpur_debit_notes.date >= "' . $start_date . '"';
}
if ($end_date) {
    $where[] = 'AND tblpur_debit_notes.date <= "' . $end_date . '"';
}

// Permission check
if (!has_permission('purchase_debit_notes', '', 'view')) {
    $where[] = 'AND (tblpur_debit_notes.addedfrom = '.get_staff_user_id().' OR tblpur_debit_notes.vendorid IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))';
}

$additionalSelect = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

// Total Debit Notes Amount
$CI->db->select_sum('total', 'total_amount');
$CI->db->from(db_prefix() . 'pur_debit_notes');
if ($start_date) {
    $CI->db->where('date >=', $start_date);
}
if ($end_date) {
    $CI->db->where('date <=', $end_date);
}
$query = $CI->db->get();
$totalDebits = $query->row()->total_amount ?? 0;
$output['total_debits'] = app_format_money($totalDebits, get_base_currency());

foreach ($rResult as $aRow) {
    $row = [];

    $numberOutput = '<a href="' . admin_url('purchase/debit_notes/' . $aRow['debit_id']) . '" target="_blank">' . format_debit_note_number($aRow['debit_id']) . '</a>';
    
    $numberOutput .= '<div class="row-options">';
    if (has_permission('purchase_debit_notes', '', 'edit')) {
        $numberOutput .= '<a href="' . admin_url('purchase/debit_note/' . $aRow['debit_id']) . '">' . _l('edit') . '</a>';
    }
    $numberOutput .= '</div>';

    $row[] = $numberOutput;
    $row[] = '<span data-order="' . strtotime($aRow['debit_date']) . '">' . _d($aRow['debit_date']) . '</span>';
    $row[] = app_format_money($aRow['amount'], $aRow['currency_name']);
    $row[] = $aRow['debit_number'];
    $row[] = format_debit_note_status($aRow['status']);
    $row[] = $aRow['reference_no'];
    $row[] = app_format_money($aRow['remaining_amount'], $aRow['currency_name']);
    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . $aRow['vendor_company'] . '</a>';
    $row[] = $aRow['vendor_id'];

    $output['aaData'][] = $row;
}

echo json_encode($output); 
exit;