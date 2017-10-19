<?php

//	Copyright 2012 Cre8aweb

class Cre8aweb_MyGate_Model_Functions extends Mage_Payment_Model_Method_Abstract {

protected $_code = 'mygate_functions';
protected $_formBlockType = 'mygate/form';
protected $_canCapture = true;

public function assignData($data) {
	$data = new Varien_Object($data);
	$_SESSION['owner'] = $data->getCcOwner();
	$_SESSION['type'] = $data->getCcType();
	$_SESSION['number'] = $data->getCcNumber();
	$_SESSION['month'] = $data->getCcExpMonth();
	$_SESSION['year'] = $data->getCcExpYear();
	$_SESSION['cvv'] = $data->getCcCid();
}

public function mode() {
	return $this->getConfigData('mode');
}

public function gatewayid() {
	return $this->getConfigData('gatewayid');
}

public function merchantid() {
	return $this->getConfigData('merchantid');
}

public function applicationid() {
	return $this->getConfigData('applicationid');
}

public function getOrder() {
	$order = Mage::getModel('sales/order');
	$session = Mage::getSingleton('checkout/session');
	$order->loadByIncrementId($session->getLastRealOrderId());
	return $order;
}

public function getOrderPlaceRedirectUrl() {
	return Mage::getUrl('mygate/processing/check');
}

function restock($order) {
	$product = Mage::getModel('catalog/product');
	$items = $order->getAllItems();
	foreach($items as $id=>$item) {
		$id = $product->getIdBySku($item->getSku());
		$ordered = $item->getQtyToInvoice();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$table = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
		$item = $connection->fetchAll("SELECT * FROM $table WHERE product_id = $id");
		$current = $item[0]['qty'];
		$total = $ordered+$current;
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$connection->query("UPDATE `$table` SET `qty` = '$total', `is_in_stock` = '1' WHERE `product_id` = '$id'");
		$table = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status');
		$connection->query("UPDATE `$table` SET `qty` = '$total', `stock_status` = '1' WHERE `product_id` = '$id'");
	}
}

}