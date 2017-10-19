<?php

class IWD_StoreLocator_Block_Search extends Mage_Core_Block_Template
{
    const XMLAPIKEY = 'storelocator/gmaps/apikey';
    /**
     * GET ALL AVAILABLE COUNTRY
     */
    public function getCountries()
    {
        $result = array();
        $collection = Mage::getModel('storelocator/stores')->getCollection()->addFieldToFilter(
            'is_active', array('eq' => '1')
        );

        $storeCodes = array();
        foreach ($collection as $item) {
            $storeCodes[] = $item->getCountryId();
        }

        $storeCodes = array_unique($storeCodes);


        $options = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();

        foreach ($options as $option) {
            if (in_array($option['value'], $storeCodes)) {
                $result[] = $option;
            }
        }

        return $result;
    }

    public function getApiKey()
    {
        return Mage::getStoreConfig(self::XMLAPIKEY);
    }


    protected function _getMarkerFile()
    {
        $folderName = IWD_StoreLocator_Model_System_Marker::UPLOAD_DIR;
        $storeConfig = Mage::getStoreConfig('storelocator/gmaps/marker');
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            $url = $faviconFile;
        } else {
            $url = Mage::getStoreConfig('web/unsecure/base_skin_url') . 'frontend/base/default/css/iwd/storelocator/images/marker.png';
        }

        return $url;
    }

    protected function _getSearchMarkerFile()
    {
        $folderName = IWD_StoreLocator_Model_System_Marker::UPLOAD_DIR;
        $storeConfig = Mage::getStoreConfig('storelocator/search_decorator/marker');
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            $url = $faviconFile;
        } else {
            $url = Mage::getStoreConfig('web/unsecure/base_skin_url') . 'frontend/base/default/css/iwd/storelocator/images/center.png';
        }

        return $url;
    }


    protected function _isFile($filename)
    {
        if (Mage::helper('core/file_storage_database')->checkDbUsage() && !is_file($filename)) {
            Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }

        return is_file($filename);
    }

    protected function getMetric()
    {
        $list = array(
            1 => Mage::helper('storelocator')->__('Km'),
            2 => Mage::helper('storelocator')->__('Miles'),
        );

        $option = Mage::getStoreConfig('storelocator/gmaps/metric');
        return $list[$option];
    }

    public function getZoom()
    {
        return Mage::getStoreConfig('storelocator/gmaps/zoom');
    }


    public function getJsonConfig()
    {

        $schema = Mage::app()->getRequest()->getScheme();
        if ($schema == 'https') {
            $_secure = true;
        } else {
            $_secure = false;
        }

        $config = new Varien_Object();

        $config->setData('image', $this->_getMarkerFile());


        $config->setData('zoomData', $this->getZoom());

        $config->setData('urlSearch', Mage::getUrl(Mage::helper('storelocator')->getRoute() . '/json/search', array('_secure' => $_secure)));
        $config->setData('scrollWheel', Mage::getStoreConfig('storelocator/gmaps/scrollwhell_zooming'));
        $config->setData('scaleControl', Mage::getStoreConfig('storelocator/gmaps/scale_control'));
        $config->setData('mapTypeControl', Mage::getStoreConfig('storelocator/gmaps/map_type_control'));
        $config->setData('closeButton', $this->getSkinUrl('css/iwd/storelocator/images/close.png'));

        if (!Mage::helper('storelocator')->isLoadInitialDisabled()) {
            $config->setData('firstload', true);
        } else {
            $config->setData('firstload', false);
        }


        /** decorator **/
        $config->setData('highlight', Mage::getStoreConfig('storelocator/search_decorator/highlight_search'));
        $config->setData('searchMarker', $this->_getSearchMarkerFile());
        $config->setData('fillColor', Mage::getStoreConfig('storelocator/search_decorator/fill_color'));
        $config->setData('fillOpacity', Mage::getStoreConfig('storelocator/search_decorator/fill_opacity'));
        $config->setData('strokeColor', Mage::getStoreConfig('storelocator/search_decorator/stroke_color'));
        $config->setData('strokeOpacity', Mage::getStoreConfig('storelocator/search_decorator/stroke_opacity'));
        $config->setData('strokeWeight', Mage::getStoreConfig('storelocator/search_decorator/stroke_weight'));


        return $config->toJson();
    }

}