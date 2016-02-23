<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibarcode
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simibarcode Helper
 * 
 * @category    
 * @package     Simibarcode
 * @author      Developer
 */
class Simi_Simibarcode_Helper_Data extends Mage_Core_Helper_Data
{

    /**
     * Barcode Config
     * 
     * return string
     */
    public function getBarcodeConfig($code)
    {
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig('simibarcode/barcode/'.$code, $storeId);
    }

    /**
     * Validate code
     * 
     * return string
     */
	public function getValidateBarcode() 
    {
        $validate = 'required-entry';
        return $validate;
    }

    /**
     * Generate code
     * 
     * return string
     */
    public function generateCode($string) 
    {
        $barcode = preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', array($this, 'convertExpression'), $string);
        $checkBarcodeExist = Mage::getModel('simibarcode/simibarcode')->load($barcode, 'barcode');

        if ($checkBarcodeExist->getId()) {
            $barcode = $this->generateCode($string);
        }

        return $barcode;
    }

    /**
     * Random code
     * 
     * return string
     */
    public function convertExpression($param) 
    {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return $this->getRandomString($param[2], $alphabet);
    }

    /**
     * get All column
     * 
     * return Array
     */
    public function getAllColumOfTable($model) 
    {
        $resource = Mage::getSingleton('core/resource');
        $tablename = $resource->getTableName($model);
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll("SHOW COLUMNS FROM " . $tablename . ";");
        $return = array();
        foreach ($results as $result) {
            $return[] = $result['Field'];
        }
         
        return $return;
    }

    /**
     * get value for barcode
     * 
     * param String $table, String $column, int $productId, array $data
     * return Array
     */
    public function getValueForBarcode($table, $column, $productId, $data) 
    {
        if ($table == 'product') {

            $model = Mage::getModel('catalog/product')->load($productId);
            return $model->getData($column);
        }
    }

    /**
     * import Product
     * 
     * param array()
     */
    public function importProduct($data) 
    {
        if (count($data)) {
            Mage::getModel('admin/session')->setData('null_barcode_product_import', 0);
        } else {
            Mage::getModel('admin/session')->setData('null_barcode_product_import', 1);
            Mage::getModel('admin/session')->setData('barcode_product_import', null);
        }
    }

}