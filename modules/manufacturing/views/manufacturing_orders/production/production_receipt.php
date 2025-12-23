<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Receipt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }


        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-container img {
            max-width: 120px; /* Adjust size */
            height: auto;
        }

        .container {
            width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            background: #112852;
            color: white;
            text-align: center;
            margin-bottom: 10px;
        }
        .section {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            background: #fff;
        }
        .section p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            font-size: 14px;
        }

        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background: #f0f0f0;
            color: #333;
        }
        /* .summary {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            border-top: 2px solid #112852;
        } */

        .summary-container {
            display: flex;
            justify-content: space-between;
            gap: 20px; /* Adjust space between left & right */
            flex-wrap: wrap;
        }

        .summary {
            /*width: 50%;*/
            flex: 1 1 calc(48% - 20px);
            background: #f0f0f0; /* Light background for visibility */
            padding: 10px;
            border-radius: 0px;
        }        

        /* PRINT STYLING */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .section-title {
                background: #112852 !important;
                color: white !important;
            }
            th {
                background: #f0f0f0 !important;
                color: #333 !important;
            }
        } 
        
        /*-- new css--*/
        
        .top_section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.print_button
{
  font-size: 15px;
    font-weight: 500;
    padding: 6px 20px;
    background: #112852;
    color: white;
    border: 0px;
    font-family: "Poppins", sans-serif;
    cursor: pointer;
}
    </style>
</head>
<body>

<div class="container">

    <!-- Logo -->
        <div class="section">
            <div class="top_section">
                <div class="top_section_left"> 
                    <div class="logo-container">
                    <?php get_company_logo('admin'); ?>
                    </div>
                </div>
                
                <div class="top_section_right">
                    <?php $sr_no = isset($_GET['sr_no']) ? (int)$_GET['sr_no'] - 1 : null; ?>
                    <!-- <p><b>Challan No:</b> <?= $manufacturing_order_code ?></p> -->
                    <p><b>Challan No:</b> <?= $manufacturing_order_code . (is_numeric($sr_no) ? '-' . $sr_no : '') ?></p>
                    <p><b>Assign Date:</b> <?= date('d M, Y', strtotime($production_inventory['created_at'])) ?></p>
                    <?php if($production_inventory['deadline']): ?>
                    <p><b>Deadline:</b> <?= date('d M, Y', strtotime($production_inventory['deadline'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <!-- Vendor Information -->
    <div class="section">
       
        <div class="section-title">Vendor Information</div>
        <p><strong>Name:</strong> <?= $production_inventory['company'] ?></p>
        <p><strong>Phone:</strong> <?= $production_inventory['phonenumber'] ?></p>
        <p><strong>Address:</strong> <?= $production_inventory['address'] ?>, <?= $production_inventory['city'] ?> - <?= $production_inventory['zip'] ?></p>
    </div>
           


<div class="section">
        <div class="section-title">Product Information</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Rate / Pc</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><?= $m_product['commodity_code'] . '_' . $m_product['description'] ?></td>
                        <td><?= $production_inventory['qty_assigned'] ?></td>
                        <td><?= app_format_money($production_inventory['price'], $base_currency) ?></td>
                        <td>
                            <?php
                                $total_amount = $production_inventory['qty_assigned'] * $production_inventory['price'];
                                echo app_format_money($total_amount, $base_currency);
                            ?>
                        </td>
                    </tr>
                   
                </tbody>
            </table>
        </div>
    </div>
    
    
     

    <!-- Assigned Raw Materials -->
    <div class="section">
        <div class="section-title">Assigned Raw Materials</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Qty Consume / Pc</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr_no = 1;
                    foreach ($production_inventory_details as $item): ?>
                        <tr>
                            <td><?= $sr_no++ ?></td>
                            <td><?= $item['product_name'] ?></td>
                            <td><?= number_format($item['qty_assigned'] / $production_inventory['qty_assigned'], 2) ?> <?= $item['unit_name'] ?></td>
                            <td><?= number_format($item['qty_assigned'], 2) ?> <?= $item['unit_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
     <!-- Quantity Summary -->
    <div class="section">
        <div class="section-title">Other Information</div>
        <div class="summary-container">
          
           
            <div class="summary">
                <?php 
                    $base_currency = get_base_currency_pur();                
                    $base_currency = $base_currency->name;                
                ?>
                <p><strong>Price Summery</strong></p>
                <p>Receive Price - Per Piece : <?= app_format_money($production_inventory['price'], $base_currency) ?></p>
                <p>Lost Price - Per Piece : <?= app_format_money($production_inventory['deduct_price'], $base_currency) ?></p> 
                
                <p>Assinged Total : <?php $assinged_total = $production_inventory['price'] * $production_inventory['qty_assigned']; echo app_format_money($assinged_total, $base_currency); ?></p>
                <p>Lost Total : <?php $lost_total = $production_inventory['deduct_price'] * $production_inventory['qty_lost']; echo app_format_money($lost_total, $base_currency); ?></p>
                <p>Receivable Amount Total : <?php echo app_format_money($assinged_total - $lost_total, $base_currency)  ?></p>
            </div>
            
             <div class="summary">
                <p><strong>Quantity Summery</strong></p>
                <p>Total Quantity Assigned : <?= $production_inventory['qty_assigned'] ?></p>
                <p>Total Quantity Received : <?= $production_inventory['qty_received'] ?></p>
                <p>Total Quantity Lost : <?= $production_inventory['qty_lost'] ?></p>
                <p>Total Quantity Pending : <?= $production_inventory['qty_pending'] ?></p>
            </div>
           
        </div>
    </div>  

    <div style="text-align:right;">
    <button onclick="window.print()" class="print_button">Print</button>
    </div>
</div>

</body>
</html>

<?php /*
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }


        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-container img {
            max-width: 120px; 
            height: auto;
        }

        .container {
            width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            background: #112852;
            color: white;
            text-align: center;
            margin-bottom: 10px;
        }
        .section {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            background: #fff;
        }
        .section p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            font-size: 14px;
        }

        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background: #f0f0f0;
            color: #333;
        }


        .summary-container {
            display: flex;
            justify-content: space-between;
            gap: 20px; 
            flex-wrap: wrap;
        }

        .summary {
        
            flex: 1 1 calc(48% - 20px);
            background: #f8f9fa; 
            padding: 10px;
            border-radius: 5px;
        }        


        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .section-title {
                background: #112852 !important;
                color: white !important;
            }
            th {
                background: #f0f0f0 !important;
                color: #333 !important;
            }
        }        
    </style>
</head>
<body>

<div class="container">

    <!-- Logo -->
    <div class="section">
        <div class="logo-container">
            <?php get_company_logo('admin'); ?>
        </div>
    </div>
    
    <!-- Vendor Information -->
    <div class="section">
        <div class="section-title">Vendor Information</div>
        <p><strong>Name:</strong> <?= $production_inventory['company'] ?></p>
        <p><strong>Phone:</strong> <?= $production_inventory['phonenumber'] ?></p>
        <p><strong>Address:</strong> <?= $production_inventory['address'] ?>, <?= $production_inventory['city'] ?> - <?= $production_inventory['zip'] ?></p>
    </div>

    <!-- Quantity Summary -->
    <div class="section">
        <div class="section-title">Other Information</div>
        <div class="summary-container">
            <div class="summary">
                <p><strong>Product Information</strong></p>
                <p>Name : <?= $m_product['commodity_code'].'_'.$m_product['description'] ?></p>
            </div>
            <div class="summary">
                <p><strong>Quantity Summery</strong></p>
                <p>Total Quantity Assigned : <?= $production_inventory['qty_assigned'] ?></p>
                <p>Total Quantity Received : <?= $production_inventory['qty_received'] ?></p>
                <p>Total Quantity Lost : <?= $production_inventory['qty_lost'] ?></p>
                <p>Total Quantity Pending : <?= $production_inventory['qty_pending'] ?></p>
            </div>
            <div class="summary">
                <?php 
                    $base_currency = get_base_currency_pur();                
                    $base_currency = $base_currency->name;                
                ?>
                <p><strong>Price Summery</strong></p>
                <p>Receive Price - Per Piece : <?= app_format_money($production_inventory['price'], $base_currency) ?></p>
                <p>Lost Price - Per Piece : <?= app_format_money($production_inventory['deduct_price'], $base_currency) ?></p> 
                
                <p>Assinged Total : <?php $assinged_total = $production_inventory['price'] * $production_inventory['qty_received']; echo app_format_money($assinged_total, $base_currency); ?></p>
                <p>Lost Total : <?php $lost_total = $production_inventory['deduct_price'] * $production_inventory['qty_lost']; echo app_format_money($lost_total, $base_currency); ?></p>
                <p>Receivable Amount Total : <?php echo app_format_money($assinged_total - $lost_total, $base_currency)  ?></p>
            </div>
            <div class="summary">
                <p><strong>Assigned Date</strong></p>
                <p><strong>Date:</strong> <?= date('d M, Y', strtotime($production_inventory['created_at'])) ?></p>
            </div>   
            
           
        </div>
    </div>    

    <!-- Assigned Raw Materials -->
    <div class="section">
        <div class="section-title">Assigned Raw Materials</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Qty Consume / Pc</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr_no = 1;
                    foreach ($production_inventory_details as $item): ?>
                        <tr>
                            <td><?= $sr_no++ ?></td>
                            <td><?= $item['product_name'] ?></td>
                            <td><?= number_format($item['qty_assigned'] / $production_inventory['qty_assigned'], 2) ?> <?= $item['unit_name'] ?></td>
                            <td><?= number_format($item['qty_assigned'], 2) ?> <?= $item['unit_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="text-align:right;">
    <button onclick="window.print()">Print</button>
    </div>
</div>

</body>
</html>

*/ ?>
