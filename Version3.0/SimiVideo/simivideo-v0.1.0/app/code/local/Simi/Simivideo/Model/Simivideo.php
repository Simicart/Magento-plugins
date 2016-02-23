<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category  
 * @package   Simivideo
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Simi Model
 * 
 * @category  
 * @package   Simivideo
 * @author    Developer
 */
class Simi_Simivideo_Model_Simivideo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simivideo/simivideo');
    }
}