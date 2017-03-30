<?php

class Simi_Simipayuindia_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'simipayuindia';
    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
	
	protected $_formBlockType = 'simipayuindia/simipayuindia'; 
	
 	/**
     * Return Order place direct url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('simipayuindia/api/index', array('_secure' => true));
    }
	
    public function getCustomerId()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/customer_id');
    }
    
    // public function getAccepteCurrency()
    // {
    //     return Mage::getStoreConfig('payment/' . $this->getCode() . '/currency');
    // }
    
    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    public function parseResponse()
    {       

            return true;
    
    }
}