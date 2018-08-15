<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simideeplink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simideeplink Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Simideeplink
 * @author      Magestore Developer
 */
class Simi_Simideeplink_Block_Adminhtml_Simideeplink extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_simideeplink';
        $this->_blockGroup = 'simideeplink';
        $this->_headerText = Mage::helper('simideeplink')->__('Link Manager');
        $this->_addButtonLabel = Mage::helper('simideeplink')->__('Add Link');
        parent::__construct();
    }
}