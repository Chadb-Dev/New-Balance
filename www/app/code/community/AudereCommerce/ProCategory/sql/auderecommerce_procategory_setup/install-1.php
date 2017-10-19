<?php
/**
 * Audere Commerce
 *
 * NOTICE OF LICENCE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customise this module for your
 * needs please contact Audere Commerce (http://www.auderecommerce.com).
 *
 * @category    AudereCommerce
 * @package     AudereCommerce_ProCategory
 * @copyright   Copyright (c) 2013 Audere Commerce Limited. (http://www.auderecommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      James Withers <james.withers@auderecommerce.com>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$ruleTableName = $installer->getTable('auderecommerce_procategory/rule');
$sql = <<<SQL
CREATE TABLE `$ruleTableName` (
	`rule_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Rule Id',
	`name` VARCHAR(255) NULL DEFAULT NULL,
	`is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`description` TEXT NULL,
	`strict` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`conditions_serialized` MEDIUMTEXT NULL,
	PRIMARY KEY (`rule_id`)
)
COMMENT='AudereCommerce Category Product'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;        
SQL;
$installer->getConnection()->query($sql);

$categoryProductTableName = $installer->getTable('auderecommerce_procategory/category_product');
$catalogCategoryEntity = $installer->getTable('catalog/category');
$catalogProductEntity = $installer->getTable('catalog/product');
$sql = <<<SQL
CREATE TABLE `$categoryProductTableName` (
	`rule_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`category_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Category ID',
	`product_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	UNIQUE INDEX `UNQ_AC_C_P` (`rule_id`, `category_id`, `product_id`),
	INDEX `FK_AC_C_P_P_I` (`product_id`),
	INDEX `FK_AC_C_P_C_I` (`category_id`),
	CONSTRAINT `FK_AC_C_P_C_I` FOREIGN KEY (`category_id`) REFERENCES `$catalogCategoryEntity` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_AC_C_P_P_I` FOREIGN KEY (`product_id`) REFERENCES `$catalogProductEntity` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_AC_C_P_R_I` FOREIGN KEY (`rule_id`) REFERENCES `$ruleTableName` (`rule_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;     
SQL;
$installer->getConnection()->query($sql);

$installer->endSetup();