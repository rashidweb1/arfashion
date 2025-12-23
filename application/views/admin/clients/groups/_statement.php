<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-5 account-summary">
    <div class="text-right">
        <h4 class="no-margin bold"><?php echo _l('account_summary'); ?></h4>
        <p class="text-muted"><?php echo e(_l('statement_from_to', [$from, $to])); ?></p>
        <hr />
        <table class="table statement-account-summary">
            <tbody>
                <tr>
                    <td class="text-left"><?php echo _l('statement_beginning_balance'); ?>:</td>
                    <td><?php echo e(app_format_money($statement['beginning_balance'], $statement['currency'])); ?></td>
                </tr>
                <tr>
                    <td class="text-left"><?php echo _l('invoiced_amount'); ?>:</td>
                    <td><?php echo e(app_format_money($statement['invoiced_amount'], $statement['currency'])); ?></td>
                </tr>
                <tr>
                    <td class="text-left"><?php echo _l('amount_paid'); ?>:</td>
                    <td><?php echo e(app_format_money($statement['amount_paid'], $statement['currency'])); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-left"><b><?php echo _l('balance_due'); ?></b>:</td>
                    <td><?php echo e(app_format_money($statement['balance_due'], $statement['currency'])); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- <div class="col-md-12">
    <div class="text-center mb-3">
        <button id="btnShowGrouped" class="btn btn-secondary btn-sm">Grouped Records</button>
        <button id="btnShowOriginal" class="btn btn-secondary btn-sm">Original Records</button>
    </div>
</div> -->

<div class="col-md-12">
    <div class="text-center bold padding-10">
        <?php echo _l('customer_statement_info', [$from, $to]); ?>
    </div>
    <div class="table-responsive">
        <table id="originalTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><b><?php echo _l('statement_heading_date'); ?></b></th>
                    <th><b><?php echo _l('statement_heading_details'); ?></b></th>
                    <th class="text-right"><b><?php echo _l('statement_heading_amount'); ?></b></th>
                    <th class="text-right"><b><?php echo _l('statement_heading_payments'); ?></b></b></th>
                    <th class="text-right"><b><?php echo _l('statement_heading_balance'); ?></b></b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo e($from); ?></td>
                    <td><?php echo _l('statement_beginning_balance'); ?></td>
                    <td class="text-right">
                        <?php echo e(app_format_money($statement['beginning_balance'], $statement['currency'], true)); ?>
                    </td>
                    <td></td>
                    <td class="text-right">
                        <?php echo e(app_format_money($statement['beginning_balance'], $statement['currency'], true)); ?>
                    </td>
                </tr>
                <?php
       $tmpBeginningBalance = $statement['beginning_balance'];
       $tds_amount = 0;
       foreach ($statement['result'] as $data) { ?>
                <tr>
                    <td><?php echo e(_d($data['date'])); ?></td>
                    <td>
                        <?php
            if (isset($data['invoice_id'])) {
                echo _l('statement_invoice_details', ['<a href="' . admin_url('invoices/list_invoices/' . $data['invoice_id']) . '" target="_blank">' . e(format_invoice_number($data['invoice_id'])) . '</a>', e(_d($data['duedate']))]);
            } elseif (isset($data['payment_id'])) {
                echo _l('statement_payment_details', ['<a href="' . admin_url('payments/payment/' . $data['payment_id']) . '" target="_blank">' . '#' . $data['payment_id'] . '</a>', e(format_invoice_number($data['payment_invoice_id']))]);
               
                $payment_mode = get_payment_mode_name($data['payment_id']);
                if (!empty($payment_mode)) {
                    echo '<br>';
                    echo 'Payment Mode : ' . e($payment_mode);

                    if(strtolower($payment_mode) == 'tds'){
                       $tds_amount += $data['payment_total'];
                    }
                }
            } elseif (isset($data['credit_note_id'])) {
                echo _l('statement_credit_note_details', ['<a href="' . admin_url('credit_notes/list_credit_notes/' . $data['credit_note_id']) . '" target="_blank">' . e(format_credit_note_number($data['credit_note_id'])) . '</a>']);
            } elseif (isset($data['credit_id'])) {
                echo _l(
                    'statement_credits_applied_details',
                    [
              '<a href="' . admin_url('credit_notes/list_credit_notes/' . $data['credit_applied_credit_note_id']) . '" target="_blank">' . e(format_credit_note_number($data['credit_applied_credit_note_id'])) . '</a>',
              e(app_format_money($data['credit_amount'], $statement['currency'], true)),
              e(format_invoice_number($data['credit_invoice_id'])),
            ]
                );
            } elseif (isset($data['credit_note_refund_id'])) {
                echo e(_l('statement_credit_note_refund', format_credit_note_number($data['refund_credit_note_id'])));
            }
          ?>
                    </td>
                    <td class="text-right">
                        <?php
          if (isset($data['invoice_id'])) {
              echo e(app_format_money($data['invoice_amount'], $statement['currency'], true));
          } elseif (isset($data['credit_note_id'])) {
              echo e(app_format_money($data['credit_note_amount'], $statement['currency'], true));
          }
          ?>
                    </td>
                    <td class="text-right">
                        <?php
          if (isset($data['payment_id'])) {
              echo e(app_format_money($data['payment_total'], $statement['currency'], true));
          } elseif (isset($data['credit_note_refund_id'])) {
              echo e(app_format_money($data['refund_amount'], $statement['currency'], true));
          }
          ?>
                    </td>
                    <td class="text-right">
                        <?php
          if (isset($data['invoice_id'])) {
              $tmpBeginningBalance = ($tmpBeginningBalance + $data['invoice_amount']);
          } elseif (isset($data['payment_id'])) {
              $tmpBeginningBalance = ($tmpBeginningBalance - $data['payment_total']);
          } elseif (isset($data['credit_note_id'])) {
              $tmpBeginningBalance = ($tmpBeginningBalance - $data['credit_note_amount']);
          } elseif (isset($data['credit_note_refund_id'])) {
              $tmpBeginningBalance = ($tmpBeginningBalance + $data['refund_amount']);
          }
          if (!isset($data['credit_id'])) {
              echo e(app_format_money($tmpBeginningBalance, $statement['currency'], true));
          }
          ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot class="statement_tfoot">
                <tr>
                    <td colspan="3" class="text-right">
                        <b><?php echo _l('balance_due'); ?></b>
                    </td>
                    <td class="text-right" colspan="2">
                        <b><?php echo e(app_format_money($statement['balance_due'], $statement['currency'])); ?></b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">
                        <b><?php echo _l('TDS'); ?></b>
                    </td>
                    <td class="text-right" colspan="2">
                        <b><?php echo e(app_format_money($tds_amount, $statement['currency'])); ?></b>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
// ----------- Group payments by group_id -----------
$groupedResults = [];
foreach ($statement['result'] as $row) {
    if (isset($row['payment_id']) && !empty($row['group_id'])) {
        $gid = $row['group_id'];

        // Initialize group if not exists
        if (!isset($groupedResults[$gid])) {
            $groupedResults[$gid] = $row;
            $groupedResults[$gid]['details_list'] = [];
        }

        // Add each payment detail into details_list
        $groupedResults[$gid]['details_list'][] = [
            'payment_id'    => $row['payment_id'],
            'invoice_id'    => $row['payment_invoice_id'],
            'payment_total' => $row['payment_total'],
            'payment_mode'  => get_payment_mode_name($row['payment_id']),
        ];

        // Sum payment_total
        if (isset($groupedResults[$gid]['payment_total'])) {
            $groupedResults[$gid]['payment_total'] += $row['payment_total'];
        } else {
            $groupedResults[$gid]['payment_total'] = $row['payment_total'];
        }

        // Always keep the last note & date (latest tmp_date)
        if (!isset($groupedResults[$gid]['tmp_date']) || $row['tmp_date'] > $groupedResults[$gid]['tmp_date']) {
            $groupedResults[$gid]['tmp_date'] = $row['tmp_date'];
            $groupedResults[$gid]['date']     = $row['date'];
            $groupedResults[$gid]['note']     = $row['note'];
        }
    } else {
        // Keep invoices/credits/etc. untouched
        $groupedResults[] = $row;
    }
}

// Convert associative groups back to indexed array & sort by tmp_date
$groupedResults = array_values($groupedResults);
usort($groupedResults, function ($a, $b) {
    return strtotime($a['tmp_date']) <=> strtotime($b['tmp_date']);
});
?>

<div class="col-md-12">
    <div class="table-responsive group-table-responsive">
        <table id="groupedTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><b><?= _l('statement_heading_date') ?></b></th>
                    <th><b><?= _l('statement_heading_details') ?></b></th>
                    <th class="text-right"><b><?= _l('statement_heading_amount') ?></b></th>
                    <th class="text-right"><b><?= _l('statement_heading_payments') ?></b></th>
                    <th class="text-right"><b><?= _l('statement_heading_balance') ?></b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= e($from) ?></td>
                    <td><?= _l('statement_beginning_balance') ?></td>
                    <td class="text-right">
                        <?= app_format_money($statement['beginning_balance'], $statement['currency'], true) ?>
                    </td>
                    <td></td>
                    <td class="text-right">
                        <?= app_format_money($statement['beginning_balance'], $statement['currency'], true) ?>
                    </td>
                </tr>
                
                <?php
                $tmpBeginningBalance = $statement['beginning_balance'];
                $tds_amount = 0;
                
                foreach ($groupedResults as $data) :
                ?>
                <tr>
                    <td><?= _d($data['date']) ?></td>
                    <td>
                        <?php
                        if (isset($data['invoice_id'])) {
                            echo _l('statement_invoice_details', [
                                '<a href="' . admin_url('invoices/list_invoices/' . $data['invoice_id']) . '" target="_blank">' . 
                                e(format_invoice_number($data['invoice_id'])) . '</a>',
                                e(_d($data['duedate']))
                            ]);
                        } elseif (isset($data['payment_id'])) {
                            // Show last note on top (latest record)
                            if (!empty($data['note'])) {
                                echo '<b>' . e($data['note']) . '</b><br>';
                            }
                            
                            echo '<span style="font-size: 12px;">';
                            
                            // Loop through stored details
                            if (!empty($data['details_list'])) {
                                foreach ($data['details_list'] as $d) {
                                    echo _l('statement_payment_details', [
                                        '<a href="' . admin_url('payments/payment/' . $d['payment_id']) . '" target="_blank">#' . 
                                        $d['payment_id'] . '</a>',
                                        e(format_invoice_number($d['invoice_id']))
                                    ]);

                                    if (!empty($d['payment_mode'])) {
                                        echo ' (Mode: ' . e($d['payment_mode']) . ')';
                                        if (strtolower($d['payment_mode']) == 'tds') {
                                            $tds_amount += $d['payment_total'];
                                        }
                                    }
                                    echo "<br>";
                                }
                            }
                            echo '</span>';
                        } elseif (isset($data['credit_note_id'])) {
                            echo _l('statement_credit_note_details', [
                                '<a href="' . admin_url('credit_notes/list_credit_notes/' . $data['credit_note_id']) . 
                                '" target="_blank">' . e(format_credit_note_number($data['credit_note_id'])) . '</a>'
                            ]);
                        } elseif (isset($data['credit_id'])) {
                echo _l(
                    'statement_credits_applied_details',
                    [
              '<a href="' . admin_url('credit_notes/list_credit_notes/' . $data['credit_applied_credit_note_id']) . '" target="_blank">' . e(format_credit_note_number($data['credit_applied_credit_note_id'])) . '</a>',
              e(app_format_money($data['credit_amount'], $statement['currency'], true)),
              e(format_invoice_number($data['credit_invoice_id'])),
            ]
                );
            }
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        if (isset($data['invoice_id'])) {
                            echo app_format_money($data['invoice_amount'], $statement['currency'], true);
                        } elseif (isset($data['credit_note_id'])) {
                            echo app_format_money($data['credit_note_amount'], $statement['currency'], true);
                        }
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        if (isset($data['payment_id'])) {
                            echo app_format_money($data['payment_total'], $statement['currency'], true);
                        }
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        if (isset($data['invoice_id'])) {
                            $tmpBeginningBalance += $data['invoice_amount'];
                        } elseif (isset($data['payment_id'])) {
                            $tmpBeginningBalance -= $data['payment_total'];
                        }
                        echo app_format_money($tmpBeginningBalance, $statement['currency'], true);
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><b><?= _l('balance_due') ?></b></td>
                    <td colspan="2" class="text-right"><b><?= app_format_money($statement['balance_due'], $statement['currency']) ?></b></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><b><?= _l('TDS') ?></b></td>
                    <td colspan="2" class="text-right"><b><?= app_format_money($tds_amount, $statement['currency']) ?></b></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // By default, grouped table is visible
    $("#groupedTable").show();
    $("#originalTable").hide();
    $("#btnShowGrouped").focus();

    // Show Grouped Table
    $("body").on("click", "#btnShowGrouped", function() {
        $("#originalTable").hide();
        $("#groupedTable").show();
    });

    // Show Original Table
    $("body").on("click", "#btnShowOriginal", function() {
        $("#groupedTable").hide();
        $("#originalTable").show();
    });
});
</script>

<style>
table.table {
    margin-top: 25px;
    font-size: 12px;
}
</style>


<!--Printable Area Code-->
<div id="printArea" style="display:none">
  <div class="organization-printable"></div>
  <div class="row">
    <div class="col-md-6 customer-printable"></div>
    <div class="col-md-6 summery-printable"></div>
  </div>
  <div class="text-center"><?php echo _l('customer_statement_info',array($from,$to)); ?></div>
  <div class="table-printable"></div>
</div>
<style>
  /* Print: Remove hyperlink styles and URLs */
  @media print {
    a[href]:after {
      content: none !important;
    }
    a {
      text-decoration: none !important;
      color: black !important;
    }
  }

  /* Print: Force Bootstrap grid columns to behave */
  @media print {
    .col-md-6 {
      float: left;
      width: 50% !important;
    }
    .row::after {
      content: "";
      display: table;
      clear: both;
    }
  }

@media print {
  .no-print,
  .page-title,
  header,
  title,
  footer {
    display: none !important;
    visibility: hidden !important;
  }
} 

</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>

<script>
  $("#printBtn").off("click").on("click", function () {
    // Fill print sections with dynamic HTML
    $(".table-printable").html($(".group-table-responsive").html());
    $(".organization-printable").html($(".format_organization_info").html());
    $(".customer-printable").html($(".format_customer_info").html());
    $(".summery-printable").html($(".account-summary").html());

    $("#printArea").show();

    // Print after DOM updates
    setTimeout(function () {
      $("#printArea").printThis({
        importCSS: true,
        importStyle: true,
        beforePrintEvent: function ($iframe) {
          // Strip links inside the print iframe
          $iframe.find("a").each(function () {
            var $link = $(this);
            var text = $link.text();
            $link.replaceWith(text);
          });
        },
        afterPrint: function () {
            // Hide again after printing
            $("#printArea").hide();
        }        
      });
    }, 500); // Delay ensures updated DOM is cloned
  });
</script>
<!--Printable Area Code-->
