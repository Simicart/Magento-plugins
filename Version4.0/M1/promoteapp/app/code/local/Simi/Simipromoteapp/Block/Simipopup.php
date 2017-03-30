<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Popup
 * @copyright   Copyright (c) 2016
 * @license    
 */
class Simi_Simipromoteapp_Block_Simipopup extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        $this->isMobile();
        return parent::_prepareLayout();
    }

    public function isMobile() {
        $this->setTemplate('simipromoteapp/popup.phtml');
        return true;
    }

}
