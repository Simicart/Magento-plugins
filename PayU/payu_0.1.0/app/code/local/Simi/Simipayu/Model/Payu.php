<?php

class Simi_Simipayu_Model_Payu extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'simipayu';
    protected $_canUseForMultishipping  = false;
	
	protected $_formBlockType = 'simipayu/form'; 
 	protected $_infoBlockType = 'simipayu/info';
	
 	/**
     * Return Order place direct url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('simipayu/api/index', array('_secure' => true));
    }
	
}