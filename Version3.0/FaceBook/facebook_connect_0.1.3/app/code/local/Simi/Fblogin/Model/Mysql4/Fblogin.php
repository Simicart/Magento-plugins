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
 * @category 	Magestore
 * @package 	Magestore_Fblogin
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Fblogin Resource Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Fblogin
 * @author  	Magestore Developer
 */
class Simi_Fblogin_Model_Mysql4_Fblogin extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('fblogin/fblogin', 'fblogin_id');
	}
}