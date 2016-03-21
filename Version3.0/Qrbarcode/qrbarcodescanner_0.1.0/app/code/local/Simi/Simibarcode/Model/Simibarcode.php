<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simibarcode
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simibarcode Model
 * 
 * @category 	
 * @package 	Simibarcode
 * @author  	Developer
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
		$information = $this->statusError(array(Mage::helper('simibarcode')->__('No product matching code')));
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