<?php

/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable {

    protected $store_id;

    public function __construct() {
        $this->store_id = Mage::app()->getStore()->getId();
        return parent::__construct();
    }

    public function getJsonConfig() {
        if( ! Mage::getStoreConfig('spp/setting/enableModule', $this->store_id)) {
            return parent::getJsonConfig();
        }
        if( ! $this->getProduct()->getUseSimpleProductPricing() && ! Mage::getStoreConfig('spp/setting/useglobally', Mage::app()->getStore()->getId())) {
            return parent::getJsonConfig();
        }
        $attrs = array();
        $saveCache = false;
        $cache_lifetime = intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime', $this->store_id));
        $cache = Mage::app()->getCacheInstance();
        $cacheKey = $this->getProduct()->getId() . "_" . $this->store_id;
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $cacheKey = $this->getProduct()->getId() . "_" . $this->store_id . "_" . Mage::getModel('customer/session')->getCustomer()->getId();
        }
        if(intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime', $this->store_id)) > 0) {
            if($cache->load("jsonconfig_" . $cacheKey)) {
                $cache_jsonconfig = unserialize($cache->load("jsonconfig_" . $cacheKey));
                return $cache_jsonconfig;
            } else {
                $saveCache = true;
            }
        }
        $showOutOfStock = false;
        $config = Zend_Json::decode(parent::getJsonConfig());
        if(Mage::getStoreConfig('spp/details/show', $this->store_id)) {
            $showOutOfStock = true;
        }
        //Create the extra price and tier price data/html we need.
        foreach($this->getAllowProducts() as $product) {
            $isInStock = true;
            $productId = $product->getId();
            if(Mage::app()->getStore()->isAdmin()) {
                $product = Mage::getModel('catalog/product')->load($productId);
            }
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if(($stockItem->getQty() <= 0 && $stockItem->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO) || ( ! $stockItem->getIsInStock() && $stockItem->getManageStock())) {
                $isInStock = false;
            }
            if( ! $isInStock) {
                $stockInfo[$productId] = array(
                    "stockLabel" => $this->__('Out of stock'),
                    "stockQty" => intval($stockItem->getQty()),
                    "is_in_stock" => false,
                );
            } else {
                $stockInfo[$productId] = array(
                    "stockLabel" => $this->__('In Stock'),
                    "stockQty" => intval($stockItem->getQty()),
                    "is_in_stock" => true,
                );
            }
            $finalPrice = $product->getFinalPrice();
            if($product->getCustomerGroupId()) {
                $finalPrice = $product->getGroupPrice();
            }
            if($product->getTierPrice()) {
                $tprices = array();
                foreach($tierprices = $product->getTierPrice() as $tierprice) {
                    $tprices[] = $tierprice['price'];
                }
                $tierpricing = min($tprices);
            } else {
                $tierpricing = '';
            }
            $has_image = false;
            if(Mage::getStoreConfig('spp/media/updateproductimage', $this->store_id)) {
                if($product->getData('thumbnail') && ($product->getData('thumbnail') != 'no_selection')) {
                    $has_image = true;
                }
            }
            $childProducts[$productId] = array(
                "price" => $this->_registerJsPrice($this->_convertPrice($product->getPrice())),
                "finalPrice" => $this->_registerJsPrice($this->_convertPrice($finalPrice)),
                "tierpricing" => $this->_registerJsPrice($this->_convertPrice($tierpricing)),
                "has_image" => $has_image
            );
            if(Mage::getStoreConfig('spp/details/producturl', $this->store_id)) {
                $childProducts[$productId]["urlKey"] = Mage::helper('urlrewrite')->getRealUrlKey($product->getId());
            }

            if(Mage::getStoreConfig('spp/details/productname', $this->store_id)) {
                $ProductNames[$productId] = array(
                    "ProductName" => $product->getName()
                );
            }
            if (Mage::getStoreConfig('spp/details/productsku', $this->store_id)) {
                $ProductSkus[$productId] = array(
                    "ProductSku" => $product->getSku()
                );
            }
            if(Mage::getStoreConfig('spp/details/shortdescription', $this->store_id)) {
                $shortDescriptions[$productId] = array(
                    "shortDescription" => $this->helper('catalog/output')->productAttribute($product, nl2br($product->getShortDescription()), 'short_description')
                );
            }
            if(Mage::getStoreConfig('spp/details/description', $this->store_id)) {
                $Descriptions[$productId] = array(
                    "Description" => $this->helper('catalog/output')->productAttribute($product, nl2br($product->getDescription()), 'description')
                );
            }

            if(Mage::getStoreConfig('spp/details/productAttributes', $this->store_id)) {
                $config['productAttributes'] = true;
                $config['product_attributes_markup'] = Mage::getStoreConfig('spp/markup/product_attributes_markup', $this->store_id);
            } else {
                $config['productAttributes'] = false;
            }
        }
        if(Mage::getStoreConfig('spp/details/customstockdisplay', $this->store_id)) {
            $config['customStockDisplay'] = true;
            $config['product_customstockdisplay_markup'] = Mage::getStoreConfig('spp/markup/product_customstockdisplay_markup', $this->store_id);
        } else {
            $config['customStockDisplay'] = false;
        }
        $config['showOutOfStock'] = $showOutOfStock;
        $config['stockInfo'] = $stockInfo;
        $config['childProducts'] = $childProducts;

        $config['showPriceRangesInOptions'] = true;
        $config['rangeToLabel'] = $this->__('-');
        if(Mage::getStoreConfig('spp/setting/hideprices', $this->store_id)) {
            $config['hideprices'] = true;
        } else {
            $config['hideprices'] = false;
        }
        if(Mage::getStoreConfig('spp/setting/instocklabel', $this->store_id)) {
            $config['instocklabel'] = true;
        } else {
            $config['instocklabel'] = false;
        }

        if(Mage::getStoreConfig('spp/details/disable_out_of_stock_option', $this->store_id)) {
            $config['disable_out_of_stock_option'] = true;
        } else {
            $config['disable_out_of_stock_option'] = false;
        }
        if(Mage::getStoreConfig('spp/details/productname', $this->store_id)) {
            $config['productName'] = $this->getProduct()->getName();
            $config['ProductNames'] = $ProductNames;
            $config['product_name_markup'] = Mage::getStoreConfig('spp/markup/product_name_markup', $this->store_id);
            $config['updateProductName'] = true;
        } else {
            $config['updateProductName'] = false;
        }
        if (Mage::getStoreConfig('spp/details/productsku', $this->store_id)) {
            $config['productSku'] = $this->getProduct()->getSku();
            $config['ProductSkus'] = $ProductSkus;
            $config['product_sku_markup'] = Mage::getStoreConfig('spp/markup/product_sku_markup');
            $config['updateProductSku'] = true;
        } else {
            $config['updateProductSku'] = false;
        }
        if(Mage::getStoreConfig('spp/details/shortdescription', $this->store_id)) {
            $config['shortDescription'] = $this->getProduct()->getShortDescription();
            $config['shortDescriptions'] = $shortDescriptions;
            $config['product_shortdescription_markup'] = Mage::getStoreConfig('spp/markup/product_shortdescription_markup', $this->store_id);
            $config['updateShortDescription'] = true;
        } else {
            $config['updateShortDescription'] = false;
        }
        if(Mage::getStoreConfig('spp/details/description', $this->store_id)) {
            $config['description'] = $this->getProduct()->getDescription();
            $config['Descriptions'] = $Descriptions;
            $config['product_description_markup'] = Mage::getStoreConfig('spp/markup/product_description_markup', $this->store_id);
            $config['updateDescription'] = true;
        } else {
            $config['updateDescription'] = false;
        }
        if(Mage::getStoreConfig('spp/details/showfromprice', $this->store_id)) {
            $config['showfromprice'] = true;
        } else {
            $config['showfromprice'] = false;
        }
        if(Mage::getStoreConfig('spp/media/updateproductimage', $this->store_id)) {
            $config['updateproductimage'] = true;
            $config['product_image_markup'] = Mage::getStoreConfig('spp/markup/product_image_markup', $this->store_id);
        } else {
            $config['updateproductimage'] = false;
        }
        $config['priceFromLabel'] = $this->__(Mage::getStoreConfig('spp/setting/spp_from', $this->store_id));
        if(Mage::app()->getStore()->isCurrentlySecure()) {
            $config['ajaxBaseUrl'] = Mage::getUrl('spp/ajax/', array('_secure' => true));
        } else {
            $config['ajaxBaseUrl'] = Mage::getUrl('spp/ajax/');
        }
        $config['product_price_markup'] = Mage::getStoreConfig('spp/markup/product_price_markup', $this->store_id);
        if(Mage::app()->getRequest()->getActionName() != 'configure') {
            $cheapestId = Mage::helper("spp")->findCheapestSimpleProdyctId($childProducts);
            $attrs = $this->showOutOfStockProducts($cheapestId);
        } else {
            $cart = Mage::getSingleton('checkout/cart');
            $quoteItem = $cart->getQuote()->getItemById(Mage::app()->getRequest()->getParam('id'));
            $attrs = $this->showOutOfStockProducts(Mage::app()->getRequest()->getParam('id'));
            $productOptions = $quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($quoteItem->getProduct());
            $simple = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getIdBySku($productOptions['simple_sku']));
            $config['configure'] = $simple->getId();
        }

        if( ! empty($attrs)) {
            foreach($attrs as $key => $attr) {
                if(isset($attr['options'])) {
                    foreach($attr['options'] as $id => $info) {
                        if(isset($info['products'])) {
                            $config['attributes'][$key]['options'][$id]['products'] = $info['products'];
                        }
                    }
                }
                if(isset($attr['preselect'])) {
                    $config['attributes'][$key]['preselect'] = $attr['preselect'];
                } else {
                    $config['preselect'] = false;
                }
            }
        }
        $config['zoomtype'] = Mage::getStoreConfig('spp/media/zoomtype', $this->store_id);
        if( ! Mage::getStoreConfig('spp/details/producturl', $this->store_id) || Mage::getStoreConfig('catalog/seo/product_use_categories', $this->store_id)) {
            $config['updateUrl'] = false;
        } else {
            $config['updateUrl'] = true;
            $config['cUrlKey'] = Mage::helper('urlrewrite')->getRealUrlKey($this->getProduct()->getId());
        }
        $jsonConfig = Zend_Json::encode($config);
        if(intval(Mage::getStoreConfig('spp/setting/spp_cache_lifetime', $this->store_id)) > 0 && $saveCache) {
            $cache->save(serialize($jsonConfig), "jsonconfig_" . $cacheKey, array("jsonconfig_cache_" . $this->getProduct()->getId()), $cache_lifetime);
        }
        return $jsonConfig;
    }

    public function getAllowProducts() {
        if( ! $this->hasAllowProducts()) {
            $products = array();
            $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                    ->getUsedProducts(null, $this->getProduct());
            foreach($allProducts as $product) {
                if(Mage::getStoreConfig('spp/details/show', $this->store_id)) {
                    $products[] = $product;
                } else {
                    if($product->isSaleable() || $skipSaleableCheck) {
                        $products[] = $product;
                    }
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    /**
     * this is needed for CE 1.9.3+ and EE 1.14.3+
     * @return array
     */
    public function showOutOfStockProducts($cheapestId) {
        $preselect = false;
        if($this->getProduct()->getId() == Mage::app()->getRequest()->getParam("id")) {
            if(Mage::getStoreConfig('spp/setting/preselect', $this->store_id)) {
                $preselect = true;
            }
            if($preselectedId = Mage::helper("spp")->getPreslectedId($this->getProduct()->getId())) {
                $preselect = true;
                $cheapestId = $preselectedId;
            }
        } else {
            $preselect = true;
            $cheapestId = (int) Mage::app()->getRequest()->getParam("id");
        }

        $child = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($cheapestId);
        $attributes = array();
        $options = array();
        $productStock = array();
        foreach($this->getAllowProducts() as $product) {
            $productId = $product->getId();
            $productStock[$productId] = $product->getStockItem()->getIsInStock();
            foreach($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if( ! isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }
                if( ! isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
            foreach($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();
                $info = array(
                    'options' => array()
                );
                $prices = $attribute->getPrices();
                if(is_array($prices)) {
                    foreach($prices as $value) {
                        if($preselect) {
                            $childValue = $child->getData($productAttribute->getAttributeCode());
                            if($value['value_index'] == $childValue) {
                                $selectedOption = $value['value_index'];
                            }
                        }
                        if( ! $this->_validateAttributeValue($attributeId, $value, $options)) {
                            continue;
                        }
                        if(isset($options[$attributeId][$value['value_index']])) {
                            $productsIndexOptions = $options[$attributeId][$value['value_index']];
                            $productsIndex = array();
                            foreach($productsIndexOptions as $productIndex) {
                                if(Mage::getStoreConfig('spp/details/show', $this->store_id)) {
                                    $productsIndex[] = $productIndex;
                                } else {
                                    if($productStock[$productIndex]) {
                                        $productsIndex[] = $productIndex;
                                    }
                                }
                            }
                        } else {
                            $productsIndex = array();
                        }
                        $info['options'][] = array(
                            'products' => $productsIndex,
                        );
                    }
                }
                $attributes[$attributeId] = $info;
                if($preselect) {
                    $attributes[$attributeId]['preselect'] = $selectedOption;
                }
            }
        }
        return $attributes;
    }

    /**
     * this is needed for CE 1.9.3- and EE 1.14.3-
     * @return array
     */
    public function preSelectSimpleProduct($cheapestId) {

        $child = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($cheapestId);
        $attributes = array();
        $options = array();
        foreach($this->getAllowProducts() as $product) {
            $productId = $product->getId();
            foreach($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if( ! isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }
                if( ! isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
            foreach($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();
                $prices = $attribute->getPrices();
                if(is_array($prices)) {
                    foreach($prices as $value) {
                        $childValue = $child->getData($productAttribute->getAttributeCode());
                        if($value['value_index'] == $childValue) {
                            $selectedOption = $value['value_index'];
                        }
                        if( ! $this->_validateAttributeValue($attributeId, $value, $options)) {
                            continue;
                        }
                    }
                }
                $attributes[$attributeId]['preselect'] = $selectedOption;
            }
        }
        return $attributes;
    }

}
