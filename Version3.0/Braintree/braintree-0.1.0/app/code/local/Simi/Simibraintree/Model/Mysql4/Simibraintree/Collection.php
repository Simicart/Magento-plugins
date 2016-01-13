<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simicart.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Simi
 * @package 	Simi_Simibraintree
 * @copyright 	Copyright (c) 2012 Magestore (http://www.simicart.com/)
 * @license 	http://www.simicart.com/license-agreement.html
 */

 /**
 * Simibraintree Resource Collection Model
 * 
 * @category 	Simi
 * @package 	Simi_Simibraintree
 * @author  	Simi Developer
 */
class Simi_Simibraintree_Model_Mysql4_Simibraintree_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('simibraintree/simibraintree');
	}
}