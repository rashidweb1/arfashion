<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="centralize-expense-payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Expense</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- ! -->
                <div class="expense-type-options">
                    <label class="expense-option">
                        <input type="radio" name="expense_type" value="regular"> Regular Expense
                    </label>

                    <label class="expense-option">
                        <input type="radio" name="expense_type" value="purchase"> Purchase Expense
                    </label>

                    <label class="expense-option">
                        <input type="radio" name="expense_type" value="salary"> Salary Expense
                    </label>

                    <label class="expense-option">
                        <input type="radio" name="expense_type" value="payment"> Payment Notes
                    </label>
                </div>
                <hr>
                <!-- Section container -->
                <div id="expense-section-container">Please select an option above</div>
            
                <!-- ! -->
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
$(function(){
    $('input[name="expense_type"]').on('change', function(){
        var selected = $(this).val();
        var $container = $("#expense-section-container");

        $container.html('<p>Loading...</p>'); // optional loader

        $.ajax({
            url: admin_url + "nex_reports/centralize_expense/components",   // single handler file
            type: "POST",
            data: { type: selected },             // pass selected as data
            success: function(response){
                $container.html(response);
            },
            error: function(xhr, status, error){
                $container.html('<p style="color:red;">Error loading section: ' + error + '</p>');
            }
        });
    });
});
</script>

<style>
/* ----- CSS (pure, keeps your HTML unchanged) ----- */

.expense-type-options {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 15px;
}

.expense-option {
  position: relative;
  padding: 8px 8px;
  border: 2px solid #d6d6d6;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: border-color .15s ease, color .15s ease, box-shadow .15s ease;
  flex: 1;
  text-align: center;
  color: #222;
  background: #fff;
  -webkit-tap-highlight-color: transparent;
}

/* Make the radio cover the whole label but invisible (keeps accessibility + focus) */
.expense-option input[type="radio"] {
  position: absolute;
  inset: 0;               /* top:0; right:0; bottom:0; left:0; */
  width: 100%;
  height: 100%;
  margin: 0;
  opacity: 0;
  cursor: pointer;
}

/* hover effect */
.expense-option:hover {
  border-color: #007bff;
  color: #007bff;
}

/* focus (keyboard) — same as hover */
.expense-option:has(input[type="radio"]:focus) {
  border-color: #007bff;
  color: #007bff;
  outline: 3px solid rgba(0,123,255,0.08);
  outline-offset: 4px;
}

/* checked state: same look as hover (modern browsers) */
.expense-option:has(input[type="radio"]:checked) {
  border-color: #007bff;
  color: #007bff;
  box-shadow: 0 6px 18px rgba(0,123,255,0.06);
}

</style>

