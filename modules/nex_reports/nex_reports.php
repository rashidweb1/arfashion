<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Nex Reports
Description: A simple custom reporting module
Version: 1.0
Author: Your Name
*/

define('NEX_REPORTS_MODULE_NAME', 'nex_reports');

hooks()->add_action('admin_init', 'nex_reports_module_init_menu_items');
hooks()->add_action('app_admin_head', 'nex_reports_module_head');

// function nex_reports_module_init_menu_items() {
//     $CI = &get_instance();
//     if (is_admin()) {
//         // Non-clickable Parent menu
//         $CI->app_menu->add_sidebar_menu_item('nex_reports', [
//             'slug'     => 'nex-reports',
//             'name'     => _l('AR Reports'), 
//             'icon'     => 'fa fa-bar-chart', 
//             'href'     => '#', // <-- Makes it non-clickable
//             'position' => 90,
//         ]);

//         // Submenu item - Cashflow
//         $CI->app_menu->add_sidebar_children_item('nex_reports', [
//             'slug'     => 'nex-reports-cashflow',
//             'name'     => _l('Cashflow'),
//             'href'     => admin_url('nex_reports/cashflow'),
//             'position' => 1,
//         ]);
//     }
// }

function nex_reports_module_init_menu_items()
{
    $CI = &get_instance();    

    // Parent menu
    $CI->app_menu->add_sidebar_menu_item('menu', [
        'name' => _l('AR Reports'),
        'icon' => 'fa fa-rupee',
        'href' => admin_url('#'),
        'position' => 90,
    ]);  

    // Submenu
    $CI->app_menu->add_sidebar_children_item('menu', [
        'slug' => 'nex_reports_order_list',
        'name' => _l('Cashflow'),
        //'icon' => 'fa fa-rupee',                
        'href' => admin_url('nex_reports/cashflow_group'),
    ]);    

    // Submenu
    // $CI->app_menu->add_sidebar_children_item('menu', [
    //     'slug' => 'nex_reports_order_list',
    //     'name' => _l('Cashflow Details'),
    //     //'icon' => 'fa fa-rupee',                
    //     'href' => admin_url('nex_reports/cashflow'),
    // ]);          
}

function nex_reports_module_head() {
    echo '<!-- Nex Reports Module -->';
}