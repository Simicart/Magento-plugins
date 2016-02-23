<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simicontact
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simicontact Model
 * 
 * @category    
 * @package     Simicontact
 * @author      Developer
 */
class Simi_Simicontact_Model_Contact extends Simi_Connector_Model_Abstract {

    public function getContacts() {
        if ($this->getConfig("enable") == 0) {
            $information = $this->statusError(array('Extesnion was disabled'));
            return $information;
        }
        $data = array(
            'email' => $this->_getEmails(),
            'phone' => $this->_getPhoneNumbers(),
            'message' => $this->_getMessageNumbers(),
            'website' => $this->getConfig("website"),
            'style' => $this->getConfig("style"),
            'activecolor' => $this->getConfig("icon_color")
        );
        $information = $this->statusSuccess();
        $information['data'] = array($data);
        return $information;
    }

    public function _getPhoneNumbers() {
        return explode(",", str_replace(' ', '', $this->getConfig("phone")));
    }

    public function _getMessageNumbers() {
        return explode(",", str_replace(' ', '', $this->getConfig("message")));
    }
    
    public function _getEmails() {
        $emails = explode(",", str_replace(' ', '', $this->getConfig("email")));
        foreach ($emails as $index=>$email) {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                unset($emails[$index]);
        }
        return $emails;
    }

    public function getConfig($value) {
        return Mage::getStoreConfig("simicontact/general/" . $value);
    }

}
