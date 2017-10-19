<?php

class IWD_StoreLocator_Adminhtml_Iwd_Storelocator_JsonController extends Mage_Core_Controller_Front_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/iwdall/storelocator');
    }

    public function regionAction()
    {
        $region = Mage::helper('storelocator')->prepareRegion($this);
    }

    public function searchAction()
    {
        Mage::helper('storelocator')->prepareResultSearch($this);
    }

    public function geolocationAction()
    {
        Mage::helper('storelocator/geolocation')->getDataJson($this);
    }
}