<?php 
/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {

    protected function _prepareValues($attr) {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
                'apply_to' => $this->_getValue($attr, 'apply_to'),
                 'used_in_product_listing'       => $this->_getValue($attr, 'used_in_product_listing', 1),
            )
        );
        return $data;
  }
  
  public function getDefaultEntities() {

        return array(
            'catalog_product' => array(
                'entity_model' => 'catalog/product',
                'attribute_model' => 'catalog/resource_eav_attribute',
                'table' => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes' => array(
                    'use_simple_product_pricing' => array(
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Use simple product pricing',
                        'note' => 'If set to "Yes", configurable product will use simple product pricing',
                        'input' => 'boolean',
                        'class' => '',
                        'source' => 'eav/entity_attribute_source_boolean',
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => 1,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'apply_to' => 'configurable',
                        'used_in_product_listing' => true,
                        'unique' => false,
                    )
                ),
            ),
        );
    }

}