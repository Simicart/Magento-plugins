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
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Model_Mysql4_Productlabelentity extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('productlabel/productlabelentity', 'product_label_entity_id');
    }
    
}