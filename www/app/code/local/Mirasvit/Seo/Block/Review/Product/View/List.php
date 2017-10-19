<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.3.22
 * @build     1454
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */


if (Mage::helper('mstcore')->isModuleInstalled('Magpleasure_Ajaxreviews') && class_exists('Magpleasure_Ajaxreviews_Block_Review_Product_View_List')) {
    abstract class Mirasvit_Seo_Block_Review_Product_View_List_Abstract extends Magpleasure_Ajaxreviews_Block_Review_Product_View_List {
    }
} else {
    abstract class Mirasvit_Seo_Block_Review_Product_View_List_Abstract extends Mage_Review_Block_Product_View_List {
    }
}

class Mirasvit_Seo_Block_Review_Product_View_List extends Mirasvit_Seo_Block_Review_Product_View_List_Abstract
{
    public function getConfig()
    {
    	return Mage::getSingleton('seo/config');
    }

    public function getReviewUrl($id)
    {
        $product = Mage::registry('current_product');

        if ($this->getConfig()->isEnabledReviewSeoUrls()) {
            $review = Mage::getModel('review/review')->load($id);
            $title  = $review->getTitle();
            $title  = $product->formatUrlKey($title);
            $uri    = $product->getUrlKey();

            return Mage::getUrl($uri.'/reviews/'.$title.'-'.$id);
        } else {
            return parent::getReviewUrl($id);
        }
    }
}