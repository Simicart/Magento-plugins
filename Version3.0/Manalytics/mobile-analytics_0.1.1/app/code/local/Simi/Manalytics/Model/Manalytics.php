<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Manalytics
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Manalytics Model
 * 
 * @category 	
 * @package 	Manalytics
 * @author  	Developer
 */
class Simi_Manalytics_Model_Manalytics extends Simi_Connector_Model_Abstract
{
	public function getGAId(){
		$enable = (int) Mage::getStoreConfig('manalytics/general/enable');
		if ($enable == 0){
			 $mesage = Mage::helper("core")->__("Exenstion Manalytics was disabled");
			 $information = $this->statusError();         
			 $information['message'] = array($mesage);
			 return $information;
		}else{
			 $id =  Mage::getStoreConfig('manalytics/general/ga_id');
			 $data = array(
				"ga_id" => $id,
			 );
			 $information = $this->statusSuccess();         
			 $information['data'] = array($data);
			 return $information;
		}
		
	}
}