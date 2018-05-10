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


$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('seo');
if ($version == '0.2.6') {
    return;
} elseif ($version != '0.2.5') {
    die("Please, run migration 0.2.5");
}

$installer->startSetup();
$helper = Mage::helper('seo/migration');

$sql = "ALTER TABLE `{$this->getTable('seo/redirect')}` ADD `redirect_type` int(10) UNSIGNED NOT NULL DEFAULT '301' COMMENT 'REDIRECT STATUS CODE';";

$helper->trySql($installer, $sql);

$sql = "INSERT INTO `{$this->getTable('seo/redirect')}` (url_from, url_to, is_redirect_only_error_page, comments, redirect_type) 
values ('/*', '/', '1', 'Example redirect rule', '301');";

$helper->trySql($installer, $sql);
$installer->endSetup();