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
 * @package     Magestore_SimiAvenue
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simiavenue Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiAvenue
 * @author      Magestore Developer
 */
class Simi_SimiAvenue_Model_Mysql4_Simiavenue extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simiavenue/simiavenue', 'simiavenue_id');
    }
}