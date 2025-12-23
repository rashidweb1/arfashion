<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <!-- <label for="start_date">Start Date</label> -->
                                <input type="date" id="start_date" value="<?php echo date("Y-m-d") ?>" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <!-- <label for="end_date">End Date</label> -->
                                <input type="date" id="end_date" value="<?php echo date("Y-m-d") ?>" class="form-control" />
                            </div>
                            <div class="col-md-3 align-self-end">
                                <button id="filter-date" class="btn btn-primary">Filter</button>
                                <button id="reset-filter" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="panel_s">
                <div class="panel-body">
                    <h5 class="tw-mb-2 tw-mt-0 tw-font-bold">Financial Summary</h5>
                    <div class="tw-flex tw-flex-col tw-gap-1 text-sm">
                    <div class="tw-flex tw-justify-between">
                        <span>Total Income</span>
                        <span id="total-income" class="tw-font-medium">₹0.00</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Total Expenses</span>
                        <span id="total-expense" class="tw-font-medium">₹0.00</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Total Purchase Expenses</span>
                        <span id="total-purchase-expense" class="tw-font-medium">₹0.00</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Total Debit Notes</span>
                        <span id="total-debit-note" class="tw-font-medium">₹0.00</span>
                    </div>                    
                    <!-- <hr> -->
                    <div class="tw-mt-2 tw-flex tw-justify-between tw-font-bold">
                        <span>Net Balance</span>
                        <span id="net-balance">₹0.00</span>
                    </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="col-md-12" id="income-report">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="table-heading">Income Report</h5>
                        <?php
                        render_datatable([
                        'Payment #',
                        'Date',
                        'Amount',
                        'Invoice ID',
                        'Mode',
                        'Transaction ID',
                        'Note',
                        'Customer',
                        [
                            'name'    => 'Client ID',    // DB column name or unique identifier
                            'th_attrs' => ['class' => 'not-export'], // optional
                            'visible' => false,         // hide this column
                        ],
                        [
                            'name'    => 'Invoice Number',    // DB column name or unique identifier
                            'th_attrs' => ['class' => 'not-export'], // optional
                            'visible' => false,         // hide this column
                        ],                        
                        ], 'income-table', ['table-small']);
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="expense-report">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="table-heading">Expense Report</h5>
                        <?php
                        render_datatable([
                            'Payment #',
                            'Date',
                            'Amount',
                            'Category',
                            'Mode',
                            'Note',
                            'Vendor',                            
                            [
                                'name'    => 'Vendor ID',  
                                'th_attrs' => ['class' => 'not-export'],
                                'visible' => false, 
                            ]                           
                        ], 'expense-table', ['table-small']);
                        ?>
                    </div>
                </div>
            </div> 
            
            <div class="col-md-12" id="purchase-expense-report">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="table-heading">Purchase Expense Report</h5>
                        <?php
                        render_datatable([
                            'Payment #',
                            'Date',
                            'Amount',
                            'Purchase Invoice ID',
                            'Mode',
                            'Transaction ID',
                            'Note',
                            'Vendor',
                            [
                                'name'    => 'Vendor ID',  
                                'th_attrs' => ['class' => 'not-export'],
                                'visible' => false, 
                            ]                            
                        ], 'purchase-expense-table', ['table-small']);
                        ?>
                    </div>
                </div>
            </div>  
            
            <div class="col-md-12" id="debit-note-report">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="table-heading">Debit Note Report</h5>
                        <?php
                        $table_columns = [
                            'Debit Note Number',
                            'Date',
                            'Amount',
                            'Debit Note Number Short', 
                            'Status',
                            'Reference No',
                            'Remaining Amount',
                            'Vendor',
                            [
                                'name'    => 'Vendor ID',  
                                'th_attrs' => ['class' => 'not-export'],
                                'visible' => false, 
                            ] 
                        ];
                        render_datatable($table_columns, 'debit-note-table', ['table-small']);
                        ?>
                    </div>
                </div>
            </div>           

        </div>
    </div>
</div>
<?php init_tail(); ?>
<style>
.table-heading {
    position: absolute;
    /*top: 4%;*/
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    z-index: 10;
}

/* Reduce padding inside table cells only for .table-small */
.table-small > tbody > tr > td,
.table-small > tbody > tr > th,
.table-small > tfoot > tr > td,
.table-small > tfoot > tr > th,
.table-small > thead > tr > td,
.table-small > thead > tr > th {
    padding: 4px;
}

/* Smaller font size for .table-small */
.table-small {
    font-size: 12px;
}

/* Optional: adjust sorting icon alignment for .table-small */
.table-small.dataTable thead .sorting:after {
    content: "";
    height: 18px;
    margin-left: 5px;
    opacity: 0;
    top: 5px;
    transition: opacity 0.3s ease-out;
    width: 18px;
}
</style>

<script>

  // Function to calculate and update net balance
  function updateNetBalance() {
      const income = parseFloat($('#total-income').text().replace(/[^0-9.-]+/g,"")) || 0;
      const expense = parseFloat($('#total-expense').text().replace(/[^0-9.-]+/g,"")) || 0;
      const purchaseExpense = parseFloat($('#total-purchase-expense').text().replace(/[^0-9.-]+/g,"")) || 0;
      const debitNote = parseFloat($('#total-debit-note').text().replace(/[^0-9.-]+/g,"")) || 0;
      var currencySymbol = '<?php echo $this->currencies_model->get_base_currency()->symbol; ?>';
      
      // Subtract expenses and debit notes from income
      const netBalance = income - (expense + purchaseExpense + debitNote);
      
      // Format the number with 2 decimal places and currency symbol
      const formattedBalance = currencySymbol + netBalance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      
      // Update the net balance display
      const $netBalanceElement = $('#net-balance');
      $netBalanceElement.text(formattedBalance);
      
      // Change color based on positive/negative
      if (netBalance >= 0) {
          $netBalanceElement.removeClass('text-danger').addClass('text-success');
      } else {
          $netBalanceElement.removeClass('text-success').addClass('text-danger');
      }
  }

  // Reusable function to initialize a report table with filter/reset support
  function initTableWithFilter(selector, urlSegment, hiddenColumns = [], totalFieldId = null, totalFieldKey = null) {
    const table = initDataTable(selector, admin_url + 'nex_reports/' + urlSegment, [], [], {});
    
    table.page.len(10).draw();
    table.order([0, 'desc']).draw();

    if (totalFieldId && totalFieldKey) {
      table.on('xhr', function () {
        const json = table.ajax.json();
        if (json[totalFieldKey]) {
          if($('#' + totalFieldId).length > 0){
            $('#' + totalFieldId).html(json[totalFieldKey]);
            updateNetBalance();
          }
        }
      });
    }

    // Hide specified columns after DataTable is initialized
    if (hiddenColumns.length) {
      setTimeout(() => {
        hiddenColumns.forEach(index => {
          console.log(index);
          table.column(index).visible(false);
        });
      }, 1000);
    }

    // Date filter logic
    $('#filter-date').on('click', function () {
      const startDate = $('#start_date').val();
      const endDate = $('#end_date').val();
      const query = `?start_date=${startDate}&end_date=${endDate}`;
      table.ajax.url(admin_url + 'nex_reports/' + urlSegment + query).load();
    });

    // Reset logic
    $('#reset-filter').on('click', function () {
      $('#start_date').val('<?php echo date("Y-m-d") ?>');
      $('#end_date').val('<?php echo date("Y-m-d") ?>');
      table.ajax.url(admin_url + 'nex_reports/' + urlSegment).load();
    });
  }

  // Initialize all 3 reports on DOM ready
  $(function () {
    // Income Report
    initTableWithFilter(
      '.table-income-table',
      'income_table',
      [8,9],
      'total-income',
      'total_income'
    );

    // Expense Report
    initTableWithFilter(
      '.table-expense-table',
      'expense_table',
      [7],
      'total-expense',
      'total_expense'
    );

    // Purchase Expense Report
    initTableWithFilter(
      '.table-purchase-expense-table',
      'purchase_expense_table',
      [8],
      'total-purchase-expense',
      'total_expense'
    );

    // Debit Note Report
    initTableWithFilter(
        '.table-debit-note-table',
        'debit_note_table',
        [8], // Column index to hide (Vendor ID)
        'total-debit-note', // Total field ID
        'total_debits'     // JSON response key from server (matches your PHP code)
    );    
  });

    $(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section');

    if (section) {
        const $target = $('#' + section);
        if ($target.length) {
        $('html, body').animate({
            scrollTop: $target.offset().top - 100 // Adjust offset as needed
        }, 800);
        }
    }
    });  
</script>

<!--<script>
//Income Report
$(function () {
  var tableIncome = initDataTable('.table-income-table', admin_url + 'nex_reports/income_table', [], [], {});

  tableIncome.page.len(10).draw();
  tableIncome.order([0, 'desc']).draw();

  tableIncome.on('xhr', function () {
    var json = tableIncome.ajax.json();
    if (json.total_income) {
      $('#total-income').html(json.total_income);
    }
  });  

  setTimeout(function() {
    tableIncome.column(3).visible(false);
    tableIncome.column(4).visible(false);
  }, 500);

   // On filter button click reload table with dates
  $('#filter-date').on('click', function () {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();

    // Reload DataTable with extra params
    tableIncome.ajax.url(admin_url + 'nex_reports/income_table?start_date=' + start_date + '&end_date=' + end_date).load();
  });

  // Reset button clears filters
  $('#reset-filter').on('click', function () {
    $('#start_date').val('');
    $('#end_date').val('');
    tableIncome.ajax.url(admin_url + 'nex_reports/income_table').load();
  }); 
});

//Expense Report
$(function () {
  var tableExpense = initDataTable('.table-expense-table', admin_url + 'nex_reports/expense_table', [], [], {});

  tableExpense.page.len(10).draw();
  tableExpense.order([0, 'desc']).draw();

  // Handle total expense display
    tableExpense.on('xhr', function () {
    var json = tableExpense.ajax.json();
    if (json.total_expense) {
        $('#total-expense').html(json.total_expense);
    }
    });

  // Date filter
  $('#filter-date').on('click', function () {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    tableExpense.ajax.url(admin_url + 'nex_reports/expense_table?start_date=' + start_date + '&end_date=' + end_date).load();
  });

  // Reset date filter
  $('#reset-filter').on('click', function () {
    $('#start_date').val('');
    $('#end_date').val('');
    tableExpense.ajax.url(admin_url + 'nex_reports/expense_table').load();
  });
});

//Purchase Report
$(function () {
  var tablePurchase = initDataTable('.table-purchase-expense-table', admin_url + 'nex_reports/purchase_expense_table', [], [], {});

  tablePurchase.page.len(10).draw();
  tablePurchase.order([0, 'desc']).draw();

  tablePurchase.on('xhr', function () {
    var json = tablePurchase.ajax.json();
    if (json.total_expense) {
      $('#total-purchase-expense').html(json.total_expense);
    }
  });

  setTimeout(function () {
    tablePurchase.column(6).visible(false);
  }, 500);  

  $('#filter-date').on('click', function () {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    tablePurchase.ajax.url(admin_url + 'nex_reports/purchase_expense_table?start_date=' + start_date + '&end_date=' + end_date).load();
  });

  $('#reset-filter').on('click', function () {
    $('#start_date').val('');
    $('#end_date').val('');
    tablePurchase.ajax.url(admin_url + 'nex_reports/purchase_expense_table').load();
  });
});

</script>-->

<!-- <div class="col-md-4">
    <div class="panel_s">
        <div class="panel-body">
            <div class="tw-flex tw-justify-between tw-items-center">
                <h5 class="tw-mb-1 tw-mt-0">Total Income</h5>
                <div class="tw-text-2xl tw-font-bold tw-text-success" id="total-income"></div>
            </div>                            
        </div>
    </div>
</div>                
<div class="col-md-4">
    <div class="panel_s">
        <div class="panel-body">
            <div class="tw-flex tw-justify-between tw-items-center">
                <h5 class="tw-mb-1 tw-mt-0">Total Expense </h5>
                <div class="tw-text-2xl tw-font-bold tw-text-success" id="total-expense"></div>
            </div>                            
        </div>
    </div>
</div> 
<div class="col-md-4">
    <div class="panel_s">
        <div class="panel-body">
            <div class="tw-flex tw-justify-between tw-items-center">
                <h5 class="tw-mb-1 tw-mt-0">Total Purchase Expense</h5>
                <div class="tw-text-2xl tw-font-bold tw-text-success" id="total-purchase-expense"></div>
            </div>                            
        </div>
    </div>
</div>  -->