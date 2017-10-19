<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class IWD_StoreLocator_Model_Import extends Mage_ImportExport_Model_Abstract
{


    /**
     * Form field names (and IDs)
     */
    const FIELD_NAME_SOURCE_FILE = 'import_file';
    const FIELD_NAME_IMG_ARCHIVE_FILE = 'import_image_archive';


    /**
     * Entity adapter.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected $_entityAdapter;


    private $_required = array('stores', 'title', 'is_active', 'country_id', 'region', 'street', 'city', 'postal_code');

    private $_currentHead = array();

    /**
     * Create instance of entity adapter and returns it.
     *
     * @throws Mage_Core_Exception
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $validTypes = array('storelocator' => array('model' => 'storelocator/import_entity_data', 'Label' => 'Storelocator'));


            if (isset($validTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = Mage::getModel($validTypes[$this->getEntity()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('storelocator')->__('Invalid entity model')
                    );
                }

                if (!($this->_entityAdapter instanceof Mage_ImportExport_Model_Import_Entity_Abstract)) {
                    Mage::throwException(
                        Mage::helper('storelocator')->__('Entity adapter object must be an instance of Mage_ImportExport_Model_Import_Entity_Abstract')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('storelocator')->__('Invalid entity'));
            }

            // check for entity codes integrity
            if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                Mage::throwException(
                    Mage::helper('storelocator')->__('Input entity code is not equal to entity adapter code')
                );
            }

            $this->_entityAdapter->setParameters($this->getData());
        }

        return $this->_entityAdapter;
    }

    /**
     * Returns source adapter object.
     *
     * @param string $sourceFile Full path to source file
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    protected function _getSourceAdapter($sourceFile)
    {
        return Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile);
    }


    /**
     * Override standard entity getter.
     *
     * @throw Mage_Core_Exception
     * @return string
     */
    public function getEntity()
    {
        if (empty($this->_data['entity'])) {
            Mage::throwException(Mage::helper('storelocator')->__('Entity is unknown'));
        }

        return $this->_data['entity'];
    }


    /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    public static function getWorkingDir()
    {
        return Mage::getBaseDir('var') . DS . 'storelocator' . DS;
    }

    /**
     * Import source file structure to DB.
     *
     * @return bool
     */
    public function importSource($sourceFile, $resultBlock)
    {

        $csv = new Varien_File_Csv();
        $data = $csv->getData($sourceFile);

        foreach ($data as $index => $row) {
            if ($index == 0) {
                $result = $this->prepareHead($row, $resultBlock);
                if ($result['error']) {
                    return $result;
                }
            } else {
                $result = $this->checkRow($row, $resultBlock, $index + 1);
                if (!$result['error']) {
                    $this->createRecord($row);
                }
            }
        }

        return array('error' => false);
    }

    private function createRecord($row)
    {
        $model = Mage::getModel('storelocator/stores');

        foreach ($this->_currentHead as $field => $data) {
            $value = $row[$data['index']];
            $value = trim($value);

            if ($field == 'region') {
                $indexField = $this->_currentHead['country_id']['index'];
                $value = $this->getRegion($value, $row[$indexField]);

                if (is_numeric($value)) {
                    $field = 'region_id';
                }
            }

            if ($field == 'country_id') {
                $value = strtoupper($value);
                $value = trim($value);
                if ($value == 'USA') {
                    $value = 'US';
                }
            }

            if ($field == 'latitude' || $field == 'longitude') {
                $value = floatval($value);
            }


            $value = trim($value);
            if ($field == 'stores') {
                $value = explode(',', $value);
            }

            $model->setData($field, $value);
        }

        try {
            $model->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    private function getRegion($region, $countryCode)
    {

        $exist = false;

        $states = Mage::getModel('directory/region_api')->items($countryCode);
        if (count($states) == 0) {
            return $region;
        }

        $region = strtoupper($region);
        $region = trim($region);
        foreach ($states as $state) {
            if ($state['code'] == $region) {
                return $state['region_id'];
            }
        }
    }

    private function checkRow($row, $resultBlock, $index)
    {
        $error = false;
        foreach ($this->_required as $field) {
            $indexField = $this->_currentHead[$field]['index'];

            if (is_null($row[$indexField])) {
                $resultBlock->addError('Required field ' . $field . ' is empty in row: ' . $index . '; This record has not imported;');
                $error = true;
            }

            if ($field == 'country_id') {
                $result = $this->checkCountry($row[$indexField], $resultBlock, $index);
                if ($result['error']) {
                    $error = true;
                }
            }

            if ($field == 'region') {
                $indexCounty = $this->_currentHead['country_id']['index'];
                $result = $this->checkRegion($row[$indexField], $resultBlock, $index, $row[$indexCounty]);
                if ($result['error']) {
                    $error = true;
                }
            }
        }

        //check duplicate
        $countryIndex = $this->_currentHead['country_id']['index'];
        $zipIndex = $this->_currentHead['postal_code']['index'];
        $titleIndex = $this->_currentHead['title']['index'];
        $titleIndex = trim($titleIndex);

        $country = $row[$countryIndex];
        $country = strtoupper($country);
        $country = trim($country);
        if ($country == 'USA') {
            $country = 'US';
        }

        return array('error' => $error);
    }

    private function checkRegion($region, $resultBlock, $index, $countryCode)
    {
        $countryCode = strtoupper($countryCode);
        $countryCode = trim($countryCode);

        if ($countryCode != 'US' && $countryCode != 'CA') {
            return array('error' => false);
        }

        $states = Mage::getModel('directory/region_api')->items($countryCode);

        $exist = false;
        $region = strtoupper($region);
        $region = trim($region);

        foreach ($states as $state) {
            if ($state['code'] == $region) {
                $exist = true;
            }
        }

        if (!$exist) {
            $resultBlock->addError('Unknown state "' . $region . '" in row: ' . $index . '; This record has not imported;');
            return array('error' => true);
        }

        return array('error' => false);
    }

    private function checkCountry($country, $resultBlock, $index)
    {

        $country = strtolower($country);
        $country = trim($country);
        if ($country == 'usa') {
            $country = 'us';
        }

        $optionsSession = Mage::getSingleton('core/session')->getCountryOptions(false);

        if (!$optionsSession) {
            $options = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
            Mage::getSingleton('core/session')->setCountryOptions($options);
        } else {
            $options = $optionsSession;
        }

        $exist = false;
        foreach ($options as $item) {
            if (strtolower($item['value']) == $country) {
                $exist = true;
            }
        }

        if (!$exist) {
            $resultBlock->addError('Unknown country "' . $country . '" in row: ' . $index . '; This record has not imported;');
            return array('error' => true);
        }

        return array('error' => false);

    }

    private function prepareHead($row, $resultBlock)
    {
        $this->_currentHead = array();
        foreach ($row as $index => $field) {
            $this->_currentHead[$field] = array('index' => $index);
        }

        $error = false;

        foreach ($this->_required as $field) {
            if (!isset($this->_currentHead[$field])) {
                $resultBlock->addError('Required field ' . $field . ' not found;');
                $error = true;
            }
        }

        return array('error' => $error, 'resultBlock' => $resultBlock);
    }


    /**
     * Move uploaded file and create source adapter instance.
     *
     * @throws Mage_Core_Exception
     * @return string Source file path
     */
    public function uploadSource()
    {
        $entity = $this->getEntity();
        $uploader = Mage::getModel('core/file_uploader', self::FIELD_NAME_SOURCE_FILE);
        $uploader->skipDbProcessing(true);
        $result = $uploader->save(self::getWorkingDir());
        $extension = pathinfo($result['file'], PATHINFO_EXTENSION);

        $uploadedFile = $result['path'] . $result['file'];
        if (!$extension) {
            unlink($uploadedFile);
            Mage::throwException(Mage::helper('storelocator')->__('Uploaded file has no extension'));
        }

        $sourceFile = self::getWorkingDir() . $entity;

        $sourceFile .= '.' . $extension;

        if (strtolower($uploadedFile) != strtolower($sourceFile)) {
            if (file_exists($sourceFile)) {
                unlink($sourceFile);
            }

            if (!@rename($uploadedFile, $sourceFile)) {
                Mage::throwException(Mage::helper('storelocator')->__('Source file moving failed'));
            }
        }

        // trying to create source adapter for file and catch possible exception to be convinced in its adequacy
        try {
            $this->_getSourceAdapter($sourceFile);
        } catch (Exception $e) {
            unlink($sourceFile);
            Mage::throwException($e->getMessage());
        }

        return $sourceFile;
    }

}

