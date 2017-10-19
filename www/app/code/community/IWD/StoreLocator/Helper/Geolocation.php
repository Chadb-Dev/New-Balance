<?php

class IWD_StoreLocator_Helper_Geolocation extends Mage_Core_Helper_Abstract
{

    public function getDataJson($controller)
    {
        $response = $this->getGetDecoderAddress($controller);
        $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    public function getGetDecoderAddress($controller, $modelStore = false)
    {
        if ($controller) {
            $data = $controller->getRequest()->getParams();
        } else {
            $data = $modelStore->getData();
        }

        $position = array();
        $address = $data['street'] . ' ' . $data['city'] . ' , ' . $this->prepareState($data) . $data['postal_code'] . ' ' . $data['country_id'];
        $query_str = '';
        $baseurl = "http://maps.googleapis.com/maps/api/geocode/json?";
        $data = array(
            "&address" => urlencode($address)
        );

        foreach ($data as $key => $value) {
            $query_str .= $key . "=" . $value;
        }

        $ch = curl_init();
        $ch = curl_init($baseurl . $query_str);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($json);

        if (!isset($data->results[0])) {
            $position['lat'] = '0';
            $position['long'] = '0';
        } else {
            $position['lat'] = $data->results[0]->geometry->location->lat;
            $position['long'] = $data->results[0]->geometry->location->lng;
        }

        return $position;
    }

    private function prepareState($data)
    {
        $countryCode = $data['country_id'];
        if (isset($data['region'])) {
            $region = $data['region'];
        } else {
            $region = '';
        }

        if (isset($data['region_id'])) {
            $regionId = $data['region_id'];
        } else {
            $regionId = '';
        }

        if (!empty($region)) {
            return $region;
        } else {
            try {
                $states = Mage::getModel('directory/region_api')->items($countryCode);
            } catch (Mage_Api_Exception $e) {
                return '';
            }

            $result = array();
            foreach ($states as $state) {
                if ($state['region_id'] == $regionId) {
                    if (empty($state['name'])) {
                        $regionModel = Mage::getModel('directory/region')->load($regionId);
                        return $regionModel->getName();
                    } else {
                        return $state['name'];
                    }
                }
            }
        }

        return '';
    }


    public function fillGeoData()
    {
        $collection = Mage::getModel('storelocator/stores')->getCollection()
            ->addFieldToFilter('latitude', array('eq' => ''))
            ->addFieldToFilter('longitude', array('eq' => ''));

        foreach ($collection as $model) {
            $position = Mage::helper('storelocator/geolocation')->getGetDecoderAddress(false, $model);

            $model->setData('latitude', $position['lat']);
            $model->setData('longitude', $position['long']);

            try {
                $model->save();
            } catch (Exception $e) {

            }
        }


        $collection = Mage::getModel('storelocator/stores')->getCollection()
            ->addFieldToFilter('latitude', array('eq' => '0'))
            ->addFieldToFilter('longitude', array('eq' => '0'));

        foreach ($collection as $model) {
            $position = Mage::helper('storelocator/geolocation')->getGetDecoderAddress(false, $model);

            $model->setData('latitude', $position['lat']);
            $model->setData('longitude', $position['long']);

            try {
                $model->save();
            } catch (Exception $e) {

            }
        }


    }

}