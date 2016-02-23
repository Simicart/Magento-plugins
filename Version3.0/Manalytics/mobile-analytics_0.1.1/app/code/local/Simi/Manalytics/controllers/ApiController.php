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
 * Manalytics Index Controller
 * 
 * @category 	
 * @package 	Manalytics
 * @author  	Developer
 */
class Simi_Manalytics_ApiController extends Simi_Connector_Controller_Action
{
	/**
	 * index action
	 */
	public function get_ga_idAction(){
		 $information = Mage::getModel('manalytics/manalytics')->getGAId();
        $this->_printDataJson($information);
	}
}