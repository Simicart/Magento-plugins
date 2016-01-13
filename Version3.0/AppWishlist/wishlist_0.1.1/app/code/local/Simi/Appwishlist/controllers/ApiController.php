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
 * @package     Magestore_Appwishlist
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Appwishlist Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Appwishlist
 * @author      Magestore Developer
 */
class Simi_Appwishlist_ApiController extends Simi_Connector_Controller_Action
{
    /**
     * index action
     */
    public function get_wishlist_productsAction(){
        $data = $this->getData();
        $information = Mage::getModel('appwishlist/appwishlist')->getWishlistProducts($data);
        $this->_printDataJson($information);
    }
	public function remove_product_from_wishlistAction(){
        $data = $this->getData();
        $information = Mage::getModel('appwishlist/appwishlist')->removeProductFromWishlist($data);
        $this->_printDataJson($information);
    }
	public function add_product_to_wishlistAction(){      
        $data = $this->getData();
        $information = Mage::getModel('appwishlist/appwishlist')->addProductToWishlist($data);
        $this->_printDataJson($information);
    }
	public function add_wishlist_product_to_cartAction(){
        $data = $this->getData();
		$information = Mage::getModel('appwishlist/appwishlist')->addWishlistProductToCart($data);
		$this->_printDataJson($information);
	}    
    
	public function get_qtyAction(){
		$information = Mage::getModel('appwishlist/appwishlist')->getWishlistQty();
		$this->_printDataJson($information);
	}
}