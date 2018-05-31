<?php


class Helper
{

    function getOrders($offset) {
        global $config;
        $url = $config['url'] . 'orders/search?token=' . $config['token'] . '&status=ordered&limit=' . $config['limit'] . '&offset=' . $offset;
        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $arr = explode("\r\n\r\n", $response, 2);
        $body = "";
        if (count($arr) == 2) {
            $body = $arr[1];
        }
        return json_decode($body);
    }

    function getOrder($id) {
        global $config;
        $url = $config['url'] . 'orders/' . $id . '?token=' . $config['token'];
        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $arr = explode("\r\n\r\n", $response, 2);
        $body = "";
        if (count($arr) == 2) {
            $body = $arr[1];
        }
        return json_decode($body);
    }

    function elasticSearch($payload) {
        global $config;
        $url = $config['url'] . 'products/elastic_search?token=' . $config['token'] . '&channel_id=' . $config['channel_id'];
        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Expect:'
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $arr = explode("\r\n\r\n", $response, 2);
        $body = "";
        if (count($arr) == 2) {
            $body = $arr[1];
        }
        return json_decode($body);
    }

}