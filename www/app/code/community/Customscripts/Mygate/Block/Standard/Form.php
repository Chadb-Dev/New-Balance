<?php
/*
 * Copyright (C) 2015 CustomScripts - All Rights Reserved
 *
 * Unauthorized copying, modifying or distribution of this file,
 * via any medium is strictly prohibited.
 *
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from CustomScripts.
 *
 * Written by Pierre du Plessis <info@customscripts.co.za>, January 2015
 */

/**
 * CustomScripts MyGate Extension for Magento
 *
 * @category   Payment
 * @package    Customscripts_Mygate_Block_Standard
 * @author     Pierre du Plessis
 * @copyright  Copyright (c) 2015 Customscripts (http://www.customscripts.co.za)
 */
class Customscripts_Mygate_Block_Standard_Form extends Mage_Payment_Block_Form
{
    /**
     * This constructor intentionally only have 1 underscore
     *
     * @see Mage_Core_Block_Abstract::_construct
     */
    protected function _construct()
    {
        $this->setTemplate('mygate/standard/form.phtml');
        parent::_construct();
    }
}