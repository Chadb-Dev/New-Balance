<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
 
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute('catalog_product', 'preorder', array(
    'label'        => 'Pre-Order',
    //'group'        => 'General',
    'visible'      => 0,
    'required'     => 0,
    'position'     => 1,
    'type'         => 'int',
    'input'        => '0',
    'default'      => '0',
));

$installer->addAttribute('catalog_product', 'preorderdescript', array(
    'label'        => 'Pre-Order Note',
    //'group'        => 'General',
    'visible'      => 0,
    'required'     => 0,
    'position'     => 1,
    'type'         => 'varchar',
    'input'        => '0',
));


$installer->endSetup(); 

?>