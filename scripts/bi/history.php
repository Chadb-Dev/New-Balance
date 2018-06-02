<?php
/**
 * Cron for generating historical reports.
 */

include_once "config.php";
include_once "Helper.php";

$helper = new Helper();
$today = new DateTime();
$currentMonth = $today->format("m");
$currentYear = $today->format("Y");
$endDate = new \DateTime('2018-03-20T00:00:00'); // store launch day

// ---------------------------------------------------------
// PROCESS ORDERS / SALES REPORTS
// ---------------------------------------------------------

$offset = 0;
$completed = false;
$reportMonth = 0;
$files = array();
do {
    $orders = $helper->getOrders($offset);
    if(count($orders->system_orders) === 0) {
        $completed = true;
    }
    foreach ($orders->system_orders as $order) {

        // check month, exit if it is older than this month
        $created = new \DateTime($order->created);
        $orderYear = $created->format("Y");
        $orderMonth = $created->format("m");

        // skip orders for this month
        if($orderYear === $currentYear && $currentMonth === $orderMonth) {
            continue;
        }

        // Only write reports for current year
        if($orderYear < $currentYear) {
            $completed = true;
            break;
        }

        // stop at end date
        if($created < $endDate) {
            $completed = true;
            break;
        }

        $reportPeriod = $currentMonth - $orderMonth;
        if(!isset($files[$reportPeriod])) {
            if($reportPeriod < 10) {
                $period = "0" . $reportPeriod;
            } else {
                $period = (string) $reportPeriod;
            }
            $files[$reportPeriod] = new \stdClass();
            $files[$reportPeriod]->sales_sum_mtd = fopen('output/NBE_ST2S_ZA_sales_sum_mtd_ty0_' . $period . '.txt', 'w');
            $files[$reportPeriod]->style_sales_sum_mtd = fopen('output/NBE_ST2S_ZA_style_sales_sum_mtd_ty0_' . $period . '.txt', 'w');
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
            fputcsv($files[$reportPeriod]->style_sales_sum_mtd, $styleSalesSum, "\t");

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
        fputcsv($files[$reportPeriod]->sales_sum_mtd, $salesSum, "\t");

    }
    $offset += $config['limit'];
}  while(!$completed);

foreach ($files as $file) {
    fclose($file->sales_sum_mtd);
    fclose($file->style_sales_sum_mtd);
}
