<?php
/**
 * Cron creates all required files for NB BI reports.
 */

include_once "config.php";
include_once "Helper.php";

$helper = new Helper();

// Files to add
$fp_store_lu = fopen('output/NBE_ST2S_ZA_store_lu.txt', 'w');
$fp_sales_sum_mtd = fopen('output/NBE_ST2S_ZA_sales_sum_mtd.txt', 'w');
$fp_style_sales_sum_mtd = fopen('output/NBE_ST2S_ZA_style_sales_sum_mtd.txt', 'w');
$fp_inv_balances = fopen('output/NBE_ST2S_ZA_inv_balances.txt', 'w');

// Globals to this script
$today = new DateTime();

// ---------------------------------------------------------
// PROCESS ORDERS / SALES REPORTS
// ---------------------------------------------------------

$ym = $today->format("Ym");
$offset = 0;
$completed = false;
do {
    $orders = $helper->getOrders($offset);
    if(count($orders->system_orders) === 0) {
        $completed = true;
    }
    foreach ($orders->system_orders as $order) {
        // check month, exit if it is older than this month
        $created = new \DateTime($order->created);
        $orderYM = $created->format("Ym");
        if($orderYM !== $ym) {
            $completed = true;
            break;
        }
        $orderDetail = $helper->getOrder($order->id);
        $units = 0;
        $tax_cents = 0;
        $sub_total_cents = 0;
        $total_cents = 0;
        $shipping_cents = 0;
        $i = 1;
        foreach ($orderDetail->system_order->line_items as $item) {
            $units += $item->qty;
            $item_sub_total_cents = ($item->qty * $item->price * 100);
            $sub_total_cents += $item_sub_total_cents;
            if(count($item->tax_lines) > 0) {
                $item_total_cents = $item_sub_total_cents * (1 + ($item->tax_lines[0]->rate/100));
                $item_tax_cents = $item_total_cents - $item_sub_total_cents;
                $tax_cents += $item_tax_cents;
            }

            // style sales summary
//            $payload = '
//            {
//              "query": {
//                "bool": {
//                  "filter": [ {
//                      "term": {
//                        "variants.sku": "' . $item->sku . '"
//                      }
//                    }]
//                }
//              },
//              "from": 0,
//              "size": "1"
//            }';
//            $search = $helper->elasticSearch($payload);
//            $currentVariant = false;
//            if(isset($search->system_products[0])) {
//                foreach ($search->system_products[0]->variants as $variant) {
//                    if($variant->sku === $item->sku) {
//                        $currentVariant = $variant;
//                        break;
//                    }
//                }
//                if(!$currentVariant) {
//                    print_r($item);
//                    // TODO should probably notify someone
//                }
//            } else {
//                print_r($item);
//                // TODO should probably notify someone
//            }
//            $width = ($currentVariant)? $currentVariant->option2: "";
//            $size = ($currentVariant)? $currentVariant->option1: "";
            $styleSalesSum = array(
                "ShopNB",
                "ZA",
                $created->format("Y/m/d"),
                $order->channel_order_code,
                $i,
                "100",
                "NB",
                $item->sku,
                "",
                "",
                "",
                "",
                $item->qty,
                round($item_sub_total_cents / 100, 2),
                0,
                round($item_tax_cents / 100, 2),
                0,
                round($orderDetail->system_order->total_discount, 2),
                0,
                0,
                0
            );
            fputcsv($fp_style_sales_sum_mtd, $styleSalesSum, "\t");

            $i++;
        }
        foreach ($orderDetail->system_order->shipping_lines as $item) {
            $shipping_cents = ($item->price * 100);
            if(count($item->tax_lines) > 0) {
                $item_total_cents = $shipping_cents * (1 + ($item->tax_lines[0]->rate/100));
                $tax_cents += $item_total_cents - $shipping_cents;
            }
        }

        // sales summary
        $salesSum = array(
            "ZA",
            "ShopNB",
            $created->format("Y/m/d"),
            $order->channel_order_code,
            $order->channel_customer_code,
            "",
            $units,
            round($sub_total_cents / 100, 2),
            0,
            round($tax_cents / 100, 2),
            0,
            round($orderDetail->system_order->total_discount, 2),
            round($shipping_cents / 100, 2),
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
        );
        fputcsv($fp_sales_sum_mtd, $salesSum, "\t");

    }
    $offset += $config['limit'];
}  while(!$completed);

// ---------------------------------------------------------
// PROCESS INVENTORY REPORTS
// ---------------------------------------------------------

$offset = 0;
$completed = false;
do {
    $payload = '{
      "from": ' . $offset . ',
      "size": ' . $config['limit'] . '
    }
    ';
    $search = $helper->elasticSearch($payload);
    if(!isset($search->system_products) || count($search->system_products) < $config['limit']) {
        $completed = true;
    }
    if(isset($search->system_products)) {
        foreach ($search->system_products as $product) {
            foreach ($product->variants as $variant) {
                $inv_balance = array(
                    "NB e-Comm",
                    "ZA",
                    "ShopNB",
                    "NB",
                    $today->format("Y/m/d"),
                    $variant->sku,
                    $variant->option2,
                    $variant->option2,
                    $variant->option1,
                    0,
                    $variant->price,
                    $variant->qty,
                    0,
                    "1900/01/01",
                    $variant->option1
                );
                fputcsv($fp_inv_balances, $inv_balance, "\t");
            }
        }
    }

    $offset += $config['limit'];
}  while(!$completed);


// ---------------------------------------------------------
// PROCESS LOOKUP / MASTER FILES
// ---------------------------------------------------------
$store = array(
    "NB e-Comm",
    "ShopNB",
    "ZA",
    "ZAR",
    "ShopNB",
    "ZEC",
    "COMP",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "1/05/2018",
    "",
    "",
    "",
    "",
    ""
);
fputcsv($fp_store_lu, $store, "\t");

// Close files to add
fclose($fp_store_lu);
fclose($fp_sales_sum_mtd);
fclose($fp_style_sales_sum_mtd);
fclose($fp_inv_balances);