<?php

class IWD_StoreLocator_Model_Observer
{

    public function checkRequiredModules($observer)
    {
        $cache = Mage::app()->getCache();

        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                if ($cache->load("iwd_storelocator") === false) {
                    $message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Store Locator</strong> installation.<br />
                        Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and setup it via Magento Connect.<br />
                        Please refer to installation <a href="https://docs.google.com/document/d/1Q2FmWcv4lIipqPR0QhaLVQs1IzrrYrN97XQds1MyJ_0/edit" target="_blank">guide</a>';

                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_storelocator', array("iwd_storelocator"), $lifeTime = 5);
                }
            }
        }
    }
}