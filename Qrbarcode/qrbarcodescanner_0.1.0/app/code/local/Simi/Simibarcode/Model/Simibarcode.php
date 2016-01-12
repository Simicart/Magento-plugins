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
 * @package 	Magestore_Simibarcode
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simibarcode Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Simibarcode
 * @author  	Magestore Developer
 */
class Simi_Simibarcode_Model_Simibarcode extends Simi_Connector_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('simibarcode/simibarcode');
	}

	public function checkCode($data)
	{
		$code = $data->code;
		$type = $data->type;
		$arrayReturn = array();
		$information = $this->statusError(array(Mage::helper('simibarcode')->__('No Product matching the code')));
		if(isset($code) && $code != ''){
			if($type == '1'){
				$qrcode = Mage::getModel('simibarcode/simibarcode')->load($code, 'qrcode');
				if($qrcode->getId() && $qrcode->getBarcodeStatus() == '1'){
				// if($code == 'King'){
					$productId = $qrcode->getProductEntityId();
					$product = Mage::getModel('catalog/product')->load($productId);
					if($product->getStatus() == '1'){
						$information = $this->statusSuccess();
						$arrayReturn[] = array('product_id' => $productId);
						$information['data'] = $arrayReturn;
					}
				}	
			}else{
				$barcode = Mage::getModel('simibarcode/simibarcode')->load($code, 'barcode');
				if($barcode->getId() && $barcode->getBarcodeStatus() == '1'){
					$productId = $barcode->getProductEntityId();
					$product = Mage::getModel('catalog/product')->load($productId);
					if($product->getStatus() == '1'){
						$information = $this->statusSuccess();
						$arrayReturn[] = array('product_id' => $productId);
						$information['data'] = $arrayReturn;
					}
				}
			}
		}
		return $information;
	}

	// public function statusError($error = array('NO DATA')) 
	// {
 //        return array(
 //            'status' => 'Scanning Error',
 //            'message' => $error,            
 //        );
 //    }
}