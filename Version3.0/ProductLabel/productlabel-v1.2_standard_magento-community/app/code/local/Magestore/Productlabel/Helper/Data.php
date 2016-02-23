<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Productlabel
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Productlabel Helper
 * 
 * @category    
 * @package     Productlabel
 * @author      Developer
 */
class Magestore_Productlabel_Helper_Data extends Mage_Core_Helper_Abstract {

    public function convertBackendString($string, $product, $storeId = null) {
		if (!is_object($product)) {
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product);
        }
        // Special Price
        $special_price = $product->getFinalPrice();
		
		$pId        = $product->getId();
        $storeId    = $product->getStoreId();
		$date = Mage::app()->getLocale()->storeTimeStamp($storeId);
		if ($product->hasCustomerGroupId()) {
            $gId = $product->getCustomerGroupId();
        } else {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
		
		$websites = Mage::app()->getWebsites();
		
		$final_price = 0;
		
		foreach($websites as $website) {
			$wId = $website->getId();
			if($wId) {
				
				$key = "$date|$wId|$gId|$pId";
				
				
				
				$rulePrice = Mage::getResourceModel('catalogrule/rule')
					->getRulePrice($date, $wId, $gId, $pId);
				
				$final_price = $rulePrice;
				
				
				break;
				
			} 
		}
	
	
		if($special_price && $final_price) {
			$special_price = min($final_price, $special_price);
		} elseif($final_price && $special_price == 0) {
			$special_price = $final_price;
		} else {
			$special_price = $special_price;
		} 
		// print_r($special_price);echo "<br>";
		if ($special_price)
            $str = str_replace("{{special_price}}", Mage::helper('core')->currency($special_price, true, false), $string);
        else
            $str = str_replace("{{special_price}}", "", $string);
		
        // if ($special_price)
            // $str = str_replace("{{special_price}}", Mage::helper('core')->currency($special_price, true, false), $string);
        // else
            // $str = str_replace("{{special_price}}", "", $string);
			
        // Regular Price
        $regular_price = $product->getPrice();
        if ($regular_price) {
            $str = str_replace("{{regular_price}}", Mage::helper('core')->currency($regular_price, true, false), $str);
        } else {
            $str = str_replace("{{regular_price}}", "", $str);
		}
		
        // Discount Amount
        if ($special_price) {
            if ($special_price < $regular_price) {
				$discount_amount = 100 - round($special_price / $regular_price * 100);
                $str = str_replace("{{discount_amount}}", $discount_amount . "%", $str);
            }
            else {
                $str = str_replace("{{discount_amount}}", "", $str);
			}
        }

        // Save Amount
        if ($special_price) {
            if ($special_price < $regular_price) {
                $save_amount = $regular_price - $special_price;
                //Zend_Debug::dump($save_amount);die('1');
            }
            $str = str_replace("{{save_amount}}", Mage::helper('core')->currency($save_amount, true, false), $str);
        }
        else
            $str = str_replace("{{save_amount}}", "", $str);
        // Line Break
        $str = str_replace("{{line_break}}", "<br />", $str);
        // Product SKU
        $sku = $product->getSku();
        $str = str_replace("{{sku}}", $sku, $str);
        // In stock Amount
        $in_stock_amount = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
        if ($in_stock_amount)
            $str = str_replace("{{in_stock_amount}}", $in_stock_amount, $str);
        else
            $str = str_replace("{{in_stock_amount}}", "", $str);
			$str = str_replace('\\r\\n', '', Mage::getSingleton('core/resource')->getConnection('default_write')->quote($str));
			$str = str_replace('\'', '', $str);
        return $str;
    }

    public function getIsActiveLabel($storeid) {
        return Mage::getStoreConfig('productlabel/general/enable', $storeid);
    }

    public static function uploadImage($type) {
        self::createImageFolder();

        $image_path = Mage::getBaseDir('media') . DS . 'productlabel' . DS . 'label' . DS;

        $image = "";
        if (isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader($type);

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                $uploader->save($image_path, $_FILES[$type]['name']);
            } catch (Exception $e) {
                
            }

            $image = $_FILES[$type]['name'];
        }
        return $image;
    }

    public static function createImageFolder() {
        $image_path = Mage::getBaseDir('media') . DS . 'productlabel' . DS . 'label';
        if (!is_dir($image_path)) {
            try {

                mkdir($image_path);
                chmod($image_path, 0777);
            } catch (Exception $e) {
                
            }
        }
    }

    public static function deleteImageFile($image) {

        if (!$image) {
            return;
        }
        $dirImg = Mage::getBaseDir() . str_replace("/", DS, strstr($image, '/media'));
        if (!file_exists($dirImg)) {
            return;
        }
        
        try {

            unlink($dirImg);
        } catch (Exception $e) {
            
        }
    }

}