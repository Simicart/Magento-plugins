<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Paytabsmobile
 * @copyright 	Copyright (c) 2012
 * @license 	
 */

/**
 * Paytabsmobile Resource Collection Model
 * 
 * @category 	
 * @package 	Paytabsmobile
 * @author  	Developer
 */
class Simi_Paytabsmobile_Model_Mysql4_Paytabsmobile_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('paytabsmobile/paytabsmobile');
    }

}
