<?php
/**
 * Resets visibility of all children products of configurable products to
 * be "not visible individually" i.e. visibility = 1.
 *
 */
require '../www/app/Mage.php';
Mage::app();
$products = Mage::getResourceModel('catalog/product_collection')
    ->addAttributeToFilter("type_id",array("eq","configurable"));
foreach ($products as $p) {
    $productId = $p->getId();
    echo "parent id = " . $productId . "\n";
    $childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($productId);
    foreach($childIds[0] as $id) {
        echo "child id = " . $id . "\n";
        $product = Mage::getModel('catalog/product')->load($id);
        $product->setVisibility(1);
        $product->save();
    }
}

?>
