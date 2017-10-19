<?php

class IWD_StoreLocator_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout(array('default', 'dealers_index_index'));
        $title = Mage::getStoreConfig('storelocator/meta/title');
        $description = Mage::getStoreConfig('storelocator/meta/description');
        $keywords = Mage::getStoreConfig('storelocator/meta/keywords');
        $head = $this->getLayout()->getBlock('head');

        if (!empty($title)) {
            $head->setTitle($title);
        }

        if (!empty($description)) {
            $head->setDescription($description);
        }

        if (!empty($keywords)) {
            $head->setKeywords($keywords);
        }

        $this->renderLayout();
    }

}
