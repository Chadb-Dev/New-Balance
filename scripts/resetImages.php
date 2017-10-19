<?php
/**
 * Resets all images (base image, small image, thumbnail)
 * to the first image for each product
 *
 */

require '../www/app/Mage.php';
Mage::app();


$products = Mage::getResourceModel('catalog/product_collection')
    ->addAttributeToFilter("type_id",array("eq","configurable"));
foreach ($products as $p) {
    $productId = $p->getId();

//load the product
    $product = Mage::getModel('catalog/product')->load($productId);

//get all images
    $mediaGallery = $product->getMediaGallery();
//if there are images
    if (isset($mediaGallery['images'])) {

        if(count($mediaGallery['images']) > 0) {
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                array($product->getId()),
                array('image' => $mediaGallery['images'][0]['file']),
                0
            );
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                array($product->getId()),
                array('thumbnail' => $mediaGallery['images'][0]['file']),
                0
            );
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                array($product->getId()),
                array('small_image' => $mediaGallery['images'][0]['file']),
                0
            );
        }
//        //loop through the images
//        foreach ($mediaGallery['images'] as $image) {
//
//            //set the first image as the base image
//            Mage::getSingleton('catalog/product_action')->updateAttributes(
//                array($product->getId()), array('image' => $image['file']), 0
//            );
//            //stop
//            break;
//        }
    }
}

?>
