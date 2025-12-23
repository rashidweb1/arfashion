<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nex_reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('currencies_model'); 
        $this->load->library('app_modules');     
        
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);        
    }

    public function index()
    {
        $data['title'] = _l('nex_reports');
        $this->load->view('nex_reports_view', $data);
    }

    public function cashflow()
    {
        $data['title'] = _l('Cashflow');
        $this->load->view('cashflow', $data);
    }  
    
    public function income_table()
    {
        $this->app->get_table_data(module_views_path('nex_reports', 'tables/nex_income_table'));
    }    
    public function expense_table()
    {
        $this->app->get_table_data(module_views_path('nex_reports', 'tables/nex_expense_table'));
    }     
    
    public function purchase_expense_table()
    {
        $this->app->get_table_data(module_views_path('nex_reports', 'tables/purchase_expense_table'));
    }   
    
    public function debit_note_table()
    {
        $this->app->get_table_data(module_views_path('nex_reports', 'tables/debit_note_table'));
    }  
    
    public function cashflow_group($param=null)
    {
        if($param == 'income_table') {
            $this->app->get_table_data(module_views_path('nex_reports', 'tables/nex_income_table_group'));
            return true;
        }

        if($param == 'income_by_gid_modal') {
            $data['group_id'] = $this->input->get('group_id');
            $data['amount'] = $this->input->get('amount');
            $this->load->view('modals/income_by_gid_modal', $data);
            return true;
        }

        if($param == 'purchase_expense_by_gid_modal') {
            $data['group_id'] = $this->input->get('group_id');
            $data['amount'] = $this->input->get('amount');
            $this->load->view('modals/purchase_expense_by_gid_modal', $data);
            return true;
        }        

        if($param == 'purchase_expense_table') {
            $this->app->get_table_data(module_views_path('nex_reports', 'tables/nex_purchase_expense_table_group'));
            return true;
        }   
        
        if($param == 'expense_table') {
            $this->app->get_table_data(module_views_path('nex_reports', 'tables/nex_expense_table_group'));
            return true;
        }

        if($param == 'debit_note_table') {
            $this->app->get_table_data(module_views_path('nex_reports', 'tables/nex_debit_note_table_group'));
            return true;
        }

        $data['title'] = _l('Cashflow');
        $this->load->view('cashflow-group', $data);
    }   
    
    public function centralize_expense($param1 = "", $param2 = ""){

        if($param1 == 'components') {
            $data['param2'] = $param2;
            $this->load->view('components/expense/'.$this->input->post('type'), $data);
            return true;   
        }

        $this->load->view('modals/centralize_expense_modal', $data);
        return true;
    }
}