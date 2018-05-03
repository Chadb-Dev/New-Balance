<?php
/**
 * Customer model
 *
 * @category    GoMedia
 * @package     GoMedia_Customer
 * @author      Stock2Shop Team <suppor@stock2shop.com>
 */
class GoMedia_Customer_Model_Customer extends Mage_Customer_Model_Customer
{

    /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @param string $password
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0', $password = null)
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

//        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
//            array('customer' => $this, 'back_url' => $backUrl), $storeId);
        $this->cleanPasswordsValidationData();

        return $this;
    }
}
