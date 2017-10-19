<?php

/**
 * Observer to handle event
 * Sends JSON to parcel ninja
 *
 * @author Chris Sohn (www.gomedia.co.za)
 * @copyright  Copyright (c) 2015 Go Media
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class GoMedia_ParcelNinjaWebhook_Model_Observer {

    /**
     * Used to ensure the event is not fired multiple times
     * http://magento.stackexchange.com/questions/7730/sales-order-save-commit-after-event-triggered-twice
     *
     * @var bool
     */
    private $_processFlag = false;

    /**
     *
     * The comment that is saved with order
     * @var string
     */
    private $_orderMessage = "";


    /**
     * event: sales_order_save_after
     * @param Varien_Event_Observer $observer
     * @return GoMedia_ParcelNinja_Model_Observer
     */
    public function postOrder($observer) {

        $config = Mage::getStoreConfig('parcelninja_webhook/order');
        if(!$config['enabled']) {
            return $this;
        }

        // check if already processed
        if ($this->_processFlag) {
            return $this;
        } else {
            $this->_processFlag = true;
        }

        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        // check valid state

        $status = ($config['order_status'] != "") ? $config['order_status'] : "processing";
        $currentStatus = $order->getStatus();
        if ($status != $currentStatus) {
            return $this;
        }

        // send outbound order to PN
        if ($order->canShip()) {
            $this->_createShipment($order);
        } else {
            $this->_setMessage('Order cannot be shipped');
        }

        // if we have a message to give, give it.
        // this means saving the order, this will cal this observer to get fired again
        $message = $this->_getMessage();
        if($message != "") {
            $order->addStatusHistoryComment("Parcel Ninja: $message")
                ->setIsVisibleOnFront(false)
                ->setIsCustomerNotified(false);
            $order->save();
        }
        return $this;
    }

    /**
     * A shipment is a PN outbound order
     * We use Shipment to check which orders have been sent to PN.
     *
     * @param $order Mage_Sales_Model_Order
     */
    private function _createShipment($order) {

        // since we are trying to raise an order on PN, do not allow further attempts
//        $this->_processFlag = true;

        // set data
        $config = Mage::getStoreConfig('parcelninja_webhook/order');
        $data = $order->getData();

        /** @var $order Mage_Sales_Model_Order_Address */
        $shipping = $order->getShippingAddress();

        // validate shipping address
        $payload = array();
        $payload['clientId'] = $data['increment_id'];

        // 2: Customer Outbound Order, 4: Return Stock Order (RSO)
        $payload['typeId'] = 2;

        // deliver address
        $payload['deliveryInfo'] = array();
        $payload['deliveryInfo']['contactNo'] = str_replace('+27', '0', $shipping['telephone']);
        $payload['deliveryInfo']['contactNo'] = preg_replace('/[^0-9]/', '', $payload['deliveryInfo']['contactNo']);
        $payload['deliveryInfo']['contactNo'] = substr($payload['deliveryInfo']['contactNo'], 0, 10);
        if (strlen($payload['deliveryInfo']['contactNo']) != 10) {
            $this->_setMessage('Invalid Telephone Number');
            return;
        }
        if ($shipping['street'] == "") {
            $this->_setMessage('Invalid Street');
            return;
        }
        if ($shipping['city'] == "") {
            $this->_setMessage('Invalid City');
            return;
        }
        if ($shipping['postcode'] == "") {
            $this->_setMessage('Invalid Postal Code');
            return;
        }
        $payload['deliveryInfo']['addressLine1'] = $shipping['street'];
        $payload['deliveryInfo']['suburb'] = $shipping['city'];
        $payload['deliveryInfo']['postalCode'] = $shipping['postcode'];
        $payload['deliveryInfo']['ForCollection'] = false;
        $payload['deliveryInfo']['deliveryOption'] = array(
            'deliveryQuoteId' => 0
        );
        $payload['deliveryInfo']['customer'] = $shipping['firstname'] . ' ' . $shipping['lastname'];
        $payload['deliveryInfo']['email'] = $data['customer_email'];

        // line items
        $payload['items'] = array();
        foreach ($order->getAllItems() as $item) {

            // item for parcel ninja payload
            $itemData  =$item->getData();
            if ($itemData['product_type'] == 'simple') {

                // item for parcel ninja payload
                array_push(
                    $payload['items'],
                    array(
                        'itemNo' => $item->getSku(),
                        'name' => $item->getName(),
                        'qty' => (int)$item['qty_ordered']
                    )
                );
            }

            // item added to magento order shipment
            $shipmentQTY = $item->getQtyOrdered()
                - $item->getQtyShipped()
                - $item->getQtyRefunded()
                - $item->getQtyCanceled();
            $magentoShipment[$item->getId()] = $shipmentQTY;
        }

        // send order to parcel ninja (create outbound)
        $response = $this->_request('outbounds', 'POST', json_encode($payload), $order);

        // fetch outbound id
        $outboundID = $this->_getOutboundID($response);

        // if no outbound ID returned
        if (!$outboundID) {
            $message = $this->_getResponseError($response);
            $this->_setMessage($message);
            return;
        } else {

            // order created on PN
            $this->_setMessage("Outbound Order Created: $outboundID");

            // save shipment:
            /* @var $shipment Mage_Sales_Model_Order_Shipment */
            $shipment = $order->prepareShipment($magentoShipment);
            if ($shipment) {
                $shipment->register();
                $shipment->addComment("Automatically created shipment from Parcel Ninja: " . json_encode($response));
                $shipment->getOrder()->setIsInProcess(true);
                try {
                    Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
                    $this->_setMessage("Shipment successfully created");
                    if ($config['notify_customer']) {
                        $shipment->sendEmail();
                    }

                } catch (Mage_Core_Exception $e) {
                    $this->_setMessage("Shipment could not be saved");
                }
            }
        }
    }

    /**
     * Checks response for outbound id or returns false eif not found
     *
     * @param $response
     * @return mixed
     */
    private function _getOutboundID($response) {
        if (
            !isset($response['headers']['x-parcelninja-outbound-id']) ||
            $response['headers']['x-parcelninja-outbound-id'] == 0
        ) {
            return false;
        } else {
            return $response['headers']['x-parcelninja-outbound-id'];
        }
    }

    /**
     * Returns PN response error or 'unknown error'
     *
     * @param $response
     * @return string
     */
    private function _getResponseError($response) {
        if (isset($response['headers']['x-parcelninja-error-message'])) {
            return $response['headers']['x-parcelninja-error-message'];
        } else {
            return "Unknown Error";
        }
    }

    private function _request($resource = '', $method = 'GET', $payload, $order) {
        $config = Mage::getStoreConfig('parcelninja_webhook/order');
        if (
            $resource != '' &&
            $config['api_url'] != '' &&
            $config['api_username'] != '' &&
            $config['api_password'] != ''
        ) {
            list($protocol, $api_url) = explode('://', $config['api_url']);

            // check for trailing slash. If not present, add it before appending resource
            $api_url .= (substr($api_url, -1) == '/') ? $resource : '/' . $resource;
            $url = $protocol . '://' . $config['api_username'] . ':' . $config['api_password'] . '@' . $api_url;

            //  Initiate curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            if (strtoupper($method) == 'POST') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $options = array(
                    'Accept: application/json',
                    'Content-type: application/json',
                    'Content-Length: ' . strlen($payload),
                    // http://stackoverflow.com/questions/10384778/curl-request-with-headers-separate-body-a-from-a-header
                    'Expect:'
                );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
            }
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // if status is not 200, write out to log
            if ($status != 200 && $status != 204) {
                $this->_setMessage("Request failure: " . $response);
            }
            curl_close($ch);
            return $this->_respond($response);

        }
        return false;
    }


    private function _respond($response) {

        // get header and body individually
        // useful to see the header when debugging
        $arr = explode("\r\n\r\n", $response, 2);
        $headers = array();
        $body = "";
        if (count($arr) == 2) {
            $headers = $this->_http_parse_headers($arr[0]);
            $body = $arr[1];
        } else {
            return array("error" => "Unexpected response");
        }
        return array("headers" => $headers, "body" => $body);

    }

    /**
     * Set order message
     *
     * @param $message
     */
    private function _setMessage($message) {
        $this->_orderMessage .= " " . $message;
    }

    /**
     * get order message
     *
     * @return string
     */
    private function _getMessage() {
        return $this->_orderMessage;
    }

    /**
     * takes raw headers and parses them into array
     * http://php.net/manual/en/function.http-parse-headers.php#112986
     *
     * @param $raw_headers
     * @return array
     */
    private function _http_parse_headers($raw_headers) {
        $headers = array();
        $key = ''; // [+]

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]])) {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                } else {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            } else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t" . trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);
                trim($h[0]); // [+]
            } // [+]
        }

        return $headers;
    }

    
}